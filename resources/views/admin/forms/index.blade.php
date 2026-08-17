@extends('admin.layout')

@section('title', 'Form Builder')
@section('breadcrumb', 'Form Builder')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="12" y1="18" x2="12" y2="12" />
                    <line x1="9" y1="15" x2="15" y2="15" />
                </svg>
                Form Builder
            </h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.forms.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                >
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M8 3v10M3 8h10" /></svg>
                    <span>Create Form</span>
                </a>
            </div>
        </header>

        <x-admin::sortable-list
            title="All Forms"
            :items="$forms"
            sortable-id="sortable-forms"
            data-key="formId"
            reorder-route="admin.forms.reorder"
            edit-route="admin.forms.editor"
            delete-route="admin.forms.destroy"
            update-route="admin.forms.edit"
            builder-route="admin.forms.editor"
            builder-label="Form Builder"
            entries-route="admin.forms.entries"
            entries-label="Submissions"
            empty-text="No forms yet"
            empty-link-text="Get started by creating one."
            empty-link-route="admin.forms.create"
        />
    </div>
@endsection

<style>
    #sortable-forms .sortable-ghost {
        opacity: 0 !important;
    }
    #sortable-forms .sortable-drag {
        opacity: 0.9 !important;
        box-shadow: none !important;
        border-radius: 0.75rem !important;
        background: var(--color-content-bg, #fff) !important;
    }
</style>

@push('scripts')
    <script>
        window.addEventListener('load', function () {
            const el = document.getElementById('sortable-forms');
            if (!el || typeof Sortable === 'undefined') return;

            new Sortable(el, {
                handle: '[data-drag-handle]',
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd() {
                    const formIds = [...el.querySelectorAll('[data-form-id]')].map(row => row.dataset.formId);
                    fetch('{{ route("admin.forms.reorder") }}', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ form_ids: formIds }),
                    });
                },
            });
        });
    </script>
@endpush
