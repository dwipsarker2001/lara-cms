@extends('admin.layout')

@section('title', 'Pages')
@section('breadcrumb', 'Pages')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        {{-- CollectionHeader --}}
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                </svg>
                Pages
            </h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.pages.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                >
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                        <path d="M8 3v10M3 8h10" />
                    </svg>
                    <span>Create Page</span>
                </a>
            </div>
        </header>

        {{-- CollectionPanel --}}
        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] py-3 text-sm font-medium text-text-heading flex items-center justify-between">
                <div>All Pages</div>
            </div>
            <div class="px-1.5 pb-2" id="sortable-pages">
                @if ($pages->isEmpty())
                    <div class="flex flex-col items-center justify-center py-8">
                        <p class="text-sm font-medium text-text-heading">No pages yet</p>
                        <p class="text-sm text-text-muted mt-1">Get started by creating one.</p>
                    </div>
                @else
                    @foreach ($pages as $page)
                        <div class="flex rounded-xl shadow-sm bg-content-bg mb-px group px-3" data-page-id="{{ $page->id }}">
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
                                    <span class="inline-block w-2 h-2 rounded-full shrink-0 {{ $page->published ? 'bg-success' : 'bg-text-muted' }}"></span>
                                    <span class="text-sm font-semibold text-text-heading truncate">
                                        {{ $page->title }}
                                        @if ($page->slug === 'home')
                                            <span class="text-xs text-text-muted font-normal ml-1">(Home)</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                                    <code class="text-xs bg-panel-bg px-2 py-0.5 rounded shrink-0 text-text-muted">{{ $page->route() }}</code>
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
                                            <a href="{{ route('admin.pages.editor', $page) }}" role="menuitem"
                                                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                                <span>Edit</span>
                                            </a>
                                            <a href="{{ route('admin.pages.edit', $page) }}" role="menuitem"
                                                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                                    <circle cx="12" cy="12" r="3" />
                                                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                                                </svg>
                                                <span>Settings</span>
                                            </a>
                                            @if ($page->slug !== 'home')
                                                <hr class="my-1 border-content-border">
                                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="w-full">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" role="menuitem"
                                                        onclick="return confirm('Delete this page?')"
                                                        class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
                                                    >
                                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-red-500">
                                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                                        </svg>
                                                        <span>Delete</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('load', function () {
            const el = document.getElementById('sortable-pages');
            if (!el || typeof Sortable === 'undefined') return;

            new Sortable(el, {
                handle: '[data-drag-handle]',
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd() {
                    const pageIds = [...el.querySelectorAll('[data-page-id]')].map(row => row.dataset.pageId);
                    fetch('{{ route("admin.pages.reorder") }}', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ page_ids: pageIds }),
                    });
                },
            });
        });
    </script>
@endpush
