<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\PublicFormController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home']);

Route::get('login', [LoginController::class, 'create'])->name('login');
Route::post('login', [LoginController::class, 'store']);
Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

Route::post('register', [RegisterController::class, 'store'])->name('register');

Route::post('/forms/submit', [PublicFormController::class, 'submit'])->name('forms.public-submit-default');
Route::post('/forms/{form}/submit', [PublicFormController::class, 'submit'])->name('forms.public-submit');

// TOOD: Shoud be check that properly
Route::get('/{collectionSlug}/{slug}', [PageController::class, 'showCollectionEntry'])
    ->where('collectionSlug', '^(?!admin|login|dev-login|logout|blogs|app|unsubscribe|tack-open|track-click|sendgrid).+');

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|login|dev-login|logout|app|unsubscribe|tack-open|track-click|sendgrid).+')
    ->name('page.show');
