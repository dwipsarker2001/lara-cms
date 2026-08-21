<div class="grid grid-cols-2 gap-2">
    @foreach ($stats as $stat)
        @php
            $colors = [
                'indigo'  => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-600'],
                'violet'  => ['bg' => 'bg-violet-50',  'text' => 'text-violet-600'],
                'sky'     => ['bg' => 'bg-sky-50',     'text' => 'text-sky-600'],
                'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
            ];
            $c = $colors[$stat['color']] ?? $colors['indigo'];
        @endphp
        <div class="flex items-center gap-2.5 rounded-xl bg-gray-50 border border-gray-100 px-3 py-2.5">
            <div class="flex size-7 shrink-0 items-center justify-center rounded-lg {{ $c['bg'] }} {{ $c['text'] }}">
                @if ($stat['icon'] === 'page')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                @elseif ($stat['icon'] === 'admin')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                @elseif ($stat['icon'] === 'media')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                @elseif ($stat['icon'] === 'plugin')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5"><path d="M20.24 12.24a6 6 0 00-8.49-8.49L5 10.5V19h8.5z"/><line x1="16" y1="8" x2="2" y2="22"/><line x1="17.5" y1="15" x2="9" y2="15"/></svg>
                @endif
            </div>
            <div class="min-w-0">
                <div class="text-[18px] font-bold leading-none text-text-heading">{{ $stat['value'] }}</div>
                <div class="mt-0.5 truncate text-[11px] text-text-muted">{{ $stat['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>
