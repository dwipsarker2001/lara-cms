@extends('admin.layout')

@section('title', 'Pages')
@section('breadcrumb', 'Pages')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
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

        <x-admin::sortable-list
            title="All Pages"
            :items="$pages"
            sortable-id="sortable-pages"
            data-key="pageId"
            reorder-route="admin.pages.reorder"
            edit-route="admin.pages.editor"
            delete-route="admin.pages.destroy"
            empty-text="No pages yet"
            empty-link-text="Get started by creating one."
            empty-link-route="admin.pages.create"
            show-route
            show-settings
            settings-route="admin.pages.edit"
            :protected-items="['home']"
            badge-field="slug"
            badge-value="home"
            badge-label="(Home)"
        />
    </div>
@endsection

<style>
    #sortable-pages .sortable-ghost {
        opacity: 0 !important;
    }
    #sortable-pages .sortable-drag {
        opacity: 0.9 !important;
        box-shadow: none !important;
        border-radius: 0.75rem !important;
        background: var(--color-content-bg, #fff) !important;
    }
</style>

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
