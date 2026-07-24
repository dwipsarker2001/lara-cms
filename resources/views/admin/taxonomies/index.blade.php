@extends('admin.layout')

@section('title', 'Categories')
@section('breadcrumb', 'Categories')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <line x1="4" y1="9" x2="20" y2="9" />
                    <line x1="4" y1="15" x2="20" y2="15" />
                    <line x1="10" y1="3" x2="8" y2="21" />
                    <line x1="16" y1="3" x2="14" y2="21" />
                </svg>
                Categories
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.taxonomies.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Create Category
                </a>
            </div>
        </header>

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] py-3 text-sm font-medium text-text-heading flex items-center gap-2">
                <div>All Categories</div>
            </div>
            <div class="px-1.5 pb-2">
                @if ($taxonomies->isEmpty())
                    <div class="flex flex-col items-center justify-center py-8">
                        <p class="text-sm font-medium text-text-heading">No categories yet.</p>
                        <p class="text-sm text-text-muted mt-1">
                            <a href="{{ route('admin.taxonomies.create') }}" class="text-primary hover:text-primary/80 no-underline font-medium">Create your first category group</a>
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($taxonomies as $taxonomy)
                            <div class="flex rounded-xl shadow-sm bg-content-bg group px-3">
                                <div class="flex-1 min-w-0 py-3">
                                    <a href="{{ route('admin.taxonomies.edit', $taxonomy) }}" class="flex items-center gap-2 no-underline min-w-0">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                            <line x1="4" y1="9" x2="20" y2="9" />
                                            <line x1="4" y1="15" x2="20" y2="15" />
                                            <line x1="10" y1="3" x2="8" y2="21" />
                                            <line x1="16" y1="3" x2="14" y2="21" />
                                        </svg>
                                        <span class="text-sm font-semibold text-text-heading truncate group-hover:text-primary transition-colors">
                                            {{ $taxonomy->title }}
                                        </span>
                                    </a>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                                        <button
                                            type="button"
                                            aria-haspopup="menu"
                                            :aria-expanded="open"
                                            aria-label="Open menu"
                                            @click="open = !open"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-transparent text-text-primary/60 hover:bg-text-primary/10 hover:text-text-primary transition-colors"
                                        >
                                            <svg viewBox="0 0 16 3" class="size-4" fill="currentColor" aria-hidden="true">
                                                <circle cx="2" cy="1.5" r="1.5" />
                                                <circle cx="8" cy="1.5" r="1.5" />
                                                <circle cx="14" cy="1.5" r="1.5" />
                                            </svg>
                                        </button>
                                        <div
                                            x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            role="menu"
                                            style="z-index: 9999;"
                                            class="absolute right-0 top-full mt-1 min-w-[12rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5"
                                        >
                                            <a href="{{ route('admin.taxonomies.edit', $taxonomy) }}" role="menuitem"
                                                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-emerald-600 hover:bg-emerald-50"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-emerald-500">
                                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                                <span>Edit</span>
                                            </a>
                                            <form method="POST" action="{{ route('admin.taxonomies.destroy', $taxonomy) }}" class="w-full">
                                                @csrf @method('DELETE')
                                                <button type="submit" role="menuitem"
                                                    onclick="return confirm('Delete this category group?')"
                                                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-red-500">
                                                        <polyline points="3 6 5 6 21 6" />
                                                        <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                                                    </svg>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection