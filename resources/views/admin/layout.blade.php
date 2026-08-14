<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <style>[x-cloak] { display: none !important; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        window.FA_ICONS = {{ \Illuminate\Support\Js::from(file_exists(public_path('fa-icons.json')) ? json_decode(file_get_contents(public_path('fa-icons.json'))) : []) }};
        window.categoryPicker = function(initialSelected, termsList) {
            return {
                selected: Array.isArray(initialSelected) ? initialSelected : (initialSelected ? [initialSelected] : []),
                terms: termsList || [],
                open: false,
                isSelected(id, title, slug) {
                    if (!this.selected || this.selected.length === 0) return false;
                    const sel = this.selected.map(String);
                    const strId = String(id);
                    const strTitle = title ? String(title).toLowerCase() : '';
                    const strSlug = slug ? String(slug).toLowerCase() : '';
                    return sel.some(function(s) {
                        const strS = String(s).toLowerCase();
                        return strS === strId || (strTitle && strS === strTitle) || (strSlug && strS === strSlug);
                    });
                },
                toggle(id, title) {
                    if (this.isSelected(id, title)) {
                        const strId = String(id);
                        const strTitle = title ? String(title).toLowerCase() : '';
                        this.selected = this.selected.filter(function(x) {
                            const strX = String(x).toLowerCase();
                            return strX !== strId && (strTitle ? strX !== strTitle : true);
                        });
                    } else {
                        this.selected.push(id);
                    }
                },
                getSelectedLabels() {
                    if (!this.selected || this.selected.length === 0) return 'Choose categories...';
                    const self = this;
                    const labels = [];
                    for (let i = 0; i < this.terms.length; i++) {
                        const t = this.terms[i];
                        if (self.isSelected(t.id, t.title, t.slug)) {
                            labels.push(t.title);
                        }
                    }
                    return labels.length ? labels.join(', ') : 'Choose categories...';
                }
            };
        };
        document.addEventListener('alpine:init', function() {
            if (window.Alpine) {
                window.Alpine.data('categoryPicker', window.categoryPicker);
            }
        });
    </script>
    @stack('styles')
</head>
<body class="admin-root antialiased bg-header-bg text-text-primary min-h-full" x-data="{ navCollapsed: {{ (request()->routeIs('admin.forms.editor') || request()->routeIs('admin.collections.entries.editor')) ? 'true' : 'false' }}, userMenuOpen: false }">
    {{-- Fixed header --}}
    <header class="fixed top-0 left-0 right-0 h-14 px-4 flex items-center gap-3 z-50 bg-header-bg text-header-text">
        <div class="flex items-center gap-3">
            <button
                type="button"
                aria-label="Toggle sidebar"
                :aria-pressed="navCollapsed"
                @click="navCollapsed = !navCollapsed"
                class="p-1.5 rounded-md hover:bg-white/10 text-white/85 transition-colors"
            >
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4">
                    <path stroke-linecap="round" d="M3 5h14M3 10h14M3 15h14" />
                </svg>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 text-[13px] text-white/85 no-underline">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
                {{ config('app.name') }}
            </a>
            <span class="text-[11px] font-normal leading-none text-body-bg bg-white/15 px-[5px] pb-[1px] rounded-[3px]">Pro</span>

        </div>

        <div class="flex-1"></div>

        <div class="flex items-center gap-3">
            <button
                type="button"
                aria-label="Search"
                @click="$dispatch('open-command-palette')"
                class="hidden sm:flex items-center gap-2.5 h-9 w-56 pl-3 pr-2 rounded-lg bg-white/10 hover:bg-white/[0.16] text-white/55 hover:text-white/80 transition-colors"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-[18px] h-[18px] shrink-0">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <span class="text-[13px]">Search</span>
                <span class="ml-auto flex items-center gap-1">
                    <kbd class="inline-flex h-5 min-w-[20px] items-center justify-center rounded-[5px] bg-white/15 px-1.5 text-white/80">⌘</kbd>
                    <kbd class="inline-flex h-5 min-w-[20px] items-center justify-center rounded-[5px] bg-white/15 px-1.5 text-[11px] font-semibold leading-none text-white/80">K</kbd>
                </span>
            </button>
            <div class="flex items-center gap-1.5">
                <a
                    href="#"
                    aria-label="Support"
                    title="Support"
                    class="flex items-center justify-center size-8 rounded-md bg-white/10 text-white/80 hover:text-white hover:bg-white/20 transition-colors"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 22c-2 0-4-4-4-9s2-9 4-9 4 4 4 9-2 9-4 9z" />
                        <path d="M22 12c0 2-4 4-9 4s-9-2-9-4 4-4 9-4 9 2 9 4z" />
                        <circle cx="12" cy="12" r="2" />
                    </svg>
                </a>
                <a
                    href="/"
                    aria-label="View Site"
                    title="View Site"
                    target="_blank"
                    rel="noreferrer"
                    class="flex items-center justify-center size-8 rounded-md bg-white/10 text-white/80 hover:text-white hover:bg-white/20 transition-colors"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6" />
                        <polyline points="15 3 21 3 21 9" />
                        <line x1="10" y1="14" x2="21" y2="3" />
                    </svg>
                </a>
            </div>

            {{-- UserMenu --}}
            <div class="relative ml-2" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                <button
                    type="button"
                    aria-haspopup="menu"
                    :aria-expanded="open"
                    aria-label="Open user menu"
                    @click="open = !open"
                    class="size-8 rounded-lg overflow-hidden flex items-center justify-center ring-2 ring-transparent hover:ring-white/30 transition"
                >
                    @if (Auth::guard('admin')->user()->avatar)
                        <img src="{{ Auth::guard('admin')->user()->avatar }}" alt="{{ Auth::guard('admin')->user()->name }}" class="size-8 rounded-lg object-cover">
                    @else
                        <span class="flex size-8 items-center justify-center bg-primary text-white font-medium text-xs rounded-lg">
                            {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'U', 0, 2)) }}
                        </span>
                    @endif
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
                    class="absolute right-0 top-full mt-2 z-50 min-w-64 rounded-xl border border-content-border bg-body-bg shadow-xl overflow-hidden"
                >
                    <header class="flex items-center gap-2 px-3.5 py-3 bg-content-bg border-b border-content-border">
                        @if (Auth::guard('admin')->user()->avatar)
                            <img src="{{ Auth::guard('admin')->user()->avatar }}" alt="{{ Auth::guard('admin')->user()->name }}" class="size-8 shrink-0 rounded-lg object-cover">
                        @else
                            <span class="flex size-8 shrink-0 items-center justify-center bg-primary text-white font-medium text-xs rounded-lg">
                                {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'U', 0, 2)) }}
                            </span>
                        @endif
                        <div class="min-w-0 text-sm text-text-heading truncate">{{ Auth::guard('admin')->user()->email ?? 'admin@ecms.com' }}</div>
                    </header>

                    <div class="p-1.5 bg-content-bg border-b border-content-border rounded-b-xl">
                        <a href="{{ route('admin.profile.edit') }}" role="menuitem" class="flex w-full items-center rounded-lg px-1 py-1.5 text-sm text-text-primary hover:bg-body-bg transition-colors no-underline cursor-pointer">
                            <svg viewBox="0 0 14 14" fill="none" class="size-4 shrink-0 text-text-muted mx-1">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m3.17188 12.5625 0.12377 -0.528c0.20645 -0.7775 0.65299 -1.4723 1.27877 -1.9838 0.68452 -0.55951 1.54143 -0.86515 2.42552 -0.86515s1.74099 0.30564 2.42552 0.86515c0.62574 0.5115 1.07234 1.2063 1.27874 1.9838l0.1239 0.5436" />
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 13.5c4.16 0 6.5 -2.34 6.5 -6.5S11.16 0.5 7 0.5 0.5 2.84 0.5 7s2.34 6.5 6.5 6.5Z" />
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M6.99927 7.57996c1.4 0 2.1875 -0.7875 2.1875 -2.1875s-0.7875 -2.1875 -2.1875 -2.1875 -2.1875 0.7875 -2.1875 2.1875 0.7875 2.1875 2.1875 2.1875Z" />
                            </svg>
                            <span class="px-2">Manage profile</span>
                        </a>
                        <a href="#" role="menuitem" class="flex w-full items-center rounded-lg px-1 py-1.5 text-sm text-text-primary hover:bg-body-bg transition-colors no-underline cursor-pointer">
                            <svg viewBox="0 0 14 14" fill="none" class="size-4 shrink-0 text-text-muted mx-1">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="1" d="M4.78125 9.21875c-1.08301 0.68519 -1.90856 1.51075 -2.59375 2.59375m2.59375 -7.03124C4.09605 3.69825 3.27051 2.8727 2.1875 2.18751m7.03124 7.03124c1.08306 0.68519 1.90856 1.51075 2.59376 2.59375M9.21874 4.78126c0.6852 -1.08301 1.51076 -1.90856 2.59376 -2.59375" />
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 13.5c4.16 0 6.5 -2.34 6.5 -6.5S11.16 0.5 7 0.5 0.5 2.84 0.5 7s2.34 6.5 6.5 6.5Z" />
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 10c1.92 0 3-1.08 3-3S8.92 4 7 4 4 5.08 4 7s1.08 3 3 3Z" />
                            </svg>
                            <span class="px-2">Get support</span>
                        </a>
                        <a href="{{ route('admin.settings') }}" role="menuitem" class="flex w-full items-center rounded-lg px-1 py-1.5 text-sm text-text-primary hover:bg-body-bg transition-colors no-underline cursor-pointer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted mx-1">
                                <circle cx="12" cy="12" r="3" />
                                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                            </svg>
                            <span class="px-2">Settings</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem" class="flex w-full items-center rounded-lg px-1 py-1.5 text-sm text-text-primary hover:bg-body-bg transition-colors no-underline cursor-pointer">
                                <svg viewBox="0 0 14 14" fill="none" class="size-4 shrink-0 text-text-muted mx-1">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13.4213 1.46922c0.1192 2.99162 0.1032 5.91395 -0.0482 8.92428 -0.0177 0.351 -0.2092 0.6672 -0.505 0.838l-3.70288 2.1379c-0.63714 0.3679 -1.4247 -0.0824 -1.45484 -0.8386 -0.11922 -2.99164 -0.10314 -5.91397 0.04824 -8.92428 0.01765 -0.351 0.20914 -0.66721 0.50503 -0.83805L11.9665 0.63059c0.6371 -0.367859 1.4247 0.082407 1.4548 0.83863Z" />
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12.363 0.498047H5.63873c-0.52523 0 -0.9616 0.406231 -0.99232 0.930573 -0.06864 1.17121 -0.11329 2.35355 -0.13396 3.53926M7.6316 12.5049H5.63873c-0.52523 0 -0.96159 -0.4062 -0.99232 -0.9305 -0.05116 -0.873 -0.089 -1.7522 -0.11351 -2.63434" />
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m5.13867 7.00098 -4.469846 0" />
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.27034 5.22949C1.56221 5.58356 0.854069 6.2917 0.5 6.99981c0.354069 0.70816 1.06221 1.4163 1.77034 1.77037" />
                                </svg>
                                <span class="px-2">Sign out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Shell: light gray container with rounded top, holds sidebar + content --}}
    <div class="fixed top-14 left-0 right-0 bottom-0 bg-body-bg rounded-t-[16px] overflow-hidden flex">
        {{-- NavClient sidebar --}}
        <aside
            :style="'width: ' + (navCollapsed ? '0px' : '208px')"
            class="shrink-0 overflow-hidden transition-all duration-300 ease-in-out"
        >
            <div class="w-52 px-3 py-6 overflow-y-auto h-full">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.dashboard')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                >
                    <span class="flex w-4 shrink-0 items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                            <rect x="3" y="3" width="7" height="9" rx="1" />
                            <rect x="14" y="3" width="7" height="5" rx="1" />
                            <rect x="14" y="12" width="7" height="9" rx="1" />
                            <rect x="3" y="16" width="7" height="5" rx="1" />
                        </svg>
                    </span>
                    Dashboard
                </a>

                {{-- Collection --}}
                <div class="mt-5">
                    <div class="px-2.5 pb-1.5 text-xs font-semibold uppercase tracking-wider text-text-muted/70">Collection</div>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ route('admin.collections.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.collections.index')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <rect x="3" y="3" width="7" height="7" rx="1" />
                                        <rect x="14" y="3" width="7" height="7" rx="1" />
                                        <rect x="3" y="14" width="7" height="7" rx="1" />
                                        <rect x="14" y="14" width="7" height="7" rx="1" />
                                    </svg>
                                </span>
                                All Collections
                            </a>
                        </li>
                        @foreach($sidebarCollections ?? [] as $sidebarCollection)
                            <li>
                                <a href="{{ route('admin.collections.entries.index', $sidebarCollection) }}"
                                    class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->route('collection')?->id === $sidebarCollection->id) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                                >
                                    <span class="flex w-4 shrink-0 items-center justify-center">
                                        @if($sidebarCollection->icon)
                                            <i class="{{ $sidebarCollection->icon }} text-xs"></i>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                                <rect x="3" y="3" width="7" height="7" rx="1" />
                                                <rect x="14" y="3" width="7" height="7" rx="1" />
                                                <rect x="3" y="14" width="7" height="7" rx="1" />
                                                <rect x="14" y="14" width="7" height="7" rx="1" />
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="truncate">{{ $sidebarCollection->name }}</span>
                                </a>
                            </li>
                        @endforeach
                        {{-- Pages, Blog, and Packages menus have been removed --}}

                        <li>
                            <a href="{{ route('admin.assets.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.assets.*')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <polyline points="21 15 16 10 5 21" />
                                    </svg>
                                </span>
                                Assets
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Taxonomies --}}
                <div class="mt-5">
                    <div class="px-2.5 pb-1.5 text-xs font-semibold uppercase tracking-wider text-text-muted/70">Taxonomies</div>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ route('admin.taxonomies.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.taxonomies.index') || request()->routeIs('admin.taxonomies.create')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
                                        <path d="m15 5 6.3 6.3a2.4 2.4 0 0 1 0 3.4L17 19" />
                                        <path d="M9.586 2.586A2 2 0 0 0 8.172 2H3a1 1 0 0 0-1 1v5.172a2 2 0 0 0 .586 1.414l8.828 8.828a2 2 0 0 0 2.828 0l5.172-5.172a2 2 0 0 0 0-2.828z" />
                                        <path d="M6.5 6.5h.01" />
                                    </svg>
                                </span>
                                All Taxonomies
                            </a>
                        </li>
                        @foreach($sidebarTaxonomies ?? [] as $sidebarTax)
                            <li>
                                <a href="{{ route('admin.taxonomies.show', $sidebarTax) }}"
                                    class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->route('taxonomy')?->id === $sidebarTax->id) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                                >
                                    <span class="flex w-4 shrink-0 items-center justify-center">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-3.5 text-text-muted/80">
                                            <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z" />
                                            <path d="M7 7h.01" />
                                        </svg>
                                    </span>
                                    <span class="truncate">{{ $sidebarTax->title }}</span>
                                    <span class="ml-auto text-[10px] font-medium px-1.5 py-0.2 rounded-full bg-gray-200/80 text-text-muted shrink-0">
                                        {{ $sidebarTax->terms_count ?? $sidebarTax->terms()->count() }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Forms --}}
                <div class="mt-5">
                    <div class="px-2.5 pb-1.5 text-xs font-semibold uppercase tracking-wider text-text-muted/70">Forms</div>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ route('admin.forms.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.forms.index')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                        <line x1="12" y1="18" x2="12" y2="12" />
                                        <line x1="9" y1="15" x2="15" y2="15" />
                                    </svg>
                                </span>
                                All Forms
                            </a>
                        </li>
                        @foreach($sidebarForms ?? [] as $sidebarForm)
                            <li>
                                <a href="{{ route('admin.forms.entries', $sidebarForm) }}"
                                    class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.forms.entries') && request()->route('form')?->id === $sidebarForm->id) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                                >
                                    <span class="flex w-4 shrink-0 items-center justify-center">
                                        @if($sidebarForm->icon)
                                            <i class="{{ $sidebarForm->icon }} text-xs"></i>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                                <line x1="3" y1="9" x2="21" y2="9" />
                                                <line x1="9" y1="21" x2="9" y2="9" />
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="truncate">{{ $sidebarForm->title }}</span>
                                    @php
                                        $isCurrentFormOpen = request()->routeIs('admin.forms.entries') && (
                                            (is_object(request()->route('form')) ? request()->route('form')->id : request()->route('form')) == $sidebarForm->id
                                        );
                                    @endphp
                                    @if(($sidebarForm->entries_count ?? 0) > 0 && ! $isCurrentFormOpen)
                                        <span class="ml-auto flex size-5 shrink-0 items-center justify-center rounded-full bg-red-500 text-[11px] font-semibold leading-none text-white">{{ $sidebarForm->entries_count }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Advance --}}
                <div class="mt-5">
                    <div class="px-2.5 pb-1.5 text-xs font-semibold uppercase tracking-wider text-text-muted/70">Advance</div>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ route('admin.administrators.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.administrators.*')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    </svg>
                                </span>
                                Administrator
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.settings')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.51a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </span>
                                Settings
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.seo') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.seo')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                        <circle cx="11" cy="11" r="8" />
                                        <path d="m21 21-4.3-4.3" />
                                        <path d="m8 11 2 2 4-4" />
                                    </svg>
                                </span>
                                SEO Pro
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        {{-- Main content area --}}
        <main class="flex-1 overflow-hidden">
            <div class="h-full overflow-y-auto">
                @hasSection('content-full')
                    @yield('content-full')
                @else
                    {{-- PageShell --}}
                    <div class="bg-content-bg min-h-[calc(100%-8px)] mx-2 mt-2 px-6 lg:px-20 pt-6 rounded-t-2xl border border-content-border border-b-0 relative" style="container-type: inline-size;">

                        @yield('content')
                    </div>
                @endif
            </div>
        </main>
    </div>

    <x-admin::command-palette />
    <x-admin::asset-picker />
    <x-admin::section-picker />
    <x-admin::toast />

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function resetSubmittingState() {
                var topLoader = document.getElementById('global-submit-loader');
                if (topLoader) {
                    topLoader.classList.add('hidden');
                }
                var submittingButtons = document.querySelectorAll('[data-submitting="true"]');
                submittingButtons.forEach(function(btn) {
                    btn.removeAttribute('data-submitting');
                    btn.disabled = false;
                    btn.classList.remove('opacity-80', 'cursor-not-allowed', 'pointer-events-none');
                    var originalHtml = btn.getAttribute('data-original-html');
                    if (originalHtml) {
                        btn.innerHTML = originalHtml;
                        btn.removeAttribute('data-original-html');
                    }
                });
            }

            window.addEventListener('pageshow', function() {
                resetSubmittingState();
            });

            document.addEventListener('submit', function(e) {
                var form = e.target;
                if (!form || e.defaultPrevented) return;

                if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                    return;
                }

                var submitButtons = [];
                if (form.id) {
                    var externalButtons = document.querySelectorAll('button[form="' + form.id + '"][type="submit"], input[form="' + form.id + '"][type="submit"]');
                    externalButtons.forEach(function(b) { submitButtons.push(b); });
                }
                var innerButtons = form.querySelectorAll('button[type="submit"], button:not([type="button"]):not([type="reset"]), input[type="submit"]');
                innerButtons.forEach(function(b) { submitButtons.push(b); });

                submitButtons.forEach(function(btn) {
                    if (btn.disabled || btn.getAttribute('data-submitting') === 'true') return;
                    btn.setAttribute('data-submitting', 'true');
                    btn.setAttribute('data-original-html', btn.innerHTML);

                    btn.disabled = true;
                    btn.classList.add('opacity-80', 'cursor-not-allowed', 'pointer-events-none');

                    if (btn.tagName === 'BUTTON') {
                        var textNode = btn.querySelector('span') || btn;
                        var label = textNode.textContent ? textNode.textContent.trim() : 'Processing...';
                        if (/^(Save|Create|Update|Add)/.test(label)) {
                            label = label.replace(/^(Save|Create|Update|Add)/, function(m) {
                                return m === 'Add' ? 'Adding' : m + 'ing';
                            });
                        } else if (!label) {
                            label = 'Saving...';
                        }
                        btn.innerHTML = '<svg class="animate-spin -ml-0.5 mr-2 h-4 w-4 text-current inline-block shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>' + label + '</span>';
                    }
                });

                var topLoader = document.getElementById('global-submit-loader');
                if (!topLoader) {
                    topLoader = document.createElement('div');
                    topLoader.id = 'global-submit-loader';
                    topLoader.className = 'fixed top-0 left-0 right-0 z-[9999] h-1 bg-primary/20 overflow-hidden';
                    topLoader.innerHTML = '<div class="h-full bg-primary animate-pulse w-full"></div>';
                    document.body.appendChild(topLoader);
                } else {
                    topLoader.classList.remove('hidden');
                }
            });
        });
    </script>
</body>
</html>
