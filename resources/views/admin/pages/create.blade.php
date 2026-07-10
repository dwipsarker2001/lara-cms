@extends('admin.layout')

@section('title', 'Create Page')
@section('breadcrumb', 'Create Page')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                </svg>
                Create Page
            </h1>
        </header>

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] py-3 text-sm font-medium text-text-heading">Page Details</div>
            <div class="px-1.5 pb-2 max-w-2xl">
                <form method="POST" action="{{ route('admin.pages.store') }}" class="bg-content-bg rounded-xl shadow-sm p-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-text-primary mb-1">Title</label>
                        <input name="title" value="{{ old('title') }}"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            required>
                        @error('title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-text-primary mb-1">Slug</label>
                        <input name="slug" value="{{ old('slug') }}"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            required>
                        @error('slug') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <label class="text-sm font-semibold text-text-primary">Published</label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="published" value="1" checked
                                class="rounded border-gray-300 text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:ring-2 focus:ring-primary/30">
                            <span class="text-sm text-text-muted">Yes</span>
                        </label>
                    </div>
                    <div class="flex items-center gap-2 pt-2">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                        >
                            Create
                        </button>
                        <a href="{{ route('admin.pages.index') }}"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-gradient-to-b from-content-bg to-gray-50 hover:to-gray-100 text-text-primary border border-content-border shadow-sm"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
