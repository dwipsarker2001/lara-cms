@extends('admin.layout')

@section('title', 'Email Marketing')
@section('breadcrumb', 'Email Marketing')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0" style="max-width: 64rem;">
        <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 text-text-muted shrink-0">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
                Email Marketing
            </h1>
        </header>

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Plugin Active</div>
            <p class="px-[18px] pb-3 text-sm text-text-muted">
                The <strong>Email Marketing</strong> plugin is loaded and running. This view is served from
                <code class="text-xs bg-content-border/40 px-1.5 py-0.5 rounded font-mono">plugins/email-marketing/views/</code>
                and will <strong>never be removed</strong> during a CMS update.
            </p>
            <div class="px-1.5 pb-2">
                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-6 space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="size-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-sm font-medium text-text-heading">Plugin is active and running</span>
                    </div>
                    <p class="text-sm text-text-muted">
                        Build your email marketing tools here. Add subscriber management, campaign builders,
                        and analytics — all safely protected in the <code class="font-mono text-xs">plugins/</code> directory.
                    </p>
                    <a href="{{ route('admin.plugins.email-marketing.subscribers') }}"
                        class="inline-flex items-center gap-2 text-sm font-medium px-4 py-2 rounded-lg bg-primary hover:opacity-90 text-white transition-colors cursor-pointer no-underline">
                        View Subscribers
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
