<?php

use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('does not have site_title column in settings table', function () {
    expect(Schema::hasColumn('settings', 'site_title'))->toBeFalse();
});

it('updates global settings without site_title field', function () {
    $response = put(route('admin.settings.update'), [
        'theme_color' => '#123456',
        'currency' => 'EUR',
        'admin_theme' => 'light',
    ]);

    $response->assertRedirect();

    $settings = Setting::firstOrCreate(['id' => 1]);
    expect($settings->theme_color)->toBe('#123456')
        ->and($settings->currency)->toBe('EUR')
        ->and($settings->admin_theme)->toBe('light');
});

it('renders global settings page without site_title input', function () {
    get(route('admin.settings'))
        ->assertSuccessful()
        ->assertDontSee('id="field-site-title"', false)
        ->assertDontSee('name="site_title"', false);
});

it('can store and update recaptcha keys in settings and load on login page', function () {
    $response = put(route('admin.settings.update'), [
        'recaptcha_site_key' => '6LtestSiteKey123',
        'recaptcha_secret_key' => '6LtestSecretKey456',
    ]);

    $response->assertRedirect();

    $settings = Setting::first();
    expect($settings->recaptcha_site_key)->toBe('6LtestSiteKey123')
        ->and($settings->recaptcha_secret_key)->toBe('6LtestSecretKey456');

    // Unauthenticate and check login page
    auth('admin')->logout();

    get(route('login'))
        ->assertSuccessful()
        ->assertSee('https://www.google.com/recaptcha/api.js?render=6LtestSiteKey123', false);
});

it('returns correct site name via getSiteName method', function () {
    expect(Setting::getSiteName())->toBe(config('app.name', 'LaraCMS'));

    Setting::updateOrCreate(['id' => 1], [
        'seo' => ['site_title' => 'My Custom Site Title'],
    ]);

    expect(Setting::getSiteName())->toBe('My Custom Site Title');
});

it('returns logo and contact number via helper methods', function () {
    Setting::updateOrCreate(['id' => 1], [
        'logo_light' => '/storage/logo-light.png',
        'logo_dark' => '/storage/logo-dark.png',
        'contact_number' => '+123456789',
    ]);

    expect(Setting::getLogo('light'))->toBe('/storage/logo-light.png')
        ->and(Setting::getLogo('dark'))->toBe('/storage/logo-dark.png')
        ->and(Setting::getContactNumber())->toBe('+123456789');
});

it('can store and update ai settings including base url, api key, and model name', function () {
    $response = put(route('admin.settings.update'), [
        'ai_base_url' => 'https://api.groq.com/openai/v1',
        'ai_api_key' => 'sk-test-ai-secret-key-999',
        'ai_model' => 'llama-3.3-70b-versatile',
    ]);

    $response->assertRedirect();

    $settings = Setting::first();
    expect($settings->ai_base_url)->toBe('https://api.groq.com/openai/v1')
        ->and($settings->ai_api_key)->toBe('sk-test-ai-secret-key-999')
        ->and($settings->ai_model)->toBe('llama-3.3-70b-versatile');

    $maskedKey = $settings->getMaskedAiApiKey();
    expect($maskedKey)->toContain('sk-t')
        ->and($maskedKey)->toContain('-999')
        ->and($maskedKey)->toContain('*')
        ->and($maskedKey)->not->toBe('sk-test-ai-secret-key-999');

    get(route('admin.settings'))
        ->assertSuccessful()
        ->assertSee('name="ai_base_url"', false)
        ->assertSee('name="ai_api_key"', false)
        ->assertSee('name="ai_model"', false)
        ->assertSee('https://api.groq.com/openai/v1', false)
        ->assertSee('llama-3.3-70b-versatile', false)
        ->assertSee($maskedKey, false)
        ->assertDontSee('sk-test-ai-secret-key-999', false);

    // Saving with masked key or blank preserves existing raw key
    put(route('admin.settings.update'), [
        'ai_base_url' => 'https://api.groq.com/openai/v1',
        'ai_api_key' => $maskedKey,
        'ai_model' => 'llama-3.3-70b-versatile',
    ]);

    expect(Setting::first()->ai_api_key)->toBe('sk-test-ai-secret-key-999');
});
