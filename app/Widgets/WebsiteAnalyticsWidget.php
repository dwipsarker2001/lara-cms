<?php

namespace App\Widgets;

class WebsiteAnalyticsWidget extends Widget
{
    public static function type(): string
    {
        return 'website_analytics';
    }

    public static function zone(): string
    {
        return 'chart';
    }

    public function label(): string
    {
        return 'Website Analytics';
    }

    public function render()
    {
        return view('admin.widgets.website-analytics', ['widget' => (object) [
            'days' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            'series' => [280, 410, 340, 530, 480, 620, 510],
            'metrics' => [
                (object) ['label' => 'Visitors', 'value' => '4,790', 'delta' => '+8%', 'up' => true],
                (object) ['label' => 'Page Views', 'value' => '18,240', 'delta' => '+12%', 'up' => true],
                (object) ['label' => 'Sessions', 'value' => '6,130', 'delta' => '-3%', 'up' => false],
                (object) ['label' => 'Avg. Duration', 'value' => '4m 12s', 'delta' => '+5%', 'up' => true],
            ],
        ]]);
    }
}
