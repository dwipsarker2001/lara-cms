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

        $result = [
            'success' => true,
            'thought' => $parsed['thought'] ?? '',
            'message' => $parsed['message'] ?? 'Changes processed successfully.',
            'actions' => $parsed['actions'] ?? [],
            'suggestions' => $parsed['suggestions'] ?? [],
            'usage' => $completion['usage'] ?? null,
            'raw' => $rawContent,
        ];

        return $result;
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

        try {
            $endpoint = $this->resolveEndpoint($baseUrl);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($endpoint, [
                'model' => $model,
                'messages' => $payloadMessages,
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.3,
                'max_tokens' => 8192,
            ]);

            if (! $response->successful()) {
                Log::error('AI API Error: '.$response->status().' - '.$response->body());

                $status = $response->status();
                $apiError = $response->json('error.message') ?? $response->json('message') ?? $response->json('error');

                $friendlyMessage = ! empty($apiError) && is_string($apiError)
                    ? $apiError
                    : match ($status) {
                        401 => 'Invalid API key. Please check your credentials in Admin Settings > AI Assistant.',
                        402 => 'Account credit balance reached. Please check your AI provider billing.',
                        429 => 'Rate limit reached. Please wait a moment and try again.',
                        500, 502, 503 => 'The AI service is temporarily unavailable (HTTP '.$status.'). Please try again in a moment.',
                        default => 'The AI service returned an error (HTTP '.$status.'). Please try again.',
                    };

                return [
                    'success' => false,
                    'error_message' => $friendlyMessage,
                    'raw_error' => $response->body(),
                ];
            }

            $jsonResponse = $response->json();
            $rawContent = $jsonResponse['choices'][0]['message']['content'] ?? '';

            return [
                'success' => true,
                'content' => $rawContent,
                'usage' => $jsonResponse['usage'] ?? null,
            ];
        } catch (\Throwable $e) {
            $endpointStr = $this->resolveEndpoint($baseUrl);
            Log::error('AiAgentService Direct HTTP Exception: '.$e->getMessage().' [Endpoint: '.$endpointStr.']');

            return [
                'success' => false,
                'error_message' => 'Unable to reach AI service ('.$endpointStr.'): '.$e->getMessage(),
                'raw_error' => $e->getMessage(),
            ];
        }
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
     * Designed to be token-efficient: schemas/assets only included when provided by client.
     *
     * @param  array<string, mixed>  $context
     */
    protected function buildSystemPrompt(array $context): string
    {
        $schemas = $context['schemas'] ?? null;   // null = not changed this turn
        $blockList = $context['blockList'] ?? null; // null = not changed this turn
        $sections = $context['sections'] ?? [];    // compact digest from client
        $activeSectionIndex = $context['activeSectionIndex'] ?? null;
        $activeSectionName = $context['activeSectionName'] ?? null;
        $activeSectionData = $context['activeSectionData'] ?? null;
        $assets = $context['assets'] ?? null;    // null = not image-related request
        $entryData = $context['entryData'] ?? [];

        // Compact JSON — no pretty print to save tokens
        $sectionsJson = json_encode($sections, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $entryJson = json_encode($entryData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $activeInfo = ($activeSectionIndex !== null && $activeSectionIndex !== '')
            ? "Active section in sidebar: index={$activeSectionIndex}, block={$activeSectionName}"
            : 'No section active (user on section list / page overview)';

        $activeDataBlock = '';
        if ($activeSectionData !== null) {
            $activeDataJson = json_encode($activeSectionData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $activeDataBlock = "\nActive section full data:\n{$activeDataJson}";
        }

        $schemasBlock = '';
        if ($schemas !== null) {
            $blockListJson = json_encode($blockList ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $schemasJson = json_encode($schemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $schemasBlock = <<<SCHEMA

========================
AVAILABLE BLOCKS & SCHEMAS (sent once per session on change)
========================
Block names: {$blockListJson}
Full schemas: {$schemasJson}
SCHEMA;
        }

        $assetsBlock = '';
        if ($assets !== null) {
            $assetsJson = json_encode($assets, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $assetsBlock = <<<ASSETS

========================
MEDIA ASSETS (image-related request detected)
========================
{$assetsJson}
Prefer real assets from the list (use `url`). If none fit, use high-quality Unsplash URLs.
ASSETS;
        }

        $collections = $context['collections'] ?? null;
        $collectionsBlock = '';
        if (! empty($collections)) {
            $colsJson = json_encode($collections, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $collectionsBlock = <<<COLS

========================
AVAILABLE COLLECTIONS & ENTRIES (use these to set links or select collection items)
========================
{$colsJson}
COLS;
        }

        return <<<PROMPT
You are an autonomous AI Visual Copilot built into Lara-CMS — a flexible, block-based CMS for building any type of website (business, portfolio, e-commerce, travel, SaaS, blog, etc.).
You have FULL programmatic control over the page editor: adding/removing/updating sections, editing any field value, setting images, and saving the page.

======================================================
INTENT CLASSIFICATION — FOLLOW STRICTLY
======================================================

RULE 1 — EDIT CONTENT (update, change, write, rewrite, polish, quick items, about section, itinerary, etc.)
- NEVER call add_section. Modify existing section(s) in-place with update_field, update_section, or add_list_item.
- In every action, "section_index" MUST be a valid integer index (0, 1, 2, ...). NEVER leave "section_index" empty or null.
- When the user asks to "update quick items", "update about section", "improve headline", or any general request:
  DO NOT ask questions or reply with "How else can I help?". PROACTIVELY write compelling, fitting copy for those fields and generate the action(s) immediately!
- If a section is active ({$activeInfo}), update THAT section's fields using its index.
- If no section is active, find the section matching the name/topic in the "Page sections" list below and use its index integer.

RULE 2 — BUILD / CREATE PAGE (user says "create page", "build full page", "generate landing page")
- Use replace_all_sections or sequential add_section to assemble a complete multi-block page.
- Choose block types that make sense for the requested website type from the available block list.
- Write realistic, high-converting copy tailored to the brief.

RULE 3 — ADD SECTION / BLOCK (user says "add a testimonials section", "insert FAQ", etc.)
- Call add_section with a block name from the registered block list.
- Populate all fields with great copy and high-quality image URLs.

RULE 4 — IMAGE & MEDIA (user says "update all images", "update gallery", "change image", "find photos", "add images", etc.)
- Use update_field or set_image to set image URLs directly. NEVER leave image fields empty.
- CONTEXTUAL IMAGE SELECTION:
  * Dynamically generate high-resolution, relevant direct image URLs (e.g. from Unsplash `https://images.unsplash.com/photo-...`) matching the specific topic, industry, product, or theme of the website/page.
  * When asked to update all images or a gallery:
    Inspect all sections for top-level image fields (e.g. `image`, `backgroundImage`, `heroImage`) and nested list image fields (e.g. `gallery.0.image`, `items.0.image`, `team.0.avatar`).
    Output an update_field or set_image action for EVERY image field with a distinct, topic-relevant image URL.
  * Format:
    {"action":"update_field","section_index":0,"field_path":"image","value":"https://images.unsplash.com/photo-..."}
    {"action":"set_image","section_index":0,"field_path":"heroImage","image_url":"https://images.unsplash.com/photo-..."}

RULE 5 — PRICES & NUMBERS
- Never include currency symbols ($, €, £, etc.). Output raw numbers only (e.g. "49", "199").

======================================================
COPYWRITING STANDARDS (apply to all generated text)
======================================================
- Headlines: 5–9 words, action-oriented, specific value proposition. No generic phrases.
- Descriptions: 15–25 words, remove friction, communicate unique benefit.
- CTAs: short action verbs ("Get Started", "Explore Now", "Book Your Spot").
- Lists/items: parallel structure, concrete and believable claims.
- Maintain brand voice already present on the page.

======================================================
PATH FORMAT FOR NESTED FIELDS
======================================================
Use dot notation for nested list fields:
- "fieldName"                 → top-level field (string, text, boolean, select, etc.)
- "listName.0.subField"       → first item in a list (index 0)
- "listName.2.subField"       → third item in a list (index 2)

Examples:
- "itinerary.0.dayTitle"
- "itinerary.0.stopName"
- "itinerary.0.departure"
- "highlights.2.text"
- "members.1.name"
- "faqs.0.question"

IMPORTANT — LINK & COLLECTION FIELDS:
1. Fields of type "link" (e.g. bookNowLink, buttonLink, ctaLink, whatsappLink):
   Set them to any external URL (e.g. "https://wa.me/...") OR to an available collection entry's route/slug from the list below (e.g. "/packages/sylhet-adventure", "/destinations/coxs-bazar"):
   {"action":"update_field","section_index":0,"field_path":"bookNowLink","value":"/packages/sylhet-adventure"}

2. Fields of type "collection" / "collectionEntry" (e.g. package_id, destination_id, deal_id, entry_id):
   Set the value to the matching collection entry's ID:
   {"action":"update_field","section_index":0,"field_path":"package_id","value":"3"}

3. Binding a field to a collection entry source:
   Use set_field_source:
   {"action":"set_field_source","section_index":0,"field_path":"title","source":"entry:3:title"}

IMPORTANT — SECTIONS DIGEST FORMAT:
The "Page sections" below is a compact digest. For list fields it shows:
{"_count": N, "_fields": {first item's field keys and truncated values}}
This tells you: (a) how many items exist, (b) what sub-field names to use in dot-notation paths.
When a section is active, its FULL data is shown above — use that for accurate editing.

========================
CURRENT EDITOR STATE
========================
{$activeInfo}
{$activeDataBlock}

Page sections (compact digest — use section index for actions):
{$sectionsJson}

Entry metadata: {$entryJson}
{$schemasBlock}
{$collectionsBlock}
{$assetsBlock}

========================
RESPONSE FORMAT (strict JSON, always)
========================
{
  "thought": "1-2 sentences: intent classification, which section/field to target, what action to take",
  "message": "Concise markdown reply to user. No emoji spam. Professional and helpful.",
  "actions": [ ...action objects... ],
  "suggestions": [ "3-4 short follow-up prompt ideas" ]
}

========================
ACTION TYPES
========================

1. Add section (only for build/add requests):
{"action":"add_section","name":"BlockName","data":{...},"position":0}

2. Update entire section (merge fields including lists):
{"action":"update_section","section_index":0,"data":{"headline":"...","items":[...]}}

3. Update single field (dot-notation path for nested):
{"action":"update_field","section_index":0,"field_path":"listName.0.subField","value":"..."}

4. Set image:
{"action":"set_image","section_index":0,"field_path":"backgroundImage","image_url":"/storage/assets/..."}

5. Remove section:
{"action":"remove_section","section_index":2}

6. Reorder sections:
{"action":"reorder_sections","order":[2,0,1]}

7. Replace all sections:
{"action":"replace_all_sections","sections":[{"name":"BlockName","enabled":true,"data":{...}}]}

8. Navigate to field in sidebar:
{"action":"navigate_to_field","section_index":0,"field_path":"headline"}

9. Save page:
{"action":"save_page"}

10. Add a new item to a list field (e.g. add itinerary day, highlight, FAQ, feature):
{"action":"add_list_item","section_index":0,"list_path":"itinerary","data":{"dayLabel":"Day-05","dayTitle":"Return Journey","stopName":"","departure":"","dayDescription":"..."}}
- list_path: dot-path to the list field (e.g. "itinerary", "highlights", "faqs", "locations")
- data: object with all sub-field values for the new item
- The new item is appended at the end of the list

11. Remove an item from a list field by index:
{"action":"remove_list_item","section_index":0,"list_path":"itinerary","index":2}
- index: 0-based position of the item to delete

========================
KEY RULES RECAP
========================
- Edit request → update_field / update_section only. Never add_section.
- Add list item → add_list_item (NOT update_section with the whole array).
- Remove list item → remove_list_item with the 0-based index.
- Build/create request → replace_all_sections or add_section sequence.
- Always output valid JSON. No markdown wrapper.
PROMPT;
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
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchAssets(string $query = '', int $limit = 30): array
    {
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

            return $builder->orderByDesc('id')
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

        $decoded = json_decode($repaired, true);
        if (is_array($decoded) && (isset($decoded['message']) || isset($decoded['actions']))) {
            return $decoded;
        }

        // 4. Extract JSON substring if surrounded by extra text
        $firstBrace = strpos($repaired, '{');
        $lastBrace = strrpos($repaired, '}');

        if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
            $extracted = substr($repaired, $firstBrace, $lastBrace - $firstBrace + 1);
            $decoded = json_decode($extracted, true);

            if (is_array($decoded) && (isset($decoded['message']) || isset($decoded['actions']))) {
                return $decoded;
            }
        }

        // 5. Fallback: Regex extraction if full JSON parsing failed
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

    public static function getActiveModelName(): string
    {
        $settings = Setting::first();

        return (string) ($settings?->ai_model ?: config('services.deepseek.model', 'DeepSeek V4'));
    }
}
