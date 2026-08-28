<?php

use App\Services\CmsUpdaterService;
use Illuminate\Support\Facades\Http;

it('checks for updates via cms:update --check', function () {
    Http::fake([
        'raw.githubusercontent.com/*' => Http::response([
            'version' => '2.0.0',
            'download_url' => 'https://github.com/dwipsarker2001/lara-cms/archive/refs/tags/v2.0.0.zip',
        ], 200),
    ]);

    $this->artisan('cms:update', ['--check' => true])
        ->expectsOutputToContain('Checking for Lara-CMS updates...')
        ->expectsOutputToContain('A new version (v2.0.0) is available!')
        ->assertSuccessful();
});

it('reports system is up to date when current matches latest', function () {
    $current = app(CmsUpdaterService::class)->getCurrentVersion();

    Http::fake([
        'raw.githubusercontent.com/*' => Http::response([
            'version' => $current,
            'download_url' => "https://github.com/dwipsarker2001/lara-cms/archive/refs/tags/v{$current}.zip",
        ], 200),
    ]);

    $this->artisan('cms:update', ['--check' => true])
        ->expectsOutputToContain('Your system is up to date.')
        ->assertSuccessful();
});

it('aborts update if user declines confirmation', function () {
    Http::fake([
        'raw.githubusercontent.com/*' => Http::response([
            'version' => '99.0.0',
            'download_url' => 'https://github.com/dwipsarker2001/lara-cms/archive/refs/tags/v99.0.0.zip',
        ], 200),
    ]);

    $this->artisan('cms:update')
        ->expectsConfirmation('Do you want to proceed with the update?', 'no')
        ->expectsOutputToContain('Update aborted.')
        ->assertSuccessful();
});
