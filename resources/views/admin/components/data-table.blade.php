@props([
    'title' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'bg-panel-bg rounded-2xl mb-8 p-2']) }}>
    @if (isset($title) || isset($actions))
        <div class="flex items-center justify-between px-2 pb-2.5">
            @if (isset($title))
                <span class="flex items-center gap-2 text-[14px] font-medium text-text-heading">
                    @if ($icon)
                        <x-dynamic-component :component="'svg.' . $icon" class="size-4 shrink-0 text-text-muted" />
                    @endif
                    {{ $title }}
                </span>
            @endif
            @if (isset($actions))
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-[5px]">
        {{ $slot }}
    </div>

    @if (isset($footer))
        {{ $footer }}
    @endif
</div>
