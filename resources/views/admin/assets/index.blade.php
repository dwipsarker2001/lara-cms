@extends('admin.layout')

@section('title', 'Assets')
@section('breadcrumb', 'Assets')

@section('content')
<div x-data="assetsPage()" x-init="init()">
    <div class="max-w-5xl mx-auto">

        {{-- PageHeader --}}
        <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
            <div>
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5">
                    <svg viewBox="0 0 16 16" class="size-5 text-text-muted" fill="none" stroke="currentColor" stroke-width="1" style="stroke:currentColor">
                        <g transform="translate(1 1)" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M0.24,10.88 C0.416,12.525 1.74,13.85 3.386,14.032 C4.608,14.169 5.863,14.286 7.143,14.286 C8.422,14.286 9.677,14.169 10.9,14.032 C12.545,13.85 13.87,12.525 14.046,10.878 C14.176,9.663 14.286,8.415 14.286,7.143 C14.286,5.871 14.176,4.623 14.046,3.407 C13.87,1.761 12.545,0.437 10.9,0.253 C9.677,0.117 8.422,0 7.143,0 C5.863,0 4.608,0.117 3.386,0.253 C1.74,0.437 0.416,1.761 0.24,3.407 C0.11,4.623 0,5.871 0,7.143 C0,8.415 0.11,9.663 0.24,10.878Z" />
                            <path d="M9.641,5.842 C10.664,5.842 11.24,5.266 11.24,4.242 C11.24,3.219 10.664,2.643 9.641,2.643 C8.617,2.643 8.041,3.219 8.041,4.242 C8.041,5.266 8.617,5.842 9.641,5.842Z" />
                            <path d="M3.386,14.032 C1.74,13.85 0.416,12.525 0.24,10.878 C0.187,10.384 0.138,9.884 0.097,9.38 C1.071,8.111 2.237,7.143 3.517,7.143 C6.365,7.143 8.654,11.936 9.494,14.174 C8.721,14.241 7.937,14.286 7.143,14.286 C5.863,14.286 4.608,14.169 3.386,14.032Z" />
                            <path d="M13.837,11.75 C12.799,10.255 11.505,9.036 10.069,9.036 C9.163,9.036 8.314,9.521 7.55,10.254 C8.439,11.631 9.114,13.163 9.494,14.174 C9.967,14.133 10.436,14.084 10.9,14.032 C12.243,13.883 13.371,12.974 13.837,11.75Z" />
                        </g>
                    </svg>
                    Assets
                </h1>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button
                    type="button"
                    @click="showCreateDir = true"
                    class="inline-flex items-center justify-center whitespace-nowrap shrink-0 font-medium text-sm px-4 py-2 rounded-lg border border-content-border bg-white hover:bg-gray-50 text-text-heading shadow-sm cursor-pointer transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-plus size-[18px] mr-1.5 shrink-0 text-text-muted">
                        <path d="M12 10v6"/>
                        <path d="M9 13h6"/>
                        <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L8.6 3.3A2 2 0 0 0 7.1 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2z"/>
                    </svg>
                    Create Directory
                </button>
                <label
                    for="asset-upload-input"
                    class="inline-flex items-center justify-center whitespace-nowrap shrink-0 font-medium text-sm px-4 py-2 rounded-lg bg-primary text-white hover:opacity-90 shadow-sm transition-all cursor-pointer select-none"
                    :class="uploading ? 'opacity-75 pointer-events-none cursor-wait' : ''"
                >
                    <template x-if="!uploading">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[18px] mr-1.5 shrink-0" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                    </template>
                    <template x-if="uploading">
                        <svg class="animate-spin size-[18px] mr-1.5 shrink-0 text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="uploading ? 'Uploading...' : 'Upload'"></span>
                </label>
                <input
                    id="asset-upload-input"
                    type="file"
                    accept="image/*,.pdf,.doc,.docx,.zip"
                    class="hidden"
                    :disabled="uploading"
                    @change="uploadFile($event)"
                >
            </div>
        </header>

        {{-- DataTable --}}
        <div class="bg-panel-bg rounded-2xl p-[7px] mb-8">
            <div class="flex flex-wrap items-center justify-between gap-3 px-2 pb-2.5 pt-1">
                <div class="flex items-center gap-2 text-[14px] font-medium text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4 shrink-0 text-text-muted" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                    <nav class="flex items-center gap-1.5 flex-wrap">
                        <button
                            @click="navigateTo('')"
                            @dragenter.prevent="crumbDragEnter('')"
                            @dragleave="crumbDragLeave()"
                            @dragover.prevent
                            @drop.prevent="crumbDrop($event, '')"
                            class="transition-colors cursor-pointer"
                            :class="currentDirectory ? 'text-text-muted hover:text-primary font-medium' : 'text-text-heading font-semibold'"
                            :style="dragOverCrumb === '' ? 'background: var(--color-primary); border-radius: 4px; padding: 0 4px; color: white;' : ''"
                        >All Assets</button>

                        <template x-for="(crumb, idx) in breadcrumbs" :key="idx">
                            <span class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-text-muted shrink-0">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>

                                <template x-if="!crumb.isEllipsis">
                                    <button
                                        @click="navigateTo(crumb.path)"
                                        @dragenter.prevent="crumbDragEnter(crumb.path)"
                                        @dragleave="crumbDragLeave()"
                                        @dragover.prevent
                                        @drop.prevent="crumbDrop($event, crumb.path)"
                                        class="transition-colors cursor-pointer"
                                        :class="idx < breadcrumbs.length - 1 ? 'text-text-muted hover:text-primary font-medium' : 'text-text-heading font-semibold'"
                                        :style="dragOverCrumb === crumb.path ? 'background: var(--color-primary); border-radius: 4px; padding: 0 4px; color: white;' : ''"
                                        x-text="crumb.name"
                                    ></button>
                                </template>

                                <template x-if="crumb.isEllipsis">
                                    <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                                        <button
                                            type="button"
                                            @click="open = !open"
                                            class="inline-flex items-center justify-center px-1.5 py-0.5 rounded border border-content-border bg-content-bg hover:bg-body-bg hover:text-primary text-text-muted text-xs font-bold transition-colors cursor-pointer"
                                            title="Click to view hidden directories"
                                        >
                                            ...
                                        </button>
                                        <div
                                            x-show="open"
                                            x-cloak
                                            class="absolute left-0 top-full mt-1 min-w-[13rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 space-y-0.5 z-[100]"
                                        >
                                            <template x-for="item in crumb.hiddenParts" :key="item.path">
                                                <button
                                                    type="button"
                                                    @click="navigateTo(item.path); open = false"
                                                    class="flex w-full items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs text-text-primary hover:bg-body-bg hover:text-primary transition-colors cursor-pointer text-left"
                                                >
                                                    <svg viewBox="0 0 20 20" fill="#F59E0B" class="size-4 shrink-0">
                                                        <path d="M3.5 4.5A1.5 1.5 0 015 3h3.086a1.5 1.5 0 011.06.44l1.415 1.414A1.5 1.5 0 0011.586 5H14.5A1.5 1.5 0 0116 6.5v.378a.5.5 0 01-.5.5H4a.5.5 0 01-.5-.5V4.5z" />
                                                        <path d="M3.5 7.878V14.5A1.5 1.5 0 005 16h10a1.5 1.5 0 001.5-1.5V7.878a.5.5 0 00-.5-.5H4a.5.5 0 00-.5.5z" />
                                                    </svg>
                                                    <span class="truncate font-medium text-text-heading" x-text="item.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </span>
                        </template>
                    </nav>
                </div>
                <div class="flex items-center gap-2 flex-nowrap shrink-0">
                    {{-- Search Input --}}
                    <div class="relative shrink-0">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-text-muted">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Search assets..."
                            aria-label="Search assets"
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
                            class="absolute right-0 top-full mt-2 min-w-[14rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 space-y-0.5 z-[100]">
                            <button type="button" @click="filterColumn = 'all'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'all' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>All Columns</span>
                                <span x-show="filterColumn === 'all'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="filterColumn = 'name'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'name' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>File Name</span>
                                <span x-show="filterColumn === 'name'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="filterColumn = 'size'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'size' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Size</span>
                                <span x-show="filterColumn === 'size'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="filterColumn = 'created_at'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'created_at' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Last Modified</span>
                                <span x-show="filterColumn === 'created_at'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="filterColumn = 'width'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'width' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Width</span>
                                <span x-show="filterColumn === 'width'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="filterColumn = 'height'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'height' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Height</span>
                                <span x-show="filterColumn === 'height'" class="font-bold">✓</span>
                            </button>
                        </div>
                    </div>

                    {{-- Sort Dropdown --}}
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
                            class="absolute right-0 top-full mt-2 min-w-[14rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 space-y-0.5 z-[100]">
                            <button type="button" @click="sortField = 'name'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortField === 'name' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>File Name</span>
                                <span x-show="sortField === 'name'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="sortField = 'size'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortField === 'size' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Size</span>
                                <span x-show="sortField === 'size'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="sortField = 'created_at'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortField === 'created_at' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Last Modified</span>
                                <span x-show="sortField === 'created_at'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="sortField = 'width'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortField === 'width' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Width</span>
                                <span x-show="sortField === 'width'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="sortField = 'height'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortField === 'height' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Height</span>
                                <span x-show="sortField === 'height'" class="font-bold">✓</span>
                            </button>

                            <div class="my-1 border-t border-content-border"></div>

                            <button type="button" @click="sortDir = 'asc'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortDir === 'asc' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Ascending (A-Z)</span>
                                <span x-show="sortDir === 'asc'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="sortDir = 'desc'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortDir === 'desc' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Descending (Z-A)</span>
                                <span x-show="sortDir === 'desc'" class="font-bold">✓</span>
                            </button>
                        </div>
                    </div>

                    {{-- Column Settings Dropdown --}}
                    <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button"
                            @click="open = !open"
                            title="Column Settings"
                            class="flex size-8 items-center justify-center rounded-lg border border-content-border bg-white text-text-muted hover:text-text-heading hover:bg-body-bg shadow-sm transition-colors cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.72l-.15.1a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.51a2 2 0 0 1 1-1.72l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                            class="absolute right-0 top-full mt-2 min-w-[15rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-2 space-y-1 z-[100]">
                            <div class="px-2 py-1 text-[11px] font-semibold text-text-muted uppercase tracking-wider">
                                Display Columns
                            </div>
                            <div class="my-1 border-t border-content-border"></div>
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['checkbox']" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span>Selection Box</span>
                            </label>
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['name']" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span>File Name</span>
                            </label>
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['size']" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span>Size</span>
                            </label>
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['created_at']" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span>Last Modified</span>
                            </label>
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['width']" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span>Width</span>
                            </label>
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['height']" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span>Height</span>
                            </label>
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['actions']" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span>Actions</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-1.5 pb-2">
                <template x-if="loading">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-sm text-text-muted">Loading assets...</div>
                    </div>
                </template>

                <template x-if="!loading && filteredAssets.length === 0">
                    <div class="flex flex-col items-center justify-center py-10 text-center px-6">
                        <img src="/empty-collection.svg" alt="No items" class="size-28 mb-3 opacity-60">
                        <p class="text-sm font-medium text-text-heading">No assets yet</p>
                        <p class="text-xs text-text-muted mt-1">Upload a file or create a directory to get started.</p>
                    </div>
                </template>

                <template x-if="!loading && filteredAssets.length > 0">
                    <div class="rounded-xl ring-1 ring-content-border bg-content-bg shadow-sm overflow-hidden">
                        <div class="overflow-x-auto table-scrollbar">
                            <table class="w-full min-w-full border-separate border-spacing-y-0 text-left text-[13px]">
                                <thead>
                                    <tr class="bg-[#f9fafb]">
                                        <th x-show="visibleColumns['checkbox'] !== false" class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border rounded-tl-xl w-12 text-center">
                                            <input type="checkbox" class="size-4 rounded border-gray-300 accent-zinc-900 focus:ring-0 cursor-pointer">
                                        </th>
                                        <th x-show="visibleColumns['name'] !== false" class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                            <button @click="toggleSort('name')" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                                File
                                                <svg viewBox="0 0 14 14" fill="none" class="size-3 text-gray-300">
                                                    <path d="M7 0.75 7 13.25" stroke="currentColor" stroke-width="1" stroke-linecap="round" />
                                                    <path d="M11.086 4.836C10.269 3.202 8.635 1.567 7 0.75 5.366 1.567 3.731 3.202 2.914 4.836" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </th>
                                        <th x-show="visibleColumns['size'] !== false" class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                            <button @click="toggleSort('size')" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                                Size
                                                <svg viewBox="0 0 14 14" fill="none" class="size-3 text-gray-300">
                                                    <path d="M7 0.75 7 13.25" stroke="currentColor" stroke-width="1" stroke-linecap="round" />
                                                    <path d="M11.086 4.836C10.269 3.202 8.635 1.567 7 0.75 5.366 1.567 3.731 3.202 2.914 4.836" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </th>
                                        <th x-show="visibleColumns['created_at'] !== false" class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                            <button @click="toggleSort('created_at')" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                                Last Modified
                                                <svg viewBox="0 0 14 14" fill="none" class="size-3 text-gray-300">
                                                    <path d="M7 0.75 7 13.25" stroke="currentColor" stroke-width="1" stroke-linecap="round" />
                                                    <path d="M11.086 4.836C10.269 3.202 8.635 1.567 7 0.75 5.366 1.567 3.731 3.202 2.914 4.836" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </th>
                                        <th x-show="visibleColumns['width'] !== false" class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                            <button @click="toggleSort('width')" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                                Width
                                                <svg viewBox="0 0 14 14" fill="none" class="size-3 text-gray-300">
                                                    <path d="M7 0.75 7 13.25" stroke="currentColor" stroke-width="1" stroke-linecap="round" />
                                                    <path d="M11.086 4.836C10.269 3.202 8.635 1.567 7 0.75 5.366 1.567 3.731 3.202 2.914 4.836" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </th>
                                        <th x-show="visibleColumns['height'] !== false" class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                            <button @click="toggleSort('height')" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                                Height
                                                <svg viewBox="0 0 14 14" fill="none" class="size-3 text-gray-300">
                                                    <path d="M7 0.75 7 13.25" stroke="currentColor" stroke-width="1" stroke-linecap="round" />
                                                    <path d="M11.086 4.836C10.269 3.202 8.635 1.567 7 0.75 5.366 1.567 3.731 3.202 2.914 4.836" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </th>
                                        <th x-show="visibleColumns['actions'] !== false" class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border sticky right-0 bg-[#f9fafb] z-20 text-right rounded-tr-xl">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-if="uploading">
                                        <tr class="animate-pulse bg-primary/5">
                                            <td x-show="visibleColumns['checkbox'] !== false" class="px-4 py-3 border-b border-content-border">
                                                <div class="size-4 rounded bg-gray-200"></div>
                                            </td>
                                            <td x-show="visibleColumns['name'] !== false" class="px-4 py-3 border-b border-content-border">
                                                <div class="flex items-center gap-3">
                                                    <div class="size-8 rounded bg-primary/10 flex items-center justify-center shrink-0">
                                                        <svg class="animate-spin size-4 text-primary" viewBox="0 0 24 24" fill="none">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs font-semibold text-primary" x-text="uploadingFileName ? `Uploading ${uploadingFileName}...` : 'Uploading file...'"></span>
                                                        </div>
                                                        <div class="h-1.5 w-40 rounded-full bg-gray-200 overflow-hidden">
                                                            <div class="h-full bg-primary animate-pulse w-3/4 rounded-full"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td x-show="visibleColumns['size'] !== false" class="px-4 py-3 border-b border-content-border">
                                                <div class="h-3 w-12 rounded bg-gray-200"></div>
                                            </td>
                                            <td x-show="visibleColumns['created_at'] !== false" class="px-4 py-3 border-b border-content-border">
                                                <div class="h-3 w-20 rounded bg-gray-200"></div>
                                            </td>
                                            <td x-show="visibleColumns['width'] !== false" class="px-4 py-3 border-b border-content-border">
                                                <div class="h-3 w-8 rounded bg-gray-200"></div>
                                            </td>
                                            <td x-show="visibleColumns['height'] !== false" class="px-4 py-3 border-b border-content-border">
                                                <div class="h-3 w-8 rounded bg-gray-200"></div>
                                            </td>
                                            <td x-show="visibleColumns['actions'] !== false" class="px-4 py-3 border-b border-content-border text-right sticky right-0 bg-white">
                                                <div class="h-3 w-6 rounded bg-gray-200 ml-auto"></div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(asset, rowIndex) in paginatedAssets" :key="asset.id">
                                        <tr
                                            class="group hover:bg-[#f9fafb] transition-colors"
                                            draggable="true"
                                            @dragstart="onDragStart($event, asset)"
                                            @dragenter="onDragEnter($event, asset, rowIndex)"
                                            @dragleave="onDragLeave($event, asset)"
                                            @dragover="onDragOver($event, asset)"
                                            @drop="onDrop($event, asset)"
                                            :class="dragOverFolderId === asset.id ? 'bg-primary/5 ring-2 ring-primary/30' : ''"
                                        >
                                            <td x-show="visibleColumns['checkbox'] !== false" class="px-4 py-3 text-text-muted text-xs whitespace-nowrap min-w-[50px] border-b border-content-border group-last:border-b-0 group-last:rounded-bl-xl">
                                                <input type="checkbox" class="size-4 rounded border-gray-300 accent-zinc-900 focus:ring-0 cursor-pointer">
                                            </td>
                                            <td x-show="visibleColumns['name'] !== false" class="px-4 py-3 text-text-primary whitespace-nowrap border-b border-content-border group-last:border-b-0">
                                                <div class="flex items-center gap-3">
                                                    <template x-if="asset.is_directory">
                                                        <svg viewBox="0 0 20 20" fill="#F59E0B" class="size-8 shrink-0">
                                                            <path d="M3.5 4.5A1.5 1.5 0 015 3h3.086a1.5 1.5 0 011.06.44l1.415 1.414A1.5 1.5 0 0011.586 5H14.5A1.5 1.5 0 0116 6.5v.378a.5.5 0 01-.5.5H4a.5.5 0 01-.5-.5V4.5z" />
                                                            <path d="M3.5 7.878V14.5A1.5 1.5 0 005 16h10a1.5 1.5 0 001.5-1.5V7.878a.5.5 0 00-.5-.5H4a.5.5 0 00-.5.5z" />
                                                        </svg>
                                                    </template>
                                                    <template x-if="!asset.is_directory && asset.mime_type?.startsWith('image/')">
                                                        <div class="size-8 rounded overflow-hidden bg-gray-100 shrink-0">
                                                            <img
                                                                :src="`/storage/${asset.path}`"
                                                                :alt="asset.name"
                                                                class="size-full object-cover"
                                                                x-on:error='$el.style.display="none";$el.parentElement.innerHTML=`<div class="size-full flex items-center justify-center text-xs text-blue-600 font-medium bg-gradient-to-br from-blue-100 to-blue-200">IMG</div>`'
                                                            >
                                                        </div>
                                                    </template>
                                                    <template x-if="!asset.is_directory && !asset.mime_type?.startsWith('image/')">
                                                        <div class="size-8 rounded bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-xs text-blue-600 font-medium shrink-0">
                                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                                <path fill-rule="evenodd" d="M5.5 2a3 3 0 00-3 3v10a3 3 0 003 3h9a3 3 0 003-3V5a3 3 0 00-3-3h-9zM4 5a1.5 1.5 0 011.5-1.5h9A1.5 1.5 0 0116 5v10a1.5 1.5 0 01-1.5 1.5h-9A1.5 1.5 0 014 15V5z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    </template>
                                                    <button
                                                        @click="asset.is_directory ? navigateTo(asset.directory_path) : openPreview(asset, rowIndex)"
                                                        class="text-text-heading font-medium cursor-pointer normal-nums select-none hover:text-primary text-start"
                                                        x-text="asset.name"
                                                    ></button>
                                                </div>
                                            </td>
                                            <td x-show="visibleColumns['size'] !== false" class="px-4 py-3 text-text-primary whitespace-nowrap border-b border-content-border group-last:border-b-0" x-text="asset.is_directory ? '—' : (asset.size ?? '—')"></td>
                                            <td x-show="visibleColumns['created_at'] !== false" class="px-4 py-3 text-text-primary whitespace-nowrap border-b border-content-border group-last:border-b-0" x-text="relativeDate(asset.created_at)"></td>
                                            <td x-show="visibleColumns['width'] !== false" class="px-4 py-3 text-text-primary whitespace-nowrap border-b border-content-border group-last:border-b-0" x-text="asset.is_directory ? '—' : (asset.width ?? '—')"></td>
                                            <td x-show="visibleColumns['height'] !== false" class="px-4 py-3 text-text-primary whitespace-nowrap border-b border-content-border group-last:border-b-0" x-text="asset.is_directory ? '—' : (asset.height ?? '—')"></td>
                                            <td x-show="visibleColumns['actions'] !== false" class="sticky right-0 bg-white group-hover:bg-[#f9fafb] group-last:rounded-br-xl z-10 px-4 py-3 text-right whitespace-nowrap transition-colors border-b border-content-border group-last:border-b-0">
                                                <button
                                                    @click="openActionMenu($event, asset)"
                                                    class="inline-flex size-7 items-center justify-center rounded-md text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors cursor-pointer"
                                                >
                                                    <svg viewBox="0 0 16 3" class="size-4" fill="currentColor" aria-hidden="true">
                                                        <circle cx="2" cy="1.5" r="1.5" />
                                                        <circle cx="8" cy="1.5" r="1.5" />
                                                        <circle cx="14" cy="1.5" r="1.5" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Footer (Only shown if total assets > 10) --}}
            <footer class="flex justify-between flex-wrap items-center px-[18px] pt-2.5 md:pt-3 pb-2.5 antialiased" x-show="filteredAssets.length > 10">
                <div class="text-sm text-text-muted">
                    <span x-text="filteredAssets.length > 0 ? `${(page - 1) * perPage + 1}\u2013${Math.min(page * perPage, filteredAssets.length)} of ${filteredAssets.length}` : 'No assets'"></span>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" @click="if (page > 1) page--" :disabled="page <= 1" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-400/10 text-text-heading disabled:opacity-50 transition-colors cursor-pointer">
                        <svg viewBox="0 0 15 15" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M8.842 3.135a.5.5 0 01.023.707L5.435 7.5l3.43 3.658a.5.5 0 01-.73.684l-3.75-4a.5.5 0 010-.684l3.75-4a.5.5 0 01.707-.023" clip-rule="evenodd" /></svg>
                    </button>
                    <span class="inline-flex items-center justify-center px-3 h-8 rounded-full bg-gray-400/10 text-text-heading text-sm font-medium" x-text="page">1</span>
                    <button type="button" @click="if (page < totalPages) page++" :disabled="page >= totalPages" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-400/10 text-text-heading disabled:opacity-50 transition-colors cursor-pointer">
                        <svg viewBox="0 0 15 15" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M6.158 3.135a.5.5 0 01-.023.707L9.565 7.5l-3.43 3.658a.5.5 0 00.73.684l3.75-4a.5.5 0 000-.684l-3.75-4a.5.5 0 00-.707-.023" clip-rule="evenodd" /></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2 text-sm text-text-muted">
                    <span>Per Page</span>
                    <span class="px-2 py-1 border border-content-border rounded text-text-heading">10</span>
                </div>
            </footer>
        </div>

    </div>

    {{-- Action Menu --}}
    <div
        x-show="actionAsset"
        x-cloak
        class="fixed inset-0 z-50"
        @click="actionAsset = null"
        @keydown.escape.window="actionAsset = null"
    >
        <div
            class="absolute min-w-[12rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5"
            :style="`left: ${actionMenuX}px; top: ${actionMenuY}px;`"
            @click.stop
        >
            <template x-if="actionAsset?.is_directory">
                <button type="button" role="menuitem" @click="executeAction('open')"
                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                    <span>Open</span>
                </button>
            </template>
            <template x-if="!actionAsset?.is_directory">
                <button type="button" role="menuitem" @click="executeAction('preview')"
                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                    <span>Preview</span>
                </button>
            </template>
            <template x-if="!actionAsset?.is_directory">
                <button type="button" role="menuitem" @click="executeAction('copy')"
                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" /><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" /></svg>
                    <span>Copy URL</span>
                </button>
            </template>
            <button type="button" role="menuitem" @click="executeAction('rename')"
                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" /><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" /></svg>
                <span>Rename</span>
            </button>
            <template x-if="!actionAsset?.is_directory">
                <button type="button" role="menuitem" @click="executeAction('download')"
                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6" /><polyline points="15 3 21 3 21 9" /><line x1="10" y1="14" x2="21" y2="3" /></svg>
                    <span>Download</span>
                </button>
            </template>
            <hr class="my-1 border-content-border">
            <button type="button" role="menuitem"
                @click="executeAction('delete')"
                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-red-500"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
                <span>Delete</span>
            </button>
        </div>
    </div>

    {{-- Create Directory Dialog --}}
    <div
        x-show="showCreateDir"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
        @keydown.escape.window="showCreateDir = false"
    >
        <div
            x-show="showCreateDir"
            x-transition:enter="transition-opacity ease-linear duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/40"
            @click="showCreateDir = false"
        ></div>

        <div
            x-show="showCreateDir"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-[400px] bg-content-bg rounded-2xl border border-content-border shadow-2xl p-6 z-10"
        >
            {{-- Close Button --}}
            <button
                type="button"
                @click="showCreateDir = false"
                class="absolute top-5 right-5 size-7 flex items-center justify-center rounded-lg text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors cursor-pointer"
                title="Close"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>

            {{-- SVG Icon --}}
            <div class="mb-4">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="size-12 shrink-0">
                    <mask id="path-1-inside-1_7585_9241" fill="white">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8 3C3.58172 3 0 6.58172 0 11V19.8V19.954V32.2C0 36.6804 0 38.9206 0.871948 40.6319C1.63893 42.1372 2.86278 43.3611 4.36808 44.1281C6.07937 45 8.31958 45 12.8 45H35.2C39.6804 45 41.9206 45 43.6319 44.1281C45.1372 43.3611 46.3611 42.1372 47.1281 40.6319C48 38.9206 48 36.6804 48 32.2V19.8C48 15.3196 48 13.0794 47.1281 11.3681C46.3611 9.86278 45.1372 8.63893 43.6319 7.87195C41.9206 7 39.6804 7 35.2 7H26.5719C22.7981 7 19.2047 3 15.431 3H8Z"/>
                    </mask>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8 3C3.58172 3 0 6.58172 0 11V19.8V19.954V32.2C0 36.6804 0 38.9206 0.871948 40.6319C1.63893 42.1372 2.86278 43.3611 4.36808 44.1281C6.07937 45 8.31958 45 12.8 45H35.2C39.6804 45 41.9206 45 43.6319 44.1281C45.1372 43.3611 46.3611 42.1372 47.1281 40.6319C48 38.9206 48 36.6804 48 32.2V19.8C48 15.3196 48 13.0794 47.1281 11.3681C46.3611 9.86278 45.1372 8.63893 43.6319 7.87195C41.9206 7 39.6804 7 35.2 7H26.5719C22.7981 7 19.2047 3 15.431 3H8Z" fill="#047857"/>
                    <path d="M0.871948 40.6319L1.76295 40.1779H1.76295L0.871948 40.6319ZM4.36808 44.1281L4.82207 43.237L4.36808 44.1281ZM43.6319 44.1281L43.1779 43.237L43.6319 44.1281ZM47.1281 40.6319L46.237 40.1779L47.1281 40.6319ZM47.1281 11.3681L46.237 11.8221V11.8221L47.1281 11.3681ZM43.6319 7.87195L43.1779 8.76295V8.76295L43.6319 7.87195ZM1 11C1 7.13401 4.13401 4 8 4V2C3.02944 2 -1 6.02944 -1 11H1ZM1 19.8V11H-1V19.8H1ZM1 19.954V19.8H-1V19.954H1ZM1 32.2V19.954H-1V32.2H1ZM1.76295 40.1779C1.41078 39.4868 1.20961 38.6451 1.10567 37.373C1.00078 36.0891 1 34.4567 1 32.2H-1C-1 34.4237 -1.00078 36.1516 -0.887685 37.5358C-0.773639 38.9317 -0.538833 40.0658 -0.0190584 41.0859L1.76295 40.1779ZM4.82207 43.237C3.50493 42.5659 2.43407 41.4951 1.76295 40.1779L-0.0190589 41.0859C0.843802 42.7794 2.22063 44.1562 3.91409 45.0191L4.82207 43.237ZM12.8 44C10.5433 44 8.9109 43.9992 7.62705 43.8943C6.35487 43.7904 5.51325 43.5892 4.82207 43.237L3.91409 45.0191C4.9342 45.5388 6.06833 45.7736 7.46418 45.8877C8.84837 46.0008 10.5763 46 12.8 46V44ZM35.2 44H12.8V46H35.2V44ZM43.1779 43.237C42.4868 43.5892 41.6451 43.7904 40.373 43.8943C39.0891 43.9992 37.4567 44 35.2 44V46C37.4237 46 39.1516 46.0008 40.5358 45.8877C41.9317 45.7736 43.0658 45.5388 44.0859 45.0191L43.1779 43.237ZM46.237 40.1779C45.5659 41.4951 44.4951 42.5659 43.1779 43.237L44.0859 45.0191C45.7794 44.1562 47.1562 42.7794 48.0191 41.0859L46.237 40.1779ZM47 32.2C47 34.4567 46.9992 36.0891 46.8943 37.373C46.7904 38.6451 46.5892 39.4868 46.237 40.1779L48.0191 41.0859C48.5388 40.0658 48.7736 38.9317 48.8877 37.5358C49.0008 36.1516 49 34.4237 49 32.2H47ZM47 19.8V32.2H49V19.8H47ZM46.237 11.8221C46.5892 12.5132 46.7904 13.3549 46.8943 14.627C46.9992 15.9109 47 17.5433 47 19.8H49C49 17.5763 49.0008 15.8484 48.8877 14.4642C48.7736 13.0683 48.5388 11.9342 48.0191 10.9141L46.237 11.8221ZM43.1779 8.76295C44.4951 9.43407 45.5659 10.5049 46.237 11.8221L48.0191 10.9141C47.1562 9.22063 45.7794 7.8438 44.0859 6.98094L43.1779 8.76295ZM35.2 8C37.4567 8 39.0891 8.00078 40.373 8.10567C41.6451 8.20961 42.4868 8.41078 43.1779 8.76295L44.0859 6.98094C43.0658 6.46117 41.9317 6.22636 40.5358 6.11231C39.1516 5.99922 37.4237 6 35.2 6V8ZM26.5719 8H35.2V6H26.5719V8ZM8 4H15.431V2H8V4ZM26.5719 6C24.9881 6 23.3815 5.15439 21.4786 4.12118C19.698 3.15439 17.6209 2 15.431 2V4C17.0148 4 18.6213 4.84561 20.5243 5.87882C22.3049 6.84561 24.3819 8 26.5719 8V6Z" fill="url(#path-1-inside-1_7585_9241)" mask="url(#path-1-inside-1_7585_9241)"/>
                    <path d="M3 14C3 11.7909 4.79086 10 7 10H41C43.2091 10 45 11.7909 45 14V38C45 40.2091 43.2091 42 41 42H7C4.79086 42 3 40.2091 3 38V14Z" fill="white"/>
                    <rect x="0.5" y="13.5" width="47" height="31" rx="7.5" fill="#10B981" stroke="url(#paint1_linear_7585_9241)"/>
                    <rect opacity="0.2" y="13" width="48" height="32" rx="8" fill="url(#paint2_linear_7585_9241)"/>
                    <defs>
                        <linearGradient id="paint0_linear_7585_9241" x1="24" y1="3" x2="24" y2="45" gradientUnits="userSpaceOnUse">
                            <stop stop-color="white" stop-opacity="0.12"/>
                            <stop offset="1" stop-color="white" stop-opacity="0"/>
                        </linearGradient>
                        <linearGradient id="paint1_linear_7585_9241" x1="24" y1="13" x2="24" y2="45" gradientUnits="userSpaceOnUse">
                            <stop stop-color="white" stop-opacity="0.12"/>
                            <stop offset="1" stop-color="white" stop-opacity="0"/>
                        </linearGradient>
                        <linearGradient id="paint2_linear_7585_9241" x1="0" y1="13" x2="33.0094" y2="43.9225" gradientUnits="userSpaceOnUse">
                            <stop stop-color="white"/>
                            <stop offset="1" stop-color="white" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
            </div>

            {{-- Title & Description --}}
            <div class="mb-5">
                <h3 class="text-lg font-bold text-text-heading leading-tight">Create directory</h3>
                <p class="text-sm text-text-muted mt-1">Please enter a name for this directory.</p>
            </div>

            {{-- Form Input --}}
            <div class="mb-6">
                <input
                    type="text"
                    x-model="newDirName"
                    @keydown.enter="createDirectory()"
                    x-ref="dirInput"
                    placeholder="e.g. Website design"
                    class="w-full rounded-xl border border-content-border bg-content-bg px-4 py-3 text-sm text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-xs"
                >
            </div>

            {{-- Footer Side-by-Side Action Buttons --}}
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    @click="showCreateDir = false"
                    class="flex-1 py-3 rounded-xl border border-content-border bg-content-bg text-sm font-semibold text-text-heading hover:bg-body-bg transition-colors cursor-pointer text-center"
                >Cancel</button>
                <button
                    type="button"
                    @click="createDirectory()"
                    class="flex-1 py-3 rounded-xl bg-primary text-sm font-semibold text-white hover:opacity-90 active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed shadow-sm shadow-primary/20 transition-all cursor-pointer text-center"
                >Confirm</button>
            </div>
        </div>
    </div>

    {{-- Rename Dialog --}}
    <div
        x-show="renamingAsset"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
        @keydown.escape.window="renamingAsset = null"
    >
        <div
            x-show="renamingAsset"
            x-transition:enter="transition-opacity ease-linear duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/40"
            @click="renamingAsset = null"
        ></div>

        <div
            x-show="renamingAsset"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-[420px] bg-content-bg rounded-2xl border border-content-border shadow-2xl p-6 z-10"
        >
            <button
                type="button"
                @click="renamingAsset = null"
                class="absolute top-5 right-5 size-7 flex items-center justify-center rounded-lg text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors cursor-pointer"
                title="Close"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>

            <div class="flex items-center gap-3 mb-5">
                <div class="size-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-edit-3">
                        <path d="M12 20h9"/>
                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-text-heading leading-snug">Rename Asset</h3>
                    <p class="text-xs text-text-muted mt-0.5">Enter a new name for this asset.</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-medium text-text-heading mb-1.5">New Name</label>
                <input
                    type="text"
                    x-model="renameValue"
                    @keydown.enter="doRename()"
                    placeholder="Enter new name..."
                    class="w-full rounded-xl border border-content-border bg-content-bg px-3.5 py-2.5 text-sm text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-xs"
                >
            </div>

            <div class="flex items-center justify-end gap-2.5">
                <button type="button" @click="renamingAsset = null"
                    class="px-4 py-2 text-xs font-semibold rounded-xl text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors cursor-pointer"
                >Cancel</button>
                <button type="button" @click="doRename()"
                    class="px-5 py-2 text-xs font-semibold rounded-xl bg-primary text-white hover:opacity-90 active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed shadow-sm shadow-primary/20 transition-all cursor-pointer"
                >Save Changes</button>
            </div>
        </div>
    </div>

    {{-- Delete Dialog --}}
    <div
        x-show="deletingAsset"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
        @keydown.escape.window="deletingAsset = null"
    >
        <div
            x-show="deletingAsset"
            x-transition:enter="transition-opacity ease-linear duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/40"
            @click="deletingAsset = null"
        ></div>

        <div
            x-show="deletingAsset"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-[400px] bg-content-bg rounded-2xl border border-content-border shadow-2xl p-6 z-10"
        >
            {{-- Close Button --}}
            <button
                type="button"
                @click="deletingAsset = null"
                class="absolute top-5 right-5 size-7 flex items-center justify-center rounded-lg text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors cursor-pointer"
                title="Close"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>

            {{-- Top Red Icon Badge --}}
            <div class="mb-4">
                <div class="size-12 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2">
                        <path d="M3 6h18"/>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                        <line x1="10" y1="11" x2="10" y2="17"/>
                        <line x1="14" y1="11" x2="14" y2="17"/>
                    </svg>
                </div>
            </div>

            {{-- Title & Description --}}
            <div class="mb-6">
                <h3 class="text-lg font-bold text-text-heading leading-tight">Delete asset</h3>
                <p class="text-sm text-text-muted mt-1">Are you sure you want to delete this asset?</p>
            </div>

            {{-- Footer Side-by-Side Action Buttons --}}
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    @click="deletingAsset = null"
                    class="flex-1 py-3 rounded-xl border border-content-border bg-content-bg text-sm font-semibold text-text-heading hover:bg-body-bg transition-colors cursor-pointer text-center"
                >Cancel</button>
                <button
                    type="button"
                    @click="confirmDelete()"
                    class="flex-1 py-3 rounded-xl bg-red-600 text-sm font-semibold text-white hover:bg-red-700 active:scale-[0.98] shadow-sm shadow-red-500/20 transition-all cursor-pointer text-center"
                >Delete</button>
            </div>
        </div>
    </div>

    {{-- Full-screen AssetPreview --}}
    <div
        x-show="previewAsset"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm"
        @keydown.escape.window="previewAsset = null"
        @keydown.arrow-left.window="previewPrev()"
        @keydown.arrow-right.window="previewNext()"
    >
        <div
            class="relative flex max-h-[90vh] max-w-[90vw] bg-content-bg rounded-2xl shadow-2xl overflow-hidden"
            @click.outside="previewAsset = null"
        >
            <div class="flex flex-col md:flex-row">
                <div class="relative flex items-center justify-center bg-gray-950 min-h-[300px] md:min-h-[500px] md:w-[600px]">
                    <button
                        x-show="previewIndex > 0"
                        @click="previewPrev()"
                        class="absolute left-3 top-1/2 -translate-y-1/2 z-10 size-10 flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60 transition-colors"
                    >
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button
                        x-show="previewIndex < filteredAssets.filter(a => !a.is_directory).length - 1"
                        @click="previewNext()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 z-10 size-10 flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60 transition-colors"
                    >
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <template x-if="previewAsset?.mime_type?.startsWith('image/')">
                        <img
                            :src="`/storage/${previewAsset.path}`"
                            :alt="previewAsset.name"
                            class="max-h-[500px] max-w-full object-contain"
                        >
                    </template>
                    <template x-if="!previewAsset?.mime_type?.startsWith('image/')">
                        <div class="size-24 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-10 text-blue-500">
                                <path fill-rule="evenodd" d="M5.5 2a3 3 0 00-3 3v10a3 3 0 003 3h9a3 3 0 003-3V5a3 3 0 00-3-3h-9zM4 5a1.5 1.5 0 011.5-1.5h9A1.5 1.5 0 0116 5v10a1.5 1.5 0 01-1.5 1.5h-9A1.5 1.5 0 014 15V5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </template>
                </div>
                <div class="relative w-full md:w-72 p-6 space-y-5">
                    <button
                        @click="previewAsset = null"
                        class="absolute top-0 right-0 m-3 size-7 flex items-center justify-center rounded-md bg-gray-200 text-text-muted hover:bg-gray-300 transition-colors"
                    >
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                    <div class="mt-4">
                        <h2 class="text-sm font-medium text-text-muted uppercase tracking-wide">File</h2>
                        <p class="text-sm text-text-heading mt-1 break-all" x-text="previewAsset?.name"></p>
                    </div>
                    <template x-if="previewAsset?.size">
                        <div>
                            <h2 class="text-sm font-medium text-text-muted uppercase tracking-wide">Size</h2>
                            <p class="text-sm text-text-heading mt-1" x-text="previewAsset?.size"></p>
                        </div>
                    </template>
                    <template x-if="previewAsset?.width && previewAsset?.height">
                        <div>
                            <h2 class="text-sm font-medium text-text-muted uppercase tracking-wide">Dimensions</h2>
                            <p class="text-sm text-text-heading mt-1" x-text="previewAsset?.width + ' \u00d7 ' + previewAsset?.height"></p>
                        </div>
                    </template>
                    <div>
                        <h2 class="text-sm font-medium text-text-muted uppercase tracking-wide">Last Modified</h2>
                        <p class="text-sm text-text-heading mt-1" x-text="formatDate(previewAsset?.created_at)"></p>
                    </div>
                    <template x-if="previewAsset?.mime_type">
                        <div>
                            <h2 class="text-sm font-medium text-text-muted uppercase tracking-wide">Type</h2>
                            <p class="text-sm text-text-heading mt-1" x-text="previewAsset?.mime_type"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function assetsPage() {
        return {
            assets: [],
            currentDirectory: '',
            showCreateDir: false,
            newDirName: '',
            previewAsset: null,
            previewIndex: -1,
            renamingAsset: null,
            renameValue: '',
            deletingAsset: null,
            actionAsset: null,
            actionMenuX: 0,
            actionMenuY: 0,
            sortField: 'name',
            sortDir: 'asc',
            search: '',
            filterColumn: 'all',
            page: 1,
            perPage: 10,
            visibleColumns: {
                checkbox: true,
                name: true,
                size: true,
                created_at: true,
                width: true,
                height: true,
                actions: true,
            },
            loading: true,
            uploading: false,
            uploadingFileName: '',
            dragOverFolderId: null,
            dragOverCrumb: null,
            fileAssets: [],

            get filterColumnLabel() {
                if (this.filterColumn === 'all') return 'Filter: All';
                const labels = {
                    name: 'File Name',
                    size: 'Size',
                    created_at: 'Last Modified',
                    width: 'Width',
                    height: 'Height',
                };
                return 'Filter: ' + (labels[this.filterColumn] || this.filterColumn);
            },

            get directoryParts() {
                if (!this.currentDirectory) return [];
                return this.currentDirectory.split('/');
            },

            get breadcrumbs() {
                if (!this.currentDirectory) return [];
                const parts = this.currentDirectory.split('/');
                if (parts.length <= 3) {
                    return parts.map((part, idx) => ({
                        name: part,
                        path: parts.slice(0, idx + 1).join('/'),
                        isEllipsis: false,
                    }));
                }
                const firstPart = parts[0];
                const lastTwoParts = parts.slice(-2);
                const hiddenParts = parts.slice(1, -2).map((part, idx) => ({
                    name: part,
                    path: parts.slice(0, idx + 2).join('/'),
                }));

                return [
                    {
                        name: firstPart,
                        path: firstPart,
                        isEllipsis: false,
                    },
                    {
                        name: '...',
                        isEllipsis: true,
                        hiddenParts: hiddenParts,
                    },
                    {
                        name: lastTwoParts[0],
                        path: parts.slice(0, -1).join('/'),
                        isEllipsis: false,
                    },
                    {
                        name: lastTwoParts[1],
                        path: parts.join('/'),
                        isEllipsis: false,
                    },
                ];
            },

            get totalPages() {
                return Math.ceil(this.filteredAssets.length / this.perPage) || 1;
            },

            get paginatedAssets() {
                if (this.filteredAssets.length <= this.perPage) {
                    return this.filteredAssets;
                }
                const start = (this.page - 1) * this.perPage;
                return this.filteredAssets.slice(start, start + this.perPage);
            },

            get filteredAssets() {
                let arr = [...this.assets];
                if (this.search.trim()) {
                    const q = this.search.toLowerCase().trim();
                    if (this.filterColumn === 'all') {
                        arr = arr.filter(a =>
                            (a.name && String(a.name).toLowerCase().includes(q)) ||
                            (a.size && String(a.size).toLowerCase().includes(q)) ||
                            (a.created_at && String(a.created_at).toLowerCase().includes(q)) ||
                            (a.width && String(a.width).toLowerCase().includes(q)) ||
                            (a.height && String(a.height).toLowerCase().includes(q))
                        );
                    } else {
                        arr = arr.filter(a => {
                            const val = a[this.filterColumn];
                            return val !== null && val !== undefined && String(val).toLowerCase().includes(q);
                        });
                    }
                }
                const dir = this.sortDir === 'asc' ? 1 : -1;
                arr.sort((a, b) => {
                    if (a.is_directory !== b.is_directory) return a.is_directory ? -1 : 1;
                    const aVal = a[this.sortField] ?? '';
                    const bVal = b[this.sortField] ?? '';
                    return aVal < bVal ? -dir : aVal > bVal ? dir : 0;
                });
                return arr;
            },

            toggleSort(field) {
                if (this.sortField === field) {
                    this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortField = field;
                    this.sortDir = 'asc';
                }
            },

            async init() {
                await this.loadAssets();
                this.$nextTick(() => {
                    if (this.$refs.dirInput) setTimeout(() => this.$refs.dirInput.focus(), 100);
                });
            },

            async loadAssets() {
                this.loading = true;
                const params = new URLSearchParams({ directory: this.currentDirectory });
                const res = await fetch(`{{ route("admin.assets.list") }}?${params}`);
                const data = await res.json();
                this.assets = data.assets;
                this.fileAssets = this.assets.filter(a => !a.is_directory);
                this.loading = false;
            },

            async navigateTo(dir) {
                this.currentDirectory = dir;
                await this.loadAssets();
            },

            async uploadFile(event) {
                const file = event.target.files?.[0];
                if (!file) return;
                this.uploading = true;
                this.uploadingFileName = file.name;
                const formData = new FormData();
                formData.append('file', file);
                formData.append('directory', this.currentDirectory);
                try {
                    const res = await fetch('{{ route("admin.assets.store") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData,
                    });
                    if (res.ok) {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: `File "${file.name}" uploaded successfully.`, type: 'success' }
                        }));
                    } else {
                        const data = await res.json().catch(() => ({}));
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: data.message || 'Failed to upload file.', type: 'error' }
                        }));
                    }
                } catch (e) {
                    console.error('Upload error:', e);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Failed to upload file.', type: 'error' }
                    }));
                } finally {
                    this.uploading = false;
                    this.uploadingFileName = '';
                    event.target.value = '';
                    await this.loadAssets();
                }
            },

            async createDirectory() {
                const name = this.newDirName.trim();
                if (!name) return;
                const res = await fetch('{{ route("admin.assets.directory") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ name, directory: this.currentDirectory }),
                });
                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message || 'Failed to create directory.', type: 'error' }
                    }));
                    return;
                }
                this.showCreateDir = false;
                this.newDirName = '';
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: `Directory "${name}" created successfully.`, type: 'success' }
                }));
                await this.loadAssets();
            },

            openPreview(asset, index) {
                this.previewAsset = asset;
                this.previewIndex = index !== undefined ? index : this.fileAssets.indexOf(asset);
            },

            previewPrev() {
                if (this.previewIndex > 0) {
                    this.previewIndex--;
                    this.previewAsset = this.fileAssets[this.previewIndex];
                }
            },

            previewNext() {
                if (this.previewIndex < this.fileAssets.length - 1) {
                    this.previewIndex++;
                    this.previewAsset = this.fileAssets[this.previewIndex];
                }
            },

            openActionMenu(event, asset) {
                const rect = event.currentTarget.getBoundingClientRect();
                this.actionMenuX = rect.left;
                this.actionMenuY = rect.bottom + 4;
                this.actionAsset = asset;
            },

            executeAction(type) {
                const asset = this.actionAsset;
                this.actionAsset = null;
                if (!asset) return;

                if (type === 'open') {
                    this.navigateTo(asset.directory_path);
                } else if (type === 'preview') {
                    this.openPreview(asset);
                } else if (type === 'download') {
                    const link = document.createElement('a');
                    link.href = `/admin/assets/${asset.id}/file?download=1`;
                    link.download = asset.name || 'download';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else if (type === 'copy') {
                    this.copyUrl(asset);
                } else if (type === 'rename') {
                    this.startRename(asset);
                } else if (type === 'delete') {
                    this.deletingAsset = asset;
                }
            },

            copyUrl(asset) {
                navigator.clipboard?.writeText(`${window.location.origin}/admin/assets/${asset.id}/file`).then(() => {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Link copied to clipboard.', type: 'success' }
                    }));
                }).catch(() => {});
            },

            startRename(asset) {
                this.renamingAsset = asset;
                this.renameValue = asset.name;
            },

            async doRename() {
                const name = this.renameValue.trim();
                if (!name || !this.renamingAsset) return;
                const res = await fetch(`/admin/assets/${this.renamingAsset.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ name }),
                });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: `Renamed to "${name}" successfully.`, type: 'success' }
                    }));
                }
                this.renamingAsset = null;
                this.renameValue = '';
                await this.loadAssets();
            },

            async confirmDelete() {
                if (!this.deletingAsset) return;
                const assetName = this.deletingAsset.name;
                const res = await fetch(`/admin/assets/${this.deletingAsset.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                });
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: `"${assetName}" deleted successfully.`, type: 'success' }
                    }));
                }
                this.deletingAsset = null;
                await this.loadAssets();
            },

            onDragStart(event, asset) {
                event.dataTransfer.setData('text/plain', JSON.stringify({ assetId: asset.id }));
                event.dataTransfer.effectAllowed = 'move';
            },

            onDragEnter(event, asset) {
                if (!asset.is_directory) return;
                event.preventDefault();
                this.dragOverFolderId = asset.id;
            },

            onDragLeave(event, asset) {
                if (!asset.is_directory) return;
                event.preventDefault();
                this.dragOverFolderId = null;
            },

            onDragOver(event, asset) {
                if (!asset.is_directory) return;
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
            },

            async onDrop(event, asset) {
                if (!asset.is_directory) return;
                event.preventDefault();
                event.stopPropagation();
                this.dragOverFolderId = null;
                const raw = event.dataTransfer.getData('text/plain');
                try {
                    const d = JSON.parse(raw);
                    if (d.assetId && d.assetId !== asset.id) {
                        const targetDir = asset.directory_path;
                        const res = await fetch(`/admin/assets/${d.assetId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ directory: targetDir }),
                        });
                        if (res.ok) {
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: { message: `Moved to "${asset.name}" successfully.`, type: 'success' }
                            }));
                        } else {
                            const data = await res.json().catch(() => ({}));
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: { message: data.message || 'Failed to move asset.', type: 'error' }
                            }));
                        }
                        await this.loadAssets();
                    }
                } catch (e) {
                    console.error('Drop error:', e);
                }
            },

            crumbDragEnter(path) {
                this.dragOverCrumb = path;
            },

            crumbDragLeave() {
                this.dragOverCrumb = null;
            },

            async crumbDrop(event, path) {
                event.preventDefault();
                this.dragOverCrumb = null;
                const raw = event.dataTransfer.getData('text/plain');
                try {
                    const d = JSON.parse(raw);
                    if (d.assetId) {
                        const res = await fetch(`/admin/assets/${d.assetId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ directory: path }),
                        });
                        if (res.ok) {
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: { message: 'Asset moved successfully.', type: 'success' }
                            }));
                        } else {
                            const data = await res.json().catch(() => ({}));
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: { message: data.message || 'Failed to move asset.', type: 'error' }
                            }));
                        }
                        await this.loadAssets();
                    }
                } catch (e) {
                    console.error('Crumb drop error:', e);
                }
            },

            formatDate(iso) {
                if (!iso) return '\u2014';
                const d = new Date(iso);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            },

            relativeDate(iso) {
                if (!iso) return '\u2014';
                const date = new Date(iso);
                const now = new Date();
                const diffMs = now.getTime() - date.getTime();
                const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                if (diffDays === 0) return 'Today';
                if (diffDays === 1) return 'Yesterday';
                if (diffDays < 30) return diffDays + ' days ago';
                if (diffDays < 365) {
                    const months = Math.floor(diffDays / 30);
                    return months + ' month' + (months > 1 ? 's' : '') + ' ago';
                }
                const years = Math.floor(diffDays / 365);
                return years + ' year' + (years > 1 ? 's' : '') + ' ago';
            },
        };
    }
</script>
@endpush
