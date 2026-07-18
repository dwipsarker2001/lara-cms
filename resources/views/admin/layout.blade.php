<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>window.FA_ICONS = {{ Js::from(json_decode(file_get_contents(public_path('fa-icons.json')))) }};</script>
    @stack('styles')
</head>
<body class="admin-root antialiased bg-header-bg text-text-primary min-h-full" x-data="{ navCollapsed: {{ (request()->routeIs('admin.pages.editor') || request()->routeIs('admin.posts.editor') || request()->routeIs('admin.layouts.editor') || request()->routeIs('admin.packages.editor') || request()->routeIs('admin.forms.editor') || request()->routeIs('admin.collections.entries.editor')) ? 'true' : 'false' }}, userMenuOpen: false }">
    {{-- Fixed header --}}
    <header class="fixed top-0 left-0 right-0 h-14 px-4 flex items-center gap-3 z-[1] bg-header-bg text-header-text">
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
                            <a href="{{ route('admin.collections.create') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.collections.create') || request()->routeIs('admin.collections.edit')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                        <path d="M8 3v10M3 8h10" />
                                    </svg>
                                </span>
                                Create
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
                        <li>
                            <a href="{{ route('admin.pages.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.pages.*')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                        <line x1="16" y1="13" x2="8" y2="13" />
                                        <line x1="16" y1="17" x2="8" y2="17" />
                                        <polyline points="10 9 9 9 8 9" />
                                    </svg>
                                </span>
                                Pages
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.posts.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors {{ request()->routeIs('admin.posts.*') ? 'text-text-heading bg-gray-200 font-semibold' : 'text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium' }}"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                                    </svg>
                                </span>
                                Blog
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.packages.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors {{ request()->routeIs('admin.packages.*') ? 'text-text-heading bg-gray-200 font-semibold' : 'text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium' }}"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <path d="M16.5 9.4 7.55 4.24" />
                                        <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" />
                                        <polyline points="3.29 7 12 12 20.71 7" />
                                        <line x1="12" y1="22" x2="12" y2="12" />
                                    </svg>
                                </span>
                                Packages
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.layouts.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.layouts.*')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                        <line x1="3" y1="9" x2="21" y2="9" />
                                        <line x1="9" y1="21" x2="9" y2="9" />
                                    </svg>
                                </span>
                                Layouts
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.taxonomies.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.taxonomies.*')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <line x1="4" y1="9" x2="20" y2="9" />
                                        <line x1="4" y1="15" x2="20" y2="15" />
                                        <line x1="10" y1="3" x2="8" y2="21" />
                                        <line x1="16" y1="3" x2="14" y2="21" />
                                    </svg>
                                </span>
                                Taxonomies
                            </a>
                        </li>
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

                {{-- Forms --}}
                <div class="mt-5">
                    <div class="px-2.5 pb-1.5 text-xs font-semibold uppercase tracking-wider text-text-muted/70">Forms</div>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="{{ route('admin.forms.create') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.forms.create') || request()->routeIs('admin.forms.editor')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                        <path d="M8 3v10M3 8h10" />
                                    </svg>
                                </span>
                                Create
                            </a>
                        </li>
                        @foreach($sidebarForms ?? [] as $sidebarForm)
                            <li>
                                <a href="{{ route('admin.forms.entries', $sidebarForm) }}"
                                    class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.forms.entries') && request()->route('form')?->id === $sidebarForm->id) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                                >
                                    <span class="flex w-4 shrink-0 items-center justify-center">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                            <line x1="3" y1="9" x2="21" y2="9" />
                                            <line x1="9" y1="21" x2="9" y2="9" />
                                        </svg>
                                    </span>
                                    {{ $sidebarForm->title }}
                                    @if(($sidebarForm->entries_count ?? 0) > 0)
                                        <span class="ml-auto text-[11px] font-semibold bg-primary/10 text-primary px-1.5 py-0.5 rounded-full">{{ $sidebarForm->entries_count }}</span>
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
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <line x1="21" x2="14" y1="4" y2="4" /><line x1="10" x2="3" y1="4" y2="4" />
                                        <line x1="21" x2="12" y1="12" y2="12" /><line x1="8" x2="3" y1="12" y2="12" />
                                        <line x1="21" x2="16" y1="20" y2="20" /><line x1="12" x2="3" y1="20" y2="20" />
                                        <line x1="14" y1="2" x2="14" y2="6" /><line x1="8" y1="10" x2="8" y2="14" />
                                        <line x1="16" y1="18" x2="16" y2="22" />
                                    </svg>
                                </span>
                                Preference
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.seo') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.seo')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                                        <polyline points="16 7 22 7 22 13" />
                                    </svg>
                                </span>
                                SEO Pro
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.dynamic-blocks.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.dynamic-blocks.*')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <rect x="3" y="3" width="18" height="18" rx="2" />
                                        <path d="M21 12H3" />
                                    </svg>
                                </span>
                                Blocks
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
</body>
</html>
