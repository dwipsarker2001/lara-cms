<?php

use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    Cache::forget('cms_latest_release_info');
    config(['cms.github_repo' => 'dwipsarker2001/lara-cms']);
    config(['cms.github_branch' => 'main']);
    $admin = Admin::factory()->create();
    actingAs($admin, 'admin');
});

it('can view the settings page with version info', function () {
    Setting::firstOrCreate(['id' => 1], ['cms_version' => '1.0.0']);

    get(route('admin.settings'))
        ->assertSuccessful();
});

it('check endpoint returns update_available when remote reports newer version', function () {
    Http::fake([
        'https://raw.githubusercontent.com/dwipsarker2001/lara-cms/main/version.json' => Http::response([
            'version' => '1.1.0',
            'download_url' => 'https://github.com/dwipsarker2001/lara-cms/archive/refs/tags/v1.1.0.zip',
        ], 200),
    ]);

    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.0.0']);

    get(route('admin.updates.check'))
        ->assertSuccessful()
        ->assertJson([
            'current_version' => '1.0.0',
            'latest_version' => '1.1.0',
            'update_available' => true,
            'status' => 'update_available',
        ]);
});

it('check endpoint reports up_to_date when versions match', function () {
    Http::fake([
        'https://raw.githubusercontent.com/dwipsarker2001/lara-cms/main/version.json' => Http::response([
            'version' => '1.1.0',
            'download_url' => 'https://github.com/dwipsarker2001/lara-cms/archive/refs/tags/v1.1.0.zip',
        ], 200),
    ]);

    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.1.0']);

    get(route('admin.updates.check'))
        ->assertSuccessful()
        ->assertJson([
            'update_available' => false,
            'status' => 'up_to_date',
        ]);
});

it('check endpoint returns check_failed when remote is unreachable', function () {
    Http::fake([
        'https://raw.githubusercontent.com/dwipsarker2001/lara-cms/main/version.json' => Http::response('Not Found', 404),
    ]);

    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.2.8']);

    get(route('admin.updates.check'))
        ->assertSuccessful()
        ->assertJson([
            'current_version' => '1.2.8',
            'latest_version' => null,
            'update_available' => false,
            'status' => 'check_failed',
        ]);
});

it('check endpoint returns check_failed when no github repo is configured', function () {
    config(['cms.github_repo' => null]);

    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.2.8']);

    get(route('admin.updates.check'))
        ->assertSuccessful()
        ->assertJson([
            'status' => 'check_failed',
            'update_available' => false,
        ]);
});

it('does not cache failed remote results', function () {
    Http::fake([
        'https://raw.githubusercontent.com/dwipsarker2001/lara-cms/main/version.json' => Http::sequence()
            ->push('Server Error', 500)
            ->push(['version' => '1.3.2', 'download_url' => 'https://github.com/dwipsarker2001/lara-cms/archive/refs/tags/v1.3.2.zip'], 200),
    ]);

    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.0.0']);

    // First check fails — should NOT be cached
    get(route('admin.updates.check'))
        ->assertJson(['status' => 'check_failed']);

    // Second check retries remote and succeeds (not serving cached failure)
    get(route('admin.updates.check'))
        ->assertJson([
            'latest_version' => '1.3.2',
            'status' => 'update_available',
        ]);
});

it('caches successful remote results', function () {
    Http::fake([
        'https://raw.githubusercontent.com/dwipsarker2001/lara-cms/main/version.json' => Http::sequence()
            ->push(['version' => '1.2.0', 'download_url' => 'https://github.com/dwipsarker2001/lara-cms/archive/refs/tags/v1.2.0.zip'], 200)
            ->push(['version' => '1.3.0', 'download_url' => 'https://github.com/dwipsarker2001/lara-cms/archive/refs/tags/v1.3.0.zip'], 200),
    ]);

    Setting::firstOrCreate(['id' => 1], ['cms_version' => '1.0.0']);

    // First check fetches v1.2.0 and caches it
    get(route('admin.updates.check'))->assertJson(['latest_version' => '1.2.0']);

    // Second check serves cached v1.2.0 (not v1.3.0)
    get(route('admin.updates.check'))->assertJson(['latest_version' => '1.2.0']);

    // Third check with force=1 invalidates cache and fetches v1.3.0
    get(route('admin.updates.check', ['force' => 1]))->assertJson(['latest_version' => '1.3.0']);
});

it('fetches latest version from version.json on raw.githubusercontent.com', function () {
    Http::fake([
        'https://raw.githubusercontent.com/dwipsarker2001/lara-cms/main/version.json' => Http::response([
            'version' => '1.2.0',
            'download_url' => 'https://github.com/dwipsarker2001/lara-cms/archive/refs/tags/v1.2.0.zip',
        ], 200),
    ]);

    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.0.0']);

    get(route('admin.updates.check'))
        ->assertSuccessful()
        ->assertJson([
            'current_version' => '1.0.0',
            'latest_version' => '1.2.0',
            'update_available' => true,
        ]);
});

it('run endpoint returns error when remote is unreachable', function () {
    Http::fake([
        'https://raw.githubusercontent.com/dwipsarker2001/lara-cms/main/version.json' => Http::response('', 500),
    ]);

    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.0.0']);

    post(route('admin.updates.run'))
        ->assertSuccessful()
        ->assertJson([
            'success' => false,
            'message' => 'Unable to determine the latest version. Please check your internet connection and try again.',
        ]);
});

it('run endpoint downloads zip, extracts it, and bumps version', function () {
    if (! class_exists('ZipArchive')) {
        $this->markTestSkipped('ZipArchive extension not installed.');
    }

    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update(['cms_version' => '1.0.0']);

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
        'https://raw.githubusercontent.com/dwipsarker2001/lara-cms/main/version.json' => Http::response([
            'version' => '1.1.0',
            'download_url' => 'https://example.com/download/v1.1.0.zip',
        ], 200),
        'https://example.com/download/v1.1.0.zip' => Http::response($zipContent, 200),
        '*' => Http::response('', 404),
    ]);

    post(route('admin.updates.run'))
        ->assertSuccessful()
        ->assertJson([
            'success' => true,
            'version' => '1.1.0',
        ]);

    Http::assertSent(function ($request) {
        return $request->hasHeader('Accept', '*/*');
    });

    $settings->refresh();
    expect($settings->cms_version)->toBe('1.1.0');

    // Clean up indicator file extracted by the test
    @unlink(base_path('.lara_cms_update_indicator'));
});
