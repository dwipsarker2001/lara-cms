@extends('admin.layout')

@section('title', 'Subscribers')
@section('breadcrumb', 'Email Marketing / Subscribers')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0" style="max-width: 64rem;">
        <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 text-text-muted shrink-0">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Subscribers
            </h1>
        </header>

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">All Subscribers</div>
            <p class="px-[18px] pb-3 text-sm text-text-muted">
                Manage your email list subscribers here. This page is part of the Email Marketing plugin
                and lives safely in <code class="text-xs bg-content-border/40 px-1.5 py-0.5 rounded font-mono">plugins/email-marketing/</code>.
            </p>
            <div class="px-1.5 pb-2">
                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-8 text-center">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-10 text-text-muted mx-auto mb-3">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    </svg>
                    <p class="text-sm font-medium text-text-heading">No subscribers yet</p>
                    <p class="text-xs text-text-muted mt-1">Start building your email list by adding a subscribe form to your website.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
