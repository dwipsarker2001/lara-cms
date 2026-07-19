<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Public\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home']);

// TODO: Shoud be rmeoved in production. 
Route::get('dev-login', [LoginController::class, 'create'])->name('login');
Route::post('dev-login', [LoginController::class, 'store']);

Route::post('login', [LoginController::class, 'store']);
Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

Route::post('register', [RegisterController::class, 'store'])->name('register');

require __DIR__.'/marketing/marketing.php';
require __DIR__.'/marketing/tracking.php';


// TOOD: Shoud be check that properly
Route::get('/{collectionSlug}/{slug}', [PageController::class, 'showCollectionEntry'])
    ->where('collectionSlug', '^(?!admin|dev-login|logout|blogs|app|unsubscribe|tack-open|track-click|sendgrid).+');

Route::get('/{slug}', [PageController::class, 'show'])->where('slug', '^(?!admin|dev-login|logout|app|unsubscribe|tack-open|track-click|sendgrid).+');
