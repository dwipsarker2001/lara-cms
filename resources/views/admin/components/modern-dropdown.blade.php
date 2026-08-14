@props(['value' => ''])

<div x-data="{ open: false, selected: '{{ $value }}' }" @click.outside="open = false" class="relative">
    <button type="button" @click="open = !open"
        class="flex items-center gap-2 rounded-lg border border-content-border bg-white px-3 py-2 text-[13px] font-medium text-text-heading shadow-sm hover:bg-gray-50 cursor-pointer">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-text-muted shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" /></svg>
        <span x-text="selected"></span>
        <svg :class="open ? 'rotate-180' : ''" class="size-3.5 text-text-muted transition-transform duration-150 shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </svg>
    </button>
    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 top-full mt-1 left-0 min-w-[160px] bg-content-bg border border-content-border rounded-lg shadow-lg p-1 space-y-0.5">
        {{ $slot }}
    </div>
</div>
