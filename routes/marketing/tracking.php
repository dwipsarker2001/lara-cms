<?php

use App\Http\Controllers\Marketing\CampaignController;
use App\Http\Controllers\Marketing\TrackerController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::post('sendgrid/webhook', [CampaignController::class, 'handle'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::get('unsubscribe', [TrackerController::class, 'unsubscribeTracker'])->name('unsubscribeTracker');
Route::get('tack-open', [TrackerController::class, 'openTracker'])->name('openTracker');
Route::get('track-click', [TrackerController::class, 'clickTracker'])->name('clickTracker');
