@extends('admin.layout')

@section('title', $collection->name . ' — Entries')
@section('breadcrumb', 'Entries')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0">
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            @if($collection->icon)
                <i class="{{ $collection->icon }} text-lg w-6 text-center text-text-muted"></i>
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>
            @endif
            {{ $collection->name }}
        </h1>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.collections.edit', $collection) }}"
                class="size-10 flex items-center justify-center rounded-lg border border-content-border bg-white text-text-primary hover:bg-gray-50 transition-colors shadow-sm"
                title="Collection Settings"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
                    <circle cx="12" cy="12" r="3" />
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
            </a>
            <a href="{{ route('admin.collections.entries.create', $collection) }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
            >
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                    <path d="M8 3v10M3 8h10" />
                </svg>
                <span>Create Entry</span>
            </a>
        </div>
    </header>

    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
        <div class="px-[18px] py-3 text-sm font-medium text-text-heading">All {{ $collection->name }}</div>
        <div class="px-1.5 pb-2">
            @if($entries->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-center px-6">
                    <img src="/empty-collection.svg" alt="No items" class="size-32 mb-4 opacity-60">
                    <p class="text-sm font-medium text-text-heading">No entries yet.</p>
                    <p class="text-xs text-text-muted mt-1">
                        <a href="{{ route('admin.collections.entries.create', $collection) }}" class="text-primary hover:text-primary/80 no-underline font-medium">Get started by creating one.</a>
                    </p>
                </div>
            @else
                <div id="sortable-entries" class="rounded-xl ring-1 ring-content-border shadow-sm divide-y divide-content-border">
                @foreach($entries as $entry)
                    <div
                        class="flex rounded-xl shadow-sm bg-content-bg mb-px group px-3"
                        data-entry-id="{{ $entry->id }}"
                    >
                        <div class="w-6 shrink-0 flex items-center justify-center text-text-muted/70 cursor-grab" data-drag-handle>
                            <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                                <circle cx="8" cy="6" r="2.5" />
                                <circle cx="16" cy="6" r="2.5" />
                                <circle cx="8" cy="12" r="2.5" />
                                <circle cx="16" cy="12" r="2.5" />
                                <circle cx="8" cy="18" r="2.5" />
                                <circle cx="16" cy="18" r="2.5" />
                            </svg>
                        </div>
                        <div class="flex flex-1 items-center px-1.5 text-xs leading-normal min-w-0">
                            <div class="flex gap-2 sm:gap-3 grow items-center py-3 min-w-0">
                                <a href="{{ route('admin.collections.entries.editor', [$collection, $entry]) }}" class="flex items-center gap-2 no-underline min-w-0">
                                    <span class="inline-block w-2 h-2 rounded-full shrink-0 bg-text-muted"></span>
                                    <span class="text-sm font-semibold text-text-heading truncate group-hover:text-primary transition-colors">{{ $entry->title }}</span>
                                </a>
                            </div>
                            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                                @if($entry->slug)
                                    <span class="text-xs text-text-muted select-all bg-panel-bg px-2 py-0.5 rounded border border-content-border">
                                        {{ $entry->route() }}
                                    </span>
                                @endif
                                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                            <button
                                type="button"
                                aria-haspopup="menu"
                                :aria-expanded="open"
                                aria-label="Open menu"
                                @click="open = !open"
                                class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-transparent text-text-primary/60 hover:bg-text-primary/10 hover:text-text-primary transition-colors"
                            >
                                <svg viewBox="0 0 16 3" class="size-4" fill="currentColor" aria-hidden="true">
                                    <circle cx="2" cy="1.5" r="1.5" />
                                    <circle cx="8" cy="1.5" r="1.5" />
                                    <circle cx="14" cy="1.5" r="1.5" />
                                </svg>
                            </button>
                            <div
                                x-show="open"
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
                                @if($entry->slug)
                                <a href="{{ $entry->route() }}" role="menuitem" target="_blank"
                                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                        <polyline points="15 3 21 3 21 9" />
                                        <line x1="10" y1="14" x2="21" y2="3" />
                                    </svg>
                                    <span>Visit</span>
                                </a>
                                @endif
                                <a href="{{ route('admin.collections.entries.edit', [$collection, $entry]) }}" role="menuitem"
                                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                        <line x1="16" y1="13" x2="8" y2="13" />
                                        <line x1="16" y1="17" x2="8" y2="17" />
                                    </svg>
                                    <span>Edit</span>
                                </a>
                                <a href="{{ route('admin.collections.entries.editor', [$collection, $entry]) }}" role="menuitem"
                                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                        <circle cx="12" cy="12" r="3" />
                                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                                    </svg>
                                    <span>Page Builder</span>
                                </a>
                                <hr class="my-1 border-content-border">
                                <form method="POST" action="{{ route('admin.collections.entries.destroy', [$collection, $entry]) }}" class="w-full mb-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" role="menuitem"
                                        onclick="return confirm('Delete this entry?')"
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
@endsection

@push('scripts')
<script>
    window.addEventListener('load', function () {
        const el = document.getElementById('sortable-entries');
        if (!el || typeof Sortable === 'undefined') return;

        new Sortable(el, {
            handle: '[data-drag-handle]',
            animation: 200,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd() {
                const entryIds = [...el.querySelectorAll('[data-entry-id]')].map(row => row.dataset.entryId);
                fetch('{{ route("admin.collections.entries.reorder", $collection) }}', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ entry_ids: entryIds }),
                });
            },
        });
    });
</script>
@endpush
