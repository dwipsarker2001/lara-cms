@props([
    'showEdit' => true,
    'showRemove' => true,
])

<div {{ $attributes->merge(['class' => 'flex rounded-lg shadow-sm bg-content-bg mb-0.5 group overflow-hidden px-2']) }}>
    <div class="w-6 shrink-0 flex items-center justify-center cursor-grab active:cursor-grabbing opacity-70 hover:opacity-100 touch-none transition-opacity text-text-muted/70">
        <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
            <circle cx="8" cy="6" r="2.5" />
            <circle cx="16" cy="6" r="2.5" />
            <circle cx="8" cy="12" r="2.5" />
            <circle cx="16" cy="12" r="2.5" />
            <circle cx="8" cy="18" r="2.5" />
            <circle cx="16" cy="18" r="2.5" />
        </svg>
    </div>
    <div class="flex flex-1 min-w-0 flex-col px-3 py-2">
        {{ $label }}
    </div>
    @if ($showEdit || $showRemove)
        <div class="flex items-center gap-0.5 shrink-0 ml-auto pr-1">
            @if ($showEdit)
                {{ $edit }}
            @endif
            @if ($showRemove)
                {{ $remove }}
            @endif
        </div>
    @endif
</div>
