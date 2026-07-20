@extends('admin.layout')

@section('title', 'Packages')
@section('breadcrumb', 'Packages')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                    <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16" />
                </svg>
                Packages
            </h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.subscription-plans.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                >
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M8 3v10M3 8h10" /></svg>
                    <span>Create Package</span>
                </a>
            </div>
        </header>

        <x-admin::sortable-list
            title="All Packages"
            :items="$plans"
            sortable-id="sortable-plans"
            data-key="id"
            reorder-route="admin.subscription-plans.index"
            edit-route="admin.subscription-plans.edit"
            delete-route="admin.subscription-plans.destroy"
            show-settings
            settings-route="admin.subscription-plans.edit"
            empty-text="No packages yet"
            empty-link-text="Get started by creating one."
            empty-link-route="admin.subscription-plans.create"
            :default-item-id="$defaultPlanId"
        />
    </div>
@endsection
