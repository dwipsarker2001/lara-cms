<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AiModelController extends Controller
{
    /**
     * List all AI models (for admin settings table and editor selector).
     */
    public function index(Request $request): JsonResponse
    {
        AiModel::ensureTableExists();

        $query = AiModel::query()->orderBy('sort_order')->orderBy('id');

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        $models = $query->get()->map(function (AiModel $model) {
            $effectiveKey = $model->api_key ?: $model->getEffectiveApiKey();
            $maskedKey = $model->getMaskedApiKey();
            if (empty($maskedKey) && ! empty($effectiveKey)) {
                $len = strlen($effectiveKey);
                $maskedKey = $len <= 8 ? '********' : substr($effectiveKey, 0, 4).'••••••••'.substr($effectiveKey, -4);
            }

            return [
                'id' => $model->id,
                'name' => $model->name,
                'model_id' => $model->model_id,
                'provider' => $model->provider,
                'base_url' => $model->base_url,
                'effective_base_url' => $model->getEffectiveBaseUrl(),
                'api_key' => $effectiveKey,
                'has_api_key' => ! empty($effectiveKey),
                'masked_api_key' => $maskedKey,
                'is_active' => (bool) $model->is_active,
                'is_default' => (bool) $model->is_default,
                'is_prebuilt' => (bool) $model->is_prebuilt,
                'description' => $model->description,
                'sort_order' => (int) $model->sort_order,
                'created_at' => $model->created_at?->toIso8601String(),
                'updated_at' => $model->updated_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'models' => $models,
        ]);
    }

    /**
     * Store a new custom AI model.
     */
    public function store(Request $request): JsonResponse
    {
        AiModel::ensureTableExists();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'model_id' => 'required|string|max:100',
            'provider' => 'required|string|max:50',
            'base_url' => 'nullable|string|max:255',
            'api_key' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $isDefault = (bool) ($validated['is_default'] ?? false);
        if ($isDefault) {
            AiModel::query()->update(['is_default' => false]);
        }

        $model = AiModel::create([
            'name' => $validated['name'],
            'model_id' => $validated['model_id'],
            'provider' => $validated['provider'],
            'base_url' => $validated['base_url'] ?? null,
            'api_key' => ! empty($validated['api_key']) ? trim((string) $validated['api_key']) : null,
            'is_active' => $validated['is_active'] ?? true,
            'is_default' => $isDefault,
            'is_prebuilt' => false,
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 99,
        ]);

        return response()->json([
            'success' => true,
            'message' => "AI Model '{$model->name}' created successfully.",
            'model' => $model,
        ]);
    }

    /**
     * Update an existing AI model.
     */
    public function update(Request $request, AiModel $aiModel): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'model_id' => 'required|string|max:100',
            'provider' => 'required|string|max:50',
            'base_url' => 'nullable|string|max:255',
            'api_key' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $isDefault = (bool) ($validated['is_default'] ?? false);
        if ($isDefault && ! $aiModel->is_default) {
            AiModel::query()->update(['is_default' => false]);
        }

        $data = [
            'name' => $validated['name'],
            'model_id' => $validated['model_id'],
            'provider' => $validated['provider'],
            'base_url' => $validated['base_url'] ?? null,
            'is_active' => $validated['is_active'] ?? $aiModel->is_active,
            'is_default' => $isDefault,
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $aiModel->sort_order,
        ];

        // Only update API key if explicitly provided and not a masked string
        if ($request->has('api_key')) {
            $rawKey = trim((string) $request->input('api_key'));
            if ($rawKey !== '' && ! str_contains($rawKey, '****')) {
                $data['api_key'] = $rawKey;
            } elseif ($rawKey === '') {
                $data['api_key'] = null;
            }
        }

        $aiModel->update($data);

        // Also sync setting if this was the active model
        if ($isDefault) {
            $settings = Setting::first();
            if ($settings) {
                $settings->update([
                    'ai_model' => $aiModel->model_id,
                    'ai_base_url' => $aiModel->base_url ?: $aiModel->getEffectiveBaseUrl(),
                    'ai_api_key' => $aiModel->api_key ?: $settings->ai_api_key,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "AI Model '{$aiModel->name}' updated successfully.",
            'model' => $aiModel,
        ]);
    }

    /**
     * Delete an AI model.
     */
    public function destroy(AiModel $aiModel): JsonResponse
    {
        $name = $aiModel->name;
        $aiModel->delete();

        // If default model was deleted, set next active model as default
        if ($aiModel->is_default) {
            $next = AiModel::where('is_active', true)->first() ?: AiModel::first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "AI Model '{$name}' deleted successfully.",
        ]);
    }

    /**
     * Toggle active state.
     */
    public function toggleActive(AiModel $aiModel): JsonResponse
    {
        $aiModel->is_active = ! $aiModel->is_active;

        // If disabling default model, pick another active default
        if (! $aiModel->is_active && $aiModel->is_default) {
            $aiModel->is_default = false;
            $next = AiModel::where('id', '!=', $aiModel->id)->where('is_active', true)->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        $aiModel->save();

        return response()->json([
            'success' => true,
            'is_active' => (bool) $aiModel->is_active,
            'is_default' => (bool) $aiModel->is_default,
            'message' => "'{$aiModel->name}' is now ".($aiModel->is_active ? 'active' : 'inactive').'.',
        ]);
    }

    /**
     * Set a model as the default model.
     */
    public function setDefault(AiModel $aiModel): JsonResponse
    {
        AiModel::query()->update(['is_default' => false]);

        $aiModel->update([
            'is_default' => true,
            'is_active' => true,
        ]);

        $settings = Setting::first();
        if ($settings) {
            $settings->update([
                'ai_model' => $aiModel->model_id,
                'ai_base_url' => $aiModel->base_url ?: $aiModel->getEffectiveBaseUrl(),
                'ai_api_key' => $aiModel->api_key ?: $settings->ai_api_key,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "'{$aiModel->name}' set as the default AI model.",
        ]);
    }

    /**
     * Test API connection with provided or saved credentials.
     */
    public function testConnection(Request $request): JsonResponse
    {
        $baseUrl = trim((string) ($request->input('base_url') ?: ''));
        $apiKey = trim((string) $request->input('api_key'));
        $modelId = trim((string) ($request->input('model_id') ?: 'deepseek-chat'));
        $provider = trim((string) ($request->input('provider') ?: 'custom'));

        // If api_key is masked or empty, lookup from DB model if model_id provided
        if ((empty($apiKey) || str_contains($apiKey, '****')) && $request->filled('id')) {
            $dbModel = AiModel::find($request->input('id'));
            if ($dbModel) {
                $apiKey = $dbModel->getEffectiveApiKey() ?: $apiKey;
                $baseUrl = $baseUrl ?: $dbModel->getEffectiveBaseUrl();
                $modelId = $modelId ?: $dbModel->model_id;
                $provider = $provider ?: $dbModel->provider;
            }
        }

        if (empty($apiKey) && $provider !== 'custom') {
            return response()->json([
                'success' => false,
                'message' => 'API Key is missing. Please enter a valid API key to test.',
            ], 422);
        }

        try {
            $endpoint = AiModel::resolveEndpoint($baseUrl, $provider, $modelId, $apiKey);

            if ($provider === 'anthropic') {
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->timeout(15)->post($endpoint, [
                    'model' => $modelId,
                    'max_tokens' => 5,
                    'messages' => [['role' => 'user', 'content' => 'hi']],
                ]);
            } elseif ($provider === 'google') {
                $response = Http::timeout(15)->post($endpoint, [
                    'contents' => [['parts' => [['text' => 'hi']]]],
                    'generationConfig' => ['maxOutputTokens' => 5],
                ]);
            } else {
                // OpenAI-compatible endpoint (DeepSeek, OpenAI, Groq, Qwen, Ollama, Custom, Tabitoken, etc.)
                $headers = ['Content-Type' => 'application/json'];
                if (! empty($apiKey)) {
                    $headers['Authorization'] = "Bearer {$apiKey}";
                }

                $response = Http::withHeaders($headers)
                    ->timeout(15)
                    ->post($endpoint, [
                        'model' => $modelId,
                        'messages' => [['role' => 'user', 'content' => 'hi']],
                        'max_tokens' => 5,
                    ]);
            }

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => "Connection successful! Provider responded (HTTP {$response->status()}).",
                ]);
            }

            $body = $response->json();
            $errMsg = $body['error']['message'] ?? $body['error'] ?? $body['message'] ?? null;
            if (is_array($errMsg)) {
                $errMsg = json_encode($errMsg);
            }
            if (empty($errMsg)) {
                $rawBody = trim($response->body());
                $errMsg = ! empty($rawBody) ? "HTTP {$response->status()}: {$rawBody}" : "HTTP {$response->status()} error";
            }

            return response()->json([
                'success' => false,
                'message' => "Connection failed: {$errMsg}",
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "Connection error: {$e->getMessage()}",
            ], 500);
        }
    }
}
