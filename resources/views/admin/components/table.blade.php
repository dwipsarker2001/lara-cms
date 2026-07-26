@props([
    'headers' => [],
    'items' => [],
    'emptyText' => 'No entries yet.',
    'emptySubtext' => 'Submissions will appear here once the form is submitted.',
])

<div class="rounded-xl ring-1 ring-content-border bg-content-bg shadow-sm overflow-hidden">
    <div class="overflow-x-auto table-scrollbar"
        x-data="{ isScrolled: false, checkScroll() { this.isScrolled = $el.scrollLeft > 5; } }"
        x-init="checkScroll()"
        @scroll.passive="checkScroll()"
        @resize.window.debounce.100ms="checkScroll()"
    >
    <table class="w-full min-w-full border-separate border-spacing-y-0 text-left text-[13px]">
        <thead>
            @if (isset($thead))
                {{ $thead }}
            @else
                <tr class="bg-[#f9fafb]">
                    @foreach ($headers as $index => $header)
                        @php
                            $key = is_array($header) ? ($header['key'] ?? $header['label']) : $header;
                            $label = is_array($header) ? $header['label'] : $header;
                            $isLast = $loop->last;
                        @endphp
                        <th 
                            @if(is_array($header) && isset($header['key']))
                                x-show="visibleColumns['{{ $header['key'] }}'] !== false"
                            @endif
                            class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] max-w-[200px] truncate {{ $loop->first ? 'rounded-tl-xl' : '' }} {{ $isLast ? 'sticky right-0 bg-[#f9fafb] z-20 text-right rounded-tr-xl transition-shadow' : '' }}"
                            :class="{ 'shadow-[-4px_0_8px_-2px_rgba(0,0,0,0.06)]': {{ $isLast ? 'isScrolled' : 'false' }} }"
                            title="{{ $label }}"
                        >
                            <span class="block max-w-[200px] truncate">{{ $label }}</span>
                        </th>
                    @endforeach
                </tr>
            @endif
        </thead>
        <tbody>
            @if ($items->isEmpty())
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-4 py-16 text-center text-text-muted border-b border-content-border bg-white rounded-b-xl">
                        <div class="flex flex-col items-center justify-center">
                            <img src="/empty-collection.svg" alt="No items" class="size-24 mb-3 opacity-60">
                            <p class="text-sm font-medium text-text-heading">{{ $emptyText }}</p>
                            @if ($emptySubtext)
                                <p class="text-xs text-text-muted mt-1">{{ $emptySubtext }}</p>
                            @endif
                        </div>
                    </td>
                </tr>
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
    </div>
</div>
