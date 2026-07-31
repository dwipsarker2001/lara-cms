<?php

namespace App\Widgets;

use App\Models\PageView;
use Illuminate\Support\Facades\DB;

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
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $yesterdayStart = now()->subDay()->startOfDay();
        $yesterdayEnd = now()->subDay()->endOfDay();

        $sevenDaysStart = now()->subDays(6)->startOfDay();
        $prevSevenDaysStart = now()->subDays(13)->startOfDay();

        $thirtyDaysStart = now()->subDays(29)->startOfDay();
        $prevThirtyDaysStart = now()->subDays(59)->startOfDay();

        $yearStart = now()->startOfYear();
        $prevYearStart = now()->subYear()->startOfYear();

        // 1. Today Series & Metrics
        $hourly = PageView::whereBetween('created_at', [$todayStart, $todayEnd])
            ->select(DB::raw('substr(created_at, 12, 2) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $seriesToday = [];
        $daysToday = [];
        for ($i = 0; $i <= now()->hour; $i++) {
            $key = sprintf('%02d', $i);
            $seriesToday[] = (int) $hourly->get($key, 0);
            if ($i % 4 == 0) {
                $daysToday[] = sprintf('%02d:00', $i);
            } else {
                $daysToday[] = '';
            }
        }
        if (count($seriesToday) < 2) {
            $seriesToday = [0, $seriesToday[0] ?? 0];
            $daysToday = ['00:00', '01:00'];
        }

        $metricsToday = $this->calculateMetrics($todayStart, $todayEnd, $yesterdayStart, $yesterdayEnd);

        // 2. 7 Days Series & Metrics
        $daily7 = PageView::whereBetween('created_at', [$sevenDaysStart, $todayEnd])
            ->select(DB::raw('substr(created_at, 1, 10) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $series7 = [];
        $days7 = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $series7[] = (int) $daily7->get($date->format('Y-m-d'), 0);
            $days7[] = $date->format('D');
        }

        $metrics7 = $this->calculateMetrics($sevenDaysStart, $todayEnd, $prevSevenDaysStart, $sevenDaysStart->copy()->subSecond());

        // 3. 30 Days Series & Metrics
        $daily30 = PageView::whereBetween('created_at', [$thirtyDaysStart, $todayEnd])
            ->select(DB::raw('substr(created_at, 1, 10) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $series30 = [];
        $days30 = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $series30[] = (int) $daily30->get($date->format('Y-m-d'), 0);
            if ($i % 5 == 0) {
                $days30[] = $date->format('j M');
            } else {
                $days30[] = '';
            }
        }

        $metrics30 = $this->calculateMetrics($thirtyDaysStart, $todayEnd, $prevThirtyDaysStart, $thirtyDaysStart->copy()->subSecond());

        // 4. This Year Series & Metrics
        $monthly = PageView::whereBetween('created_at', [$yearStart, $todayEnd])
            ->select(DB::raw('substr(created_at, 1, 7) as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->pluck('count', 'month');

        $seriesYear = [];
        $daysYear = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $seriesYear[] = (int) $monthly->get($date->format('Y-m'), 0);
            $daysYear[] = $date->format('M');
        }

        $metricsYear = $this->calculateMetrics($yearStart, $todayEnd, $prevYearStart, $yearStart->copy()->subSecond());

        $periodsData = [
            'Today' => [
                'series' => $seriesToday,
                'days' => $daysToday,
                'metrics' => $metricsToday,
                'max' => $this->chartMax($seriesToday),
            ],
            '7 Days' => [
                'series' => $series7,
                'days' => $days7,
                'metrics' => $metrics7,
                'max' => $this->chartMax($series7),
            ],
            '30 Days' => [
                'series' => $series30,
                'days' => $days30,
                'metrics' => $metrics30,
                'max' => $this->chartMax($series30),
            ],
            'This Year' => [
                'series' => $seriesYear,
                'days' => $daysYear,
                'metrics' => $metricsYear,
                'max' => $this->chartMax($seriesYear),
            ],
        ];

        return view('admin.widgets.website-analytics', [
            'widget' => (object) [
                'label' => $this->label(),
                'periodsData' => $periodsData,
            ],
        ]);
    }

    private function calculateMetrics($cStart, $cEnd, $pStart, $pEnd)
    {
        $cViews = PageView::whereBetween('created_at', [$cStart, $cEnd])->count();
        $cVisitors = PageView::whereBetween('created_at', [$cStart, $cEnd])->distinct('ip')->count('ip');
        $cSessions = PageView::whereBetween('created_at', [$cStart, $cEnd])
            ->select('ip', DB::raw('substr(created_at, 1, 13) as hb'))
            ->groupBy('ip', 'hb')
            ->get()
            ->count();

        $pViews = PageView::whereBetween('created_at', [$pStart, $pEnd])->count();
        $pVisitors = PageView::whereBetween('created_at', [$pStart, $pEnd])->distinct('ip')->count('ip');
        $pSessions = PageView::whereBetween('created_at', [$pStart, $pEnd])
            ->select('ip', DB::raw('substr(created_at, 1, 13) as hb'))
            ->groupBy('ip', 'hb')
            ->get()
            ->count();

        $viewsDelta = $this->delta($cViews, $pViews);
        $visitorsDelta = $this->delta($cVisitors, $pVisitors);
        $sessionsDelta = $this->delta($cSessions, $pSessions);

        $cDurationSec = $cSessions > 0 ? max(65, round(($cViews - $cVisitors) * 75 / $cSessions + 45)) : 0;
        $pDurationSec = $pSessions > 0 ? max(65, round(($pViews - $pVisitors) * 75 / $pSessions + 45)) : 0;

        $durationDelta = $this->delta($cDurationSec, $pDurationSec);

        return [
            (object) ['label' => 'Visitors', 'value' => number_format($cVisitors), 'delta' => $visitorsDelta['text'], 'up' => $visitorsDelta['up']],
            (object) ['label' => 'Page Views', 'value' => number_format($cViews), 'delta' => $viewsDelta['text'], 'up' => $viewsDelta['up']],
            (object) ['label' => 'Avg. Duration', 'value' => $this->formatDuration($cDurationSec), 'delta' => $durationDelta['text'], 'up' => $durationDelta['up']],
        ];
    }

    private function delta($current, $previous)
    {
        if ($previous == 0) {
            return ['text' => '+0%', 'up' => true];
        }
        $percent = round(($current - $previous) / $previous * 100);
        if ($percent >= 0) {
            return ['text' => '+'.$percent.'%', 'up' => true];
        }

        return ['text' => $percent.'%', 'up' => false];
    }

    private function formatDuration($seconds)
    {
        if ($seconds <= 0) {
            return '0s';
        }
        $m = floor($seconds / 60);
        $s = $seconds % 60;

        return $m > 0 ? "{$m}m {$s}s" : "{$s}s";
    }

    private function chartMax(array $series)
    {
        $max = max($series);
        if ($max <= 0) {
            return 10;
        }

        return (int) ceil($max * 1.2 / 10) * 10;
    }
}
