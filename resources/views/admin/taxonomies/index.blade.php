@extends('admin.layout')

@section('title', 'Taxonomies')
@section('breadcrumb', 'Taxonomies')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0">
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-6 shrink-0 text-text-muted">
                <path d="m15 5 6.3 6.3a2.4 2.4 0 0 1 0 3.4L17 19" />
                <path d="M9.586 2.586A2 2 0 0 0 8.172 2H3a1 1 0 0 0-1 1v5.172a2 2 0 0 0 .586 1.414l8.828 8.828a2 2 0 0 0 2.828 0l5.172-5.172a2 2 0 0 0 0-2.828z" />
                <path d="M6.5 6.5h.01" />
            </svg>
            Taxonomies
        </h1>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.taxonomies.create') }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
            >
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                    <path d="M8 3v10M3 8h10" />
                </svg>
                <span>Create Taxonomy</span>
            </a>
        </div>
    </header>

    @if (session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
        <div class="px-[18px] py-3 text-sm font-medium text-text-heading">All Taxonomies</div>
        <div class="px-1.5 pb-2">
            @if($taxonomies->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-center px-6">
                    <img src="/empty-collection.svg" alt="No taxonomies" class="size-32 mb-4 opacity-60">
                    <p class="text-sm font-medium text-text-heading">No taxonomies yet.</p>
                    <p class="text-xs text-text-muted mt-1">
                        <a href="{{ route('admin.taxonomies.create') }}" class="text-primary hover:text-primary/80 no-underline font-medium">Get started by creating one.</a>
                    </p>
                </div>
            @else
                <div id="sortable-taxonomies" class="space-y-px">
                @foreach($taxonomies as $taxonomy)
                    <div class="flex rounded-xl shadow-sm bg-content-bg mb-px group px-3" data-taxonomy-id="{{ $taxonomy->id }}">
                        <div class="flex flex-1 items-center px-1.5 text-xs leading-normal min-w-0">
                            <div class="shrink-0 text-text-muted/70 cursor-grab active:cursor-grabbing hover:text-text-primary transition-colors py-3 pr-1.5" data-drag-handle>
                                <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                                    <circle cx="8" cy="6" r="2.5" />
                                    <circle cx="16" cy="6" r="2.5" />
                                    <circle cx="8" cy="12" r="2.5" />
                                    <circle cx="16" cy="12" r="2.5" />
                                    <circle cx="8" cy="18" r="2.5" />
                                    <circle cx="16" cy="18" r="2.5" />
                                </svg>
                            </div>
                            <div class="flex gap-2 sm:gap-3 grow items-center py-3 min-w-0">
                                <a href="{{ route('admin.taxonomies.show', $taxonomy) }}" class="flex items-center gap-2 no-underline min-w-0">
                                    <span class="text-sm font-semibold text-text-heading truncate group-hover:text-primary transition-colors">{{ $taxonomy->title }}</span>
                                </a>
                                <span class="text-[10px] text-text-muted select-all bg-panel-bg px-1.5 py-0.5 rounded-md border border-content-border shrink-0 font-medium leading-none">
                                    {{ $taxonomy->terms_count }} {{ Str::plural('item', $taxonomy->terms_count) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                                <span class="text-[11px] text-text-muted select-all bg-panel-bg px-1.5 py-0.5 rounded-md border border-content-border font-mono leading-none">
                                    {{ $taxonomy->slug }}
                                </span>
                                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                                    <button
                                        type="button"
                                        aria-haspopup="menu"
                                        :aria-expanded="open"
                                        aria-label="Settings"
                                        @click="open = !open"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-transparent text-text-primary/60 hover:bg-text-primary/10 hover:text-text-primary transition-colors cursor-pointer"
                                        title="Settings"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings size-4">
                                            <circle cx="12" cy="12" r="3" />
                                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                        </svg>
                                    </button>
                                    <div
                                        x-show="open"
                                        x-cloak
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        role="menu"
                                        style="z-index: 9999;"
                                        class="absolute right-0 top-full mt-1 min-w-[12rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5"
                                    >
                                        <a href="{{ route('admin.taxonomies.show', $taxonomy) }}" role="menuitem"
                                            class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                                <line x1="4" y1="9" x2="20" y2="9" />
                                                <line x1="4" y1="15" x2="20" y2="15" />
                                                <line x1="10" y1="3" x2="8" y2="21" />
                                                <line x1="16" y1="3" x2="14" y2="21" />
                                            </svg>
                                            <span>Manage Items</span>
                                        </a>
                                        <a href="{{ route('admin.taxonomies.edit', $taxonomy) }}" role="menuitem"
                                            class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                                <circle cx="12" cy="12" r="3" />
                                                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                                            </svg>
                                            <span>Settings</span>
                                        </a>
                                        <hr class="my-1 border-content-border">
                                        <form method="POST" action="{{ route('admin.taxonomies.destroy', $taxonomy) }}" class="w-full mb-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" role="menuitem"
                                                onclick="return confirm('Delete this taxonomy group?')"
                                                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-red-500">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                                                </svg>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    #sortable-taxonomies .sortable-ghost {
        opacity: 0 !important;
    }
    #sortable-taxonomies .sortable-drag {
        opacity: 0.9 !important;
        box-shadow: none !important;
        border-radius: 0.75rem !important;
        background: var(--color-content-bg, #fff) !important;
    }
</style>

@push('scripts')
    <script>
        window.addEventListener('load', function () {
            const el = document.getElementById('sortable-taxonomies');
            if (!el || typeof Sortable === 'undefined') return;

            new Sortable(el, {
                handle: '[data-drag-handle]',
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd() {
                    const taxonomyIds = [...el.querySelectorAll('[data-taxonomy-id]')].map(row => row.dataset.taxonomyId);
                    fetch('{{ route("admin.taxonomies.reorder") }}', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ taxonomy_ids: taxonomyIds }),
                    });
                },
            });
        });
    </script>
@endpush
@endsection