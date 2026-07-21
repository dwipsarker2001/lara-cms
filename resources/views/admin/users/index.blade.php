@extends('admin.layout')

@section('title', 'Users')

@section('content')
<div x-data="userPage()">
    <div class="max-w-5xl mx-auto">

        {{-- PageHeader --}}
        <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 00-3-3.87" />
                    <path d="M16 3.13a4 4 0 010 7.75" />
                </svg>
                Users
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add User
                </a>
            </div>
        </header>

        {{-- DataTable --}}
        <div class="bg-panel-bg rounded-2xl mb-8 p-2">
            <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 px-2 pb-2.5">
                <span class="flex items-center gap-2 text-[14px] font-medium text-text-heading whitespace-nowrap shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                    </svg>
                    All Users
                </span>
                <div class="flex items-center gap-2 flex-nowrap shrink-0">
                    <div class="relative shrink-0">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-text-muted">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                        <input
                            id="user-search"
                            name="search"
                            type="text"
                            x-model="search"
                            :placeholder="`Search by ${filterColumnLabel.replace('Filter: ', '').toLowerCase()}...`"
                            aria-label="Search users"
                            class="h-8 w-44 sm:w-56 rounded-lg border border-content-border bg-content-bg pl-8 pr-3 text-[12px] text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/10 shadow-sm"
                        >
                    </div>

                    {{-- Filter Dropdown --}}
                    <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button"
                            @click="open = !open"
                            class="flex h-8 items-center gap-1.5 whitespace-nowrap rounded-lg border border-content-border bg-white px-3 text-[12px] font-medium text-text-heading hover:bg-body-bg shadow-sm transition-colors cursor-pointer">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-text-muted shrink-0">
                                <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                            </svg>
                            <span x-text="filterColumnLabel" class="whitespace-nowrap">Filter: All</span>
                            <svg class="size-3 text-text-muted shrink-0 transition-transform ml-0.5" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                            class="absolute right-0 top-full mt-2 min-w-[15rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 space-y-0.5 z-[100]">
                            <button type="button" @click="filterColumn = 'all'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'all' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70">
                                        <rect x="3" y="3" width="7" height="7" rx="1" />
                                        <rect x="14" y="3" width="7" height="7" rx="1" />
                                        <rect x="3" y="14" width="7" height="7" rx="1" />
                                        <rect x="14" y="14" width="7" height="7" rx="1" />
                                    </svg>
                                    <span>All Columns</span>
                                </div>
                                <span x-show="filterColumn === 'all'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="filterColumn = 'name'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'name' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    <span>Name</span>
                                </div>
                                <span x-show="filterColumn === 'name'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="filterColumn = 'email'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'email' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70">
                                        <rect x="3" y="4" width="18" height="16" rx="2" />
                                        <path d="m3 6 9 6 9-6" />
                                    </svg>
                                    <span>Email</span>
                                </div>
                                <span x-show="filterColumn === 'email'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="filterColumn = 'plan'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'plan' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 2 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                                    </svg>
                                    <span>Subscription Plan</span>
                                </div>
                                <span x-show="filterColumn === 'plan'" class="font-bold">✓</span>
                            </button>
                        </div>
                    </div>

                    {{-- Squared Sort Button & Popup --}}
                    <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button"
                            @click="open = !open"
                            title="Sort Table"
                            class="flex size-8 items-center justify-center rounded-lg border border-content-border bg-white text-text-muted hover:text-text-heading hover:bg-body-bg shadow-sm transition-colors cursor-pointer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0">
                                <path d="m3 16 4 4 4-4" />
                                <path d="M7 20V4" />
                                <path d="m21 8-4-4-4 4" />
                                <path d="M17 4v16" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                            class="absolute right-0 top-full mt-2 min-w-[15rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 space-y-0.5 z-[100]">
                            
                            {{-- Column Options --}}
                            <button type="button" @click="sortColumn = 'name'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortColumn === 'name' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                                    <span>Name</span>
                                </div>
                                <span x-show="sortColumn === 'name'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="sortColumn = 'email'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortColumn === 'email' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70"><rect x="3" y="4" width="18" height="16" rx="2" /><path d="m3 6 9 6 9-6" /></svg>
                                    <span>Email</span>
                                </div>
                                <span x-show="sortColumn === 'email'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="sortColumn = 'plan'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortColumn === 'plan' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 2 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" /></svg>
                                    <span>Subscription Plan</span>
                                </div>
                                <span x-show="sortColumn === 'plan'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="sortColumn = 'created'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortColumn === 'created' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span>Joined Date</span>
                                </div>
                                <span x-show="sortColumn === 'created'" class="font-bold">✓</span>
                            </button>

                            <div class="my-1 border-t border-content-border"></div>

                            {{-- Direction Options --}}
                            <button type="button" @click="sortDirection = 'asc'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortDirection === 'asc' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70">
                                        <path d="m3 8 4-4 4 4" /><path d="M7 4v16" />
                                    </svg>
                                    <span>Ascending (A-Z)</span>
                                </div>
                                <span x-show="sortDirection === 'asc'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="sortDirection = 'desc'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortDirection === 'desc' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70">
                                        <path d="m3 16 4 4 4-4" /><path d="M7 20V4" />
                                    </svg>
                                    <span>Descending (Z-A)</span>
                                </div>
                                <span x-show="sortDirection === 'desc'" class="font-bold">✓</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-[5px]">
                @if ($users->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="size-12 text-text-muted/40 mb-3">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                        <p class="text-sm font-medium text-text-heading">No users yet</p>
                        <p class="text-sm text-text-muted mt-1">
                            <a href="{{ route('admin.users.create') }}" class="text-primary hover:text-primary/80 no-underline font-medium">Add your first user</a>
                        </p>
                    </div>
                @else
                    <template x-if="search && filteredCount === 0">
                        <div class="flex flex-col items-center justify-center py-16">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="size-12 text-text-muted/40 mb-3">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                            <p class="text-sm font-medium text-text-heading">No users match your search</p>
                        </div>
                    </template>
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-0 text-left text-[13px]">
                            <thead>
                                <tr class="bg-[#f9fafb]">
                                    <th class="rounded-l-xl px-5 py-3 font-medium text-text-muted text-[12px]">Name</th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">Email</th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">Subscription Plan</th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">Avatar</th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">Joined</th>
                                    <th class="rounded-r-xl pr-2"></th>
                                </tr>
                                <tr class="h-2">
                                    <td colspan="6"></td>
                                </tr>
                            </thead>
                            <tbody x-ref="tbody">
                                @foreach ($users as $index => $user)
                                    @php $planName = $user->currentPlan()?->name ?? 'Default Plan'; @endphp
                                    <tr data-sortable
                                        data-name="{{ strtolower($user->name) }}"
                                        data-email="{{ strtolower($user->email) }}"
                                        data-plan="{{ strtolower($planName) }}"
                                        data-created="{{ $user->created_at->timestamp }}"
                                        x-show="matchesSearch({{ json_encode($user->name) }}, {{ json_encode($user->email) }}, {{ json_encode($planName) }})"
                                        class="group transition-colors hover:bg-gray-50/50">
                                        <td class="border-b border-gray-100 bg-content-bg px-5 py-3"
                                            @class([
                                                'rounded-tl-xl' => $index === 0,
                                                'rounded-bl-xl' => $index === $users->count() - 1,
                                            ])>
                                            <div class="flex items-center gap-3">
                                                <div class="size-8 rounded-lg overflow-hidden bg-gray-100 shrink-0 flex items-center justify-center">
                                                    @if ($user->avatar)
                                                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="size-full object-cover">
                                                    @else
                                                        <span class="text-xs font-medium text-primary">
                                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-text-heading font-medium">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-text-heading">{{ $user->email }}</td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3">
                                            @if ($user->currentPlan())
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary border border-primary/20">
                                                    {{ $user->currentPlan()->name }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500 border border-gray-200">
                                                    Default Plan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-text-muted text-[12px]">
                                            {{ $user->avatar ? 'Set' : '—' }}
                                        </td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-text-heading">{{ $user->created_at->diffForHumans() }}</td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-right"
                                            @class([
                                                'rounded-tr-xl' => $index === 0,
                                                'rounded-br-xl' => $index === $users->count() - 1,
                                            ])>
                                            <div class="relative"
                                                 x-data="{ open: false, top: 0, left: 0 }"
                                                 @click.outside="open = false"
                                                 @keydown.escape.window="open = false">
                                                <button type="button"
                                                    @click="open = !open; if (open) { const r = $event.currentTarget.getBoundingClientRect(); top = r.bottom + 4; left = Math.max(8, Math.min(r.right - 192, window.innerWidth - 220)); }"
                                                    class="inline-flex size-7 items-center justify-center rounded-md text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors cursor-pointer">
                                                    <svg viewBox="0 0 16 3" class="size-4" fill="currentColor">
                                                        <circle cx="2" cy="1.5" r="1.5" />
                                                        <circle cx="8" cy="1.5" r="1.5" />
                                                        <circle cx="14" cy="1.5" r="1.5" />
                                                    </svg>
                                                </button>
                                                <div x-show="open"
                                                    x-cloak
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    :style="`position: fixed; top: ${top}px; left: ${left}px;`"
                                                    class="min-w-[12rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 z-[100]">
                                                    <a href="{{ route('admin.users.edit', $user) }}"
                                                        class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg cursor-pointer">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                        </svg>
                                                        <span>Edit</span>
                                                    </a>
                                                    @if ($user->id !== auth()->id())
                                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="w-full">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                onclick="return confirm('Are you sure you want to delete this user?')"
                                                                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-red-600 hover:bg-red-50 cursor-pointer">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-red-500">
                                                                    <polyline points="3 6 5 6 21 6" />
                                                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                                                                </svg>
                                                                <span>Delete</span>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <footer class="flex justify-between flex-wrap items-center px-[18px] pt-2.5 md:pt-3 pb-2.5 antialiased">
                <div class="text-sm text-text-muted">
                    <span x-text="`${filteredCount} user${filteredCount !== 1 ? 's' : ''}`"></span>
                </div>
            </footer>
        </div>
    </div>
</div>

@push('scripts')
<style>
    [x-cloak] { display: none !important; }
</style>
<script>
    function userPage() {
        return {
            search: '',
            filterColumn: 'all',
            sortColumn: 'name',
            sortDirection: 'asc',
            get filterColumnLabel() {
                if (this.filterColumn === 'name') return 'Filter: Name';
                if (this.filterColumn === 'email') return 'Filter: Email';
                if (this.filterColumn === 'plan') return 'Filter: Plan';
                return 'Filter: All';
            },
            matchesSearch(name, email, plan = '') {
                if (!this.search.trim()) return true;
                const q = this.search.toLowerCase();
                if (this.filterColumn === 'name') {
                    return name.toLowerCase().includes(q);
                }
                if (this.filterColumn === 'email') {
                    return email.toLowerCase().includes(q);
                }
                if (this.filterColumn === 'plan') {
                    return plan.toLowerCase().includes(q);
                }
                return name.toLowerCase().includes(q) || email.toLowerCase().includes(q) || plan.toLowerCase().includes(q);
            },
            sortRows() {
                this.$nextTick(() => {
                    const tbody = this.$refs.tbody;
                    if (!tbody) return;
                    const rows = Array.from(tbody.querySelectorAll('tr[data-sortable]'));
                    rows.sort((a, b) => {
                        let valA = a.dataset[this.sortColumn] || '';
                        let valB = b.dataset[this.sortColumn] || '';
                        if (this.sortColumn === 'created') {
                            valA = parseInt(valA, 10) || 0;
                            valB = parseInt(valB, 10) || 0;
                        }
                        if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                        if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                        return 0;
                    });
                    rows.forEach(r => tbody.appendChild(r));
                });
            },
            get filteredCount() {
                if (!this.search.trim()) return {{ $users->count() }};
                let count = 0;
                @foreach ($users as $user)
                    if (this.matchesSearch(
                        {{ Js::from($user->name) }},
                        {{ Js::from($user->email) }},
                        {{ Js::from($user->currentPlan()?->name ?? 'Default Plan') }}
                    )) count++;
                @endforeach
                return count;
            },
        };
    }
</script>
@endpush
@endsection