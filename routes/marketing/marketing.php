<?php

use App\Http\Controllers\Marketing\AccountController;
use App\Http\Controllers\Marketing\CampaignController;
use App\Http\Controllers\Marketing\ContactController;
use App\Http\Controllers\Marketing\DashboardController;
use App\Http\Controllers\Marketing\FormController;
use App\Http\Controllers\Marketing\ReportController;
use App\Http\Controllers\Marketing\SearchController;
use App\Http\Controllers\Marketing\SettingController;
use App\Http\Controllers\Marketing\SupportController;
use App\Http\Controllers\Marketing\TemplateController;
use App\Http\Controllers\Marketing\VerifyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:web'])->prefix('app')->name('app.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Campaigns
    Route::get('campaigns', [CampaignController::class, 'index'])->name('campaign.index');
    Route::get('campaigns/create', [CampaignController::class, 'create'])->name('campaign.create');
    Route::post('campaigns/store', [CampaignController::class, 'store'])->name('campaign.store');
    Route::get('campaigns/{id}/edit', [CampaignController::class, 'edit'])->name('campaign.edit');
    Route::post('campaigns/update', [CampaignController::class, 'update'])->name('campaign.update');
    Route::post('campaigns/delete', [CampaignController::class, 'delete'])->name('campaign.delete');
    Route::post('campaigns/duplicate', [CampaignController::class, 'duplicate'])->name('campaign.duplicate');
    Route::post('campaigns/usetemplate', [CampaignController::class, 'usetemplate'])->name('campaign.usetemplate');
    Route::post('campaigns/sendtest', [CampaignController::class, 'sendtest'])->name('campaign.sendtest');
    Route::post('campaigns/send', [CampaignController::class, 'sendcampaign'])->name('campaign.send');
    Route::post('campaigns/schedule', [CampaignController::class, 'schedule'])->name('campaign.schedule');

    // Groups
    Route::get('contacts/groups', [ContactController::class, 'groupindex'])->name('group.index');
    Route::get('contacts/groups/create', [ContactController::class, 'groupcreate'])->name('group.create');
    Route::post('contacts/groups/store', [ContactController::class, 'groupstore'])->name('group.store');
    Route::get('contacts/groups/{id}/edit', [ContactController::class, 'groupedit'])->name('group.edit');
    Route::post('contacts/groups/update', [ContactController::class, 'groupupdate'])->name('group.update');
    Route::post('contacts/groups/delete', [ContactController::class, 'groupdelete'])->name('group.delete');

    // Contacts
    Route::get('contacts/{groupId}', [ContactController::class, 'index'])->name('contact.index');
    Route::get('contacts/{groupId}/create', [ContactController::class, 'create'])->name('contact.create');
    Route::post('contacts/store', [ContactController::class, 'store'])->name('contact.store');
    Route::get('contacts/{groupId}/{contact}', [ContactController::class, 'show'])->name('contact.show');
    Route::get('contacts/{id}/edit', [ContactController::class, 'edit'])->name('contact.edit');
    Route::post('contacts/update', [ContactController::class, 'update'])->name('contact.update');
    Route::post('contacts/delete', [ContactController::class, 'delete'])->name('contact.delete');
    Route::post('contacts/deleteSelected', [ContactController::class, 'deleteSelected'])->name('contact.deleteSelected');
    Route::get('contacts/{groupId}/import', [ContactController::class, 'import'])->name('contact.import');
    Route::post('contacts/{groupId}/fileimport', [ContactController::class, 'fileimport'])->name('contact.fileimport');
    Route::post('contacts/{groupId}/upload', [ContactController::class, 'upload'])->name('contact.upload');

    // Templates
    Route::get('templates', [TemplateController::class, 'index'])->name('template.index');
    Route::get('templates/create-page', [TemplateController::class, 'createPage'])->name('template.create-page');
    Route::get('templates/create', [TemplateController::class, 'create'])->name('template.create');
    Route::post('templates/select', [TemplateController::class, 'select'])->name('template.select');
    Route::post('templates/remove', [TemplateController::class, 'remove'])->name('template.remove');
    Route::get('templates/design', [TemplateController::class, 'design'])->name('template.design');
    Route::get('templates/save-name', [TemplateController::class, 'save_name'])->name('template.save-name');
    Route::post('templates/store', [TemplateController::class, 'storeTemplateDB'])->name('template.store');
    Route::post('templates/save', [TemplateController::class, 'save'])->name('template.save-content');
    Route::post('templates/upload-asset', [TemplateController::class, 'uploadAsset'])->name('template.upload-asset');
    Route::post('templates/save-thumbnail', [TemplateController::class, 'savethumbnail'])->name('template.save-thumbnail');
    Route::post('templates/rename', [TemplateController::class, 'rename'])->name('template.rename');
    Route::post('templates/duplicate', [TemplateController::class, 'duplicate'])->name('template.duplicate');
    Route::post('templates/test-email', [TemplateController::class, 'testEmailSending'])->name('template.test-email');
    Route::get('templates/content/{id}', [TemplateController::class, 'serveTemplate'])->name('template.content');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('report.index');
    Route::post('reports/filter', [ReportController::class, 'filterReportByDate'])->name('report.filter');
    Route::get('reports/delete/{id}', [ReportController::class, 'delete'])->name('report.delete');
    Route::post('reports/weekly-active', [ReportController::class, 'activeAutomatedWeeklyReport'])->name('report.weekly.active');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('setting.index');
    Route::get('settings/default', [SettingController::class, 'default'])->name('setting.default');
    Route::post('settings/default/save', [SettingController::class, 'default_save'])->name('default.save');
    Route::get('settings/sender', [SettingController::class, 'sender'])->name('setting.sender');
    Route::post('settings/sender/save', [SettingController::class, 'sender_save'])->name('setting.sender.save');
    Route::post('settings/sender/edit', [SettingController::class, 'sender_edit'])->name('setting.sender.edit');
    Route::post('settings/sender/delete', [SettingController::class, 'sender_delete'])->name('setting.sender.delete');

    // Forms
    Route::get('forms', [FormController::class, 'index'])->name('form.index');
    Route::get('forms/create', [FormController::class, 'create'])->name('form.create');
    Route::post('forms/save', [FormController::class, 'save'])->name('form.save');
    Route::get('forms/{id}/edit', [FormController::class, 'edit'])->name('form.edit');
    Route::post('forms/update', [FormController::class, 'update'])->name('form.update');
    Route::post('forms/delete', [FormController::class, 'delete'])->name('form.delete');

    // Verify
    Route::get('verify-email', [VerifyController::class, 'index'])->name('verify.index');
    Route::post('verify-email', [VerifyController::class, 'upload'])->name('verify.upload');

    // Account
    Route::get('account', [AccountController::class, 'index'])->name('account.index');
    Route::post('account/store', [AccountController::class, 'save'])->name('account.store');

    // Search
    Route::get('search', [SearchController::class, 'search'])->name('app.search');

    // Support
    Route::get('support', [SupportController::class, 'index'])->name('support.index');
});
