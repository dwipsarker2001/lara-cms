@extends('admin.layout')

@section('title', 'Layouts')
@section('breadcrumb', 'Layouts')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="9" y1="21" x2="9" y2="9" />
                </svg>
                Layouts
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.layouts.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Create Layout
                </a>
            </div>
        </header>

        <x-admin::sortable-list
            title="All Layouts"
            :items="$layouts"
            sortable-id="sortable-layouts"
            data-key="layoutId"
            reorder-route="admin.layouts.reorder"
            edit-route="admin.layouts.editor"
            delete-route="admin.layouts.destroy"
            :show-settings="true"
            settings-route="admin.layouts.edit"
            empty-text="No layouts yet."
            empty-link-text="Create your first layout"
            empty-link-route="admin.layouts.create"
            show-route
        />
    </div>
@endsection

@push('scripts')
    @if(count($layouts) > 1)
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('sortable-layouts');
                if (!el || typeof Sortable === 'undefined') return;
                Sortable.create(el, {
                    handle: '[data-drag-handle]',
                    animation: 200,
                    ghostClass: 'opacity-40',
                    dragClass: '!bg-white !shadow-lg !rounded-xl',
                    onEnd() {
                        const ids = Array.from(el.querySelectorAll('[data-layout-id]')).map(el => el.dataset.layoutId);
                        fetch('{{ route('admin.layouts.reorder') }}', {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ layout_ids: ids }),
                        }).catch(() => {});
                    },
                });
            });
        </script>
    @endif
@endpush
