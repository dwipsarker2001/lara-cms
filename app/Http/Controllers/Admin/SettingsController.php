<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
        }
    }

    public function index()
    {
        $this->ensureSchemaColumns();

        $settings = Setting::firstOrCreate(['id' => 1]);
        $currentVersion = $settings->cms_version ?? '1.0.0';

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
            'logo_light' => 'nullable|string|max:255',
            'logo_dark' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'admin_theme' => 'nullable|string|in:dark,light',
            'custom_fields' => 'nullable',
            'custom_values' => 'nullable|array',
        ]);

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
}
