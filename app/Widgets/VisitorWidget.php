<?php

namespace App\Widgets;

use App\Models\PageView;
use Illuminate\Support\Facades\DB;

class VisitorWidget extends Widget
{
    public static function type(): string
    {
        return 'visitor';
    }

    public static function zone(): string
    {
        return 'grid';
    }

    public function label(): string
    {
        return 'Daily Visitors';
    }

    public function render()
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        $daily = PageView::where('created_at', '>=', now()->subDays(6))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $data = collect(range(6, 0))->map(fn ($d) => $daily->get(now()->subDays($d)->format('Y-m-d'), 0));

        $todayCount = $data->last();
        $yesterdayCount = $daily->get($yesterday->format('Y-m-d'), 0);

        return view('admin.widgets.visitor', [
            'widget' => (object) [
                'label' => $this->label(),
                'value' => number_format($todayCount),
                'delta' => $yesterdayCount > 0 ? (
                    $todayCount >= $yesterdayCount
                        ? '+'.round(($todayCount - $yesterdayCount) / $yesterdayCount * 100).'%'
                        : '-'.round(($yesterdayCount - $todayCount) / $yesterdayCount * 100).'%'
                ) : '+0%',
                'up' => $todayCount >= $yesterdayCount,
                'data' => $data->toArray(),
            ],
        ]);
    }
}
