@extends('admin.layout')

@section('title', 'Administrators')

@section('content')
<div x-data="adminPage()">
    <div class="max-w-5xl mx-auto">

        {{-- PageHeader --}}
        <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-6 shrink-0 text-text-muted">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                Administrators
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.administrators.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add Administrator
                </a>
            </div>
        </header>

        {{-- DataTable --}}
        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 px-2 pb-2.5 pt-1">
                <span class="flex items-center gap-2 text-[14px] font-medium text-text-heading whitespace-nowrap shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0 text-text-muted">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    All Administrators
                </span>
                <div class="flex items-center gap-2 flex-nowrap shrink-0">
                    <div class="relative shrink-0">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-text-muted">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                        <input
                            id="administrator-search"
                            name="search"
                            type="text"
                            x-model="search"
                            :placeholder="`Search by ${filterColumnLabel.replace('Filter: ', '').toLowerCase()}...`"
                            aria-label="Search administrators"
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
                            <button type="button" @click="filterColumn = 'status'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'status' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                        <polyline points="22 4 12 14.01 9 11.01" />
                                    </svg>
                                    <span>Status</span>
                                </div>
                                <span x-show="filterColumn === 'status'" class="font-bold">✓</span>
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
                            <button type="button" @click="sortColumn = 'status'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortColumn === 'status' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 shrink-0 opacity-70"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" /><polyline points="22 4 12 14.01 9 11.01" /></svg>
                                    <span>Status</span>
                                </div>
                                <span x-show="sortColumn === 'status'" class="font-bold">✓</span>
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

            <div class="px-1.5 pb-2">
                @if ($admins->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-center px-6">
                        <img src="/empty-collection.svg" alt="No items" class="size-28 mb-3 opacity-60">
                        <p class="text-sm font-medium text-text-heading">No administrators yet</p>
                        <p class="text-xs text-text-muted mt-1">
                            <a href="{{ route('admin.administrators.create') }}" class="text-primary hover:text-primary/80 no-underline font-medium">Add your first administrator</a>
                        </p>
                    </div>
                @else
                    <template x-if="search && filteredCount === 0">
                        <div class="flex flex-col items-center justify-center py-10 text-center px-6">
                            <img src="/empty-collection.svg" alt="No items" class="size-28 mb-3 opacity-60">
                            <p class="text-sm font-medium text-text-heading">No administrators match your search</p>
                        </div>
                    </template>

                    <div class="rounded-xl ring-1 ring-content-border bg-content-bg shadow-sm overflow-hidden" x-show="!search || filteredCount > 0">
                        <div class="overflow-x-auto table-scrollbar">
                            <table class="w-full min-w-full border-separate border-spacing-y-0 text-left text-[13px]">
                                <thead>
                                    <tr class="bg-[#f9fafb]">
                                        <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border rounded-tl-xl">
                                            <button @click="sortColumn = 'name'; sortRows()" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                                Name
                                            </button>
                                        </th>
                                        <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                            <button @click="sortColumn = 'email'; sortRows()" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                                Email
                                            </button>
                                        </th>
                                        <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                            <button @click="sortColumn = 'status'; sortRows()" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                                Status
                                            </button>
                                        </th>
                                        <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                            <button @click="sortColumn = 'created'; sortRows()" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                                Created
                                            </button>
                                        </th>
                                        <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border sticky right-0 bg-[#f9fafb] z-20 text-right rounded-tr-xl">Actions</th>
                                    </tr>
                                </thead>
                                <tbody x-ref="tbody">
                                    @foreach ($admins as $index => $admin)
                                        @php $statusStr = $admin->is_active ? 'active' : 'inactive'; @endphp
                                        <tr data-sortable
                                            data-name="{{ strtolower($admin->name) }}"
                                            data-email="{{ strtolower($admin->email) }}"
                                            data-status="{{ strtolower($statusStr) }}"
                                            data-created="{{ $admin->created_at->timestamp }}"
                                            x-show="matchesSearch({{ json_encode($admin->name) }}, {{ json_encode($admin->email) }}, {{ json_encode($statusStr) }})"
                                            class="group hover:bg-[#f9fafb] transition-colors">
                                            <td class="px-4 py-3 text-text-heading font-medium border-b border-content-border group-last:border-b-0 group-last:rounded-bl-xl whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="size-8 rounded-lg overflow-hidden bg-gray-100 shrink-0 flex items-center justify-center">
                                                        @if ($admin->avatar)
                                                            <img src="{{ $admin->avatar }}" alt="{{ $admin->name }}" class="size-full object-cover">
                                                        @else
                                                            <span class="text-xs font-medium text-primary">
                                                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="text-text-heading font-medium">{{ $admin->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-text-heading border-b border-content-border group-last:border-b-0 whitespace-nowrap">{{ $admin->email }}</td>
                                            <td class="px-4 py-3 border-b border-content-border group-last:border-b-0 whitespace-nowrap">
                                                @if ($admin->is_active)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/10">
                                                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500 ring-1 ring-gray-200">
                                                        <span class="size-1.5 rounded-full bg-gray-400"></span>
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-text-heading border-b border-content-border group-last:border-b-0 whitespace-nowrap">{{ $admin->created_at->diffForHumans() }}</td>
                                            <td class="sticky right-0 bg-white group-hover:bg-[#f9fafb] group-last:rounded-br-xl z-10 px-4 py-3 text-right whitespace-nowrap transition-colors border-b border-content-border group-last:border-b-0">
                                                <div x-data="{
                                                        open: false,
                                                        menuTop: 0,
                                                        menuRight: 0,
                                                        toggle(e) {
                                                            if (this.open) {
                                                                this.open = false;
                                                                return;
                                                            }
                                                            const rect = e.currentTarget.getBoundingClientRect();
                                                            this.menuTop = rect.bottom + 4;
                                                            this.menuRight = window.innerWidth - rect.right;
                                                            this.open = true;
                                                        }
                                                    }"
                                                    @scroll.window="open = false"
                                                    @resize.window="open = false">
                                                    <button type="button"
                                                        @click.stop="toggle($event)"
                                                        class="inline-flex size-7 items-center justify-center rounded-md text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors cursor-pointer">
                                                        <svg viewBox="0 0 16 3" class="size-4" fill="currentColor">
                                                            <circle cx="2" cy="1.5" r="1.5" />
                                                            <circle cx="8" cy="1.5" r="1.5" />
                                                            <circle cx="14" cy="1.5" r="1.5" />
                                                        </svg>
                                                    </button>

                                                    <template x-teleport="body">
                                                        <div x-show="open"
                                                            x-cloak
                                                            @click.outside="open = false"
                                                            @keydown.escape.window="open = false"
                                                            x-transition:enter="transition ease-out duration-100"
                                                            x-transition:enter-start="opacity-0 scale-95"
                                                            x-transition:enter-end="opacity-100 scale-100"
                                                            x-transition:leave="transition ease-in duration-75"
                                                            x-transition:leave-start="opacity-100 scale-100"
                                                            x-transition:leave-end="opacity-0 scale-95"
                                                            class="fixed min-w-[12rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 z-[9999]"
                                                            :style="`top: ${menuTop}px; right: ${menuRight}px;`">
                                                            <a href="{{ route('admin.administrators.edit', $admin) }}"
                                                                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg cursor-pointer">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                                </svg>
                                                                <span>Edit</span>
                                                            </a>
                                                            @if ($admin->id !== auth('admin')->id())
                                                                <form method="POST" action="{{ route('admin.administrators.destroy', $admin) }}" class="w-full">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        onclick="return confirm('Are you sure you want to delete this administrator?')"
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
                                                    </template>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    [x-cloak] { display: none !important; }
</style>
<script>
    function adminPage() {
        return {
            search: '',
            filterColumn: 'all',
            sortColumn: 'name',
            sortDirection: 'asc',
            get filterColumnLabel() {
                if (this.filterColumn === 'name') return 'Filter: Name';
                if (this.filterColumn === 'email') return 'Filter: Email';
                if (this.filterColumn === 'status') return 'Filter: Status';
                return 'Filter: All';
            },
            matchesSearch(name, email, status = '') {
                if (!this.search.trim()) return true;
                const q = this.search.toLowerCase();
                if (this.filterColumn === 'name') {
                    return name.toLowerCase().includes(q);
                }
                if (this.filterColumn === 'email') {
                    return email.toLowerCase().includes(q);
                }
                if (this.filterColumn === 'status') {
                    return status.toLowerCase().includes(q);
                }
                return name.toLowerCase().includes(q) || email.toLowerCase().includes(q) || status.toLowerCase().includes(q);
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
                if (!this.search.trim()) return {{ $admins->count() }};
                let count = 0;
                @foreach ($admins as $admin)
                    if (this.matchesSearch(
                        {{ Js::from($admin->name) }},
                        {{ Js::from($admin->email) }},
                        {{ Js::from($admin->is_active ? 'active' : 'inactive') }}
                    )) count++;
                @endforeach
                return count;
            },
        };
    }
</script>
@endpush
@endsection
