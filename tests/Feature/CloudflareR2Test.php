<?php

use App\Models\Admin;
use App\Models\Asset;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
    Storage::fake('public');
});

it('can store and update cloudflare r2 settings', function () {
    $response = put(route('admin.settings.update'), [
        'cloudflare_r2_enabled' => '1',
        'cloudflare_r2_account_id' => 'test_account_id_12345',
        'cloudflare_r2_access_key_id' => 'test_access_key_id_67890',
        'cloudflare_r2_secret_access_key' => 'test_secret_access_key_super_secret',
        'cloudflare_r2_bucket' => 'my-media-bucket',
        'cloudflare_r2_public_url' => 'https://pub-abcdef12345.r2.dev',
    ]);

    $response->assertRedirect();

    $settings = Setting::first();
    expect($settings->cloudflare_r2_enabled)->toBeTrue()
        ->and($settings->cloudflare_r2_account_id)->toBe('test_account_id_12345')
        ->and($settings->cloudflare_r2_access_key_id)->toBe('test_access_key_id_67890')
        ->and($settings->cloudflare_r2_secret_access_key)->toBe('test_secret_access_key_super_secret')
        ->and($settings->cloudflare_r2_bucket)->toBe('my-media-bucket')
        ->and($settings->cloudflare_r2_public_url)->toBe('https://pub-abcdef12345.r2.dev');

    // Check masked key helpers
    expect($settings->getMaskedR2SecretKey())->toContain('****')
        ->and($settings->getMaskedR2AccessKey())->toContain('****');

    // Re-saving with masked secret key preserves the original secret key
    put(route('admin.settings.update'), [
        'cloudflare_r2_enabled' => '1',
        'cloudflare_r2_account_id' => 'test_account_id_12345',
        'cloudflare_r2_access_key_id' => 'test_access_key_id_67890',
        'cloudflare_r2_secret_access_key' => $settings->getMaskedR2SecretKey(),
        'cloudflare_r2_bucket' => 'my-media-bucket',
    ]);

    expect(Setting::first()->cloudflare_r2_secret_access_key)->toBe('test_secret_access_key_super_secret');
});

it('renders cloudflare r2 tab in global settings page', function () {
    get(route('admin.settings'))
        ->assertSuccessful()
        ->assertSee('Cloudflare Storage', false)
        ->assertSee('Cloudflare R2 Storage Configuration', false)
        ->assertSee('name="cloudflare_r2_account_id"', false)
        ->assertSee('name="cloudflare_r2_bucket"', false);
});

it('can test connection to cloudflare r2 via api endpoint', function () {
    Http::fake([
        'https://test_account.r2.cloudflarestorage.com/*' => Http::response('<ListBucketResult></ListBucketResult>', 200),
    ]);

    $response = postJson(route('admin.settings.test_cloudflare_r2'), [
        'account_id' => 'test_account',
        'access_key_id' => 'test_access',
        'secret_access_key' => 'test_secret',
        'bucket' => 'test-bucket',
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
        ]);
});

it('returns detailed error when test connection fails', function () {
    Http::fake([
        'https://test_account.r2.cloudflarestorage.com/*' => Http::response('<Error><Message>Invalid Access Key</Message></Error>', 403),
    ]);

    $response = postJson(route('admin.settings.test_cloudflare_r2'), [
        'account_id' => 'test_account',
        'access_key_id' => 'bad_access',
        'secret_access_key' => 'bad_secret',
        'bucket' => 'test-bucket',
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => false,
        ]);
});

it('uploads files to cloudflare r2 when enabled', function () {
    Setting::updateOrCreate(['id' => 1], [
        'cloudflare_r2_enabled' => true,
        'cloudflare_r2_account_id' => 'acc123',
        'cloudflare_r2_access_key_id' => 'key123',
        'cloudflare_r2_secret_access_key' => 'secret123',
        'cloudflare_r2_bucket' => 'media-bucket',
        'cloudflare_r2_public_url' => 'https://pub-xyz.r2.dev',
    ]);

    Http::fake([
        'https://acc123.r2.cloudflarestorage.com/*' => Http::response('', 200),
    ]);

    $file = UploadedFile::fake()->image('banner.jpg', 800, 600);

    $response = post(route('admin.assets.store'), [
        'file' => $file,
    ]);

    $response->assertSuccessful();

    $asset = Asset::first();
    expect($asset)->not->toBeNull()
        ->and($asset->disk)->toBe('r2')
        ->and($asset->name)->toBe('banner.jpg')
        ->and($asset->url)->toContain('https://pub-xyz.r2.dev/assets/');
});

it('falls back to local storage upload when cloudflare r2 is disabled', function () {
    Setting::updateOrCreate(['id' => 1], [
        'cloudflare_r2_enabled' => false,
    ]);

    $file = UploadedFile::fake()->image('local.png', 400, 300);

    $response = post(route('admin.assets.store'), [
        'file' => $file,
    ]);

    $response->assertSuccessful();

    $asset = Asset::first();
    expect($asset->disk)->toBe('public')
        ->and($asset->url)->toContain('/storage/assets/');
});

it('serves file from cloudflare r2 with redirect or stream', function () {
    Setting::updateOrCreate(['id' => 1], [
        'cloudflare_r2_enabled' => true,
        'cloudflare_r2_account_id' => 'acc123',
        'cloudflare_r2_access_key_id' => 'key123',
        'cloudflare_r2_secret_access_key' => 'secret123',
        'cloudflare_r2_bucket' => 'media-bucket',
        'cloudflare_r2_public_url' => 'https://pub-xyz.r2.dev',
    ]);

    $asset = Asset::create([
        'name' => 'photo.jpg',
        'path' => 'assets/photo.jpg',
        'disk' => 'r2',
        'mime' => 'image/jpeg',
        'size' => 1024,
    ]);

    // When public URL is configured, file view redirects to CDN
    get(route('admin.assets.file', $asset))
        ->assertRedirect('https://pub-xyz.r2.dev/assets/photo.jpg');

    // When download is requested, content is streamed with attachment header
    Http::fake([
        'https://acc123.r2.cloudflarestorage.com/*' => Http::response('image-binary-data', 200),
    ]);

    get(route('admin.assets.file', ['asset' => $asset, 'download' => 1]))
        ->assertSuccessful()
        ->assertHeader('Content-Disposition', 'attachment; filename="photo.jpg"');
});

it('deletes asset from cloudflare r2 when destroyed', function () {
    Setting::updateOrCreate(['id' => 1], [
        'cloudflare_r2_enabled' => true,
        'cloudflare_r2_account_id' => 'acc123',
        'cloudflare_r2_access_key_id' => 'key123',
        'cloudflare_r2_secret_access_key' => 'secret123',
        'cloudflare_r2_bucket' => 'media-bucket',
    ]);

    Http::fake([
        'https://acc123.r2.cloudflarestorage.com/*' => Http::response('', 204),
    ]);

    $asset = Asset::create([
        'name' => 'delete-me.jpg',
        'path' => 'assets/delete-me.jpg',
        'disk' => 'r2',
    ]);

    $response = deleteJson(route('admin.assets.destroy', $asset));
    $response->assertSuccessful();

    expect(Asset::find($asset->id))->toBeNull();
});
