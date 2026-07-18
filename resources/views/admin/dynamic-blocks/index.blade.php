@extends('admin.layout')

@section('title', 'Blocks')
@section('breadcrumb', 'Blocks')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <path d="M21 12H3" />
                </svg>
                Blocks
            </h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.dynamic-blocks.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                >
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M8 3v10M3 8h10" /></svg>
                    <span>Create Block</span>
                </a>
            </div>
        </header>

        <x-admin::sortable-list
            title="All Blocks"
            :items="$blocks"
            sortable-id="sortable-blocks"
            data-key="blockId"
            reorder-route="admin.dynamic-blocks.index"
            edit-route="admin.dynamic-blocks.editor"
            delete-route="admin.dynamic-blocks.destroy"
            show-settings
            settings-route="admin.dynamic-blocks.edit"
            empty-text="No blocks yet"
            empty-link-text="Get started by creating one."
            empty-link-route="admin.dynamic-blocks.create"
        />
    </div>
@endsection
