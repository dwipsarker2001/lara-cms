<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AssetsController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\CollectionEntryController;
use App\Http\Controllers\Admin\CommandSearchController;
use App\Http\Controllers\Admin\DynamicBlockController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\LayoutController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PreviewController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
    Route::get('search', CommandSearchController::class)->name('search');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::patch('pages/reorder', [PageController::class, 'reorder'])->name('pages.reorder');
    Route::resource('pages', PageController::class)->except(['show']);
    Route::patch('pages/{page}/sections', [PageController::class, 'updateSections'])->name('pages.update-sections');
    Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::get('pages/{page}', [PageController::class, 'editor'])->name('pages.editor');

    Route::patch('posts/reorder', [PostController::class, 'reorder'])->name('posts.reorder');
    Route::resource('posts', PostController::class)->except(['show']);
    Route::patch('posts/{post}/sections', [PostController::class, 'updateSections'])->name('posts.update-sections');
    Route::get('posts/{post}/editor', [PostController::class, 'editor'])->name('posts.editor');

    Route::patch('packages/reorder', [PackageController::class, 'reorder'])->name('packages.reorder');
    Route::resource('packages', PackageController::class)->except(['show']);
    Route::patch('packages/{package}/sections', [PackageController::class, 'updateSections'])->name('packages.update-sections');
    Route::get('packages/{package}/editor', [PackageController::class, 'editor'])->name('packages.editor');

    Route::patch('layouts/reorder', [LayoutController::class, 'reorder'])->name('layouts.reorder');
    Route::resource('layouts', LayoutController::class)->except(['show']);
    Route::patch('layouts/{layout}/sections', [LayoutController::class, 'updateSections'])->name('layouts.update-sections');
    Route::get('layouts/{layout}/editor', [LayoutController::class, 'editor'])->name('layouts.editor');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::patch('forms/reorder', [FormController::class, 'reorder'])->name('forms.reorder');
    Route::get('forms/{form}/editor', [FormController::class, 'editor'])->name('forms.editor');
    Route::get('forms/{form}/entries', [FormController::class, 'entries'])->name('forms.entries');
    Route::get('forms/{form}/entries/export', [FormController::class, 'export'])->name('forms.export');
    Route::get('forms/{form}/entries/{entry}', [FormController::class, 'entryJson'])->name('forms.entries.json');
    Route::patch('forms/{form}/fields', [FormController::class, 'updateFields'])->name('forms.update-fields');
    Route::resource('forms', FormController::class)->except(['show']);

    Route::patch('collections/reorder', [CollectionController::class, 'reorder'])->name('collections.reorder');
    Route::resource('collections', CollectionController::class)->except(['show']);

    Route::patch('collections/{collection}/entries/reorder', [CollectionEntryController::class, 'reorder'])->name('collections.entries.reorder');
    Route::get('collections/{collection}/entries/{entry}/editor', [CollectionEntryController::class, 'editor'])->name('collections.entries.editor');
    Route::patch('collections/{collection}/entries/{entry}/sections', [CollectionEntryController::class, 'updateSections'])->name('collections.entries.update-sections');
    Route::resource('collections.entries', CollectionEntryController::class);

    Route::get('seo', [SeoController::class, 'index'])->name('seo');
    Route::put('seo', [SeoController::class, 'update'])->name('seo.update');

    Route::resource('taxonomies', TaxonomyController::class)->except(['show']);

    Route::get('assets', [AssetsController::class, 'page'])->name('assets.index');
    Route::get('assets/list', [AssetsController::class, 'index'])->name('assets.list');
    Route::post('assets', [AssetsController::class, 'store'])->name('assets.store');
    Route::post('assets/directory', [AssetsController::class, 'directory'])->name('assets.directory');
    Route::put('assets/{asset}', [AssetsController::class, 'update'])->name('assets.update');
    Route::delete('assets/{asset}', [AssetsController::class, 'destroy'])->name('assets.destroy');
    Route::get('assets/{asset}/file', [AssetsController::class, 'file'])->name('assets.file');

    Route::resource('users', UserController::class)->except(['show']);

    Route::resource('administrators', AdminUserController::class)
        ->except(['show'])
        ->parameters(['administrators' => 'admin']);

    Route::post('preview', [PreviewController::class, 'render'])->name('preview');
    Route::get('block-preview/{block}', [PreviewController::class, 'blockPreview'])->name('block-preview');

    Route::resource('dynamic-blocks', DynamicBlockController::class)->except(['show']);
    Route::get('dynamic-blocks/{dynamic_block}/editor', [DynamicBlockController::class, 'editor'])->name('dynamic-blocks.editor');
    Route::put('dynamic-blocks/{dynamic_block}/editor', [DynamicBlockController::class, 'updateEditor'])->name('dynamic-blocks.update-editor');
});
