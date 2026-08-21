<?php

namespace Plugins\DemoWidgets\Widgets;

use App\Widgets\Widget;

class ContentGrowthChartWidget extends Widget
{
    public static function zone(): string
    {
        return 'chart';
    }

    public function label(): string
    {
        return 'Content Growth';
    }

    public function render()
    {
        // Demo: pages published per month for the last 6 months
        $months = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('M Y');
            $counts[] = rand(4, 22);
        }

        $total = array_sum($counts);
        $latest = end($counts);
        $prev = $counts[count($counts) - 2] ?? 0;
        $delta = $prev > 0
            ? ($latest >= $prev ? '+' : '-').round(abs($latest - $prev) / $prev * 100).'%'
            : '+0%';
        $up = $latest >= $prev;

        return view('demo-widgets::widgets.content-growth-chart', compact(
            'months', 'counts', 'total', 'latest', 'delta', 'up'
        ));
    }
}
