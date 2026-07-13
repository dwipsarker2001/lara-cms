<?php

use App\Http\Controllers\Admin\AssetsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PreviewController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');

    Route::patch('pages/reorder', [PageController::class, 'reorder'])->name('pages.reorder');
    Route::resource('pages', PageController::class)->except(['show']);
    Route::patch('pages/{page}/sections', [PageController::class, 'updateSections'])->name('pages.update-sections');
    Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::get('pages/{page}', [PageController::class, 'editor'])->name('pages.editor');

    Route::patch('posts/reorder', [PostController::class, 'reorder'])->name('posts.reorder');
    Route::resource('posts', PostController::class)->except(['show']);

    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('assets', [AssetsController::class, 'page'])->name('assets.index');
    Route::get('assets/list', [AssetsController::class, 'index'])->name('assets.list');
    Route::post('assets', [AssetsController::class, 'store'])->name('assets.store');
    Route::post('assets/directory', [AssetsController::class, 'directory'])->name('assets.directory');
    Route::put('assets/{asset}', [AssetsController::class, 'update'])->name('assets.update');
    Route::delete('assets/{asset}', [AssetsController::class, 'destroy'])->name('assets.destroy');
    Route::get('assets/{asset}/file', [AssetsController::class, 'file'])->name('assets.file');

    Route::post('preview', [PreviewController::class, 'render'])->name('preview');
    Route::get('block-preview/{block}', [PreviewController::class, 'blockPreview'])->name('block-preview');
});
