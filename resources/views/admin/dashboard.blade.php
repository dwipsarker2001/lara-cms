@extends('admin.layout')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                </svg>
                Dashboard
            </h1>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-content-bg rounded-xl border border-content-border p-5">
                <div class="text-text-muted text-sm font-medium">Pages</div>
                <div class="text-3xl font-bold text-text-heading mt-1">{{ \App\Models\Page::count() }}</div>
            </div>
            <div class="bg-content-bg rounded-xl border border-content-border p-5">
                <div class="text-text-muted text-sm font-medium">Posts</div>
                <div class="text-3xl font-bold text-text-heading mt-1">{{ \App\Models\Post::count() }}</div>
            </div>
            <div class="bg-content-bg rounded-xl border border-content-border p-5">
                <div class="text-text-muted text-sm font-medium">Bookings</div>
                <div class="text-3xl font-bold text-text-heading mt-1">{{ \App\Models\Booking::count() }}</div>
            </div>
        </div>
    </div>
@endsection
