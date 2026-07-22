<?php

use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $admin = Admin::factory()->create();
    actingAs($admin, 'admin');
});

it('can view the settings page with version info', function () {
    Setting::firstOrCreate(['id' => 1], ['cms_version' => '1.0.0']);
    config(['cms.latest_version' => '1.0.0']);

    get(route('admin.settings'))
        ->assertSuccessful();
});

it('check endpoint returns current and latest version info', function () {
    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.0.0']);
    config(['cms.latest_version' => '1.1.0']);

    get(route('admin.updates.check'))
        ->assertSuccessful()
        ->assertJson([
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'update_available' => true,
        ]);
});

it('check endpoint reports up_to_date when versions match', function () {
    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.1.0']);
    config(['cms.latest_version' => '1.1.0']);

    get(route('admin.updates.check'))
        ->assertSuccessful()
        ->assertJson(['update_available' => false]);
});

it('run endpoint returns already up to date when no update is needed', function () {
    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.1.0']);
    config(['cms.latest_version' => '1.1.0']);

    post(route('admin.updates.run'))
        ->assertSuccessful()
        ->assertJson(['success' => false]);
});

it('run endpoint downloads zip, extracts it, and bumps version', function () {
    if (! class_exists('ZipArchive')) {
        $this->markTestSkipped('ZipArchive extension not installed.');
    }

    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.0.0']);
    config(['cms.latest_version' => '1.1.0']);

    // Build a real valid in-memory ZIP to feed back via Http::fake()
    $tempFile = tempnam(sys_get_temp_dir(), 'lara_test_zip');
    $zip = new ZipArchive;
    if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        $zip->addFromString('.lara_cms_update_indicator', 'v1.1.0');
        $zip->close();
    }
    $zipContent = file_get_contents($tempFile);
    @unlink($tempFile);

    Http::fake([
        config('cms.update_url') => Http::response($zipContent, 200),
        '*' => Http::response('', 404),
    ]);

    post(route('admin.updates.run'))
        ->assertSuccessful()
        ->assertJson([
            'success' => true,
            'version' => '1.1.0',
        ]);

    $settings->refresh();
    expect($settings->cms_version)->toBe('1.1.0');

    // Clean up indicator file extracted by the test
    @unlink(base_path('.lara_cms_update_indicator'));
});
