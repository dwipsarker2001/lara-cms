<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AssetsController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\CollectionEntryController;
use App\Http\Controllers\Admin\CommandSearchController;
use App\Http\Controllers\Admin\DynamicBlockController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\PreviewController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\TrackPageViews;
use App\Models\WidgetLayout;
use App\Widgets\WidgetRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:admin', TrackPageViews::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function (WidgetRegistry $registry) {
        $allTypes = $registry->all();
        $saved = WidgetLayout::where('admin_id', auth('admin')->id())->first();
        $layout = $saved->layout ?? [];

        if (! isset($layout['grid'])) {
            if (isset($layout['order'])) {
                $layout = [
                    'grid' => ['order' => $layout['order'], 'hidden' => $layout['hidden'] ?? []],
                    'chart' => ['order' => [$layout['chart_type'] ?? 'website_analytics'], 'hidden' => ($layout['chart_hidden'] ?? false) ? [0] : []],
                    'table' => ['order' => ['form_entries_table'], 'hidden' => []],
                    'list' => ['order' => ['updates_list'], 'hidden' => []],
                ];
            } else {
                $layout = [];
            }
        }

        $zones = ['grid', 'chart', 'table', 'list'];
        $grouped = [];
        foreach ($allTypes as $type => $class) {
            $grouped[$class::zone()][] = ['type' => $type, 'label' => $class::make([])->label(), 'image' => $class::make([])->image];
        }

        $zoneData = [];
        foreach ($zones as $zone) {
            $savedZone = $layout[$zone] ?? ['order' => [], 'hidden' => []];
            $zoneTypes = collect($grouped[$zone] ?? [])->pluck('type')->all();
            $order = collect($savedZone['order'] ?? [])->filter(fn ($t) => $t && isset($allTypes[$t]))->values()->all();

            if (empty($order)) {
                $order = $zone === 'table' && in_array('form_entries_table', $zoneTypes, true)
                    ? ['form_entries_table']
                    : $zoneTypes;
            }

            $config = [];
            if ($zone === 'table' && ! empty($savedZone['form_id'])) {
                $config['form_id'] = (int) $savedZone['form_id'];
            }

            $widgets = collect($order)->map(fn ($t) => ($class = $allTypes[$t] ?? null) ? $class::make($config) : null)->filter();
            $hidden = $savedZone['hidden'] ?? [];
            $widgetList = $widgets->map(fn ($w, $i) => ['index' => $i, 'label' => $w->label(), 'image' => $w->image, 'type' => $w::type()])->values();
            $zoneData[$zone] = compact('widgets', 'widgetList', 'hidden');
        }

        $allByZone = collect($grouped)->map(fn ($items) => collect($items)->values());

        return view('admin.dashboard', array_merge(
            ['allByZone' => $allByZone],
            collect($zones)->flatMap(fn ($z) => [
                "{$z}Widgets" => $zoneData[$z]['widgets'],
                "{$z}WidgetList" => $zoneData[$z]['widgetList'],
                "{$z}Hidden" => $zoneData[$z]['hidden'],
            ])->all(),
        ));
    })->name('dashboard');

    Route::post('widgets/layout', function (Request $request) {
        $data = $request->validate([
            'grid' => 'array',
            'grid.order' => 'array',
            'grid.hidden' => 'array',
            'chart' => 'array',
            'chart.order' => 'array',
            'chart.hidden' => 'array',
            'table' => 'array',
            'table.order' => 'array',
            'table.hidden' => 'array',
            'table.form_id' => 'nullable|integer|exists:forms,id',
            'list' => 'array',
            'list.order' => 'array',
            'list.hidden' => 'array',
        ]);

        $layout = WidgetLayout::query()
            ->where('admin_id', auth('admin')->id())
            ->value('layout') ?? [];

        foreach ($data as $zone => $zoneData) {
            $layout[$zone] = array_replace($layout[$zone] ?? [], $zoneData);
        }

        WidgetLayout::updateOrCreate(
            ['admin_id' => auth('admin')->id()],
            ['layout' => $layout]
        );

        return response()->json(['saved' => true]);
    })->name('widgets.layout');

    Route::post('widgets/render', function (Request $request) {
        $data = $request->validate([
            'zone' => 'required|string|in:chart,table,list',
            'type' => 'required|string',
            'form_id' => 'nullable|integer|exists:forms,id',
        ]);

        $registry = app(WidgetRegistry::class);
        $allTypes = $registry->all();
        $class = $allTypes[$data['type']] ?? null;

        if (! $class || $class::zone() !== $data['zone']) {
            abort(404);
        }

        $config = [];
        if (! empty($data['form_id'])) {
            $config['form_id'] = (int) $data['form_id'];
        } elseif ($data['zone'] === 'table') {
            $savedFormId = WidgetLayout::query()
                ->where('admin_id', auth('admin')->id())
                ->value('layout')['table']['form_id'] ?? null;

            if ($savedFormId) {
                $config['form_id'] = (int) $savedFormId;
            }
        }

        $widget = $class::make($config);
        $html = $widget->render();

        return response()->json([
            'html' => is_string($html) ? $html : (string) $html,
            'type' => $widget::type(),
            'label' => $widget->label(),
            'image' => $widget->image,
        ]);
    })->name('widgets.render');

    Route::get('search', CommandSearchController::class)->name('search');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('updates/check', [UpdateController::class, 'check'])->name('updates.check');
    Route::post('updates/run', [UpdateController::class, 'run'])->name('updates.run');

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

    Route::get('email-templates', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::get('email-templates/create', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'create'])->name('email-templates.create');
    Route::post('email-templates', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'store'])->name('email-templates.store');
    Route::get('email-templates/{email_template}/edit', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])->name('email-templates.edit');
    Route::get('email-templates/{email_template}/editor', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'editor'])->name('email-templates.editor');
    Route::post('email-templates/{email_template}/content', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'saveContent'])->name('email-templates.save-content');
    Route::put('email-templates/{email_template}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('email-templates.update');
    Route::delete('email-templates/{email_template}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'destroy'])->name('email-templates.destroy');
});
