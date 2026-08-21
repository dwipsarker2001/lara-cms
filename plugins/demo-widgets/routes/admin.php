<?php

use Illuminate\Support\Facades\Route;

// Admin routes (auto-prefixed with /admin and protected by auth:admin middleware)
Route::get('/demo-widgets', function () {
    return view('demo-widgets::admin.index');
})->name('demo-widgets.index');
