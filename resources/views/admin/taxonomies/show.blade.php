@extends('admin.layout')

@section('title', $taxonomy->title)
@section('breadcrumb', $taxonomy->title)

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0" x-data="{ showDeleteTaxonomyModal: false, deletingTerm: null }">
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            @if($taxonomy->icon)
                <i class="{{ $taxonomy->icon }} text-2xl text-text-muted shrink-0"></i>
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-6 shrink-0 text-text-muted">
                    <path d="m15 5 6.3 6.3a2.4 2.4 0 0 1 0 3.4L17 19" />
                    <path d="M9.586 2.586A2 2 0 0 0 8.172 2H3a1 1 0 0 0-1 1v5.172a2 2 0 0 0 .586 1.414l8.828 8.828a2 2 0 0 0 2.828 0l5.172-5.172a2 2 0 0 0 0-2.828z" />
                    <path d="M6.5 6.5h.01" />
                </svg>
            @endif
            {{ $taxonomy->title }}
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
                        href="{{ route('admin.taxonomies.edit', $taxonomy) }}"
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
                        @click="settingsOpen = false; showDeleteTaxonomyModal = true"
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

            <a href="{{ route('admin.taxonomies.terms.create', $taxonomy) }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
            >
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                    <path d="M8 3v10M3 8h10" />
                </svg>
                <span>Create {{ $taxonomy->title }}</span>
            </a>
        </div>
    </header>

    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
        <div class="px-[18px] py-3 text-sm font-medium text-text-heading flex items-center justify-between">
            <div>All {{ $taxonomy->title }}</div>
        </div>
        <div class="px-1.5 pb-2" id="sortable-terms">
            @if($taxonomy->terms->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-center px-6">
                    <img src="/empty-collection.svg" alt="No items" class="size-32 mb-4 opacity-60">
                    <p class="text-sm font-medium text-text-heading">No items yet.</p>
                    <p class="text-xs text-text-muted mt-1">
                        <a href="{{ route('admin.taxonomies.terms.create', $taxonomy) }}" class="text-primary hover:text-primary/80 no-underline font-medium cursor-pointer">Get started by creating one.</a>
                    </p>
                </div>
            @else
                @foreach($taxonomy->terms as $term)
                    @php
                        $tData = $term->data ?? [];
                        $imgUrl = $tData['image'] ?? $tData['icon'] ?? $tData['photo'] ?? $tData['featured_image'] ?? null;
                    @endphp
                    <div
                        class="flex rounded-xl shadow-sm bg-content-bg mb-px group px-3"
                        data-term-id="{{ $term->id }}"
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
                        <div class="flex flex-1 items-center px-1.5 text-xs leading-normal min-w-0 h-12">
                            <div class="flex gap-2 sm:gap-3 grow items-center py-1 min-w-0">
                                <a href="{{ route('admin.taxonomies.terms.edit', [$taxonomy, $term]) }}" class="flex items-center gap-2.5 no-underline min-w-0">
                                    @if($imgUrl)
                                        <img src="{{ $imgUrl }}" alt="{{ $term->title }}" class="size-8 rounded-lg object-cover border border-gray-200 shrink-0">
                                    @endif
                                    <span class="text-sm font-semibold text-text-heading truncate group-hover:text-primary transition-colors">{{ $term->title }}</span>
                                </a>
                            </div>
                            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                                @if($term->slug)
                                    <code class="text-xs bg-panel-bg px-2 py-0.5 rounded shrink-0 text-text-muted select-all">{{ $term->slug }}</code>
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
                                        <a
                                            href="{{ route('admin.taxonomies.terms.edit', [$taxonomy, $term]) }}"
                                            role="menuitem"
                                            class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm font-medium no-underline transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil size-4 shrink-0 text-text-muted">
                                                <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                                                <path d="m15 5 4 4"/>
                                            </svg>
                                            <span>Edit {{ $taxonomy->title }}</span>
                                        </a>
                                        <hr class="my-1 border-content-border">
                                        <button type="button" role="menuitem"
                                            @click="deletingTerm = { id: '{{ $term->id }}', title: @js($term->title) }; open = false"
                                            class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2 size-4 shrink-0 text-red-500">
                                                <path d="M3 6h18"/>
                                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                            </svg>
                                            <span>Delete {{ $taxonomy->title }}</span>
                                        </button>
                                        <form id="delete-term-form-{{ $term->id }}" method="POST" action="{{ route('admin.taxonomies.terms.destroy', [$taxonomy, $term]) }}" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Delete Taxonomy Confirmation Modal --}}
    <form id="delete-taxonomy-form" method="POST" action="{{ route('admin.taxonomies.destroy', $taxonomy) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    <x-admin::delete-modal
        show="showDeleteTaxonomyModal"
        title="Delete Taxonomy"
        confirm-action="document.getElementById('delete-taxonomy-form')?.submit()"
    >
        Are you sure you want to delete <span class="font-medium text-text-heading">“{{ $taxonomy->title }}”</span>? All associated items and term data will be permanently deleted.
    </x-admin::delete-modal>

    {{-- Delete Term Confirmation Modal --}}
    <x-admin::delete-modal
        show="deletingTerm"
        title="Delete Item"
        title-expression="'Delete ' + (deletingTerm?.title ? '“' + deletingTerm.title + '”' : 'item')"
        confirm-action="document.getElementById('delete-term-form-' + deletingTerm?.id)?.submit()"
    >
        Are you sure you want to delete <span class="font-medium text-text-heading" x-text="deletingTerm?.title ? '“' + deletingTerm.title + '”' : 'this item'"></span>? This action cannot be undone.
    </x-admin::delete-modal>
</div>
@endsection

<style>
    #sortable-terms .sortable-ghost {
        opacity: 0 !important;
    }
    #sortable-terms .sortable-drag {
        opacity: 0.9 !important;
        box-shadow: none !important;
        border-radius: 0.75rem !important;
        background: var(--color-content-bg, #fff) !important;
    }
</style>

@push('scripts')
<script>
    window.addEventListener('load', function () {
        const el = document.getElementById('sortable-terms');
        if (!el || typeof Sortable === 'undefined') return;

        new Sortable(el, {
            handle: '[data-drag-handle]',
            animation: 200,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd() {
                const termIds = [...el.querySelectorAll('[data-term-id]')].map(row => row.dataset.termId);
                fetch('{{ route("admin.taxonomies.terms.reorder", $taxonomy) }}', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ term_ids: termIds }),
                });
            },
        });
    });
</script>
@endpush
