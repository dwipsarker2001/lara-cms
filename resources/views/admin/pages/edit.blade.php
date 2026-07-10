@extends('admin.layout')

@section('title', 'Edit Page')
@section('breadcrumb', 'Settings')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <circle cx="12" cy="12" r="3" />
                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                </svg>
                {{ $page->title }}
            </h1>
        </header>

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] py-3 text-sm font-medium text-text-heading">Page Settings</div>
            <div class="px-1.5 pb-2 max-w-2xl">
                <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="bg-content-bg rounded-xl shadow-sm p-4 space-y-4">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-sm font-semibold text-text-primary mb-1">Title</label>
                        <input name="title" value="{{ old('title', $page->title) }}"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            required>
                        @error('title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-text-primary mb-1">Slug</label>
                        <input name="slug" value="{{ old('slug', $page->slug) }}"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            required>
                        @error('slug') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <label class="text-sm font-semibold text-text-primary">Published</label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="published" value="1" {{ $page->published ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:ring-2 focus:ring-primary/30">
                            <span class="text-sm text-text-muted">{{ $page->published ? 'Yes' : 'No' }}</span>
                        </label>
                    </div>
                    <div class="flex items-center gap-2 pt-2">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                        >
                            Update
                        </button>
                        <a href="{{ route('admin.pages.editor', $page) }}"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-gradient-to-b from-content-bg to-gray-50 hover:to-gray-100 text-text-primary border border-content-border shadow-sm"
                        >
                            Open Editor
                        </a>
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
