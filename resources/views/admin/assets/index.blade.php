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
                <template x-if="currentDirectory">
                    <nav class="flex items-center gap-1.5 text-sm text-text-muted mt-1">
                        <span class="flex items-center gap-1.5">
                            <button
                                @click="navigateTo('')"
                                class="transition-colors hover:text-primary"
                                :class="currentDirectory ? 'hover:text-primary' : 'text-text-heading font-medium'"
                            >Assets</button>
                        </span>
                        <template x-for="(part, idx) in directoryParts" :key="idx">
                            <span class="flex items-center gap-1.5">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-gray-300">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                                <button
                                    @click="navigateTo(directoryParts.slice(0, idx + 1).join('/'))"
                                    @dragenter.prevent="crumbDragEnter(directoryParts.slice(0, idx + 1).join('/'))"
                                    @dragleave="crumbDragLeave()"
                                    @dragover.prevent
                                    @drop.prevent="crumbDrop($event, directoryParts.slice(0, idx + 1).join('/'))"
                                    class="transition-colors"
                                    :class="idx < directoryParts.length - 1 ? 'hover:text-primary' : 'text-text-heading font-medium'"
                                    :style="dragOverCrumb === directoryParts.slice(0, idx + 1).join('/') ? 'background: var(--color-primary); border-radius: 4px; padding: 0 4px;' : ''"
                                    x-text="part"
                                ></button>
                            </span>
                        </template>
                    </nav>
                </template>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button
                    type="button"
                    @click="showCreateDir = true"
                    class="inline-flex items-center justify-center whitespace-nowrap shrink-0 font-medium text-sm px-4 py-2 rounded-lg border border-content-border bg-gradient-to-b from-white to-gray-50 hover:to-gray-100 text-text-heading shadow-sm cursor-pointer transition-colors"
                >
                    <svg viewBox="0 0 20 20" fill="#F59E0B" class="size-[18px] mr-1.5 shrink-0">
                        <path d="M3.5 4.5A1.5 1.5 0 015 3h3.086a1.5 1.5 0 011.06.44l1.415 1.414A1.5 1.5 0 0011.586 5H14.5A1.5 1.5 0 0116 6.5v.378a.5.5 0 01-.5.5H4a.5.5 0 01-.5-.5V4.5z" />
                        <path d="M3.5 7.878V14.5A1.5 1.5 0 005 16h10a1.5 1.5 0 001.5-1.5V7.878a.5.5 0 00-.5-.5H4a.5.5 0 00-.5.5z" />
                    </svg>
                    Create Directory
                </button>
                <label
                    for="asset-upload-input"
                    class="inline-flex items-center justify-center whitespace-nowrap shrink-0 font-medium text-sm px-4 py-2 rounded-lg bg-primary text-white hover:opacity-90 shadow-sm cursor-pointer transition-opacity"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[18px] mr-1.5 shrink-0" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                        <polyline points="17 8 12 3 7 8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                    </svg>
                    Upload
                </label>
                <input
                    id="asset-upload-input"
                    type="file"
                    accept="image/*,.pdf,.doc,.docx,.zip"
                    class="hidden"
                    @change="uploadFile($event)"
                >
            </div>
        </header>

        {{-- DataTable --}}
        <div class="bg-panel-bg rounded-2xl mb-8 p-2">
            <div class="flex items-center justify-between px-2 pb-2.5">
                <span class="flex items-center gap-2 text-[14px] font-medium text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4 shrink-0 text-text-muted" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                    All Assets
                </span>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-text-muted">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Search assets..."
                            class="h-8 w-40 rounded-lg border border-content-border bg-content-bg pl-8 pr-3 text-[12px] text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/10 shadow-sm"
                        >
                    </div>
                    <button
                        type="button"
                        class="flex h-8 items-center gap-1.5 rounded-lg border border-content-border bg-content-bg px-3 text-[12px] font-medium text-text-heading hover:bg-body-bg shadow-sm transition-colors cursor-pointer"
                    >
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-text-muted">
                            <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                        </svg>
                        Filter
                    </button>
                    <button
                        type="button"
                        class="flex size-8 items-center justify-center rounded-lg border border-content-border bg-content-bg text-text-muted hover:bg-body-bg shadow-sm transition-colors cursor-pointer"
                    >
                        <svg viewBox="0 0 16 3" class="size-4" fill="currentColor" aria-hidden="true">
                            <circle cx="2" cy="1.5" r="1.5" />
                            <circle cx="8" cy="1.5" r="1.5" />
                            <circle cx="14" cy="1.5" r="1.5" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-[5px]">
                <template x-if="loading">
                    <div class="flex items-center justify-center py-20">
                        <div class="text-sm text-text-muted">Loading assets...</div>
                    </div>
                </template>

                <template x-if="!loading && filteredAssets.length === 0">
                    <div class="flex flex-col items-center justify-center py-16">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="size-12 text-text-muted/40 mb-3">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <polyline points="21 15 16 10 5 21" />
                        </svg>
                        <p class="text-sm font-medium text-text-heading">No assets yet</p>
                        <p class="text-sm text-text-muted mt-1">Upload a file or create a directory to get started.</p>
                    </div>
                </template>

                <template x-if="!loading && filteredAssets.length > 0">
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-0 text-left text-[13px]">
                            <thead>
                                <tr class="bg-[#f9fafb]">
                                    <th class="w-12 rounded-l-xl px-5 py-3">
                                        <input type="checkbox" class="size-4 rounded border-gray-300 accent-zinc-900 focus:ring-0 cursor-pointer">
                                    </th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px] rounded-none">
                                        <button @click="toggleSort('name')" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                            File
                                            <svg viewBox="0 0 14 14" fill="none" class="size-3 text-gray-300">
                                                <path d="M7 0.75 7 13.25" stroke="currentColor" stroke-width="1" stroke-linecap="round" />
                                                <path d="M11.086 4.836C10.269 3.202 8.635 1.567 7 0.75 5.366 1.567 3.731 3.202 2.914 4.836" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">
                                        <button @click="toggleSort('size')" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                            Size
                                            <svg viewBox="0 0 14 14" fill="none" class="size-3 text-gray-300">
                                                <path d="M7 0.75 7 13.25" stroke="currentColor" stroke-width="1" stroke-linecap="round" />
                                                <path d="M11.086 4.836C10.269 3.202 8.635 1.567 7 0.75 5.366 1.567 3.731 3.202 2.914 4.836" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">
                                        <button @click="toggleSort('created_at')" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                            Last Modified
                                            <svg viewBox="0 0 14 14" fill="none" class="size-3 text-gray-300">
                                                <path d="M7 0.75 7 13.25" stroke="currentColor" stroke-width="1" stroke-linecap="round" />
                                                <path d="M11.086 4.836C10.269 3.202 8.635 1.567 7 0.75 5.366 1.567 3.731 3.202 2.914 4.836" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">
                                        <button @click="toggleSort('width')" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                            Width
                                            <svg viewBox="0 0 14 14" fill="none" class="size-3 text-gray-300">
                                                <path d="M7 0.75 7 13.25" stroke="currentColor" stroke-width="1" stroke-linecap="round" />
                                                <path d="M11.086 4.836C10.269 3.202 8.635 1.567 7 0.75 5.366 1.567 3.731 3.202 2.914 4.836" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">
                                        <button @click="toggleSort('height')" class="inline-flex items-center gap-1 cursor-pointer hover:text-text-heading">
                                            Height
                                            <svg viewBox="0 0 14 14" fill="none" class="size-3 text-gray-300">
                                                <path d="M7 0.75 7 13.25" stroke="currentColor" stroke-width="1" stroke-linecap="round" />
                                                <path d="M11.086 4.836C10.269 3.202 8.635 1.567 7 0.75 5.366 1.567 3.731 3.202 2.914 4.836" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="rounded-r-xl pr-2"></th>
                                </tr>
                                <tr class="h-2">
                                    <td colspan="8"></td>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(asset, rowIndex) in filteredAssets" :key="asset.id">
                                    <tr
                                        class="group transition-colors hover:bg-gray-50/50"
                                        draggable="true"
                                        @dragstart="onDragStart($event, asset)"
                                        @dragenter="onDragEnter($event, asset, rowIndex)"
                                        @dragleave="onDragLeave($event, asset)"
                                        @dragover="onDragOver($event, asset)"
                                        @drop="onDrop($event, asset)"
                                        :class="dragOverFolderId === asset.id ? 'bg-primary/5 ring-2 ring-primary/30' : ''"
                                    >
                                        <td class="border-b border-gray-100 bg-content-bg px-5 py-3"
                                            :class="{'rounded-tl-xl': rowIndex === 0, 'rounded-bl-xl': rowIndex === filteredAssets.length - 1}"
                                        >
                                            <input type="checkbox" class="size-4 rounded border-gray-300 accent-zinc-900 focus:ring-0 cursor-pointer"
                                                x-init="if (rowIndex === 0) $el.checked = true">
                                        </td>
                                        <td class="border-b border-gray-100 bg-content-bg whitespace-nowrap px-4 py-3">
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
                                                            x-on:error="$el.style.display='none';$el.parentElement.innerHTML='<div class=\"size-full flex items-center justify-center text-xs text-blue-600 font-medium bg-gradient-to-br from-blue-100 to-blue-200\">IMG</div>'"
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
                                                    @click="asset.is_directory ? navigateTo(asset.path) : openPreview(asset, rowIndex)"
                                                    class="text-text-heading cursor-pointer normal-nums select-none hover:text-primary text-start"
                                                    x-text="asset.name"
                                                ></button>
                                            </div>
                                        </td>
                                        <td class="border-b border-gray-100 bg-content-bg whitespace-nowrap px-4 py-3 text-text-heading" x-text="asset.is_directory ? '\u2014' : (asset.size ?? '\u2014')"></td>
                                        <td class="border-b border-gray-100 bg-content-bg whitespace-nowrap px-4 py-3 text-text-heading" x-text="relativeDate(asset.created_at)"></td>
                                        <td class="border-b border-gray-100 bg-content-bg whitespace-nowrap px-4 py-3 text-text-heading" x-text="asset.is_directory ? '\u2014' : (asset.width ?? '\u2014')"></td>
                                        <td class="border-b border-gray-100 bg-content-bg whitespace-nowrap px-4 py-3 text-text-heading" x-text="asset.is_directory ? '\u2014' : (asset.height ?? '\u2014')"></td>
                                        <td class="border-b border-gray-100 bg-content-bg whitespace-nowrap px-4 py-3 text-right"
                                            :class="{'rounded-tr-xl': rowIndex === 0, 'rounded-br-xl': rowIndex === filteredAssets.length - 1}"
                                        >
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
                </template>
            </div>

            {{-- Footer --}}
            <footer class="flex justify-between flex-wrap items-center px-[18px] pt-2.5 md:pt-3 pb-2.5 antialiased">
                <div class="text-sm text-text-muted">
                    <span x-text="filteredAssets.length > 0 ? `1\u2013${Math.min(filteredAssets.length, 10)} of ${filteredAssets.length}` : 'No assets'"></span>
                </div>
                <div class="flex items-center gap-1">
                    <button class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-400/10 text-text-heading disabled:opacity-50 transition-colors cursor-pointer" disabled>
                        <svg viewBox="0 0 15 15" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M8.842 3.135a.5.5 0 01.023.707L5.435 7.5l3.43 3.658a.5.5 0 01-.73.684l-3.75-4a.5.5 0 010-.684l3.75-4a.5.5 0 01.707-.023" clip-rule="evenodd" /></svg>
                    </button>
                    <button class="inline-flex items-center justify-center px-3 h-8 rounded-full bg-gray-400/10 text-text-heading text-sm font-medium transition-colors cursor-pointer">1</button>
                    <button class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-400/10 text-text-heading transition-colors cursor-pointer">
                        <svg viewBox="0 0 15 15" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M6.158 3.135a.5.5 0 01-.023.707L9.565 7.5l-3.43 3.658a.5.5 0 00.73.684l3.75-4a.5.5 0 000-.684l-3.75-4a.5.5 0 00-.707-.023" clip-rule="evenodd" /></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2 text-sm text-text-muted">
                    <span>Per Page</span>
                    <span class="px-2 py-1 border border-content-border rounded text-text-heading">10</span>
                </div>
            </footer>
        </div>

        {{-- Learn about Assets --}}
        <div class="mt-12 mb-10 flex justify-center">
            <a href="#" class="inline-flex items-center justify-center border border-content-border font-normal text-sm px-4 py-2 rounded-lg text-text-muted hover:bg-body-bg no-underline transition-colors">Learn about Assets</a>
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
                <button type="button" role="menuitem" @click="actionAsset = null; navigateTo(actionAsset.path)"
                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                    <span>Open</span>
                </button>
            </template>
            <template x-if="!actionAsset?.is_directory">
                <button type="button" role="menuitem" @click="actionAsset = null; openPreview(actionAsset, previewIndex)"
                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                    <span>Preview</span>
                </button>
            </template>
            <template x-if="!actionAsset?.is_directory">
                <button type="button" role="menuitem" @click="actionAsset = null; copyUrl(actionAsset)"
                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" /><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" /></svg>
                    <span>Copy URL</span>
                </button>
            </template>
            <button type="button" role="menuitem" @click="actionAsset = null; startRename(actionAsset)"
                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" /><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" /></svg>
                <span>Rename</span>
            </button>
            <template x-if="!actionAsset?.is_directory">
                <a :href="`/admin/assets/${actionAsset?.id}/file`" target="_blank" rel="noreferrer" role="menuitem"
                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6" /><polyline points="15 3 21 3 21 9" /><line x1="10" y1="14" x2="21" y2="3" /></svg>
                    <span>Download</span>
                </a>
            </template>
            <hr class="my-1 border-content-border">
            <button type="button" role="menuitem"
                @click="actionAsset = null; deletingAsset = actionAsset"
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
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm"
        @keydown.escape.window="showCreateDir = false"
    >
        <div
            class="relative w-full max-w-md bg-content-bg rounded-2xl shadow-2xl p-6"
            @click.outside="showCreateDir = false"
        >
            <button
                @click="showCreateDir = false"
                class="absolute top-3 right-3 size-7 flex items-center justify-center rounded-md bg-gray-200 text-text-muted hover:bg-gray-300 transition-colors"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
            <h2 class="text-lg font-medium text-text-heading mb-4">Create Directory</h2>
            <input
                type="text"
                x-model="newDirName"
                @keydown.enter="createDirectory()"
                x-ref="dirInput"
                placeholder="Directory name"
                class="w-full border border-content-border rounded-lg px-3 py-2 text-sm text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary mb-4"
            >
            <div class="flex justify-end gap-2">
                <button type="button" @click="showCreateDir = false"
                    class="inline-flex items-center justify-center font-medium text-sm px-4 py-2 rounded-lg border border-content-border bg-content-bg text-text-primary hover:bg-body-bg transition-colors cursor-pointer"
                >Cancel</button>
                <button type="button" @click="createDirectory()"
                    class="inline-flex items-center justify-center font-medium text-sm px-4 py-2 rounded-lg bg-primary text-white hover:opacity-90 disabled:opacity-60 disabled:cursor-not-allowed transition-opacity cursor-pointer"
                >Create</button>
            </div>
        </div>
    </div>

    {{-- Rename Dialog --}}
    <div
        x-show="renamingAsset"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm"
        @keydown.escape.window="renamingAsset = null"
    >
        <div
            class="relative w-full max-w-md bg-content-bg rounded-2xl shadow-2xl p-6"
            @click.outside="renamingAsset = null"
        >
            <button
                @click="renamingAsset = null"
                class="absolute top-3 right-3 size-7 flex items-center justify-center rounded-md bg-gray-200 text-text-muted hover:bg-gray-300 transition-colors"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
            <h2 class="text-lg font-medium text-text-heading mb-4">Rename</h2>
            <input
                type="text"
                x-model="renameValue"
                @keydown.enter="doRename()"
                placeholder="New name"
                class="w-full border border-content-border rounded-lg px-3 py-2 text-sm text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary mb-4"
            >
            <div class="flex justify-end gap-2">
                <button type="button" @click="renamingAsset = null"
                    class="inline-flex items-center justify-center font-medium text-sm px-4 py-2 rounded-lg border border-content-border bg-content-bg text-text-primary hover:bg-body-bg transition-colors cursor-pointer"
                >Cancel</button>
                <button type="button" @click="doRename()"
                    class="inline-flex items-center justify-center font-medium text-sm px-4 py-2 rounded-lg bg-primary text-white hover:opacity-90 disabled:opacity-60 disabled:cursor-not-allowed transition-opacity cursor-pointer"
                >Save</button>
            </div>
        </div>
    </div>

    {{-- Delete Dialog --}}
    <div
        x-show="deletingAsset"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm"
        @keydown.escape.window="deletingAsset = null"
    >
        <div
            class="relative w-full max-w-md bg-content-bg rounded-2xl shadow-2xl p-6"
            @click.outside="deletingAsset = null"
        >
            <button
                @click="deletingAsset = null"
                class="absolute top-3 right-3 size-7 flex items-center justify-center rounded-md bg-gray-200 text-text-muted hover:bg-gray-300 transition-colors"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
            <h2 class="text-lg font-medium text-text-heading mb-2">Delete Asset</h2>
            <p class="text-sm text-text-primary mb-4">
                Are you sure you want to delete <strong class="text-text-heading" x-text="deletingAsset?.name"></strong>?
                <template x-if="deletingAsset?.is_directory">
                    <span>This will also delete all files inside this directory.</span>
                </template>
                This action cannot be undone.
            </p>
            <div class="flex justify-end gap-2">
                <button type="button" @click="deletingAsset = null"
                    class="inline-flex items-center justify-center font-medium text-sm px-4 py-2 rounded-lg border border-content-border bg-content-bg text-text-primary hover:bg-body-bg transition-colors cursor-pointer"
                >Cancel</button>
                <button type="button" @click="confirmDelete()"
                    class="inline-flex items-center justify-center font-medium text-sm px-4 py-2 rounded-lg bg-danger text-white hover:opacity-90 transition-opacity cursor-pointer"
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
            loading: true,
            dragOverFolderId: null,
            dragOverCrumb: null,
            fileAssets: [],

            get directoryParts() {
                if (!this.currentDirectory) return [];
                return this.currentDirectory.split('/');
            },

            get filteredAssets() {
                let arr = [...this.assets];
                if (this.search.trim()) {
                    const q = this.search.toLowerCase();
                    arr = arr.filter(a => a.name.toLowerCase().includes(q));
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
                const formData = new FormData();
                formData.append('file', file);
                formData.append('directory', this.currentDirectory);
                await fetch('{{ route("admin.assets.store") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData,
                });
                event.target.value = '';
                await this.loadAssets();
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
                    alert(data.message || 'Failed to create directory');
                    return;
                }
                this.showCreateDir = false;
                this.newDirName = '';
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

            copyUrl(asset) {
                navigator.clipboard?.writeText(`${window.location.origin}/admin/assets/${asset.id}/file`).catch(() => {});
            },

            startRename(asset) {
                this.renamingAsset = asset;
                this.renameValue = asset.name;
            },

            async doRename() {
                const name = this.renameValue.trim();
                if (!name || !this.renamingAsset) return;
                await fetch(`/admin/assets/${this.renamingAsset.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ name }),
                });
                this.renamingAsset = null;
                this.renameValue = '';
                await this.loadAssets();
            },

            async confirmDelete() {
                if (!this.deletingAsset) return;
                await fetch(`/admin/assets/${this.deletingAsset.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                });
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
                    if (d.assetId) {
                        await fetch(`/admin/assets/${d.assetId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ directory: asset.path }),
                        });
                        await this.loadAssets();
                    }
                } catch {}
            },

            crumbDragEnter(path) {
                this.dragOverCrumb = path;
            },

            crumbDragLeave() {
                this.dragOverCrumb = null;
            },

            async crumbDrop(event, path) {
                this.dragOverCrumb = null;
                const raw = event.dataTransfer.getData('text/plain');
                try {
                    const d = JSON.parse(raw);
                    if (d.assetId) {
                        await fetch(`/admin/assets/${d.assetId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ directory: path }),
                        });
                        await this.loadAssets();
                    }
                } catch {}
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
