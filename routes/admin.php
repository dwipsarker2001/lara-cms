<?php

use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PreviewController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');

    Route::resource('pages', PageController::class)->except(['show']);
    Route::patch('pages/reorder', [PageController::class, 'reorder'])->name('pages.reorder');
    Route::patch('pages/{page}/sections', [PageController::class, 'updateSections'])->name('pages.update-sections');
    Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::get('pages/{page}', [PageController::class, 'editor'])->name('pages.editor');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::post('preview', [PreviewController::class, 'render'])->name('preview');
});
