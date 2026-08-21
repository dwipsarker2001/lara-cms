<?php

use Illuminate\Support\Facades\Route;

// Public web routes for Demo Widgets
Route::get('/demo-widgets', function () {
    return view('demo-widgets::index');
});
