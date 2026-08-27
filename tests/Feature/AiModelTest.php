<?php

use App\Models\Admin;
use App\Models\AiModel;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->admin = Admin::factory()->create([
        'email' => 'admin@example.com',
        'is_active' => true,
    ]);
});

test('guest cannot access ai-models endpoint', function () {
    $response = $this->get(route('admin.ai-models.index'));
    $response->assertRedirect(route('login'));
});

test('admin can list ai models', function () {
    AiModel::create([
        'name' => 'DeepSeek V3',
        'model_id' => 'deepseek-chat',
        'provider' => 'deepseek',
        'base_url' => 'https://api.deepseek.com',
        'is_active' => true,
        'is_default' => true,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->getJson(route('admin.ai-models.index'));

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'models' => [
                '*' => ['id', 'name', 'model_id', 'provider', 'api_key', 'masked_api_key', 'has_api_key', 'is_active', 'is_default', 'is_prebuilt'],
            ],
        ]);

    expect(AiModel::where('model_id', 'deepseek-chat')->exists())->toBeTrue();
});

test('admin can create custom ai model', function () {
    $response = $this->actingAs($this->admin, 'admin')->postJson(route('admin.ai-models.store'), [
        'name' => 'Custom Local Ollama',
        'model_id' => 'llama3:8b',
        'provider' => 'custom',
        'base_url' => 'http://localhost:11434/v1',
        'api_key' => 'optional-key',
        'is_active' => true,
        'is_default' => false,
        'description' => 'Local Ollama instance',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $model = AiModel::where('model_id', 'llama3:8b')->first();
    expect($model)->not->toBeNull();
    expect($model->name)->toBe('Custom Local Ollama');
    expect($model->getMaskedApiKey())->not->toBeNull();
});

test('admin can update ai model and mask api key', function () {
    AiModel::seedPrebuiltDefaults();
    $model = AiModel::where('model_id', 'deepseek-chat')->first();

    $response = $this->actingAs($this->admin, 'admin')->putJson(route('admin.ai-models.update', $model), [
        'name' => 'DeepSeek V3 Custom',
        'model_id' => 'deepseek-chat',
        'provider' => 'deepseek',
        'base_url' => 'https://api.deepseek.com',
        'api_key' => 'sk-secret-key-1234567890',
        'is_active' => true,
        'is_default' => true,
    ]);

    $response->assertOk();
    $model->refresh();
    expect($model->name)->toBe('DeepSeek V3 Custom');
    expect($model->api_key)->toBe('sk-secret-key-1234567890');
    expect($model->is_default)->toBeTrue();
});

test('admin can toggle active status of an ai model', function () {
    AiModel::seedPrebuiltDefaults();
    $model = AiModel::first();
    $originalState = $model->is_active;

    $response = $this->actingAs($this->admin, 'admin')->postJson(route('admin.ai-models.toggle-active', $model));
    $response->assertOk();

    $model->refresh();
    expect($model->is_active)->toBe(! $originalState);
});

test('admin can set default ai model', function () {
    AiModel::seedPrebuiltDefaults();
    $gpt4 = AiModel::where('model_id', 'gpt-4o')->first();

    $response = $this->actingAs($this->admin, 'admin')->postJson(route('admin.ai-models.set-default', $gpt4));
    $response->assertOk();

    $gpt4->refresh();
    expect($gpt4->is_default)->toBeTrue();
    expect(AiModel::where('id', '!=', $gpt4->id)->where('is_default', true)->count())->toBe(0);
});

test('admin can test api connection endpoint', function () {
    Http::fake([
        'https://api.deepseek.com/chat/completions' => Http::response([
            'choices' => [
                ['message' => ['content' => 'Hello']],
            ],
        ], 200),
    ]);

    $response = $this->actingAs($this->admin, 'admin')->postJson(route('admin.ai-models.test-connection'), [
        'provider' => 'deepseek',
        'model_id' => 'deepseek-chat',
        'base_url' => 'https://api.deepseek.com',
        'api_key' => 'sk-test-key-12345',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);
});

test('admin can delete an ai model', function () {
    $model = AiModel::create([
        'name' => 'Temporary Model',
        'model_id' => 'temp-model',
        'provider' => 'custom',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->deleteJson(route('admin.ai-models.destroy', $model));
    $response->assertOk();

    expect(AiModel::find($model->id))->toBeNull();
});

test('store returns 422 if required fields are missing', function () {
    $response = $this->actingAs($this->admin, 'admin')->postJson(route('admin.ai-models.store'), [
        'name' => '',
        'model_id' => '',
        'provider' => '',
    ]);

    expect($response->status())->toBe(422);
    expect($response->json('errors'))->toHaveKeys(['name', 'model_id', 'provider']);
});

test('admin can create models for various providers', function (string $provider, string $name, string $modelId, string $baseUrl) {
    $response = $this->actingAs($this->admin, 'admin')->postJson(route('admin.ai-models.store'), [
        'name' => $name,
        'model_id' => $modelId,
        'provider' => $provider,
        'base_url' => $baseUrl,
        'api_key' => 'sk-test-key-12345',
        'is_active' => true,
        'is_default' => false,
    ]);

    $response->assertOk()->assertJson(['success' => true]);

    $model = AiModel::where('model_id', $modelId)->first();
    expect($model)->not->toBeNull();
    expect($model->provider)->toBe($provider);
})->with([
    ['openai', 'GPT-4o', 'gpt-4o', 'https://api.openai.com/v1'],
    ['anthropic', 'Claude 3.5 Sonnet', 'claude-3-5-sonnet-20241022', 'https://api.anthropic.com/v1'],
    ['deepseek', 'DeepSeek R1', 'deepseek-reasoner', 'https://api.deepseek.com'],
    ['grok', 'Grok 2', 'grok-2-latest', 'https://api.x.ai/v1'],
    ['google', 'Gemini 2.0 Flash', 'gemini-2.0-flash', 'https://generativelanguage.googleapis.com/v1beta'],
    ['qwen', 'Qwen 2.5 72B', 'qwen-plus', 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1'],
    ['groq', 'Groq LLaMA 3.3 70B', 'llama-3.3-70b-versatile', 'https://api.groq.com/openai/v1'],
]);
