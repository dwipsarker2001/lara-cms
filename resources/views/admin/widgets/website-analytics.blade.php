<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-2 text-[14px] font-medium text-text-heading">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-text-muted"><path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 5-5"/></svg>
        Website Analytics
    </div>
    <div class="flex items-center gap-1 rounded-lg bg-gray-100 p-1">
        @foreach (['Today', '7 Days', '30 Days', 'This Year'] as $opt)
            <button
                @click="selected = '{{ $opt }}'"
                class="rounded-md px-2.5 py-1 text-[11px] font-medium transition-colors"
                :class="selected === '{{ $opt }}' ? 'bg-white text-text-heading shadow-sm' : 'text-text-muted hover:text-text-heading'"
            >{{ $opt }}</button>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-4 gap-3 mb-5">
    @foreach ($widget->metrics as $metric)
        <div class="rounded-xl bg-gray-50 border border-gray-100 p-3">
            <div class="text-[11px] font-medium text-text-muted">{{ $metric->label }}</div>
            <div class="mt-1 text-xl font-semibold text-text-heading">{{ $metric->value }}</div>
            <div class="mt-0.5 text-[11px] {{ $metric->up ? 'font-medium text-emerald-600' : 'font-medium text-red-500' }}">{{ $metric->delta }}</div>
        </div>
    @endforeach
</div>

@php
$pts = $widget->series;
$min = 0;
$max = 700;
$range = $max - $min;
$w = 700;
$h = 200;
$step = $w / (count($pts) - 1);
$points = implode(' ', array_map(fn($i, $v) => round($step * $i, 1).','.round($h - (($v - $min) / $range) * $h, 1), array_keys($pts), $pts));
@endphp

<div class="relative h-52">
    <svg viewBox="0 0 700 200" class="w-full h-full" preserveAspectRatio="none">
        <defs>
            <linearGradient id="wa-gradient" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#6366f1" stop-opacity="0.2" />
                <stop offset="100%" stop-color="#6366f1" stop-opacity="0" />
            </linearGradient>
        </defs>
        <path d="M0,200 L{{ $points }} L700,200 Z" fill="url(#wa-gradient)" />
        <polyline fill="none" stroke="#6366f1" stroke-width="2.5" points="{{ $points }}" stroke-linecap="round" stroke-linejoin="round" />
        @foreach ($pts as $i => $v)
            <circle cx="{{ round($step * $i, 1) }}" cy="{{ round($h - (($v - $min) / $range) * $h, 1) }}" r="4" fill="#6366f1" stroke="white" stroke-width="2" />
        @endforeach
    </svg>
    <div class="absolute inset-x-0 bottom-0 flex justify-between px-1">
        @foreach ($widget->days as $day)
            <span class="text-[11px] text-text-muted">{{ $day }}</span>
        @endforeach
    </div>
</div>
