@props([
    'pickerOpen' => false,
    'pickerCallback' => null,
])

<div
    x-data="assetPicker()"
    x-init="init({{ $pickerOpen ? 'true' : 'false' }}, @js($pickerCallback))"
    x-on:open-asset-picker.window="open($event.detail.callback)"
    x-show="isOpen || closing"
    class="fixed inset-0 z-[100] flex justify-end font-sans"
    style="display: none;"
>
    <div
        x-show="isOpen || closing"
        x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"
        @click="close()"
    ></div>

    <div
        class="relative w-full max-w-[480px] bg-white shadow-2xl flex flex-col border-l border-gray-200 my-2 mr-2 rounded-xl h-[calc(100%-1rem)]"
        x-show="isOpen || closing"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3.5 shrink-0 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Assets</h2>
            <div class="flex items-center gap-2">
                <label class="inline-flex items-center justify-center size-9 rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors cursor-pointer shrink-0" title="Upload File">
                    <template x-if="!uploading">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-5">
                            <path d="M4 14.899A7 7 0 1115.71 8h1.79a4.5 4.5 0 012.5 8.242" />
                            <path d="M12 12v9" />
                            <path d="m16 16-4-4-4 4" />
                        </svg>
                    </template>
                    <template x-if="uploading">
                        <svg class="animate-spin size-5 text-primary" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <input type="file" accept="image/*,.pdf,.doc,.docx,.zip" multiple class="hidden" @change="upload($event)" :disabled="uploading">
                </label>
                <button type="button" @click="createDir()" class="inline-flex items-center justify-center size-9 rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors shrink-0 cursor-pointer" title="New Folder">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-5">
                        <path d="M20 20H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2z" />
                        <line x1="12" y1="10" x2="12" y2="16" />
                        <line x1="9" y1="13" x2="15" y2="13" />
                    </svg>
                </button>
                <button type="button" @click="toggleLinkMode()" class="inline-flex items-center justify-center size-9 rounded-lg transition-colors shrink-0 cursor-pointer" :class="showLinkInput ? 'text-primary bg-primary/10' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100'" title="Add Image URL / Link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-5">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                    </svg>
                </button>
                <button type="button" @click="close()" class="inline-flex items-center justify-center size-9 rounded-lg text-gray-400 hover:text-gray-800 hover:bg-gray-100 transition-colors shrink-0 cursor-pointer" title="Close Panel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Link URL Input Card --}}
        <div
            x-show="showLinkInput"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.98]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.98]"
            class="mx-5 my-3 p-3 bg-gradient-to-b from-gray-50 to-white rounded-xl border border-gray-200/90 shadow-md shadow-gray-100 shrink-0 space-y-2.5 relative"
        >
            {{-- Card Header --}}
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-gray-900 tracking-tight">External Image Link</h3>
                <button type="button" @click="showLinkInput = false" class="size-6 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-200/60 transition-colors cursor-pointer" title="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>

            {{-- Input Row with Thumbnail Preview --}}
            <div class="flex items-center gap-2">
                {{-- Live Thumbnail Preview --}}
                <template x-if="externalUrl && (externalUrl.startsWith('http') || externalUrl.startsWith('//'))">
                    <div class="relative size-9 rounded-lg border border-gray-200 bg-gray-100 overflow-hidden shrink-0 shadow-2xs">
                        <img :src="externalUrl" x-on:error="$el.style.display='none'" class="size-full object-cover">
                    </div>
                </template>

                <div class="relative flex-1">
                    <input
                        type="url"
                        x-model="externalUrl"
                        x-ref="externalUrlInput"
                        @keydown.enter.prevent="applyExternalUrl()"
                        @keydown.escape.prevent="showLinkInput = false"
                        placeholder="https://example.com/image.jpg"
                        class="w-full pl-3 pr-8 py-2 text-xs font-medium bg-white rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 text-gray-800 placeholder:text-gray-400 transition-all outline-none shadow-2xs"
                    >
                    <button
                        type="button"
                        x-show="externalUrl"
                        x-cloak
                        @click="externalUrl = ''"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 size-4 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer"
                        title="Clear URL"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-3">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <button
                    type="button"
                    @click="applyExternalUrl()"
                    :disabled="!externalUrl.trim()"
                    class="px-4 py-2 bg-primary hover:bg-primary/90 active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-lg text-xs font-semibold transition-all shadow-sm shadow-primary/25 flex items-center gap-1.5 shrink-0 cursor-pointer"
                >
                    <span>Insert</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="px-5 py-2.5 shrink-0 border-b border-gray-100 bg-white">
            <div class="relative group">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-gray-400 group-focus-within:text-primary transition-colors pointer-events-none">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
                <input
                    type="text"
                    x-model="searchQuery"
                    placeholder="Search files and folders..."
                    class="w-full pl-10 pr-9 py-2 text-xs font-medium bg-gray-50/80 hover:bg-gray-100/80 focus:bg-white rounded-lg border border-gray-200/80 focus:border-primary focus:ring-2 focus:ring-primary/20 text-gray-800 placeholder:text-gray-400 transition-all duration-150 outline-none"
                >
                <button type="button" x-show="searchQuery" x-cloak @click="searchQuery = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 size-5 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-200/70 transition-all cursor-pointer" title="Clear search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-3">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Delete Banner --}}
        <div x-show="deleteConfirm" x-cloak class="shrink-0 px-7 py-3 bg-gradient-to-r from-red-50 to-red-50/80 flex items-center gap-1 border-b border-red-100">
            <p class="flex-1 text-sm text-red-700 font-medium">Do you really want to delete?</p>
            <button @click="deleteConfirm = null" class="size-8 flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-100 transition-all shrink-0" title="Cancel">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
            <button @click="confirmDelete()" class="size-8 flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-100 transition-all shrink-0" title="Confirm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            </button>
        </div>

        {{-- Drop Zone --}}
        <div
            class="flex-1 overflow-y-auto transition-colors relative"
            :class="dragOver ? 'bg-primary/5' : ''"
            style="scrollbar-width: none;"
            @dragenter.prevent="onDragEnter"
            @dragleave.prevent="onDragLeave"
            @dragover.prevent="onDragOver"
            @drop.prevent="onDrop"
        >
            <div class="flex flex-col min-h-full px-1 pb-1">

                {{-- Breadcrumbs --}}
                <template x-if="currentDirectory">
                    <nav class="sticky top-0 z-10 bg-white flex items-center gap-1 px-4 py-3 border-b border-gray-100 overflow-x-auto whitespace-nowrap" style="scrollbar-width: none;">
                        <template x-for="(crumb, i) in breadcrumbs" :key="crumb.path">
                            <div class="flex items-center gap-1 shrink-0">
                                <template x-if="i > 0">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-gray-300 shrink-0">
                                        <path d="m9 18 6-6-6-6" />
                                    </svg>
                                </template>
                                <button
                                    @click="setDirectory(crumb.path)"
                                    @dragenter.prevent="crumbDragEnter(crumb.path)"
                                    @dragleave.prevent="crumbDragLeave()"
                                    @dragover.prevent
                                    @drop.prevent="crumbDrop($event, crumb.path)"
                                    class="px-2 py-1 rounded-md text-xs transition-colors whitespace-nowrap cursor-pointer"
                                    :class="crumbDragActive === crumb.path ? 'bg-primary/15 ring-2 ring-primary/60 outline-2 outline-dashed outline-primary/40 outline-offset-[-2px]' : (i === breadcrumbs.length - 1 ? 'text-gray-900 font-bold' : 'text-gray-500 hover:text-primary hover:bg-primary/5')"
                                    x-text="crumb.name"
                                ></button>
                            </div>
                        </template>
                    </nav>
                </template>

                {{-- Loading / Uploading --}}
                <template x-if="loading || uploading">
                    <div class="flex flex-col items-center justify-center py-20 gap-3">
                        <div class="animate-spin size-9 border-[3px] border-primary/10 border-t-primary rounded-full"></div>
                        <p class="text-xs font-semibold text-gray-500" x-text="uploading ? 'Uploading assets...' : 'Loading assets...'"></p>
                    </div>
                </template>

                {{-- Grid --}}
                <template x-if="!loading && !uploading && allItems.length > 0">
                    <div class="grid grid-cols-3 gap-x-4 gap-y-6 p-5 pb-28">
                        <template x-for="(item, idx) in allItems" :key="item.id">
                            <div
                                draggable="true"
                                class="group relative flex flex-col rounded-lg border bg-white shadow-sm transition-all duration-150 cursor-pointer"
                                :class="openMenuId === item.id ? 'z-[80] relative border-primary shadow-md' : (dragTargetId === item.id ? 'border-primary ring-2 ring-primary/40 bg-primary/5 relative z-0' : 'border-gray-100 hover:border-primary/30 relative z-0')"
                                @dragstart="onCardDragStart($event, item)"
                                @dragend="onCardDragEnd()"
                                @dragenter.prevent="onCardDragEnter(item)"
                                @dragleave.prevent="onCardDragLeave(item)"
                                @dragover.prevent
                                @drop.prevent="onCardDrop($event, item)"
                            >
                                {{-- Thumbnail --}}
                                <div class="relative aspect-[1/1] bg-[#F3F4F6] overflow-hidden rounded-t-lg">
                                    <button @click="selectItem(item)" class="size-full flex items-center justify-center">
                                        <template x-if="item.is_directory">
                                            <svg viewBox="0 0 48 48" class="size-16" fill="none">
                                                <path d="M4 10C4 7.79086 5.79086 6 8 6H18.7242C19.9045 6 21.011 6.52552 21.7505 7.43906L25.3218 11.8594C25.6915 12.3162 26.2448 12.5789 26.8323 12.5789H40C42.2091 12.5789 44 14.3681 44 16.5772V38C44 40.2091 42.2091 42 40 42H8C5.79086 42 4 40.2091 4 38V10Z" fill="#F59E0B" />
                                                <path opacity="0.25" d="M4 16.5771C4 14.368 5.79086 12.5789 8 12.5789H40C42.2091 12.5789 44 14.3681 44 16.5772V38C44 40.2091 42.2091 42 40 42H8C5.79086 42 4 40.2091 4 38V16.5771Z" fill="white" />
                                            </svg>
                                        </template>
                                        <template x-if="!item.is_directory && getAssetCategory(item) === 'image'">
                                            <img :src="`/admin/assets/${item.id}/file`" :alt="item.name" class="size-full object-cover" draggable="false" x-on:error="$el.style.display='none'">
                                        </template>
                                        <template x-if="!item.is_directory && getAssetCategory(item) !== 'image'">
                                            <div class="flex flex-col items-center justify-center p-2 text-center size-full select-none">
                                                <div class="size-11 rounded-xl flex flex-col items-center justify-center border shadow-2xs mb-1"
                                                    :class="{
                                                        'bg-red-50 border-red-200 text-red-600': getAssetCategory(item) === 'pdf',
                                                        'bg-blue-50 border-blue-200 text-blue-600': getAssetCategory(item) === 'doc',
                                                        'bg-emerald-50 border-emerald-200 text-emerald-600': getAssetCategory(item) === 'spreadsheet',
                                                        'bg-amber-50 border-amber-200 text-amber-600': getAssetCategory(item) === 'presentation',
                                                        'bg-purple-50 border-purple-200 text-purple-600': getAssetCategory(item) === 'archive',
                                                        'bg-indigo-50 border-indigo-200 text-indigo-600': getAssetCategory(item) === 'audio',
                                                        'bg-rose-50 border-rose-200 text-rose-600': getAssetCategory(item) === 'video',
                                                        'bg-slate-100 border-slate-200 text-slate-700': ['code', 'other'].includes(getAssetCategory(item))
                                                    }">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-5">
                                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                                        <polyline points="14 2 14 8 20 8" />
                                                    </svg>
                                                </div>
                                                <span class="text-[10px] font-extrabold tracking-wider uppercase text-gray-600" x-text="getFileExtension(item.name)"></span>
                                            </div>
                                        </template>
                                    </button>
                                </div>

                                {{-- Action Menu --}}
                                <div class="absolute top-2 right-2 transition-opacity" :class="openMenuId === item.id ? 'opacity-100 z-[90]' : 'opacity-0 group-hover:opacity-100 z-[60]'">
                                    <div class="relative" @click.outside="if (openMenuId === item.id) openMenuId = null">
                                        <button type="button" @click.stop="openMenuId = (openMenuId === item.id ? null : item.id)" class="size-7 flex items-center justify-center rounded-md bg-white/90 border border-gray-200 hover:bg-white text-gray-500 hover:text-gray-700 cursor-pointer shadow-xs">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                                <circle cx="12" cy="12" r="1" />
                                                <circle cx="19" cy="12" r="1" />
                                                <circle cx="5" cy="12" r="1" />
                                            </svg>
                                        </button>
                                        <div x-show="openMenuId === item.id" x-cloak @click.stop="openMenuId = null"
                                            class="absolute top-full mt-1 z-[100] min-w-[10rem] rounded-xl border border-gray-200 bg-white shadow-xl p-1.5"
                                            :class="(idx % 3 === 0) ? 'left-0' : 'right-0'"
                                        >
                                            <button type="button" role="menuitem" @click="openMenuId = null; startRename(item)"
                                                class="flex w-full items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 cursor-pointer"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0 text-gray-400">
                                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                    <path d="m15 5 4 4" />
                                                </svg>
                                                <span>Rename</span>
                                            </button>
                                            <hr class="my-1 border-gray-100">
                                            <button type="button" role="menuitem" @click="openMenuId = null; deleteConfirm = item"
                                                class="flex w-full items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 cursor-pointer"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0 text-red-500">
                                                    <path d="M3 6h18" />
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                                    <line x1="10" x2="10" y1="11" y2="17" />
                                                    <line x1="14" x2="14" y1="11" y2="17" />
                                                </svg>
                                                <span>Delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Name / Rename Input --}}
                                <div class="border-t border-gray-100 px-3 py-2">
                                    <template x-if="renamingId === item.id">
                                        <input
                                            type="text"
                                            :data-rename-input="item.id"
                                            x-model="renameValue"
                                            @keydown.enter.prevent="finishRename(item.id)"
                                            @keydown.escape.prevent="renamingId = null"
                                            @blur="finishRename(item.id)"
                                            class="w-full text-[13px] font-semibold text-gray-800 bg-transparent border-0 ring-0 focus:ring-0 p-0 m-0 outline-none leading-tight"
                                        >
                                    </template>
                                    <template x-if="renamingId !== item.id">
                                        <p class="text-[13px] font-semibold text-gray-800 truncate leading-tight cursor-pointer" :title="item.name" @dblclick="startRename(item)" x-text="item.name"></p>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Empty --}}
                <template x-if="!loading && !uploading && allItems.length === 0">
                    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
                        <div class="size-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="size-7 text-gray-400">
                                <path d="m6 14 1.5-2.9A2 2 0 0 1 9.3 10H20a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2v2" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-gray-700">No assets found</h4>
                        <p class="text-xs text-gray-400 mt-1" x-text="searchQuery ? 'Try clearing your search terms' : 'Upload files or create a directory to get started'"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function assetPicker() {
        return {
            isOpen: false,
            closing: false,
            callback: null,
            assets: [],
            loading: false,
            currentDirectory: '',
            searchQuery: '',
            renamingId: null,
            renameValue: '',
            deleteConfirm: null,
            openMenuId: null,
            dragOver: false,
            dragCounter: 0,
            dragTargetId: null,
            crumbDragActive: null,
            showLinkInput: false,
            externalUrl: '',
            uploading: false,

            toggleLinkMode() {
                this.showLinkInput = !this.showLinkInput;
                if (this.showLinkInput) {
                    this.$nextTick(() => {
                        if (this.$refs.externalUrlInput) {
                            this.$refs.externalUrlInput.focus();
                        }
                    });
                }
            },

            applyExternalUrl() {
                const url = (this.externalUrl || '').trim();
                if (!url) return;
                if (this.callback) {
                    this.callback(url);
                    this.externalUrl = '';
                    this.showLinkInput = false;
                    this.close();
                }
            },

            getAssetCategory(item) {
                if (!item || item.is_directory) return 'folder';
                const name = item.name || '';
                const ext = name.includes('.') ? name.split('.').pop().toLowerCase() : '';
                const mime = (item.mime_type || '').toLowerCase();

                if (mime.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico', 'avif'].includes(ext)) {
                    return 'image';
                }
                if (mime.startsWith('audio/') || ['mp3', 'wav', 'ogg', 'flac', 'm4a', 'aac', 'wma'].includes(ext)) {
                    return 'audio';
                }
                if (mime.startsWith('video/') || ['mp4', 'webm', 'mov', 'avi', 'mkv', 'wmv'].includes(ext)) {
                    return 'video';
                }
                if (mime === 'application/pdf' || ext === 'pdf') {
                    return 'pdf';
                }
                if (['doc', 'docx'].includes(ext) || mime.includes('wordprocessingml') || mime.includes('msword')) {
                    return 'doc';
                }
                if (['xls', 'xlsx', 'csv'].includes(ext) || mime.includes('spreadsheetml') || mime.includes('excel')) {
                    return 'spreadsheet';
                }
                if (['ppt', 'pptx'].includes(ext) || mime.includes('presentationml') || mime.includes('powerpoint')) {
                    return 'presentation';
                }
                if (['zip', 'rar', 'tar', 'gz', '7z', 'bz2'].includes(ext) || mime.includes('zip') || mime.includes('compressed')) {
                    return 'archive';
                }
                if (['txt', 'json', 'md', 'js', 'css', 'php', 'html', 'xml', 'yaml', 'yml', 'env', 'sh', 'py', 'ts'].includes(ext) || mime.startsWith('text/')) {
                    return 'code';
                }
                return 'other';
            },

            getFileExtension(name) {
                if (!name) return 'FILE';
                const parts = name.split('.');
                return parts.length > 1 ? parts.pop().toUpperCase() : 'FILE';
            },

            init(open, cb) {
                if (open) { this.isOpen = true; this.callback = cb; this.fetchAssets(); }
            },

            open(callback) {
                this.callback = callback;
                this.isOpen = true;
                this.currentDirectory = '';
                this.searchQuery = '';
                this.assets = [];
                this.renamingId = null;
                this.deleteConfirm = null;
                this.openMenuId = null;
                this.showLinkInput = false;
                this.externalUrl = '';
                this.uploading = false;
                this.$nextTick(() => this.fetchAssets());
            },

            close() {
                this.closing = true;
                setTimeout(() => {
                    this.closing = false;
                    this.isOpen = false;
                    this.callback = null;
                    this.showLinkInput = false;
                    this.externalUrl = '';
                    this.uploading = false;
                }, 200);
            },

            get breadcrumbs() {
                const crumbs = [{ name: 'Assets', path: '' }];
                if (!this.currentDirectory) return crumbs;
                const clean = this.currentDirectory.replace(/^assets\/?/, '');
                if (!clean) return crumbs;
                const parts = clean.split('/').filter(Boolean);
                let acc = '';
                for (const part of parts) {
                    acc = acc ? acc + '/' + part : part;
                    crumbs.push({ name: part, path: acc });
                }
                return crumbs;
            },

            get allItems() {
                let list = this.assets;
                if (this.searchQuery.trim()) {
                    const q = this.searchQuery.toLowerCase().trim();
                    list = list.filter(a => a.name.toLowerCase().includes(q));
                }
                return [
                    ...list.filter(a => a.is_directory).sort((a, b) => a.name.localeCompare(b.name)),
                    ...list.filter(a => !a.is_directory).sort((a, b) => a.name.localeCompare(b.name)),
                ];
            },

            getFileExtension(filename) {
                if (!filename) return 'FILE';
                const parts = filename.split('.');
                return parts.length > 1 ? parts.pop().toUpperCase() : 'FILE';
            },

            async fetchAssets() {
                this.loading = true;
                const params = new URLSearchParams({ directory: this.currentDirectory });
                try {
                    const res = await fetch(`{{ route("admin.assets.list") }}?${params}`);
                    const data = await res.json();
                    this.assets = data.assets || [];
                } catch {} finally {
                    this.loading = false;
                }
            },

            setDirectory(dir) {
                this.currentDirectory = dir;
                this.fetchAssets();
            },

            selectItem(item) {
                if (item.is_directory) {
                    this.setDirectory(item.directory_path);
                } else if (this.callback) {
                    this.callback(`/storage/${item.path}`);
                    this.close();
                }
            },

            async upload(event) {
                const files = Array.from(event.target.files || []);
                if (files.length === 0) return;
                this.uploading = true;
                try {
                    const uploadPromises = files.map(file => {
                        const formData = new FormData();
                        formData.append('file', file);
                        if (this.currentDirectory) formData.append('directory', this.currentDirectory);
                        return fetch('{{ route("admin.assets.store") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: formData,
                        });
                    });
                    await Promise.all(uploadPromises);
                } catch {} finally {
                    event.target.value = '';
                    await this.fetchAssets();
                    this.uploading = false;
                }
            },

            focusAndSelectRenameInput(id) {
                this.$nextTick(() => {
                    setTimeout(() => {
                        const input = document.querySelector(`[data-rename-input="${id}"]`);
                        if (input) {
                            input.focus();
                            input.select();
                        }
                    }, 30);
                });
            },

            async createDir() {
                const existingNames = new Set(this.assets.filter(a => a.is_directory).map(a => a.name));
                let name = 'New Folder';
                while (existingNames.has(name)) {
                    name = 'New Folder (' + (parseInt((name.match(/\((\d+)\)/) || [0, 0])[1]) + 1) + ')';
                }
                try {
                    const res = await fetch('{{ route("admin.assets.directory") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ name, directory: this.currentDirectory || undefined }),
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    const newId = data?.id;
                    await this.fetchAssets();
                    if (newId) {
                        this.renamingId = newId;
                        this.renameValue = name;
                        this.focusAndSelectRenameInput(newId);
                    }
                } catch {}
            },

            startRename(item) {
                this.renamingId = item.id;
                this.renameValue = item.name;
                this.focusAndSelectRenameInput(item.id);
            },

            finishRename(id) {
                if (this.renamingId !== id) return;
                const val = this.renameValue;
                this.renamingId = null;
                this.doRename(id, val);
            },

            async doRename(id, customVal) {
                const val = (customVal !== undefined ? customVal : this.renameValue).trim();
                if (!val) return;
                try {
                    await fetch(`/admin/assets/${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ name: val }),
                    });
                    this.fetchAssets();
                } catch {}
            },

            async confirmDelete() {
                if (!this.deleteConfirm) return;
                try {
                    await fetch(`/admin/assets/${this.deleteConfirm.id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    });
                    this.deleteConfirm = null;
                    this.fetchAssets();
                } catch {}
            },

            onDragEnter(e) {
                this.dragCounter++;
                if (e.dataTransfer.items && e.dataTransfer.items.length > 0) {
                    this.dragOver = true;
                }
            },

            onDragLeave(e) {
                this.dragCounter--;
                if (this.dragCounter <= 0) { this.dragCounter = 0; this.dragOver = false; }
            },

            onDragOver(e) {},

            onDrop(e) {
                this.dragOver = false;
                this.dragCounter = 0;

                const raw = e.dataTransfer.getData('text/plain');
                if (raw) {
                    try {
                        const d = JSON.parse(raw);
                        if (d && d.assetId) {
                            return;
                        }
                    } catch {}
                }

                const files = Array.from(e.dataTransfer.files || []);
                if (files.length === 0) return;

                this.uploading = true;
                const uploadPromises = files.map(file => {
                    const formData = new FormData();
                    formData.append('file', file);
                    if (this.currentDirectory) formData.append('directory', this.currentDirectory);
                    return fetch('{{ route("admin.assets.store") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData,
                    });
                });
                Promise.all(uploadPromises)
                    .then(() => this.fetchAssets())
                    .finally(() => { this.uploading = false; });
            },

            onCardDragStart(e, item) {
                e.dataTransfer.setData('text/plain', JSON.stringify({
                    assetId: item.id,
                    isDir: item.is_directory,
                    dirPath: item.directory_path || item.name
                }));
                e.dataTransfer.effectAllowed = 'move';
            },

            onCardDragEnd() {
                this.dragTargetId = null;
            },

            onCardDragEnter(item) {
                if (item.is_directory) this.dragTargetId = item.id;
            },

            onCardDragLeave(item) {
                if (this.dragTargetId === item.id) this.dragTargetId = null;
            },

            async onCardDrop(e, item) {
                this.dragTargetId = null;
                const raw = e.dataTransfer.getData('text/plain');

                if (raw) {
                    try {
                        const d = JSON.parse(raw);
                        if (d && d.assetId) {
                            e.stopPropagation();
                            if (item.is_directory) {
                                if (d.assetId === item.id) return;
                                if (d.isDir && (item.directory_path === d.dirPath || item.directory_path?.startsWith(d.dirPath + '/'))) return;

                                const res = await fetch(`/admin/assets/${d.assetId}`, {
                                    method: 'PUT',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ directory: item.directory_path }),
                                });
                                this.fetchAssets();
                            }
                            return;
                        }
                    } catch {}
                }

                if (!item.is_directory) return;
                const files = Array.from(e.dataTransfer.files || []);
                if (files.length === 0) return;

                this.uploading = true;
                const uploadPromises = files.map(file => {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('directory', item.directory_path);
                    return fetch('{{ route("admin.assets.store") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData,
                    });
                });
                Promise.all(uploadPromises)
                    .then(() => this.fetchAssets())
                    .finally(() => { this.uploading = false; });
            },

            crumbDragEnter(path) { this.crumbDragActive = path; },
            crumbDragLeave() { this.crumbDragActive = null; },
            async crumbDrop(e, path) {
                this.crumbDragActive = null;
                const raw = e.dataTransfer.getData('text/plain');
                if (!raw) return;
                try {
                    const d = JSON.parse(raw);
                    if (d.assetId) {
                        if (d.isDir && (path === d.dirPath || path?.startsWith(d.dirPath + '/'))) return;
                        const res = await fetch(`/admin/assets/${d.assetId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ directory: path }),
                        });
                        this.fetchAssets();
                    }
                } catch {}
            },
        };
    }
</script>
@endpush
