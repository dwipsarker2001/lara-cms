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
