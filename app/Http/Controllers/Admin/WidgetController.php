<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WidgetLayout;
use App\Widgets\WidgetRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function layout(Request $request): JsonResponse
    {
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
    }

    public function render(Request $request, WidgetRegistry $registry): JsonResponse
    {
        $data = $request->validate([
            'zone' => 'required|string|in:chart,table,list,grid',
            'type' => 'required|string',
            'form_id' => 'nullable|integer|exists:forms,id',
        ]);

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
    }
}
