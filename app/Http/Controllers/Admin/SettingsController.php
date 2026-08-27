<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CloudflareR2Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    protected function ensureSchemaColumns(): void
    {
        if (Schema::hasTable('settings')) {
            if (! Schema::hasColumn('settings', 'custom_fields')) {
                Schema::table('settings', function ($table) {
                    $table->json('custom_fields')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'custom_values')) {
                Schema::table('settings', function ($table) {
                    $table->json('custom_values')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'language')) {
                Schema::table('settings', function ($table) {
                    $table->string('language', 10)->default('en')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'recaptcha_site_key')) {
                Schema::table('settings', function ($table) {
                    $table->string('recaptcha_site_key')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'recaptcha_secret_key')) {
                Schema::table('settings', function ($table) {
                    $table->string('recaptcha_secret_key')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'ai_base_url')) {
                Schema::table('settings', function ($table) {
                    $table->string('ai_base_url')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'ai_api_key')) {
                Schema::table('settings', function ($table) {
                    $table->text('ai_api_key')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'ai_model')) {
                Schema::table('settings', function ($table) {
                    $table->string('ai_model')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'unsplash_access_key')) {
                Schema::table('settings', function ($table) {
                    $table->text('unsplash_access_key')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'pexels_api_key')) {
                Schema::table('settings', function ($table) {
                    $table->text('pexels_api_key')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'pixabay_api_key')) {
                Schema::table('settings', function ($table) {
                    $table->text('pixabay_api_key')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'image_provider')) {
                Schema::table('settings', function ($table) {
                    $table->string('image_provider', 50)->default('auto')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'cloudflare_r2_enabled')) {
                Schema::table('settings', function ($table) {
                    $table->boolean('cloudflare_r2_enabled')->default(false);
                });
            }
            if (! Schema::hasColumn('settings', 'cloudflare_r2_account_id')) {
                Schema::table('settings', function ($table) {
                    $table->string('cloudflare_r2_account_id')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'cloudflare_r2_access_key_id')) {
                Schema::table('settings', function ($table) {
                    $table->string('cloudflare_r2_access_key_id')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'cloudflare_r2_secret_access_key')) {
                Schema::table('settings', function ($table) {
                    $table->text('cloudflare_r2_secret_access_key')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'cloudflare_r2_bucket')) {
                Schema::table('settings', function ($table) {
                    $table->string('cloudflare_r2_bucket')->nullable();
                });
            }
            if (! Schema::hasColumn('settings', 'cloudflare_r2_public_url')) {
                Schema::table('settings', function ($table) {
                    $table->string('cloudflare_r2_public_url')->nullable();
                });
            }
        }

        if (Schema::hasTable('assets') && ! Schema::hasColumn('assets', 'disk')) {
            Schema::table('assets', function ($table) {
                $table->string('disk', 30)->default('public')->nullable();
            });
        }
    }

    public function index()
    {
        $this->ensureSchemaColumns();

        $settings = Setting::firstOrCreate(['id' => 1]);

        $versionFile = base_path('version.json');
        $currentVersion = null;
        if (file_exists($versionFile)) {
            $content = json_decode(@file_get_contents($versionFile), true);
            $currentVersion = ! empty($content['version']) ? (string) $content['version'] : null;
        }

        $currentVersion = $currentVersion ?? $settings->cms_version ?? '1.0.0';

        return view('admin.settings.global', [
            'settings' => $settings,
            'currentVersion' => $currentVersion,
        ]);
    }

    public function update(Request $request)
    {
        $this->ensureSchemaColumns();

        $data = $request->validate([
            'theme_color' => 'nullable|string|max:7',
            'currency' => 'nullable|string|max:10',
            'language' => 'nullable|string|max:10',
            'recaptcha_site_key' => 'nullable|string|max:255',
            'recaptcha_secret_key' => 'nullable|string|max:255',
            'ai_base_url' => 'nullable|string|max:500',
            'ai_api_key' => 'nullable|string|max:1000',
            'ai_model' => 'nullable|string|max:150',
            'unsplash_access_key' => 'nullable|string|max:1000',
            'pexels_api_key' => 'nullable|string|max:1000',
            'pixabay_api_key' => 'nullable|string|max:1000',
            'image_provider' => 'nullable|string|in:auto,unsplash,pexels,pixabay,local',
            'cloudflare_r2_enabled' => 'nullable|boolean',
            'cloudflare_r2_account_id' => 'nullable|string|max:255',
            'cloudflare_r2_access_key_id' => 'nullable|string|max:255',
            'cloudflare_r2_secret_access_key' => 'nullable|string|max:1000',
            'cloudflare_r2_bucket' => 'nullable|string|max:255',
            'cloudflare_r2_public_url' => 'nullable|string|max:500',
            'logo_light' => 'nullable|string|max:255',
            'logo_dark' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'admin_theme' => 'nullable|string|in:dark,light',
            'custom_fields' => 'nullable',
            'custom_values' => 'nullable|array',
        ]);

        $data['cloudflare_r2_enabled'] = $request->boolean('cloudflare_r2_enabled');

        foreach (['ai_api_key', 'unsplash_access_key', 'pexels_api_key', 'pixabay_api_key', 'cloudflare_r2_secret_access_key', 'cloudflare_r2_access_key_id'] as $secretField) {
            if (array_key_exists($secretField, $data)) {
                $submittedKey = trim((string) $data[$secretField]);
                if ($submittedKey === '' || str_contains($submittedKey, '*') || str_contains($submittedKey, '•')) {
                    unset($data[$secretField]);
                }
            }
        }

        if (array_key_exists('custom_fields', $data)) {
            if (is_string($data['custom_fields'])) {
                $decoded = json_decode($data['custom_fields'], true);
                $data['custom_fields'] = is_array($decoded) ? $decoded : [];
            } elseif (! is_array($data['custom_fields'])) {
                $data['custom_fields'] = [];
            }
        }

        if (array_key_exists('custom_values', $data)) {
            $data['custom_values'] = is_array($data['custom_values']) ? $data['custom_values'] : [];
        }

        $settings = Setting::firstOrCreate(['id' => 1]);
        $settings->update($data);

        return back()->with('success', 'Settings updated successfully.');
    }

    public function testCloudflareR2(Request $request)
    {
        $this->ensureSchemaColumns();

        $settings = Setting::first();

        $accountId = $request->input('account_id') ?: $settings?->cloudflare_r2_account_id;
        $accessKeyId = $request->input('access_key_id') ?: $settings?->cloudflare_r2_access_key_id;
        $secretKey = $request->input('secret_access_key');
        if (empty($secretKey) || str_contains($secretKey, '*') || str_contains($secretKey, '•')) {
            $secretKey = $settings?->cloudflare_r2_secret_access_key;
        }
        $bucket = $request->input('bucket') ?: $settings?->cloudflare_r2_bucket;
        $publicUrl = $request->input('public_url') ?: $settings?->cloudflare_r2_public_url;

        $r2Service = new CloudflareR2Service([
            'account_id' => $accountId,
            'access_key_id' => $accessKeyId,
            'secret_access_key' => $secretKey,
            'bucket' => $bucket,
            'public_url' => $publicUrl,
        ]);

        $result = $r2Service->testConnection();

        return response()->json($result);
    }

    public function reorderCustomFields(Request $request)
    {
        $this->ensureSchemaColumns();

        $customFields = $request->input('custom_fields', []);
        if (is_string($customFields)) {
            $customFields = json_decode($customFields, true) ?? [];
        }

        $settings = Setting::firstOrCreate(['id' => 1]);
        $settings->update([
            'custom_fields' => is_array($customFields) ? $customFields : [],
        ]);

        return response()->noContent();
    }
}
