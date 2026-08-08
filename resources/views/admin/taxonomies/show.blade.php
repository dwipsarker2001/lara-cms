@extends('admin.layout')

@section('title', $taxonomy->title . ' — Items')
@section('breadcrumb', $taxonomy->title)

@section('content')
<div
    class="max-w-5xl mx-auto px-2 sm:px-0"
    x-data="{
        showAddModal: false,
        editingTerm: null,
        termTitle: '',
        termSlug: '',
        customSlug: false,

        slugify(text) {
            return text
                .toString()
                .toLowerCase()
                .trim()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        },
        openAddModal() {
            this.editingTerm = null;
            this.termTitle = '';
            this.termSlug = '';
            this.customSlug = false;
            this.showAddModal = true;
        },
        openEditModal(term) {
            this.editingTerm = term;
            this.termTitle = term.title;
            this.termSlug = term.slug;
            this.customSlug = true;
        },
        closeModal() {
            this.showAddModal = false;
            this.editingTerm = null;
            this.termTitle = '';
            this.termSlug = '';
            this.customSlug = false;
        },
        onTitleInput() {
            if (!this.customSlug) {
                this.termSlug = this.slugify(this.termTitle);
            }
        },
        onSlugInput(val) {
            this.customSlug = true;
            this.termSlug = this.slugify(val);
        }
    }"
