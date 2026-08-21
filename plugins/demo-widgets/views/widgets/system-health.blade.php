<div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm py-4 flex flex-col min-h-0 flex-1">
    {{-- Header --}}
    <div class="flex items-center justify-between px-4 pb-3 border-b border-gray-100">
        <div class="flex items-center gap-2">
            @if ($allOk)
                <span class="flex size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[13px] font-semibold text-emerald-700">All systems operational</span>
            @elseif ($hasErrors)
                <span class="flex size-2 rounded-full bg-red-500 animate-pulse"></span>
                <span class="text-[13px] font-semibold text-red-700">Issues detected</span>
            @else
                <span class="flex size-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="text-[13px] font-semibold text-amber-700">Warnings present</span>
            @endif
        </div>
        <span class="text-[11px] text-text-muted">{{ $checks->count() }} checks</span>
    </div>

    {{-- Checks list --}}
    <ul class="mt-1 flex-1 divide-y divide-gray-100">
        @foreach ($checks as $check)
            @php
                $statusConfig = match ($check['status']) {
                    'ok'    => ['bg' => 'bg-emerald-50', 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'label' => 'OK'],
                    'warn'  => ['bg' => 'bg-amber-50',   'dot' => 'bg-amber-400',   'text' => 'text-amber-700',   'label' => 'Warn'],
                    default => ['bg' => 'bg-red-50',     'dot' => 'bg-red-500',     'text' => 'text-red-700',     'label' => 'Error'],
                };
            @endphp
            <li class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50/80 transition-colors">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="size-1.5 rounded-full shrink-0 {{ $statusConfig['dot'] }}"></span>
                    <span class="text-[13px] font-medium text-text-heading truncate">{{ $check['label'] }}</span>
                    @if (!empty($check['detail']))
                        <span class="text-[11px] text-text-muted font-mono bg-gray-100 px-1.5 py-0.5 rounded">{{ $check['detail'] }}</span>
                    @endif
                </div>
                <span class="ml-3 shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                    {{ $statusConfig['label'] }}
                </span>
            </li>
        @endforeach
    </ul>
</div>
