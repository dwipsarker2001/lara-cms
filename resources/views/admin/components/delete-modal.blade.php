@props([
    'show' => 'open',
    'title' => 'Delete item',
    'titleExpression' => null,
    'description' => 'Are you sure you want to delete this item?',
    'descriptionExpression' => null,
    'confirmText' => 'Delete',
    'cancelText' => 'Cancel',
    'confirmAction' => 'confirmDelete()',
    'maxWidth' => 'max-w-[400px]',
])

<div
    x-show="{{ $show }}"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    @keydown.escape.window="{{ $show }} = null"
>
    <div
        x-show="{{ $show }}"
        x-transition:enter="transition-opacity ease-linear duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px]"
        @click="{{ $show }} = null"
    ></div>

    <div
        x-show="{{ $show }}"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full {{ $maxWidth }} bg-content-bg rounded-2xl border border-content-border shadow-2xl p-6 z-10"
    >
        {{-- Close Button --}}
        <button
            type="button"
            @click="{{ $show }} = null"
            class="absolute top-5 right-5 size-7 flex items-center justify-center rounded-lg text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors cursor-pointer"
            title="Close"
        >
            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
            </svg>
        </button>

        {{-- Top Red Icon Badge --}}
        <div class="mb-4">
            @if(isset($icon))
                {{ $icon }}
            @else
                <div class="size-12 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2">
                        <path d="M3 6h18"/>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                        <line x1="10" y1="11" x2="10" y2="17"/>
                        <line x1="14" y1="11" x2="14" y2="17"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Title & Description --}}
        <div class="mb-6">
            @if($titleExpression)
                <h3 class="text-lg font-bold text-text-heading leading-tight" x-text="{{ $titleExpression }}"></h3>
            @else
                <h3 class="text-lg font-bold text-text-heading leading-tight">{{ $title }}</h3>
            @endif

            @if(isset($slot) && $slot->isNotEmpty())
                <div class="text-sm text-text-muted mt-1.5">
                    {{ $slot }}
                </div>
            @elseif($descriptionExpression)
                <p class="text-sm text-text-muted mt-1.5" x-text="{{ $descriptionExpression }}"></p>
            @else
                <p class="text-sm text-text-muted mt-1.5">{{ $description }}</p>
            @endif
        </div>

        {{-- Footer Action Buttons --}}
        <div class="flex items-center gap-3">
            <button
                type="button"
                @click="{{ $show }} = null"
                class="flex-1 py-2.5 rounded-xl border border-content-border bg-content-bg text-sm font-medium text-text-heading hover:bg-body-bg transition-colors cursor-pointer text-center"
            >{{ $cancelText }}</button>
            <button
                type="button"
                @click="{{ $confirmAction }}"
                class="flex-1 py-2.5 rounded-xl bg-red-600 text-sm font-medium text-white hover:bg-red-700 active:scale-[0.98] shadow-sm transition-all cursor-pointer text-center"
            >{{ $confirmText }}</button>
        </div>
    </div>
</div>
