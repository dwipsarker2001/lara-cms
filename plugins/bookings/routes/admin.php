<?php

use Illuminate\Support\Facades\Route;

Route::get('/bookings', function () {
    return view('admin.dashboard');
})->name('bookings.index');
