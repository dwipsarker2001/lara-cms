<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::firstOrCreate(['id' => 1]);
        $currentVersion = $settings->cms_version ?? '1.0.0';
        $latestVersion = config('cms.latest_version', '1.0.0');

        return view('admin.settings.global', [
            'settings' => $settings,
            'currentVersion' => $currentVersion,
            'latestVersion' => $latestVersion,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_title' => 'string|max:255',
            'theme_color' => 'string|max:7',
            'logo_light' => 'nullable|string|max:255',
            'logo_dark' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'admin_theme' => 'string|in:dark,light',
        ]);

        $settings = Setting::firstOrCreate(['id' => 1]);
        $settings->update($data);

        return back()->with('success', 'Settings updated.');
    }
}
