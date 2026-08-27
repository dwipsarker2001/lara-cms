<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AiModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model_id',
        'provider',
        'base_url',
        'api_key',
        'is_active',
        'is_default',
        'is_prebuilt',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_prebuilt' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Ensure table exists dynamically.
     */
    public static function ensureTableExists(): void
    {
        if (! Schema::hasTable('ai_models')) {
            Schema::create('ai_models', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('model_id');
                $table->string('provider', 50)->default('custom');
                $table->string('base_url')->nullable();
                $table->text('api_key')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false);
                $table->boolean('is_prebuilt')->default(false);
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Get masked API key for safe UI display.
     */
    public function getMaskedApiKey(): ?string
    {
        $key = trim((string) $this->api_key);
        if (empty($key)) {
            return null;
        }

        $len = strlen($key);
        if ($len <= 8) {
            return '********';
        }

        $prefix = substr($key, 0, 4);
        $suffix = substr($key, -4);

        return $prefix.str_repeat('*', max(4, $len - 8)).$suffix;
    }

    /**
     * Resolve effective API key (model specific > setting fallback > config fallback).
     */
    public function getEffectiveApiKey(): ?string
    {
        if (! empty($this->api_key)) {
            return $this->api_key;
        }

        $settings = Setting::first();
        if ($this->provider === 'deepseek' && ! empty($settings?->ai_api_key)) {
            return $settings->ai_api_key;
        }

        return config("services.{$this->provider}.api_key", config('services.deepseek.api_key'));
    }

    /**
     * Resolve the full API endpoint URL for any provider, proxy, or custom endpoint.
     */
    public static function resolveEndpoint(string $baseUrl, string $provider = 'custom', string $modelId = '', string $apiKey = ''): string
    {
        $url = trim($baseUrl);

        if ($provider === 'anthropic') {
            if (empty($url)) {
                $url = 'https://api.anthropic.com/v1';
            }
            $url = rtrim($url, '/');
            if (str_ends_with($url, '/messages')) {
                return $url;
            }
            if (preg_match('#/(v\d+)$#i', $url)) {
                return $url.'/messages';
            }

            return $url.'/v1/messages';
        }

        if ($provider === 'google') {
            if (empty($url)) {
                $url = 'https://generativelanguage.googleapis.com/v1beta';
            }
            $url = rtrim($url, '/');
            if (str_contains($url, ':generateContent')) {
                $endpoint = $url;
            } elseif (str_contains($url, '/models/')) {
                $endpoint = "{$url}:generateContent";
            } else {
                if (! preg_match('#/(v\d+(?:beta\d*)?)$#i', $url)) {
                    $url .= '/v1beta';
                }
                $endpoint = "{$url}/models/{$modelId}:generateContent";
            }

            if (! empty($apiKey) && ! str_contains($endpoint, 'key=')) {
                $separator = str_contains($endpoint, '?') ? '&' : '?';
                $endpoint .= "{$separator}key={$apiKey}";
            }

            return $endpoint;
        }

        // OpenAI-compatible / Custom providers:
        if (empty($url)) {
            return match ($provider) {
                'openai' => 'https://api.openai.com/v1/chat/completions',
                'groq' => 'https://api.groq.com/openai/v1/chat/completions',
                'qwen' => 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1/chat/completions',
                'grok', 'xai' => 'https://api.x.ai/v1/chat/completions',
                default => 'https://api.deepseek.com/chat/completions',
            };
        }

        $url = rtrim($url, '/');

        // 1. Fix common singular typo: .../chat/completion -> .../chat/completions
        if (str_ends_with($url, '/chat/completion')) {
            return substr($url, 0, -strlen('/chat/completion')).'/chat/completions';
        }

        // 2. If it already ends with /chat/completions or /completions, it's already a complete endpoint
        if (str_ends_with($url, '/chat/completions') || str_ends_with($url, '/completions')) {
            return $url;
        }

        // 3. If URL ends with a standard version prefix (e.g. /v1, /v1beta, /api/v1, /openai/v1, /compatible-mode/v1)
        if (preg_match('#/(v\d+(?:beta\d*)?|api/v\d+|openai/v\d+|compatible-mode/v\d+)$#i', $url)) {
            return $url.'/chat/completions';
        }

        // 4. If host is api.deepseek.com (natively serves /chat/completions)
        $parsedHost = parse_url($url, PHP_URL_HOST);
        $parsedPath = parse_url($url, PHP_URL_PATH);

        if ($parsedHost === 'api.deepseek.com') {
            return $url.'/chat/completions';
        }

        // 5. If there is already a custom path (e.g. /v1/chat or custom endpoint path)
        if (! empty($parsedPath) && $parsedPath !== '/' && substr_count(trim($parsedPath, '/'), '/') >= 1) {
            if (str_ends_with($url, '/chat')) {
                return $url.'/completions';
            }
            if (str_ends_with($url, '/api')) {
                return $url.'/v1/chat/completions';
            }

            return $url;
        }

        // 6. Default for bare domain/host (e.g. https://tabitoken.com, http://localhost:11434)
        return $url.'/v1/chat/completions';
    }

    /**
     * Resolve effective Base URL.
     */
    public function getEffectiveBaseUrl(): string
    {
        if (! empty($this->base_url)) {
            return rtrim($this->base_url, '/');
        }

        return match ($this->provider) {
            'deepseek' => 'https://api.deepseek.com',
            'openai' => 'https://api.openai.com/v1',
            'anthropic' => 'https://api.anthropic.com/v1',
            'google' => 'https://generativelanguage.googleapis.com/v1beta',
            'groq' => 'https://api.groq.com/openai/v1',
            'qwen' => 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1',
            'custom' => 'http://localhost:11434/v1',
            default => 'https://api.deepseek.com',
        };
    }

    /**
     * Seed or ensure default prebuilt AI models exist.
     */
    public static function seedPrebuiltDefaults(): void
    {
        self::ensureTableExists();

        $settings = Setting::first();
        $globalApiKey = $settings?->ai_api_key ?: config('services.deepseek.api_key');
        $globalBaseUrl = $settings?->ai_base_url ?: config('services.deepseek.base_url', 'https://api.deepseek.com');
        $globalModel = $settings?->ai_model ?: config('services.deepseek.model', 'deepseek-chat');

        $prebuilt = [
            [
                'name' => 'DeepSeek V3',
                'model_id' => 'deepseek-chat',
                'provider' => 'deepseek',
                'base_url' => $globalBaseUrl ?: 'https://api.deepseek.com',
                'api_key' => $globalApiKey,
                'is_active' => true,
                'is_default' => ($globalModel === 'deepseek-chat' || empty($globalModel)),
                'is_prebuilt' => true,
                'description' => 'General coding & reasoning',
                'sort_order' => 1,
            ],
            [
                'name' => 'DeepSeek R1',
                'model_id' => 'deepseek-reasoner',
                'provider' => 'deepseek',
                'base_url' => 'https://api.deepseek.com',
                'api_key' => $globalApiKey,
                'is_active' => true,
                'is_default' => ($globalModel === 'deepseek-reasoner'),
                'is_prebuilt' => true,
                'description' => 'Reasoning & thinking',
                'sort_order' => 2,
            ],
            [
                'name' => 'GPT-4o',
                'model_id' => 'gpt-4o',
                'provider' => 'openai',
                'base_url' => 'https://api.openai.com/v1',
                'api_key' => config('services.openai.api_key'),
                'is_active' => true,
                'is_default' => ($globalModel === 'gpt-4o'),
                'is_prebuilt' => true,
                'description' => 'Flagship multimodal model',
                'sort_order' => 3,
            ],
            [
                'name' => 'GPT-4o Mini',
                'model_id' => 'gpt-4o-mini',
                'provider' => 'openai',
                'base_url' => 'https://api.openai.com/v1',
                'api_key' => config('services.openai.api_key'),
                'is_active' => true,
                'is_default' => false,
                'is_prebuilt' => true,
                'description' => 'Fast & lightweight',
                'sort_order' => 4,
            ],
            [
                'name' => 'Claude 3.5 Sonnet',
                'model_id' => 'claude-3-5-sonnet-20241022',
                'provider' => 'anthropic',
                'base_url' => 'https://api.anthropic.com/v1',
                'api_key' => config('services.anthropic.api_key'),
                'is_active' => true,
                'is_default' => false,
                'is_prebuilt' => true,
                'description' => 'Coding & analysis',
                'sort_order' => 5,
            ],
            [
                'name' => 'Claude 3.5 Haiku',
                'model_id' => 'claude-3-5-haiku-20241022',
                'provider' => 'anthropic',
                'base_url' => 'https://api.anthropic.com/v1',
                'api_key' => config('services.anthropic.api_key'),
                'is_active' => false,
                'is_default' => false,
                'is_prebuilt' => true,
                'description' => 'Fast & efficient',
                'sort_order' => 6,
            ],
            [
                'name' => 'Gemini 1.5 Pro',
                'model_id' => 'gemini-1.5-pro',
                'provider' => 'google',
                'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'api_key' => config('services.google.api_key'),
                'is_active' => true,
                'is_default' => false,
                'is_prebuilt' => true,
                'description' => 'Multimodal reasoning',
                'sort_order' => 7,
            ],
            [
                'name' => 'Gemini 1.5 Flash',
                'model_id' => 'gemini-1.5-flash',
                'provider' => 'google',
                'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'api_key' => config('services.google.api_key'),
                'is_active' => false,
                'is_default' => false,
                'is_prebuilt' => true,
                'description' => 'Fast multimodal',
                'sort_order' => 8,
            ],
            [
                'name' => 'Qwen 2.5 72B',
                'model_id' => 'qwen-plus',
                'provider' => 'qwen',
                'base_url' => 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1',
                'api_key' => config('services.qwen.api_key'),
                'is_active' => false,
                'is_default' => false,
                'is_prebuilt' => true,
                'description' => 'Open-weight flagship',
                'sort_order' => 9,
            ],
            [
                'name' => 'Groq LLaMA 3.3 70B',
                'model_id' => 'llama-3.3-70b-versatile',
                'provider' => 'groq',
                'base_url' => 'https://api.groq.com/openai/v1',
                'api_key' => config('services.groq.api_key'),
                'is_active' => false,
                'is_default' => false,
                'is_prebuilt' => true,
                'description' => 'High-speed LPU',
                'sort_order' => 10,
            ],
            [
                'name' => 'Ollama (Local)',
                'model_id' => 'llama3',
                'provider' => 'custom',
                'base_url' => 'http://localhost:11434/v1',
                'api_key' => null,
                'is_active' => false,
                'is_default' => false,
                'is_prebuilt' => true,
                'description' => 'Connect to a locally running Ollama instance with zero external API calls.',
                'sort_order' => 11,
            ],
        ];

        foreach ($prebuilt as $data) {
            self::firstOrCreate(
                ['model_id' => $data['model_id'], 'provider' => $data['provider']],
                $data
            );
        }

        // Ensure at least one model is set as default
        if (! self::where('is_default', true)->exists()) {
            $first = self::where('is_active', true)->first() ?: self::first();
            if ($first) {
                $first->update(['is_default' => true]);
            }
        }
    }
}
