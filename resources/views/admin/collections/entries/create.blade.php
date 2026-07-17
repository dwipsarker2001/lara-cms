@extends('admin.layout')

@section('title', 'Create Entry — ' . $collection->name)
@section('breadcrumb', 'Create Entry')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0">
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            @if($collection->icon)
                <i class="{{ $collection->icon }} text-lg w-6 text-center text-text-muted"></i>
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>
            @endif
            Create Entry
        </h1>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.collections.entries.index', $collection) }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200"
            >
                Back
            </a>
            <button type="submit" form="entry-form"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
            >
                Create & Edit
            </button>
        </div>
    </header>

    <form id="entry-form" method="POST" action="{{ route('admin.collections.entries.store', $collection) }}">
        @csrf

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] pt-3 pb-1">
                <div class="text-sm font-medium text-text-heading">New Entry</div>
                <p class="text-sm text-text-muted mt-1">Enter a title to create a new page in this collection.</p>
            </div>
            <div class="px-1.5 pb-2">
                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                    <div class="divide-y divide-content-border">
                        <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                            <div class="flex flex-col gap-1.5">
                                <label for="field-title" class="text-sm font-medium text-text-heading">Title</label>
                                <div class="text-sm text-text-muted">A descriptive title for this page.</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <input id="field-title" type="text" name="data[title]" value="{{ old('data.title') }}" placeholder="Enter title" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
