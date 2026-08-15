<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Form;
use App\Models\WidgetLayout;
use App\Widgets\WidgetRegistry;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(WidgetRegistry $registry): View
    {
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
            $savedZone = $layout[$zone] ?? null;
            $zoneTypes = collect($grouped[$zone] ?? [])->pluck('type')->all();

            if (! is_array($savedZone)) {
                $order = $zone === 'table' && in_array('form_entries_table', $zoneTypes, true)
                    ? ['form_entries_table']
                    : $zoneTypes;
            } else {
                $order = collect($savedZone['order'] ?? [])->filter(function ($item) use ($allTypes) {
                    $type = is_array($item) ? ($item['type'] ?? null) : $item;

                    return $type && isset($allTypes[$type]);
                })->values()->all();
            }

            $widgets = collect($order)->map(function ($item) use ($allTypes, $zone, $savedZone) {
                $type = is_array($item) ? ($item['type'] ?? null) : $item;
                $config = is_array($item) ? $item : [];
                if ($zone === 'table' && ! empty($savedZone['form_id']) && empty($config['form_id'])) {
                    $config['form_id'] = (int) $savedZone['form_id'];
                }
                $class = $allTypes[$type] ?? null;

                return $class ? $class::make($config) : null;
            })->filter()->values();

            $hidden = $savedZone['hidden'] ?? [];
            $widgetList = $widgets->map(fn ($w, $i) => [
                'index' => $i,
                'label' => $w->label(),
                'image' => $w->image,
                'type' => $w::type(),
                'form_id' => property_exists($w, 'formId') ? $w->formId : null,
            ])->values();
            $zoneData[$zone] = compact('widgets', 'widgetList', 'hidden');
        }

        $allByZone = collect($grouped)->map(fn ($items) => collect($items)->values());

        return view('admin.dashboard', array_merge(
            ['allByZone' => $allByZone],
            ['sidebarForms' => Form::orderBy('position')->get(['id', 'title'])],
            ['allCollections' => Collection::orderBy('name')->get(['id', 'name', 'slug'])],
            collect($zones)->flatMap(fn ($z) => [
                "{$z}Widgets" => $zoneData[$z]['widgets'],
                "{$z}WidgetList" => $zoneData[$z]['widgetList'],
                "{$z}Hidden" => $zoneData[$z]['hidden'],
            ])->all(),
        ));
    }
}
