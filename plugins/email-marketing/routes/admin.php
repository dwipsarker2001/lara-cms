<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Email Marketing Plugin - Admin Routes
|--------------------------------------------------------------------------
| These routes are loaded under /admin with auth:admin middleware.
| They are automatically discovered by the PluginLoader.
| They will NEVER be removed during a CMS core update.
*/

Route::get('email-marketing', function () {
    return view('email-marketing::dashboard');
})->name('plugins.email-marketing.dashboard');

Route::get('email-marketing/subscribers', function () {
    return view('email-marketing::subscribers');
})->name('plugins.email-marketing.subscribers');
