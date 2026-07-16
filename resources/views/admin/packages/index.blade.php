@extends('admin.layout')

@section('title', 'Packages')
@section('breadcrumb', 'Packages')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <path d="M16.5 9.4 7.55 4.24" />
                    <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" />
                    <polyline points="3.29 7 12 12 20.71 7" />
                    <line x1="12" y1="22" x2="12" y2="12" />
                </svg>
                Packages
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.packages.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Create Package
                </a>
            </div>
        </header>

        <x-admin::sortable-list
            title="All Packages"
            :items="$packages"
            sortable-id="sortable-packages"
            data-key="packageId"
            reorder-route="admin.packages.reorder"
            edit-route="admin.packages.editor"
            update-route="admin.packages.edit"
            delete-route="admin.packages.destroy"
            empty-text="No packages yet."
            empty-link-text="Create your first package"
            empty-link-route="admin.packages.create"
        />
    </div>
@endsection

@push('scripts')
    @if(count($packages) > 1)
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('sortable-packages');
                if (!el || typeof Sortable === 'undefined') return;
                Sortable.create(el, {
                    handle: '[data-drag-handle]',
                    animation: 200,
                    ghostClass: 'opacity-40',
                    dragClass: '!bg-white !shadow-lg !rounded-xl',
                    onEnd() {
                        const ids = Array.from(el.querySelectorAll('[data-package-id]')).map(el => el.dataset.packageId);
                        fetch('{{ route('admin.packages.reorder') }}', {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ package_ids: ids }),
                        }).catch(() => {});
                    },
                });
            });
        </script>
    @endif
@endpush