>
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                <line x1="4" y1="9" x2="20" y2="9" />
                <line x1="4" y1="15" x2="20" y2="15" />
                <line x1="10" y1="3" x2="8" y2="21" />
                <line x1="16" y1="3" x2="14" y2="21" />
            </svg>
            {{ $taxonomy->title }}
        </h1>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.taxonomies.edit', $taxonomy) }}"
                class="size-10 flex items-center justify-center rounded-lg border border-content-border bg-white text-text-primary hover:bg-gray-50 transition-colors shadow-sm"
                title="Taxonomy Settings"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
                    <circle cx="12" cy="12" r="3" />
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
            </a>
            <button
                type="button"
                @click="openAddModal()"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
            >
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                    <path d="M8 3v10M3 8h10" />
                </svg>
                <span>Create Item</span>
            </button>
        </div>
    </header>

    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
        <div class="px-[18px] py-3 text-sm font-medium text-text-heading">All {{ $taxonomy->title }}</div>
        <div class="px-1.5 pb-2">
            @if($taxonomy->terms->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-center px-6">
                    <img src="/empty-collection.svg" alt="No items" class="size-32 mb-4 opacity-60">
                    <p class="text-sm font-medium text-text-heading">No items yet.</p>
                    <p class="text-xs text-text-muted mt-1">
                        <button type="button" @click="openAddModal()" class="text-primary hover:text-primary/80 no-underline font-medium cursor-pointer">Get started by creating one.</button>
                    </p>
                </div>
            @else
                <div id="sortable-terms" class="grid grid-cols-1 md:grid-cols-2 gap-0.5">
                @foreach($taxonomy->terms as $term)
                    <div
                        class="flex items-center justify-between rounded-xl border border-content-border bg-content-bg px-2.5 py-1.5 shadow-sm hover:border-gray-300 transition-colors group"
                        data-term-id="{{ $term->id }}"
                    >
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="shrink-0 text-text-muted/70 cursor-grab" data-drag-handle>
                                <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                                    <circle cx="8" cy="6" r="2.5" />
                                    <circle cx="16" cy="6" r="2.5" />
                                    <circle cx="8" cy="12" r="2.5" />
                                    <circle cx="16" cy="12" r="2.5" />
                                    <circle cx="8" cy="18" r="2.5" />
                                    <circle cx="16" cy="18" r="2.5" />
                                </svg>
                            </div>
                            <button type="button" @click="openEditModal({ id: {{ $term->id }}, title: '{{ addslashes($term->title) }}', slug: '{{ addslashes($term->slug) }}' })" class="flex items-center gap-1.5 no-underline min-w-0 text-left cursor-pointer">
                                <span class="text-xs font-semibold text-text-heading truncate group-hover:text-primary transition-colors">{{ $term->title }}</span>
                            </button>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($term->slug)
                                <span class="text-[10px] text-text-muted font-mono select-all bg-panel-bg px-1.5 py-0.5 rounded border border-content-border">
                                    {{ $term->slug }}
                                </span>
                            @endif
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                                <button
                                    type="button"
                                    aria-haspopup="menu"
                                    :aria-expanded="open"
                                    aria-label="Open menu"
                                    @click="open = !open"
                                    class="inline-flex items-center justify-center h-7 w-7 rounded-lg bg-transparent text-text-primary/60 hover:bg-text-primary/10 hover:text-text-primary transition-colors"
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
                                    class="absolute right-0 top-full mt-1 min-w-[10rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5"
                                >
                                    <button
                                        type="button"
                                        role="menuitem"
                                        @click="open = false; openEditModal({ id: {{ $term->id }}, title: '{{ addslashes($term->title) }}', slug: '{{ addslashes($term->slug) }}' })"
                                        class="flex w-full items-center justify-start gap-2 px-2.5 py-1.5 rounded-lg text-xs text-text-primary hover:bg-body-bg cursor-pointer"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 text-text-muted">
                                            <path d="M14 2H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                        </svg>
                                        <span>Edit</span>
                                    </button>
                                    <hr class="my-1 border-content-border">
                                    <form method="POST" action="{{ route('admin.taxonomies.terms.destroy', [$taxonomy, $term]) }}" class="w-full mb-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" role="menuitem"
                                            onclick="return confirm('Delete this item?')"
                                            class="flex w-full items-center justify-start gap-2 px-2.5 py-1.5 rounded-lg text-xs transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 text-red-500">
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
                @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ADD TERM MODAL (Matching collections/edit.blade.php popup style) --}}
    <div x-show="showAddModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40"
        @click.self="closeModal()" style="display: none;">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 relative">
            <div class="px-6 pt-5 pb-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-text-heading">Add Item</h3>
                    <button type="button" @click="closeModal()"
                        class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-text-muted transition-colors">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="size-4">
                            <path d="M4 4l8 8M12 4l-8 8" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-text-muted mt-1">Create a new item under {{ $taxonomy->title }}.</p>
            </div>
            <form method="POST" action="{{ route('admin.taxonomies.terms.store', $taxonomy) }}">
                @csrf
                <div class="px-6 pb-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Title</label>
                        <input type="text" name="title" x-model="termTitle" @input="onTitleInput"
                            placeholder="e.g. Adventure, Heritage" required
                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Slug</label>
                        <input type="text" name="slug" x-model="termSlug" @input="onSlugInput($event.target.value)"
                            placeholder="Auto-generated slug"
                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-mono">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl">
                    <button type="button" @click="closeModal()"
                        class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-primary shadow-sm border border-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                        Create Item
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT TERM MODAL (Matching collections/edit.blade.php popup style) --}}
    <div x-show="editingTerm !== null" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40"
        @click.self="closeModal()" style="display: none;">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 relative">
            <div class="px-6 pt-5 pb-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-text-heading">Edit Item</h3>
                    <button type="button" @click="closeModal()"
                        class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-text-muted transition-colors">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="size-4">
                            <path d="M4 4l8 8M12 4l-8 8" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-text-muted mt-1">Configure item details.</p>
            </div>
            <template x-if="editingTerm">
                <form method="POST" :action="'/admin/taxonomies/{{ $taxonomy->id }}/terms/' + editingTerm.id">
                    @csrf @method('PUT')
                    <div class="px-6 pb-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-1">Title</label>
                            <input type="text" name="title" x-model="termTitle" @input="onTitleInput" required
                                class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-heading mb-1">Slug</label>
                            <input type="text" name="slug" x-model="termSlug" @input="onSlugInput($event.target.value)" required
                                class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-mono">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl">
                        <button type="button" @click="closeModal()"
                            class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-primary shadow-sm border border-gray-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                            Update Item
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection

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
                const termIds = [...el.querySelectorAll('[data-term-id]')] .map(row => row.dataset.termId);
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
