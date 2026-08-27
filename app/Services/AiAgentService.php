<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Streaming\Events\TextDeltaEvent;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

class AiAgentService
{
    /**
     * Handle AI chat request with editor context.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $context
     * @return array{success: bool, message: string, thought?: string, actions: array<int, array<string, mixed>>, suggestions: array<int, string>, raw?: string, error?: string}
     */
    public function chat(array $messages, array $context = []): array
    {
        $settings = Setting::first();
        $apiKey = $settings?->ai_api_key ?: config('services.deepseek.api_key');
        $baseUrl = rtrim($settings?->ai_base_url ?: config('services.deepseek.base_url', 'https://api.deepseek.com'), '/');
        $model = $settings?->ai_model ?: config('services.deepseek.model', 'deepseek-chat');

        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'AI API key is not configured. Please configure your API key in Admin Settings > AI Assistant to get started.',
                'actions' => [],
                'suggestions' => ['Configure API Key in Admin Settings'],
                'error' => 'Missing API key',
            ];
        }

        // Dynamically search local assets + external stock APIs (Pixabay / Pexels / Unsplash) for relevant photos
        $topicQuery = $this->extractImageSearchQuery($messages, $context);
        $searchedAssets = $this->searchAssets($topicQuery, 20);

        if (! empty($searchedAssets)) {
            // Provide the topic-focused stock photos and relevant local photos to AI prompt
            $context['assets'] = $searchedAssets;
        }

        $systemPrompt = $this->buildSystemPrompt($context);
        $completion = $this->executeCompletion($systemPrompt, $messages, $apiKey, $baseUrl, $model);

        if (! $completion['success']) {
            return [
                'success' => false,
                'message' => $completion['error_message'] ?? 'The AI service encountered an error. Please try again.',
                'actions' => [],
                'suggestions' => ['Try asking again with a simpler request'],
                'error' => $completion['raw_error'] ?? null,
            ];
        }

        $rawContent = $completion['content'] ?? '';
        $parsed = $this->parseJsonResponse($rawContent);
        $rawActions = $parsed['actions'] ?? [];
        $sanitizedActions = $this->sanitizeActions($rawActions, $context);

        $result = [
            'success' => true,
            'thought' => $parsed['thought'] ?? '',
            'message' => $parsed['message'] ?? 'Changes processed successfully.',
            'actions' => $sanitizedActions,
            'suggestions' => $parsed['suggestions'] ?? [],
            'usage' => $completion['usage'] ?? null,
            'raw' => $rawContent,
        ];

        return $result;
    }

    /**
     * Agentic chat — AI fetches only the data it needs via tool calls.
     * Supports sequential section editing: AI reads → edits → reads next → edits.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $context
     * @return array{success: bool, message: string, actions: array<int, mixed>, thought: string, usage: array<string, mixed>}
     */
    public function agentChat(array $messages, array $context = []): array
    {
        $settings = Setting::first();
        $apiKey = $settings?->ai_api_key ?: config('services.deepseek.api_key');
        $baseUrl = rtrim($settings?->ai_base_url ?: config('services.deepseek.base_url', 'https://api.deepseek.com'), '/');
        $model = $settings?->ai_model ?: config('services.deepseek.model', 'deepseek-chat');

        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'AI API key is not configured. Please configure it in Admin Settings › AI Assistant.',
                'actions' => [],
                'thought' => '',
                'usage' => [],
            ];
        }

        $toolService = app(AiToolService::class);
        $tools = $toolService->getToolDefinitions();
        $systemPrompt = $this->buildAgentSystemPrompt();
        $endpoint = $this->resolveEndpoint($baseUrl);

        // Build initial conversation history
        $history = [['role' => 'system', 'content' => $systemPrompt]];
        foreach ($messages as $msg) {
            if (! empty($msg['content'])) {
                $history[] = [
                    'role' => in_array($msg['role'], ['user', 'assistant']) ? $msg['role'] : 'user',
                    'content' => (string) $msg['content'],
                ];
            }
        }

        $maxIterations = 8;
        $iteration = 0;
        $allBatches = [];   // Each apply_actions call stored as a batch
        $totalUsage = ['prompt_tokens' => 0, 'completion_tokens' => 0];
        $lastMessage = '';

        while ($iteration++ < $maxIterations) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(120)->post($endpoint, [
                    'model' => $model,
                    'messages' => $history,
                    'tools' => $tools,
                    'tool_choice' => 'auto',
                    'temperature' => 0.3,
                    'max_tokens' => 4096,
                ]);

                if (! $response->successful()) {
                    Log::error('AgentChat API error: '.$response->status().' — '.$response->body());
                    break;
                }

                $json = $response->json();
                $usage = $json['usage'] ?? [];
                $totalUsage['prompt_tokens'] += (int) ($usage['prompt_tokens'] ?? 0);
                $totalUsage['completion_tokens'] += (int) ($usage['completion_tokens'] ?? 0);

                $choice = $json['choices'][0] ?? null;
                $aiMessage = $choice['message'] ?? [];
                $toolCalls = $aiMessage['tool_calls'] ?? [];

            } catch (\Throwable $e) {
                Log::error('AgentChat HTTP exception: '.$e->getMessage());
                break;
            }

            // No tool calls — plain conversational response, we are done
            if (empty($toolCalls)) {
                $lastMessage = $aiMessage['content'] ?? '';
                break;
            }

            // Append AI message (with its tool_calls) into history
            $history[] = [
                'role' => 'assistant',
                'content' => $aiMessage['content'] ?? null,
                'tool_calls' => $toolCalls,
            ];

            // Process each tool call in this turn
            foreach ($toolCalls as $call) {
                $toolName = $call['function']['name'] ?? '';
                $toolArgs = json_decode($call['function']['arguments'] ?? '{}', true) ?: [];
                $callId = $call['id'] ?? ('call_'.uniqid());

                if ($toolName === 'apply_actions') {
                    // Store this batch of actions
                    $batchActions = $toolArgs['actions'] ?? [];
                    $batchMessage = $toolArgs['message'] ?? '';

                    if (! empty($batchActions)) {
                        $allBatches[] = [
                            'actions' => $batchActions,
                            'message' => $batchMessage,
                        ];
                    }

                    if (! empty($batchMessage)) {
                        $lastMessage = $batchMessage;
                    }

                    // Tell the AI the batch was applied and it can continue or finish
                    $history[] = [
                        'role' => 'tool',
                        'tool_call_id' => $callId,
                        'content' => json_encode([
                            'status' => 'applied',
                            'action_count' => count($batchActions),
                        ]),
                    ];

                    // Empty actions array = AI signalling it is finished
                    if (empty($batchActions)) {
                        break 2; // exit both foreach and while
                    }

                    // Prompt AI to continue with remaining sections or wrap up
                    $history[] = [
                        'role' => 'user',
                        'content' => 'Section updated successfully. Continue with the next section if needed, or finish by calling apply_actions with an empty actions array and your final summary.',
                    ];
                } else {
                    // Data-fetching tool — dispatch and feed result back to AI
                    $result = $toolService->dispatch($toolName, $toolArgs, $context);
                    $history[] = [
                        'role' => 'tool',
                        'tool_call_id' => $callId,
                        'content' => json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    ];
                }
            }
        }

        // Flatten all batches into a single actions list for the frontend
        $flatActions = [];
        $finalMessage = $lastMessage;

        foreach ($allBatches as $batch) {
            foreach ((array) ($batch['actions'] ?? []) as $act) {
                if (! empty($act) && is_array($act)) {
                    $flatActions[] = $act;
                }
            }
            if (! empty($batch['message'])) {
                $finalMessage = $batch['message'];
            }
        }

        return [
            'success' => true,
            'message' => $finalMessage ?: 'Done.',
            'actions' => $flatActions,
            'thought' => "Completed {$iteration} iterations, applied ".count($flatActions).' actions across '.count($allBatches).' section(s).',
            'usage' => $totalUsage,
        ];
    }

    /**
     * Lean system prompt for the agentic loop.
     * Delegates to dedicated AiPromptBuilder / prompt template.
     */
    protected function buildAgentSystemPrompt(): string
    {
        return AiPromptBuilder::buildAgent();
    }

    /**
     * Execute completion via Prism AI library with automated provider resolution and fallback.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array{success: bool, content?: string, usage?: array<string, mixed>, error_message?: string, raw_error?: string}
     */
    protected function executeCompletion(string $systemPrompt, array $messages, string $apiKey, string $baseUrl, string $model): array
    {
        $providerName = $this->resolveProviderName($baseUrl, $model);

        // For DeepSeek and OpenAI-compatible providers, execute direct HTTP with native json_object mode and 8k tokens
        if ($providerName === 'deepseek' || $providerName === 'openai' || str_contains($baseUrl, 'deepseek') || str_contains($model, 'deepseek')) {
            return $this->executeDirectHttpCompletion($systemPrompt, $messages, $apiKey, $baseUrl, $model);
        }

        try {
            $providerConfig = [
                'api_key' => $apiKey,
            ];
            if (! empty($baseUrl)) {
                $providerConfig['url'] = $baseUrl;
            }

            $prismMessages = [];
            foreach ($messages as $msg) {
                $content = (string) ($msg['content'] ?? '');
                if (empty($content)) {
                    continue;
                }

                $role = $msg['role'] ?? 'user';
                if ($role === 'assistant') {
                    $prismMessages[] = new AssistantMessage($content);
                } else {
                    $prismMessages[] = new UserMessage($content);
                }
            }

            $prismResponse = Prism::text()
                ->using($providerName, $model, $providerConfig)
                ->withSystemPrompt($systemPrompt)
                ->withMessages($prismMessages)
                ->withClientOptions([
                    'timeout' => 120,
                ])
                ->asText();

            return [
                'success' => true,
                'content' => $prismResponse->text,
                'usage' => [
                    'prompt_tokens' => $prismResponse->usage?->promptTokens ?? 0,
                    'completion_tokens' => $prismResponse->usage?->completionTokens ?? 0,
                ],
            ];
        } catch (\Throwable $e) {
            Log::info('Prism completion attempt handled exception: '.$e->getMessage().'. Falling back to direct HTTP completion.');

            return $this->executeDirectHttpCompletion($systemPrompt, $messages, $apiKey, $baseUrl, $model);
        }
    }

    /**
     * Stream AI chat completion response using Prism streaming or direct HTTP fallback.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $context
     * @param  callable(string $jsonChunk): void  $onChunk
     */
    public function streamChat(array $messages, array $context, callable $onChunk): void
    {
        $settings = Setting::first();
        $apiKey = $settings?->ai_api_key ?: config('services.deepseek.api_key');
        $baseUrl = rtrim($settings?->ai_base_url ?: config('services.deepseek.base_url', 'https://api.deepseek.com'), '/');
        $model = $settings?->ai_model ?: config('services.deepseek.model', 'deepseek-chat');

        if (empty($apiKey)) {
            $onChunk(json_encode([
                'type' => 'error',
                'error' => 'AI API key is not configured. Please configure your API key in Admin Settings > AI Assistant.',
            ]));

            return;
        }

        $systemPrompt = $this->buildSystemPrompt($context);

        $accumulatedText = '';
        $providerName = $this->resolveProviderName($baseUrl, $model);

        $providerConfig = [
            'api_key' => $apiKey,
        ];
        if (! empty($baseUrl)) {
            $providerConfig['url'] = $baseUrl;
        }

        $prismMessages = [];
        foreach ($messages as $msg) {
            $content = (string) ($msg['content'] ?? '');
            if (empty($content)) {
                continue;
            }

            $role = $msg['role'] ?? 'user';
            if ($role === 'assistant') {
                $prismMessages[] = new AssistantMessage($content);
            } else {
                $prismMessages[] = new UserMessage($content);
            }
        }

        // For OpenAI and DeepSeek compatible APIs, stream with native response_format json_object
        if ($providerName === 'deepseek' || $providerName === 'openai' || str_contains($baseUrl, 'deepseek') || str_contains($baseUrl, 'openai')) {
            $accumulatedText = $this->streamDirectHttpCompletion($systemPrompt, $messages, $apiKey, $baseUrl, $model, $onChunk);
        } else {
            try {
                $stream = Prism::text()
                    ->using($providerName, $model, $providerConfig)
                    ->withSystemPrompt($systemPrompt)
                    ->withMessages($prismMessages)
                    ->withClientOptions([
                        'timeout' => 90,
                    ])
                    ->asStream();

                foreach ($stream as $event) {
                    if ($event instanceof TextDeltaEvent) {
                        $accumulatedText .= $event->delta;
                        $onChunk(json_encode(['type' => 'chunk', 'delta' => $event->delta]));
                    }
                }
            } catch (\Throwable $e) {
                Log::info('Prism stream handled exception: '.$e->getMessage().'. Falling back to direct HTTP stream.');
                $accumulatedText = $this->streamDirectHttpCompletion($systemPrompt, $messages, $apiKey, $baseUrl, $model, $onChunk);
            }
        }

        $parsed = $this->parseJsonResponse($accumulatedText);
        $result = [
            'success' => true,
            'thought' => $parsed['thought'] ?? '',
            'message' => $parsed['message'] ?? 'Changes processed successfully.',
            'actions' => $parsed['actions'] ?? [],
            'suggestions' => $parsed['suggestions'] ?? [],
            'raw' => $accumulatedText,
        ];

        $onChunk(json_encode(['type' => 'done', 'result' => $result]));
    }

    /**
     * Fallback direct HTTP SSE streaming using cURL with auto-fallback to non-streamed execution.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  callable(string $jsonChunk): void  $onChunk
     */
    protected function streamDirectHttpCompletion(string $systemPrompt, array $messages, string $apiKey, string $baseUrl, string $model, callable $onChunk): string
    {
        $payloadMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($messages as $msg) {
            if (! empty($msg['content'])) {
                $payloadMessages[] = [
                    'role' => in_array($msg['role'], ['user', 'assistant', 'system']) ? $msg['role'] : 'user',
                    'content' => (string) $msg['content'],
                ];
            }
        }

        $endpoint = rtrim($baseUrl, '/').'/chat/completions';
        $accumulated = '';

        $payload = json_encode([
            'model' => $model,
            'messages' => $payloadMessages,
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.3,
            'max_tokens' => 4096,
            'stream' => true,
        ]);

        $ch = curl_init($endpoint);
        if ($ch !== false) {
            $lineBuffer = '';

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer '.$apiKey,
                    'Content-Type: application/json',
                    'Accept: text/event-stream',
                ],
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_TIMEOUT => 90,
                CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$accumulated, &$lineBuffer, $onChunk) {
                    $lineBuffer .= $data;
                    $lines = explode("\n", $lineBuffer);
                    $lineBuffer = array_pop($lines) ?? '';

                    foreach ($lines as $line) {
                        $trimmed = trim($line);
                        if (! str_starts_with($trimmed, 'data:')) {
                            continue;
                        }

                        $jsonStr = trim(substr($trimmed, 5));
                        if ($jsonStr === '[DONE]') {
                            continue;
                        }

                        $decoded = json_decode($jsonStr, true);
                        if (! is_array($decoded)) {
                            continue;
                        }

                        $delta = $decoded['choices'][0]['delta']['content'] ?? '';
                        if ($delta !== '') {
                            $accumulated .= $delta;
                            $onChunk(json_encode(['type' => 'chunk', 'delta' => $delta]));
                        }
                    }

                    return strlen($data);
                },
            ]);

            $success = curl_exec($ch);
            $curlError = curl_error($ch);
            curl_close($ch);

            if (! $success || empty($accumulated)) {
                Log::info('Direct cURL stream fell back to standard HTTP POST. Error: '.$curlError);
            }
        }

        // If cURL stream did not accumulate valid content, execute standard direct HTTP request
        if (empty($accumulated)) {
            $fallback = $this->executeDirectHttpCompletion($systemPrompt, $messages, $apiKey, $baseUrl, $model);
            $accumulated = $fallback['content'] ?? '';
        }

        return $accumulated;
    }

    /**
     * Direct HTTP fallback for custom or standard OpenAI-compatible endpoints.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array{success: bool, content?: string, usage?: array<string, mixed>, error_message?: string, raw_error?: string}
     */
    protected function executeDirectHttpCompletion(string $systemPrompt, array $messages, string $apiKey, string $baseUrl, string $model): array
    {
        $payloadMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($messages as $msg) {
            if (! empty($msg['content'])) {
                $payloadMessages[] = [
                    'role' => in_array($msg['role'], ['user', 'assistant', 'system']) ? $msg['role'] : 'user',
                    'content' => (string) $msg['content'],
                ];
            }
        }

        $maxAttempts = 3;
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxAttempts) {
            $attempt++;

            try {
                $endpoint = $this->resolveEndpoint($baseUrl);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(120)->post($endpoint, [
                    'model' => $model,
                    'messages' => $payloadMessages,
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.2,
                    'max_tokens' => 8192,
                ]);

                if ($response->successful()) {
                    $jsonResponse = $response->json();
                    $rawContent = $jsonResponse['choices'][0]['message']['content'] ?? '';

                    return [
                        'success' => true,
                        'content' => $rawContent,
                        'usage' => $jsonResponse['usage'] ?? null,
                    ];
                }

                $status = $response->status();
                $body = $response->body();
                $apiError = $response->json('error.message') ?? $response->json('message') ?? $response->json('error');

                // Check if this is a transient upstream/server error suitable for auto-retry
                $isTransient = in_array($status, [429, 500, 502, 503, 504], true)
                    || str_contains(strtolower((string) $apiError), 'upstream')
                    || str_contains(strtolower((string) $apiError), 'do request failed')
                    || str_contains(strtolower($body), 'upstream error')
                    || str_contains(strtolower($body), 'do request failed');

                if ($isTransient && $attempt < $maxAttempts) {
                    Log::warning("AI API transient upstream error (HTTP {$status}, attempt {$attempt}/{$maxAttempts}): ".(is_string($apiError) ? $apiError : 'Transient error').". Retrying in {$attempt}s...");
                    usleep(1000000 * $attempt);

                    continue;
                }

                // If non-transient or attempts exhausted, return friendly error message
                Log::error('AI API Error: '.$status.' - '.$body);

                $friendlyMessage = ! empty($apiError) && is_string($apiError)
                    ? $apiError
                    : match ($status) {
                        401 => 'Invalid API key. Please check your credentials in Admin Settings > AI Assistant.',
                        402 => 'Account credit balance reached. Please check your AI provider billing.',
                        429 => 'Rate limit reached. Please wait a moment and try again.',
                        500, 502, 503 => 'The AI upstream service is temporarily overloaded (HTTP '.$status.'). Please try again in a moment.',
                        default => 'The AI service returned an error (HTTP '.$status.'). Please try again.',
                    };

                return [
                    'success' => false,
                    'error_message' => $friendlyMessage,
                    'raw_error' => $body,
                ];
            } catch (\Throwable $e) {
                $lastException = $e;
                if ($attempt < $maxAttempts) {
                    Log::warning("AiAgentService connection attempt {$attempt}/{$maxAttempts} failed: ".$e->getMessage().". Retrying in {$attempt}s...");
                    usleep(1000000 * $attempt);

                    continue;
                }
            }
        }

        $endpointStr = $this->resolveEndpoint($baseUrl);
        $errMsg = $lastException ? $lastException->getMessage() : 'Max retry attempts reached';
        Log::error('AiAgentService Direct HTTP Exception: '.$errMsg.' [Endpoint: '.$endpointStr.']');

        return [
            'success' => false,
            'error_message' => 'Unable to reach AI service ('.$endpointStr.'): '.$errMsg,
            'raw_error' => $errMsg,
        ];
    }

    /**
     * Resolve the target chat completions API endpoint from the given base URL.
     */
    protected function resolveEndpoint(string $baseUrl): string
    {
        $url = trim($baseUrl);
        if (empty($url)) {
            return 'https://api.openai.com/v1/chat/completions';
        }

        $url = rtrim($url, '/');
        if (str_ends_with($url, '/chat/completions')) {
            return $url;
        }

        return $url.'/chat/completions';
    }

    /**
     * Resolve the appropriate Prism provider name based on base URL or model name.
     */
    protected function resolveProviderName(string $baseUrl, string $model): string
    {
        $baseUrlLower = strtolower($baseUrl);
        $modelLower = strtolower($model);

        if (str_contains($baseUrlLower, 'deepseek') || str_contains($modelLower, 'deepseek')) {
            return 'deepseek';
        }
        if (str_contains($baseUrlLower, 'anthropic') || str_contains($modelLower, 'claude')) {
            return 'anthropic';
        }
        if (str_contains($baseUrlLower, 'groq') || str_contains($modelLower, 'llama') || str_contains($modelLower, 'mixtral')) {
            return 'groq';
        }
        if (str_contains($baseUrlLower, 'openrouter')) {
            return 'openrouter';
        }
        if (str_contains($baseUrlLower, 'generativelanguage.googleapis.com') || str_contains($modelLower, 'gemini')) {
            return 'gemini';
        }
        if (str_contains($baseUrlLower, 'localhost') || str_contains($baseUrlLower, '127.0.0.1')) {
            return 'ollama';
        }

        return 'openai';
    }

    /**
     * Build the system prompt incorporating CMS knowledge, schemas, and action protocols.
     * Delegates to dedicated AiPromptBuilder and prompt template file.
     *
     * @param  array<string, mixed>  $context
     */
    protected function buildSystemPrompt(array $context): string
    {
        return AiPromptBuilder::build($context);
    }

    /**
     * Fetch active image assets from the database and storage.
     *
     * @param  array<int, array<string, mixed>>|null  $provided
     * @return array<int, array<string, mixed>>
     */
    public function getAvailableAssets(?array $provided = null): array
    {
        if (is_array($provided) && ! empty($provided)) {
            return $provided;
        }

        try {
            return Asset::where('is_directory', false)
                ->whereNotNull('path')
                ->orderByDesc('id')
                ->limit(60)
                ->get()
                ->filter(function ($asset) {
                    return ! empty($asset->path);
                })
                ->map(function ($asset) {
                    $url = str_starts_with($asset->path, 'http') ? $asset->path : '/storage/'.ltrim($asset->path, '/');

                    return [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'url' => $url,
                        'path' => $asset->path,
                        'width' => $asset->width,
                        'height' => $asset->height,
                        'mime' => $asset->mime,
                        'size' => $asset->size,
                    ];
                })
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('AiAgentService asset query failed: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Search image assets by keyword / query for AI agent tool calls.
     * Searches local media library first, with optional fallback to configured stock photo APIs (Pexels, Unsplash, Pixabay).
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchAssets(string $query = '', int $limit = 30): array
    {
        $localResults = [];

        try {
            $builder = Asset::where('is_directory', false)
                ->whereNotNull('path');

            if (! empty(trim($query))) {
                $terms = array_filter(explode(' ', trim($query)));
                $builder->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $q->where(function ($sq) use ($term) {
                            $sq->where('name', 'like', "%{$term}%")
                                ->orWhere('path', 'like', "%{$term}%");
                        });
                    }
                });
            }

            $localResults = $builder->orderByDesc('id')
                ->limit($limit)
                ->get()
                ->filter(function ($asset) {
                    return ! empty($asset->path);
                })
                ->map(function ($asset) {
                    $url = str_starts_with($asset->path, 'http') ? $asset->path : '/storage/'.ltrim($asset->path, '/');

                    return [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'url' => $url,
                        'path' => $asset->path,
                        'source' => 'local',
                        'width' => $asset->width,
                        'height' => $asset->height,
                        'mime' => $asset->mime,
                        'size' => $asset->size,
                    ];
                })
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('AiAgentService asset search failed: '.$e->getMessage());
        }

        // Fetch outsourced stock photos (Pixabay / Pexels / Unsplash) when a query is provided
        $stockResults = [];
        $cleanQuery = trim($query);
        if (! empty($cleanQuery)) {
            $stockResults = $this->searchStockImages($cleanQuery, $limit);
        }

        // Prioritize outsourced stock photos first, followed by matching local assets
        return array_merge($stockResults, $localResults);
    }

    /**
     * Search stock photos using configured provider (Pexels, Unsplash, Pixabay).
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchStockImages(string $query, int $limit = 10): array
    {
        $settings = Setting::first();
        $provider = strtolower(trim((string) ($settings?->image_provider ?? 'auto')));

        if ($provider === 'local') {
            return [];
        }

        $cleanQuery = trim($query);
        if (empty($cleanQuery)) {
            return [];
        }

        $pixabayKey = trim((string) ($settings?->pixabay_api_key ?: config('services.pixabay.key', env('PIXABAY_API_KEY', ''))));
        $pexelsKey = trim((string) ($settings?->pexels_api_key ?: config('services.pexels.key', env('PEXELS_API_KEY', ''))));
        $unsplashKey = trim((string) ($settings?->unsplash_access_key ?: config('services.unsplash.access_key', env('UNSPLASH_ACCESS_KEY', ''))));

        // 1. If a specific provider is chosen, query that provider
        if ($provider === 'pixabay' && ! empty($pixabayKey)) {
            return $this->fetchFromPixabay($cleanQuery, $pixabayKey, $limit);
        }
        if ($provider === 'pexels' && ! empty($pexelsKey)) {
            return $this->fetchFromPexels($cleanQuery, $pexelsKey, $limit);
        }
        if ($provider === 'unsplash' && ! empty($unsplashKey)) {
            return $this->fetchFromUnsplash($cleanQuery, $unsplashKey, $limit);
        }

        // 2. Auto mode: check any configured provider (Pixabay, Pexels, Unsplash)
        if (! empty($pixabayKey)) {
            $results = $this->fetchFromPixabay($cleanQuery, $pixabayKey, $limit);
            if (! empty($results)) {
                return $results;
            }
        }

        if (! empty($pexelsKey)) {
            $results = $this->fetchFromPexels($cleanQuery, $pexelsKey, $limit);
            if (! empty($results)) {
                return $results;
            }
        }

        if (! empty($unsplashKey)) {
            $results = $this->fetchFromUnsplash($cleanQuery, $unsplashKey, $limit);
            if (! empty($results)) {
                return $results;
            }
        }

        return [];
    }

    /**
     * Fetch photos from Pexels API.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fetchFromPexels(string $query, string $apiKey, int $limit = 10): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->timeout(10)->get('https://api.pexels.com/v1/search', [
                'query' => $query,
                'per_page' => min(20, max(1, $limit)),
                'orientation' => 'landscape',
            ]);

            if (! $response->successful()) {
                Log::warning('Pexels API error: '.$response->status().' - '.$response->body());

                return [];
            }

            $photos = $response->json('photos') ?? [];

            return array_map(function ($p) {
                return [
                    'id' => 'pexels_'.$p['id'],
                    'name' => (! empty($p['alt']) ? $p['alt'] : 'Photo by '.$p['photographer']),
                    'url' => $p['src']['large2x'] ?? $p['src']['large'] ?? $p['src']['original'] ?? '',
                    'source' => 'pexels',
                    'width' => $p['width'] ?? null,
                    'height' => $p['height'] ?? null,
                ];
            }, $photos);
        } catch (\Throwable $e) {
            Log::warning('Pexels exception: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Fetch photos from Unsplash API.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fetchFromUnsplash(string $query, string $accessKey, int $limit = 10): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Client-ID '.$accessKey,
            ])->timeout(10)->get('https://api.unsplash.com/search/photos', [
                'query' => $query,
                'per_page' => min(20, max(1, $limit)),
                'orientation' => 'landscape',
            ]);

            if (! $response->successful()) {
                Log::warning('Unsplash API error: '.$response->status().' - '.$response->body());

                return [];
            }

            $results = $response->json('results') ?? [];

            return array_map(function ($p) {
                return [
                    'id' => 'unsplash_'.$p['id'],
                    'name' => $p['alt_description'] ?? $p['description'] ?? 'Photo by '.($p['user']['name'] ?? 'Unsplash'),
                    'url' => $p['urls']['regular'] ?? $p['urls']['full'] ?? '',
                    'source' => 'unsplash',
                    'width' => $p['width'] ?? null,
                    'height' => $p['height'] ?? null,
                ];
            }, $results);
        } catch (\Throwable $e) {
            Log::warning('Unsplash exception: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Extract topic keywords from user messages and page context to search for relevant stock images.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $context
     */
    public function extractImageSearchQuery(array $messages, array $context): string
    {
        $lastUserMsg = '';
        foreach (array_reverse($messages) as $m) {
            if (($m['role'] ?? '') === 'user' && ! empty($m['content'])) {
                $lastUserMsg = (string) $m['content'];
                break;
            }
        }

        $pageTitle = (string) ($context['entryData']['title'] ?? '');
        $activeSectionName = (string) ($context['activeSectionName'] ?? '');

        // Remove conversational filler and command words
        $clean = preg_replace('/\b(update|change|rewrite|write|polish|create|add|my|full|content|page|all|section|image|images|photos|photo|picture|pictures|make|it|to|and|the|a|an|please|with|for|in|on|set|find|put|replace)\b/i', '', $lastUserMsg);
        $clean = trim(preg_replace('/\s+/', ' ', (string) $clean));

        if (! empty($clean) && strlen($clean) >= 3) {
            return $clean;
        }

        if (! empty($pageTitle)) {
            $cleanTitle = preg_replace('/\b(details|page|template|overview|home|welcome)\b/i', '', $pageTitle);
            $cleanTitle = trim(preg_replace('/\s+/', ' ', (string) $cleanTitle));

            return $cleanTitle ?: $pageTitle;
        }

        return $activeSectionName ?: 'website';
    }

    /**
     * Fetch photos from Pixabay API.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fetchFromPixabay(string $query, string $apiKey, int $limit = 10): array
    {
        try {
            $cleanQuery = trim(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $query));
            $cleanQuery = trim(preg_replace('/\s+/', ' ', $cleanQuery));

            if (empty($cleanQuery)) {
                $cleanQuery = 'nature';
            }

            $response = Http::timeout(10)->get('https://pixabay.com/api/', [
                'key' => $apiKey,
                'q' => $cleanQuery,
                'per_page' => min(20, max(3, $limit)),
                'image_type' => 'photo',
                'orientation' => 'horizontal',
                'safesearch' => 'true',
            ]);

            if (! $response->successful()) {
                Log::warning('Pixabay API error: '.$response->status().' - '.$response->body());

                return [];
            }

            $hits = $response->json('hits') ?? [];

            // If 0 hits with full query, retry with first major keyword or fallback topic
            if (empty($hits) && str_contains($cleanQuery, ' ')) {
                $words = explode(' ', $cleanQuery);
                $firstWord = $words[0] ?? '';
                if (strlen($firstWord) >= 3) {
                    $retryResponse = Http::timeout(10)->get('https://pixabay.com/api/', [
                        'key' => $apiKey,
                        'q' => $firstWord,
                        'per_page' => min(20, max(3, $limit)),
                        'image_type' => 'photo',
                        'orientation' => 'horizontal',
                        'safesearch' => 'true',
                    ]);
                    if ($retryResponse->successful()) {
                        $hits = $retryResponse->json('hits') ?? [];
                    }
                }
            }

            return array_map(function ($h) {
                return [
                    'id' => 'pixabay_'.$h['id'],
                    'name' => $h['tags'] ?? 'Pixabay Image',
                    'url' => $h['largeImageURL'] ?? $h['webformatURL'] ?? '',
                    'source' => 'pixabay',
                    'width' => $h['imageWidth'] ?? null,
                    'height' => $h['imageHeight'] ?? null,
                ];
            }, $hits);
        } catch (\Throwable $e) {
            Log::warning('Pixabay exception: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Parse JSON from model response safely with automatic repair for common LLM syntax defects.
     *
     * @return array{thought?: string, message?: string, actions?: array<int, array<string, mixed>>, suggestions?: array<int, string>}
     */
    protected function parseJsonResponse(string $raw): array
    {
        $trimmed = trim($raw);

        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/s', $trimmed, $matches)) {
            $trimmed = trim($matches[1]);
        }

        // Auto-repair common LLM JSON syntax defects:
        // 1. Fix unescaped control characters & raw newlines inside JSON string values
        $repaired = preg_replace_callback('/"([^"\\\\]*|\\\\.)*"/s', function ($match) {
            return str_replace(
                ["\r\n", "\n", "\r", "\t"],
                ['\n', '\n', '\r', '\t'],
                $match[0]
            );
        }, $trimmed);

        // 2. Missing values before comma e.g. "section_index": , -> "section_index": 0,
        $repaired = preg_replace('/:\s*,/s', ': 0,', $repaired);

        // 3. Trailing commas before closing braces/brackets
        $repaired = preg_replace('/,\s*([\}\]])/s', '$1', $repaired);

        // 4. Try standard JSON decode
        $decoded = json_decode($repaired, true);
        if (is_array($decoded) && (isset($decoded['message']) || isset($decoded['actions']))) {
            return $decoded;
        }

        // 5. Extract JSON substring if surrounded by extra text
        $firstBrace = strpos($repaired, '{');
        $lastBrace = strrpos($repaired, '}');

        if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
            $extracted = substr($repaired, $firstBrace, $lastBrace - $firstBrace + 1);
            $decoded = json_decode($extracted, true);

            if (is_array($decoded) && (isset($decoded['message']) || isset($decoded['actions']))) {
                return $decoded;
            }
        }

        // 6. Auto-close truncated JSON using a LIFO token stack (recovers responses truncated by max_tokens)
        if ($firstBrace !== false) {
            $unclosed = substr($repaired, $firstBrace);

            // If there is an unclosed string literal, append a closing quote
            $quoteCount = substr_count(str_replace('\"', '', $unclosed), '"');
            if ($quoteCount % 2 !== 0) {
                $unclosed .= '"';
            }

            $unclosed = preg_replace('/,\s*$/', '', $unclosed);

            // Track unclosed structural tokens using a LIFO stack
            $stack = [];
            $inString = false;
            $len = strlen($unclosed);

            for ($i = 0; $i < $len; $i++) {
                $char = $unclosed[$i];
                if ($char === '"' && ($i === 0 || $unclosed[$i - 1] !== '\\')) {
                    $inString = ! $inString;

                    continue;
                }
                if ($inString) {
                    continue;
                }

                if ($char === '{') {
                    $stack[] = '}';
                } elseif ($char === '[') {
                    $stack[] = ']';
                } elseif ($char === '}' || $char === ']') {
                    if (! empty($stack) && end($stack) === $char) {
                        array_pop($stack);
                    }
                }
            }

            if (! empty($stack)) {
                // Close in exact LIFO reverse stack order
                $unclosed .= implode('', array_reverse($stack));

                $decoded = json_decode($unclosed, true);
                if (is_array($decoded) && (isset($decoded['message']) || isset($decoded['actions']))) {
                    return $decoded;
                }
            }
        }

        // 7. Fallback: Regex extraction if full JSON parsing failed
        $fallbackActions = [];
        if (preg_match('/"actions"\s*:\s*(\[.*?\])(?=\s*,\s*"|\s*\})/s', $repaired, $actMatches)) {
            $parsedActions = json_decode($actMatches[1], true);
            if (is_array($parsedActions)) {
                $fallbackActions = $parsedActions;
            }
        }

        $fallbackMessage = $raw;
        if (preg_match('/"message"\s*:\s*"((?:[^"\\\\]|\\\\.)*)"/s', $repaired, $msgMatches)) {
            $fallbackMessage = stripcslashes($msgMatches[1]);
        }

        return [
            'thought' => 'Processed response',
            'message' => $fallbackMessage,
            'actions' => $fallbackActions,
            'suggestions' => [],
        ];
    }

    /**
     * Sanitize and validate actions before sending to the editor client.
     * Prevents out-of-bounds errors, normalizes block names, and verifies field paths.
     *
     * @param  array<int, array<string, mixed>>  $actions
     * @param  array<string, mixed>  $context
     * @return array<int, array<string, mixed>>
     */
    public function sanitizeActions(array $actions, array $context = []): array
    {
        $sections = $context['full_sections'] ?? $context['sections'] ?? [];
        $sectionCount = is_array($sections) ? count($sections) : 0;
        $blockList = $context['blockList'] ?? [];
        $registeredBlockNames = [];

        foreach ($blockList as $item) {
            if (is_array($item) && ! empty($item['name'])) {
                $registeredBlockNames[] = $item['name'];
            } elseif (is_string($item) && ! empty($item)) {
                $registeredBlockNames[] = $item;
            }
        }

        $sanitized = [];

        foreach ($actions as $action) {
            if (! is_array($action) || empty($action['action'])) {
                continue;
            }

            $type = (string) $action['action'];

            switch ($type) {
                case 'update_field':
                    $idx = isset($action['section_index']) ? (int) $action['section_index'] : null;
                    if ($idx === null || $idx < 0 || ($sectionCount > 0 && $idx >= $sectionCount)) {
                        $idx = $context['activeSectionIndex'] ?? 0;
                    }
                    if (! empty($action['field_path']) && is_string($action['field_path'])) {
                        $sanitized[] = [
                            'action' => 'update_field',
                            'section_index' => (int) $idx,
                            'field_path' => trim($action['field_path']),
                            'value' => $action['value'] ?? '',
                        ];
                    }
                    break;

                case 'update_section':
                    $idx = isset($action['section_index']) ? (int) $action['section_index'] : null;
                    if ($idx === null || $idx < 0 || ($sectionCount > 0 && $idx >= $sectionCount)) {
                        $idx = $context['activeSectionIndex'] ?? 0;
                    }
                    if (isset($action['data']) && is_array($action['data'])) {
                        $sanitized[] = [
                            'action' => 'update_section',
                            'section_index' => (int) $idx,
                            'data' => $action['data'],
                        ];
                    }
                    break;

                case 'set_image':
                    $idx = isset($action['section_index']) ? (int) $action['section_index'] : null;
                    if ($idx === null || $idx < 0 || ($sectionCount > 0 && $idx >= $sectionCount)) {
                        $idx = $context['activeSectionIndex'] ?? 0;
                    }
                    $url = trim((string) ($action['image_url'] ?? ''));
                    $fieldPath = trim((string) ($action['field_path'] ?? 'image'));
                    if (! empty($url) && ! empty($fieldPath)) {
                        $sanitized[] = [
                            'action' => 'set_image',
                            'section_index' => (int) $idx,
                            'field_path' => $fieldPath,
                            'image_url' => $url,
                        ];
                    }
                    break;

                case 'add_section':
                    $name = trim((string) ($action['name'] ?? ''));
                    if (empty($name)) {
                        continue 2;
                    }

                    // Normalize block name against registered blocks (case-insensitive fuzzy match)
                    $matchedName = $name;
                    if (! empty($registeredBlockNames)) {
                        $found = false;
                        foreach ($registeredBlockNames as $regName) {
                            if (strcasecmp($regName, $name) === 0) {
                                $matchedName = $regName;
                                $found = true;
                                break;
                            }
                        }
                        if (! $found) {
                            $cleanTarget = strtolower(str_replace(['block', '_', '-'], '', $name));
                            foreach ($registeredBlockNames as $regName) {
                                $cleanReg = strtolower(str_replace(['block', '_', '-'], '', $regName));
                                if ($cleanTarget === $cleanReg || str_contains($cleanReg, $cleanTarget) || str_contains($cleanTarget, $cleanReg)) {
                                    $matchedName = $regName;
                                    $found = true;
                                    break;
                                }
                            }
                        }
                    }

                    $position = isset($action['position']) ? (int) $action['position'] : $sectionCount;
                    $sanitized[] = [
                        'action' => 'add_section',
                        'name' => $matchedName,
                        'data' => is_array($action['data'] ?? null) ? $action['data'] : [],
                        'position' => max(0, $position),
                    ];
                    break;

                case 'remove_section':
                    if (isset($action['section_index'])) {
                        $idx = (int) $action['section_index'];
                        if ($idx >= 0 && ($sectionCount === 0 || $idx < $sectionCount)) {
                            $sanitized[] = [
                                'action' => 'remove_section',
                                'section_index' => $idx,
                            ];
                        }
                    }
                    break;

                case 'reorder_sections':
                    if (isset($action['order']) && is_array($action['order'])) {
                        $sanitized[] = [
                            'action' => 'reorder_sections',
                            'order' => array_values(array_map('intval', $action['order'])),
                        ];
                    }
                    break;

                case 'replace_all_sections':
                    if (isset($action['sections']) && is_array($action['sections'])) {
                        $sanitized[] = [
                            'action' => 'replace_all_sections',
                            'sections' => $action['sections'],
                        ];
                    }
                    break;

                case 'add_list_item':
                    $idx = isset($action['section_index']) ? (int) $action['section_index'] : 0;
                    $listPath = trim((string) ($action['list_path'] ?? ''));
                    $itemData = is_array($action['data'] ?? null) ? $action['data'] : [];
                    if (! empty($listPath)) {
                        $sanitized[] = [
                            'action' => 'add_list_item',
                            'section_index' => $idx,
                            'list_path' => $listPath,
                            'data' => $itemData,
                        ];
                    }
                    break;

                case 'remove_list_item':
                    $idx = isset($action['section_index']) ? (int) $action['section_index'] : 0;
                    $listPath = trim((string) ($action['list_path'] ?? ''));
                    $itemIndex = isset($action['index']) ? (int) $action['index'] : 0;
                    if (! empty($listPath)) {
                        $sanitized[] = [
                            'action' => 'remove_list_item',
                            'section_index' => $idx,
                            'list_path' => $listPath,
                            'index' => max(0, $itemIndex),
                        ];
                    }
                    break;

                case 'navigate_to_field':
                    $idx = isset($action['section_index']) ? (int) $action['section_index'] : 0;
                    $fieldPath = trim((string) ($action['field_path'] ?? ''));
                    if (! empty($fieldPath)) {
                        $sanitized[] = [
                            'action' => 'navigate_to_field',
                            'section_index' => $idx,
                            'field_path' => $fieldPath,
                        ];
                    }
                    break;

                case 'set_field_source':
                    $idx = isset($action['section_index']) ? (int) $action['section_index'] : 0;
                    $fieldPath = trim((string) ($action['field_path'] ?? ''));
                    $source = trim((string) ($action['source'] ?? ''));
                    if (! empty($fieldPath) && ! empty($source)) {
                        $sanitized[] = [
                            'action' => 'set_field_source',
                            'section_index' => $idx,
                            'field_path' => $fieldPath,
                            'source' => $source,
                        ];
                    }
                    break;

                case 'save_page':
                    $sanitized[] = ['action' => 'save_page'];
                    break;

                default:
                    $sanitized[] = $action;
                    break;
            }
        }

        return $sanitized;
    }

    public static function getActiveModelName(): string
    {
        $settings = Setting::first();

        return (string) ($settings?->ai_model ?: config('services.deepseek.model', 'DeepSeek V4'));
    }
}
