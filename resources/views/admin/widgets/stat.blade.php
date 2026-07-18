<div class="flex items-end justify-between gap-2">
    <div>
        <div class="text-[26px] font-semibold leading-none text-text-heading">{{ $widget->value }}</div>
        <div class="mt-2 flex items-center gap-1 text-[12px]">
            <span class="{{ $widget->up ? 'font-medium text-emerald-600' : 'font-medium text-red-500' }}">{{ $widget->delta }}</span>
            <span class="text-text-muted">vs last week</span>
        </div>
    </div>
    <div class="h-10 w-24">
        <svg viewBox="0 0 100 40" class="w-full h-full" preserveAspectRatio="none">
            <defs>
                <linearGradient id="g-{{ $widget->label }}" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="{{ $widget->up ? '#10b981' : '#ef4444' }}" stop-opacity="0.25" />
                    <stop offset="100%" stop-color="{{ $widget->up ? '#10b981' : '#ef4444' }}" stop-opacity="0" />
                </linearGradient>
            </defs>
            @php
                $pts = $widget->data;
                $min = min($pts);
                $max = max($pts);
                $range = $max - $min ?: 1;
                $w = 100;
                $h = 40;
                $step = $w / (count($pts) - 1);
                $points = implode(' ', array_map(fn($i, $v) => round($step * $i, 1).','.round($h - (($v - $min) / $range) * $h, 1), array_keys($pts), $pts));
            @endphp
            <path d="M0,40 L{{ $points }} L100,40 Z" fill="url(#g-{{ $widget->label }})" />
            <polyline fill="none" stroke="{{ $widget->up ? '#10b981' : '#ef4444' }}" stroke-width="1.75" points="{{ $points }}" />
        </svg>
    </div>
</div>
