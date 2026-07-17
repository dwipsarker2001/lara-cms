@extends('admin.layout')

@section('title', 'Collection Builder')
@section('breadcrumb', 'Collection Builder')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>
                Collection Builder
            </h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.collections.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                >
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M8 3v10M3 8h10" /></svg>
                    <span>Create Collection</span>
                </a>
            </div>
        </header>

        <x-admin::sortable-list
            title="All Collections"
            :items="$collections"
            sortable-id="sortable-collections"
            data-key="collectionId"
            reorder-route="admin.collections.reorder"
            edit-route="admin.collections.edit"
            delete-route="admin.collections.destroy"
            update-route="admin.collections.edit"
            empty-text="No collections yet"
            empty-link-text="Get started by creating one."
            empty-link-route="admin.collections.create"
        />
    </div>
@endsection

<style>
    #sortable-collections .sortable-ghost {
        opacity: 0 !important;
    }
    #sortable-collections .sortable-drag {
        opacity: 0.9 !important;
        box-shadow: none !important;
        border-radius: 0.75rem !important;
        background: var(--color-content-bg, #fff) !important;
    }
</style>

@push('scripts')
    <script>
        window.addEventListener('load', function () {
            const el = document.getElementById('sortable-collections');
            if (!el || typeof Sortable === 'undefined') return;

            new Sortable(el, {
                handle: '[data-drag-handle]',
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd() {
                    const collectionIds = [...el.querySelectorAll('[data-collection-id]')].map(row => row.dataset.collectionId);
                    fetch('{{ route("admin.collections.reorder") }}', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ collection_ids: collectionIds }),
                    });
                },
            });
        });
    </script>
@endpush
