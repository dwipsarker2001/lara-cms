<?php

namespace Plugins\DemoWidgets\Widgets;

use App\Widgets\Widget;

class QuickStatsWidget extends Widget
{
    public static function zone(): string
    {
        return 'grid';
    }

    public function label(): string
    {
        return 'Quick Stats';
    }

    public function render()
    {
        $stats = [
            ['label' => 'Total Pages',    'value' => 142,  'icon' => 'page',   'color' => 'indigo'],
            ['label' => 'Active Admins',  'value' => 5,    'icon' => 'admin',  'color' => 'violet'],
            ['label' => 'Media Files',    'value' => 834,  'icon' => 'media',  'color' => 'sky'],
            ['label' => 'Plugins Active', 'value' => 3,    'icon' => 'plugin', 'color' => 'emerald'],
        ];

        return view('demo-widgets::widgets.quick-stats', compact('stats'));
    }
}
