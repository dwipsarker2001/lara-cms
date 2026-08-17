@extends('admin.layout')

@section('title', $collection->name . ' — Entries')
@section('breadcrumb', 'Entries')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0" x-data="{ showDeleteCollectionModal: false, deletingEntry: null }">
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
            {{-- Settings Dropdown --}}
            <div class="relative" x-data="{ settingsOpen: false }" @click.outside="settingsOpen = false" @keydown.escape.window="settingsOpen = false">
                <button
                    type="button"
                    @click="settingsOpen = !settingsOpen"
                    :aria-expanded="settingsOpen"
                    class="size-10 flex items-center justify-center rounded-lg border border-content-border bg-white text-text-primary hover:bg-gray-50 transition-colors shadow-sm cursor-pointer"
                    title="Settings"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                    </svg>
                </button>

                <div
                    x-show="settingsOpen"
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
                    <a
                        href="{{ route('admin.collections.edit', $collection) }}"
                        role="menuitem"
                        class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm font-medium no-underline transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil size-4 shrink-0 text-text-muted">
                            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                            <path d="m15 5 4 4"/>
                        </svg>
                        <span>Edit Config</span>
                    </a>
                    <hr class="my-1 border-content-border">
                    <button
                        type="button"
                        role="menuitem"
                        @click="settingsOpen = false; showDeleteCollectionModal = true"
                        class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2 size-4 shrink-0 text-red-500">
                            <path d="M3 6h18"/>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                        <span>Delete All</span>
                    </button>
                </div>
            </div>

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
                                    <span class="inline-block w-2 h-2 rounded-full shrink-0 {{ $entry->published ? 'bg-emerald-500' : 'bg-gray-400' }}" title="{{ $entry->published ? 'Published' : 'Draft' }}"></span>
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
                                x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                role="menu"
                                style="z-index: 9999;"
                                class="absolute right-0 top-full mt-1 min-w-[12.5rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5"
                            >
                                <a href="{{ route('admin.collections.entries.editor', [$collection, $entry]) }}" role="menuitem"
                                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm font-medium no-underline transition-colors text-text-primary hover:bg-body-bg"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="3" y1="9" x2="21" y2="9"/>
                                        <line x1="9" y1="21" x2="9" y2="9"/>
                                    </svg>
                                    <span>Visual Editor</span>
                                </a>
                                <a href="{{ route('admin.collections.entries.edit', [$collection, $entry]) }}" role="menuitem"
                                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm font-medium no-underline transition-colors text-text-primary hover:bg-body-bg"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                    <span>Edit Fields</span>
                                </a>
                                @if($entry->slug)
                                <a href="{{ $entry->route() }}" role="menuitem" target="_blank"
                                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm font-medium no-underline transition-colors text-text-primary hover:bg-body-bg"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                        <polyline points="15 3 21 3 21 9" />
                                        <line x1="10" y1="14" x2="21" y2="3" />
                                    </svg>
                                    <span>Visit Page</span>
                                </a>
                                @endif
                                <hr class="my-1 border-content-border">
                                <button type="button" role="menuitem"
                                    @click="deletingEntry = { id: '{{ $entry->id }}', title: @js($entry->title) }; open = false"
                                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2 size-4 shrink-0 text-red-500">
                                        <path d="M3 6h18"/>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                    <span>Delete</span>
                                </button>
                                <form id="delete-entry-form-{{ $entry->id }}" method="POST" action="{{ route('admin.collections.entries.destroy', [$collection, $entry]) }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
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

    {{-- Delete Collection Confirmation Modal --}}
    <form id="delete-collection-form" method="POST" action="{{ route('admin.collections.destroy', $collection) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    <x-admin::delete-modal
        show="showDeleteCollectionModal"
        title="Delete Collection"
        confirm-action="document.getElementById('delete-collection-form')?.submit()"
    >
        Are you sure you want to delete <span class="font-medium text-text-heading">“{{ $collection->name }}”</span>? All associated entries will be permanently deleted.
    </x-admin::delete-modal>

    {{-- Delete Entry Confirmation Modal --}}
    <x-admin::delete-modal
        show="deletingEntry"
        title="Delete Entry"
        title-expression="'Delete ' + (deletingEntry?.title ? '“' + deletingEntry.title + '”' : 'Entry')"
        confirm-action="document.getElementById('delete-entry-form-' + deletingEntry?.id)?.submit()"
    >
        Are you sure you want to delete <span class="font-medium text-text-heading" x-text="deletingEntry?.title ? '“' + deletingEntry.title + '”' : 'this entry'"></span>? This action cannot be undone.
    </x-admin::delete-modal>
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
