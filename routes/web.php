<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Public\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home']);

Route::get('login', [LoginController::class, 'create'])->name('login');
Route::post('login', [LoginController::class, 'store']);
Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

Route::get('/{slug}', [PageController::class, 'show'])->where('slug', '^(?!admin|login|logout).+');
