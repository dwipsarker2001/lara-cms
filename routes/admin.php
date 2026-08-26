<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AiAgentController;
use App\Http\Controllers\Admin\AssetsController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\CollectionEntryController;
use App\Http\Controllers\Admin\CommandSearchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\PreviewController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\Admin\WidgetController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('widgets/layout', [WidgetController::class, 'layout'])->name('widgets.layout');
    Route::post('widgets/render', [WidgetController::class, 'render'])->name('widgets.render');

    Route::match(['get', 'post'], 'preview', PreviewController::class)->name('preview');

    Route::get('search', CommandSearchController::class)->name('search');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::patch('settings/reorder-custom-fields', [SettingsController::class, 'reorderCustomFields'])->name('settings.reorder_custom_fields');

    Route::get('updates/check', [UpdateController::class, 'check'])->name('updates.check');
    Route::post('updates/run', [UpdateController::class, 'run'])->name('updates.run');

    Route::patch('forms/reorder', [FormController::class, 'reorder'])->name('forms.reorder');
    Route::get('forms/{form}/editor', [FormController::class, 'editor'])->name('forms.editor');
    Route::get('forms/{form}/entries', [FormController::class, 'entries'])->name('forms.entries');
    Route::get('forms/{form}/entries/create', [FormController::class, 'createEntry'])->name('forms.entries.create');
    Route::post('forms/{form}/entries', [FormController::class, 'storeEntry'])->name('forms.entries.store');
    Route::get('forms/{form}/entries/export', [FormController::class, 'export'])->name('forms.export');
    Route::get('forms/{form}/entries/{entry}', [FormController::class, 'entryJson'])->name('forms.entries.json');
    Route::put('forms/{form}/entries/{entry}', [FormController::class, 'updateEntry'])->name('forms.entries.update');
    Route::delete('forms/{form}/entries-bulk', [FormController::class, 'destroyEntriesBulk'])->name('forms.entries.bulk-destroy');
    Route::post('forms/{form}/entries-bulk-duplicate', [FormController::class, 'duplicateEntriesBulk'])->name('forms.entries.bulk-duplicate');
    Route::post('forms/{form}/entries/{entry}/duplicate', [FormController::class, 'duplicateEntry'])->name('forms.entries.duplicate');
    Route::delete('forms/{form}/entries/{entry}', [FormController::class, 'destroyEntry'])->name('forms.entries.destroy');
    Route::patch('forms/{form}/fields', [FormController::class, 'updateFields'])->name('forms.update-fields');
    Route::post('forms/{form}/columns', [FormController::class, 'saveColumns'])->name('forms.save-columns');
    Route::resource('forms', FormController::class)->except(['show']);

    Route::patch('collections/reorder', [CollectionController::class, 'reorder'])->name('collections.reorder');
    Route::resource('collections', CollectionController::class)->except(['show']);

    Route::patch('collections/{collection}/entries/reorder', [CollectionEntryController::class, 'reorder'])->name('collections.entries.reorder');
    Route::delete('collections/{collection}/entries/delete-all', [CollectionEntryController::class, 'destroyAll'])->name('collections.entries.destroy-all');
    Route::get('collections/{collection}/entries/{entry}/editor', [CollectionEntryController::class, 'editor'])->name('collections.entries.editor');
    Route::patch('collections/{collection}/entries/{entry}/update-sections', [CollectionEntryController::class, 'updateSections'])->name('collections.entries.update-sections');
    Route::resource('collections.entries', CollectionEntryController::class);

    Route::get('seo', [SeoController::class, 'index'])->name('seo');
    Route::put('seo', [SeoController::class, 'update'])->name('seo.update');

    Route::patch('taxonomies/reorder', [TaxonomyController::class, 'reorder'])->name('taxonomies.reorder');
    Route::resource('taxonomies', TaxonomyController::class);
    Route::get('taxonomies/{taxonomy}/terms/create', [TermController::class, 'create'])->name('taxonomies.terms.create');
    Route::get('taxonomies/{taxonomy}/terms/{term}/edit', [TermController::class, 'edit'])->name('taxonomies.terms.edit');
    Route::post('taxonomies/{taxonomy}/terms', [TermController::class, 'store'])->name('taxonomies.terms.store');
    Route::put('taxonomies/{taxonomy}/terms/{term}', [TermController::class, 'update'])->name('taxonomies.terms.update');
    Route::delete('taxonomies/{taxonomy}/terms/{term}', [TermController::class, 'destroy'])->name('taxonomies.terms.destroy');
    Route::patch('taxonomies/{taxonomy}/terms/reorder', [TermController::class, 'reorder'])->name('taxonomies.terms.reorder');

    Route::get('assets', [AssetsController::class, 'page'])->name('assets.index');
    Route::get('assets/list', [AssetsController::class, 'index'])->name('assets.list');
    Route::post('assets', [AssetsController::class, 'store'])->name('assets.store');
    Route::post('assets/directory', [AssetsController::class, 'directory'])->name('assets.directory');
    Route::put('assets/{asset}', [AssetsController::class, 'update'])->name('assets.update');
    Route::delete('assets/{asset}', [AssetsController::class, 'destroy'])->name('assets.destroy');
    Route::get('assets/{asset}/file', [AssetsController::class, 'file'])->name('assets.file');

    Route::resource('administrators', AdminUserController::class)
        ->except(['show'])
        ->parameters(['administrators' => 'admin']);

    Route::post('ai/chat', [AiAgentController::class, 'chat'])->name('ai.chat');
    Route::post('ai/agent', [AiAgentController::class, 'agentChat'])->name('ai.agent');
    Route::get('ai/assets', [AiAgentController::class, 'assets'])->name('ai.assets');
    Route::get('ai/images', [AiAgentController::class, 'images'])->name('ai.images');
    Route::get('ai/search-images', [AiAgentController::class, 'searchImages'])->name('ai.search-images');
});
