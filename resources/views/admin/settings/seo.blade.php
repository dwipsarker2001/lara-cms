@extends('admin.layout')

@section('title', 'SEO')
@section('breadcrumb', 'SEO')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                    <polyline points="16 7 22 7 22 13" />
                </svg>
                SEO
            </h1>
        </header>

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                <div class="px-[18px] py-3 text-sm font-medium text-text-heading">Search Engine Optimization</div>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl shadow-sm p-4 text-sm text-text-muted">
                        Configure how this site appears in search engines.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
