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

        @if ($page->slug !== 'home')
            <div class="border-t border-content-border pt-8 mt-8">
                <div class="bg-panel-bg rounded-2xl p-[7px]">
                    <div class="px-[18px] py-3 text-sm font-medium text-text-heading">Delete Page</div>
                    <div class="px-1.5 pb-2 max-w-2xl">
                        <div class="bg-content-bg rounded-xl shadow-sm p-4">
                            <p class="text-sm text-text-muted mb-4">Permanently delete this page and all its content. This action cannot be undone.</p>
                            <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Are you sure you want to delete this page?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-red-600 hover:bg-red-700 text-white shadow-sm"
                                >
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                    </svg>
                                    Delete Page
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
