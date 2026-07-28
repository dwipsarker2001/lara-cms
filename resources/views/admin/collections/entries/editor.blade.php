@extends('admin.layout')

@section('title', 'Editor — '.$entry->title)
@section('breadcrumb', 'Editor')

@section('content-full')
<script src="https://cdn.jsdelivr.net/npm/morphdom@2.7.4/dist/morphdom-umd.min.js"></script>
<script>
    window.editorSections = @json($entry->sections ?? []);
    window.editorSchemas = @json($blockSchemas);
    window.editorBlockList = @json($blockList);
    window.editorSlug = '{{ Str::slug($entry->title) }}';
    window.editorPages = @json($pages);
    window.editorHomeGlobals = @json($homeGlobals);
    window.editorEntryData = @json($entry->data ?? []);
    window.editorSaveRoute = '{{ route('admin.collections.entries.update-sections', [$collection, $entry]) }}';
    window.editorPostId = null;

</script>
<div class="flex h-full gap-3 p-3 relative" x-data="pageEditor()"     x-init="init(window.editorSections, window.editorSchemas, window.editorBlockList, window.editorSlug, window.editorPages, window.editorHomeGlobals)" x-on:section-selected.window="addSection($event.detail.name)">
    {{-- Editor panel --}}
    <div class="w-[420px] min-w-[320px] shrink-0 bg-white h-full flex flex-col rounded-2xl border border-[#e8eaed] shadow-[0_1px_2px_rgba(16,24,40,0.04),0_8px_24px_-12px_rgba(16,24,40,0.12)] overflow-hidden" x-show="sidebarOpen">
        <div class="flex-1 overflow-y-auto px-3 pt-3 pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div class="bg-gray-100 rounded-2xl p-[7px]">
                {{-- Section list mode --}}
                    <div x-show="active === null">
                        <div class="flex items-center justify-between pr-3 py-3 text-sm font-medium text-text-heading">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.collections.entries.index', $collection) }}" aria-label="Back" class="size-7 shrink-0 flex items-center justify-center rounded-full border border-gray-300 bg-white text-text-primary hover:bg-gray-100 transition-colors">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3">
                                        <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                                <div class="font-bold">Sections</div>
                            </div>
                            <button
                                type="button"
                                @click="window.dispatchEvent(new CustomEvent('open-section-picker'))"
                                class="size-6 flex items-center justify-center rounded-full bg-white text-text-primary border border-content-border hover:bg-gray-50 transition-colors"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[14px]">
                                    <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>

                        {{-- Empty state --}}
                        <div x-show="sections.length === 0" class="flex flex-col items-center justify-center py-8">
                            <img src="/empty-collection.svg" alt="No items" class="size-32 mb-4 opacity-60">
                            <p class="text-sm font-medium text-text-heading">No items.</p>
                            <p class="text-xs text-text-muted mt-1">Add a section to get started.</p>
                        </div>

                        {{-- Section list --}}
                        <div x-show="sections.length > 0" class="space-y-0.5" x-ref="sectionList">
                            <template x-for="(section, i) in sections" :key="section._key">
                                <div class="flex rounded-lg shadow-sm bg-content-bg mb-0.5 group overflow-hidden">
                                    <div class="w-6 shrink-0 flex items-center justify-center cursor-grab active:cursor-grabbing opacity-70 hover:opacity-100 touch-none transition-opacity text-text-muted/70">
                                        <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                                            <circle cx="8" cy="6" r="2.5" />
                                            <circle cx="16" cy="6" r="2.5" />
                                            <circle cx="8" cy="12" r="2.5" />
                                            <circle cx="16" cy="12" r="2.5" />
                                            <circle cx="8" cy="18" r="2.5" />
                                            <circle cx="16" cy="18" r="2.5" />
                                        </svg>
                                    </div>
                                    <div class="flex flex-1 min-w-0 flex-col px-1.5 py-2 cursor-pointer">
                                        <div
                                            @click="edit(i, 'title')"
                                            class="flex items-center"
                                        >
                                            <span class="text-sm font-semibold text-text-heading group-hover:text-primary truncate leading-normal transition-colors" :class="section.enabled === false ? 'opacity-50' : ''" x-text="sectionLabel(section)"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-0.5 shrink-0 ml-auto pr-1">
                                        <button
                                            type="button"
                                            @click.stop="moveSection(i, i - 1)"
                                            :disabled="i === 0"
                                            :class="i === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:text-primary hover:bg-text-primary/10'"
                                            class="p-1 text-text-muted/60 transition-colors rounded"
                                            title="Move section up"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4">
                                                <path d="M18 15l-6-6-6 6" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            @click.stop="moveSection(i, i + 1)"
                                            :disabled="i === sections.length - 1"
                                            :class="i === sections.length - 1 ? 'opacity-30 cursor-not-allowed' : 'hover:text-primary hover:bg-text-primary/10'"
                                            class="p-1 text-text-muted/60 transition-colors rounded"
                                            title="Move section down"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4">
                                                <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                        <button
                                            @click.stop="edit(i)"
                                            class="p-1 text-text-muted/60 hover:text-primary group-hover:text-primary transition-colors rounded hover:bg-text-primary/10"
                                            title="Edit"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click.stop="removeSection(i)"
                                            class="p-1 text-text-muted/60 hover:text-danger transition-colors rounded hover:bg-text-primary/10"
                                            title="Remove section"
                                        >
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                {{-- Field editor mode --}}
                <div x-show="active !== null">
                    <div class="flex items-center gap-2 mb-3">
                        <button
                            @click="exit()"
                            aria-label="Back"
                            class="size-7 shrink-0 flex items-center justify-center rounded-full border border-gray-300 bg-white text-text-primary hover:bg-gray-100 transition-colors"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3">
                                <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="grow truncate text-sm font-bold text-text-heading">
                            <span x-text="editorTitle()"></span>
                        </div>
                        <button
                            @click="toggleEnabled()"
                            class="relative inline-flex h-6 w-10 shrink-0 items-center rounded-full transition-colors"
                            :class="currentSection()?.enabled !== false ? 'bg-primary' : 'bg-gray-300'"
                            role="switch"
                            :aria-checked="currentSection()?.enabled !== false"
                        >
                            <span
                                class="inline-block size-4 rounded-full bg-white shadow-sm transition-transform"
                                :style="`transform: translateX(${currentSection()?.enabled !== false ? 22 : 2}px)`"
                            ></span>
                        </button>
                    </div>
                    <div class="flex flex-col gap-3 p-0.5">
                        <template x-for="field in currentFields()" :key="field.name">
                            <div :data-field-scroll="field.name">
                                {{-- string (input) --}}
                                <template x-if="field.type === 'string' && !field.multiline">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                        <template x-if="isSourceField(field.name)">
                                            <div>
                                                <input type="text" :value="getField(field.name)" disabled
                                                    class="w-full rounded-lg border border-primary/30 bg-primary/5 px-3 py-2 text-sm text-text-primary cursor-not-allowed opacity-75">
                                                <div class="flex items-center gap-1 mt-1 text-xs text-primary/70">
                                                    <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                    <span>Linked to custom entry field</span>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!isSourceField(field.name)">
                                            <input type="text" :value="getField(field.name)" @input="setField(field.name, $event.target.value)"
                                                :data-field-target="field.name"
                                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                        </template>
                                    </div>
                                </template>

                                {{-- string (textarea) --}}
                                <template x-if="field.type === 'string' && field.multiline">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                        <template x-if="isSourceField(field.name)">
                                            <div>
                                                <textarea disabled class="w-full rounded-lg border border-primary/30 bg-primary/5 px-3 py-2 text-sm text-text-primary cursor-not-allowed opacity-75 resize-y min-h-[140px]" rows="6" x-text="getField(field.name)"></textarea>
                                                <div class="flex items-center gap-1 mt-1 text-xs text-primary/70">
                                                    <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                    <span>Linked to custom entry field</span>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!isSourceField(field.name)">
                                            <textarea :value="getField(field.name)" @input="setField(field.name, $event.target.value)"
                                                :data-field-target="field.name"
                                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-y min-h-[140px]" rows="6"></textarea>
                                        </template>
                                    </div>
                                </template>

                                {{-- number --}}
                                <template x-if="field.type === 'number'">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                        <template x-if="isSourceField(field.name)">
                                            <div>
                                                <input type="number" :value="getField(field.name)" disabled
                                                    class="w-full rounded-lg border border-primary/30 bg-primary/5 px-3 py-2 text-sm text-text-primary cursor-not-allowed opacity-75">
                                                <div class="flex items-center gap-1 mt-1 text-xs text-primary/70">
                                                    <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                    <span>Linked to custom entry field</span>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!isSourceField(field.name)">
                                            <input type="number" :value="getField(field.name)" @input="setField(field.name, parseFloat($event.target.value) || '')"
                                                :data-field-target="field.name"
                                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                        </template>
                                    </div>
                                </template>

                                        {{-- boolean (toggle) --}}
                                        <template x-if="field.type === 'boolean'">
                                            <div class="flex items-center justify-between gap-3">
                                                <label class="text-sm font-semibold text-text-primary" x-text="field.label"></label>
                                                <button type="button" role="switch"
                                                    :aria-checked="isChecked(getField(field.name))"
                                                    @click="setField(field.name, isChecked(getField(field.name)) ? 'false' : 'true')"
                                                    :class="isChecked(getField(field.name)) ? 'bg-primary' : 'bg-gray-300'"
                                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/30 focus:ring-offset-1"
                                                    :data-field-target="field.name"
                                                >
                                                    <span aria-hidden="true"
                                                        :class="isChecked(getField(field.name)) ? 'translate-x-5' : 'translate-x-0'"
                                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                    ></span>
                                                </button>
                                    </div>
                                </template>

                                {{-- select --}}
                                <template x-if="field.type === 'select'">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                        <div class="relative" x-data="{ open: false }">
                                            <button type="button" @click="open = !open"
                                                :data-field-target="field.name"
                                                class="w-full flex items-center gap-2.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
                                            >
                                                <span class="inline-block size-4 rounded border border-gray-200 shrink-0" :style="'background-color: ' + (getField(field.name) || '#ffffff')"></span>
                                                <span class="flex-1 text-left" x-text="(field.options || []).find(o => o.value === getField(field.name))?.label || 'Select...'"></span>
                                                <svg class="size-4 text-text-muted transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false"
                                                class="absolute z-20 mt-1 w-full rounded-xl border border-gray-200 bg-white py-1 shadow-lg ring-1 ring-black/5 max-h-60 overflow-y-auto"
                                                x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                            >
                                                <template x-for="opt in field.options" :key="opt.value">
                                                    <button type="button" @click="setField(field.name, opt.value); open = false"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-left transition-colors"
                                                        :class="getField(field.name) === opt.value ? 'bg-primary/5 text-primary font-medium' : 'text-text-primary hover:bg-gray-50'"
                                                    >
                                                        <span class="inline-block size-4 rounded border border-gray-200 shrink-0" :style="'background-color: ' + opt.value"></span>
                                                        <span x-text="opt.label"></span>
                                                        <template x-if="getField(field.name) === opt.value">
                                                            <svg class="size-3.5 ml-auto shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                                        </template>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- image --}}
                                <template x-if="field.type === 'image'">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                        <div
                                            :data-field-target="field.name"
                                            @click="window.dispatchEvent(new CustomEvent('open-asset-picker', { detail: { callback: (url) => { setField(field.name, url) } } }))"
                                            @dragover.prevent="$event.currentTarget.classList.add('border-primary', 'bg-primary/5')"
                                            @dragleave.prevent="$event.currentTarget.classList.remove('border-primary', 'bg-primary/5')"
                                            @drop.prevent="
                                                $event.currentTarget.classList.remove('border-primary', 'bg-primary/5');
                                                const file = $event.dataTransfer.files[0];
                                                if (file && file.type.startsWith('image/')) {
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { setField(field.name, e.target.result); };
                                                    reader.readAsDataURL(file);
                                                }
                                            "
                                            class="relative w-full h-32 rounded-lg border-2 border-dashed cursor-pointer transition-colors bg-white overflow-hidden"
                                            :class="getField(field.name) ? 'border-gray-300 hover:border-gray-400' : 'border-gray-300 hover:border-gray-400'"
                                        >
                                            <template x-if="getField(field.name)">
                                                <img :src="getField(field.name)" alt="" class="w-full h-full object-cover rounded-lg">
                                            </template>
                                            <template x-if="!getField(field.name)">
                                                <div class="flex flex-col items-center justify-center w-full h-full text-text-muted">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-8 mb-1">
                                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                                        <polyline points="21 15 16 10 5 21" />
                                                    </svg>
                                                    <span class="text-xs font-medium">Click or drag to upload</span>
                                                </div>
                                            </template>
                                            <template x-if="getField(field.name)">
                                                <button type="button" @click.stop="setField(field.name, '')"
                                                    class="absolute top-1 right-1 text-[11px] font-medium text-white rounded px-2 py-0.5 transition-colors" style="background: rgba(220,38,38,0.8);" onmouseover="this.style.background='rgb(220,38,38)'" onmouseout="this.style.background='rgba(220,38,38,0.8)'"
                                                >Remove</button>
                                            </template>
                                        </div>
                                        <input type="file" accept="image/*" class="hidden"
                                            @change="const file = $event.target.files[0]; if (file && file.type.startsWith('image/')) { const reader = new FileReader(); reader.onload = (e) => { setField(field.name, e.target.result); }; reader.readAsDataURL(file); } $event.target.value = '';"
                                        >
                                    </div>
                                </template>

                                {{-- icon picker --}}
                                <template x-if="field.type === 'icon'">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                        <div class="relative">
                                            <button type="button" @click="iconPickerOpen = !iconPickerOpen; if(iconPickerOpen) { iconLoading = true; iconSearch = ''; $nextTick(() => iconLoading = false); }"
                                                class="flex items-center gap-2 w-full rounded-lg border px-3 py-2 text-sm transition-colors bg-white"
                                                :class="getField(field.name) ? 'border-primary' : 'border-gray-300 hover:border-gray-400'"
                                            >
                                                <template x-if="getField(field.name)">
                                                    <i :class="getField(field.name)" class="text-base w-5 text-center"></i>
                                                </template>
                                                <template x-if="!getField(field.name)">
                                                    <span class="text-gray-400 w-5 text-center">?</span>
                                                </template>
                                                <span class="text-text-primary" x-text="getField(field.name) ? iconLabel(getField(field.name)) : 'Choose icon'"></span>
                                                <svg class="ml-auto size-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <template x-if="iconPickerOpen">
                                                <div class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg">
                                                    <div class="p-2 border-b border-gray-100">
                                                        <input type="text" x-model="iconSearch" placeholder="Search icons (e.g. 'plane', 'check', 'heart')..."
                                                            class="w-full rounded-md border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                                        >
                                                    </div>
                                                    <div x-show="iconLoading" class="flex items-center justify-center py-4 text-sm text-gray-400">
                                                        <svg class="animate-spin size-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                        Loading icons...
                                                    </div>
                                                    <div x-show="!iconLoading" class="p-2 max-h-72 overflow-y-auto grid grid-cols-8 gap-1 scrollbar-thin">
                                                        <template x-for="icon in filteredIcons" :key="icon.c">
                                                            <button type="button" @click="setField(field.name, icon.c); iconPickerOpen = false; iconSearch = ''"
                                                                class="flex items-center justify-center size-8 rounded-md border transition-colors text-sm"
                                                                :class="getField(field.name) === icon.c ? 'border-primary bg-primary/10 ring-1 ring-primary' : 'border-gray-200 hover:border-gray-300 bg-white'"
                                                                :title="icon.l"
                                                            >
                                                                <i :class="icon.c"></i>
                                                            </button>
                                                        </template>
                                                        <template x-if="filteredIcons.length === 0">
                                                            <div class="col-span-8 py-4 text-center text-sm text-gray-400">No icons found</div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <template x-if="getField(field.name)">
                                            <button type="button" @click="setField(field.name, '')"
                                                class="mt-1 text-xs text-danger hover:text-danger/80 transition-colors"
                                            >Remove icon</button>
                                        </template>
                                    </div>
                                </template>

                                {{-- rich-text (TipTap WYSIWYG) --}}
                                 <template x-if="field.type === 'rich-text'">
                                     <div>
                                         <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                          <div class="tt-wrapper border border-gray-300 rounded-lg overflow-hidden"
                                              :data-field-target="field.name"
                                              x-init="mountTipTap(field.name, $el, getField(field.name) || '', (html) => setField(field.name, html))">
                                              <div class="tt-toolbar sticky top-0 z-30 flex items-center gap-px px-3 py-2 border-b border-gray-200 bg-white select-none overflow-hidden shadow-[0_1px_2px_rgba(0,0,0,0.04)]">
                                                  {{-- Heading dropdown --}}
                                                  <div class="relative tt-dropdown">
                                                      <button type="button" data-tt-cmd="toggle-dropdown" data-tt-dropdown="heading"
                                                              class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors flex items-center gap-px"
                                                              title="Heading">
                                                          <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 12h12"/><path d="M6 20V4"/><path d="M18 20V4"/></svg>
                                                          <svg class="size-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                                                      </button>
                                                       <div data-tt-panel="heading" class="bg-white rounded-lg shadow-lg border border-gray-200 py-1 min-w-[150px] hidden">
                                                          <button type="button" data-tt-cmd="setParagraph" class="tt-btn w-full text-left px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors">Normal text</button>
                                                          <button type="button" data-tt-cmd="toggleHeading" data-tt-args="[1]" class="tt-btn w-full text-left px-3 py-1.5 text-lg font-bold text-gray-700 hover:bg-gray-100 transition-colors">Heading 1</button>
                                                          <button type="button" data-tt-cmd="toggleHeading" data-tt-args="[2]" class="tt-btn w-full text-left px-3 py-1.5 text-base font-bold text-gray-700 hover:bg-gray-100 transition-colors">Heading 2</button>
                                                          <button type="button" data-tt-cmd="toggleHeading" data-tt-args="[3]" class="tt-btn w-full text-left px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors">Heading 3</button>
                                                      </div>
                                                  </div>

                                                  <span class="w-px h-5 bg-gray-300 shrink-0"></span>

                                                  {{-- Format dropdown --}}
                                                  <div class="relative tt-dropdown">
                                                      <button type="button" data-tt-cmd="toggle-dropdown" data-tt-dropdown="format"
                                                              class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors flex items-center gap-1"
                                                              title="Format">
                                                          <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" x2="15" y1="20" y2="20"/><line x1="12" x2="12" y1="4" y2="20"/></svg>
                                                          <svg class="size-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                                                      </button>
                                                       <div data-tt-panel="format" class="bg-white rounded-lg shadow-lg border border-gray-200 py-1 min-w-[130px] hidden">
                                                          <button type="button" data-tt-cmd="toggleBold" class="w-full text-left px-3 py-1.5 text-sm font-bold text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2"><svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 0 8H6z"/></svg> Bold</button>
                                                          <button type="button" data-tt-cmd="toggleItalic" class="w-full text-left px-3 py-1.5 text-sm italic text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2"><svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 4h-9"/><path d="M14 20H5"/><path d="M15 4 9 20"/></svg> Italic</button>
                                                          <button type="button" data-tt-cmd="toggleUnderline" class="w-full text-left px-3 py-1.5 text-sm underline text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2"><svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 3v6a6 6 0 0 0 12 0V3"/><path d="M4 21h16"/></svg> Underline</button>
                                                          <button type="button" data-tt-cmd="toggleStrike" class="w-full text-left px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2"><svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9.3 6.2A3 3 0 0 1 12 5c1.7 0 3 1.2 3 2.5"/><path d="M16 14c.5.8.8 1.5.8 2.5A3 3 0 0 1 12 20c-2 0-3.5-1.5-3.8-3"/><path d="M4 12h16"/></svg> Strike</button>
                                                      </div>
                                                  </div>

                                                  <span class="w-px h-5 bg-gray-300 shrink-0"></span>

                                                  {{-- List dropdown --}}
                                                  <div class="relative tt-dropdown">
                                                      <button type="button" data-tt-cmd="toggle-dropdown" data-tt-dropdown="list"
                                                              class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors flex items-center gap-1"
                                                              title="List">
                                                          <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="21" y1="6" x2="8" y2="6"/><line x1="21" y1="12" x2="8" y2="12"/><line x1="21" y1="18" x2="8" y2="18"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></svg>
                                                          <svg class="size-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                                                      </button>
                                                       <div data-tt-panel="list" class="bg-white rounded-lg shadow-lg border border-gray-200 py-1 min-w-[120px] hidden">
                                                          <button type="button" data-tt-cmd="toggleBulletList" class="w-full text-left px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2"><svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="21" y1="6" x2="8" y2="6"/><line x1="21" y1="12" x2="8" y2="12"/><line x1="21" y1="18" x2="8" y2="18"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></svg> Bullet List</button>
                                                          <button type="button" data-tt-cmd="toggleOrderedList" class="w-full text-left px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2"><svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6h11"/><path d="M10 12h11"/><path d="M10 18h11"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg> Ordered List</button>
                                                      </div>
                                                  </div>

                                                  <span class="w-px h-5 bg-gray-300 shrink-0"></span>

                                                  {{-- Alignment dropdown --}}
                                                  <div class="relative tt-dropdown">
                                                      <button type="button" data-tt-cmd="toggle-dropdown" data-tt-dropdown="align"
                                                              class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors flex items-center gap-1"
                                                              title="Align">
                                                          <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M3 12h18"/><path d="M3 18h18"/></svg>
                                                          <svg class="size-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                                                      </button>
                                                       <div data-tt-panel="align" class="bg-white rounded-lg shadow-lg border border-gray-200 py-1 min-w-[130px] hidden">
                                                          <button type="button" data-tt-cmd="setTextAlign" data-tt-args='["left"]' class="w-full text-left px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2"><svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 6H3"/><path d="M15 12H3"/><path d="M17 18H3"/></svg> Left</button>
                                                          <button type="button" data-tt-cmd="setTextAlign" data-tt-args='["center"]' class="w-full text-left px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2"><svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 6H3"/><path d="M21 12H3"/><path d="M21 18H3"/></svg> Center</button>
                                                          <button type="button" data-tt-cmd="setTextAlign" data-tt-args='["right"]' class="w-full text-left px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-2"><svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 6H3"/><path d="M15 12H9"/><path d="M17 18H7"/></svg> Right</button>
                                                      </div>
                                                  </div>

                                                  <span class="w-px h-5 bg-gray-300 shrink-0"></span>

                                                  {{-- Single actions --}}
                                                  <button type="button" data-tt-cmd="prompt-link"
                                                          class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors"
                                                          title="Link">
                                                      <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                                      <span class="tt-label">Link</span>
                                                  </button>
                                                  <button type="button" data-tt-cmd="prompt-image"
                                                          class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors"
                                                          title="Image">
                                                      <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                                      <span class="tt-label">Image</span>
                                                  </button>

                                                  <span class="w-px h-5 bg-gray-300 shrink-0"></span>

                                                  <button type="button" data-tt-cmd="toggleBlockquote"
                                                          class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors"
                                                          title="Blockquote">
                                                      <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10 11h-4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v2c0 2.5-2 5-4 5"/><path d="M20 11h-4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v2c0 2.5-2 5-4 5"/></svg>
                                                      <span class="tt-label">Quote</span>
                                                  </button>
                                                  <button type="button" data-tt-cmd="toggleCodeBlock"
                                                          class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors"
                                                          title="Code Block">
                                                      <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m16 18 6-6-6-6"/><path d="m8 6-6 6 6 6"/></svg>
                                                      <span class="tt-label">Code block</span>
                                                  </button>

                                                  <span class="w-px h-5 bg-gray-300 shrink-0"></span>

                                                  <button type="button" data-tt-cmd="undo"
                                                          class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 rounded transition-colors"
                                                          title="Undo">
                                                      <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 10h13a4 4 0 0 1 0 8H7"/><path d="m7 6-4 4 4 4"/></svg>
                                                      <span class="tt-label">Undo</span>
                                                  </button>
                                                  <button type="button" data-tt-cmd="redo"
                                                          class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 rounded transition-colors"
                                                           title="Redo">
                                                       <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10H8a4 4 0 0 0 0 8h9"/><path d="m17 6 4 4-4 4"/></svg>
                                                      <span class="tt-label">Redo</span>
                                                  </button>

                                                  <div class="relative tt-dropdown tt-overflow-dd shrink-0 ml-auto" style="display:none">
                                                      <button type="button" data-tt-cmd="toggle-dropdown" data-tt-dropdown="overflow"
                                                              class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors"
                                                              title="More tools">
                                                          <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
                                                      </button>
                                                      <div data-tt-panel="overflow" class="bg-white rounded-lg shadow-lg border border-gray-200 p-1 min-w-[170px] hidden"></div>
                                                  </div>
                                              </div>
                                             <div class="tt-editor px-4 py-3 min-h-[200px] max-h-[480px] overflow-y-auto prose prose-sm max-w-none focus:outline-none"></div>
                                         </div>
                                     </div>
                                 </template>

                                {{-- link --}}
                                        <template x-if="field.type === 'link'">
                                            <div :data-field-target="field.name">
                                                <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                                <div class="flex gap-2">
                                                    {{-- Mode select --}}
                                                    <div class="relative w-36 shrink-0" @keydown="selectKeydown($event, 'mode-' + field.name)">
                                                        <button type="button" @click.stop="toggleSelect('mode-' + field.name)"
                                                            class="w-full flex items-center justify-between gap-2 bg-white border border-gray-300 text-sm rounded-lg px-3 h-10 cursor-pointer transition-shadow duration-150 hover:shadow-sm"
                                                            :class="getSelect('mode-' + field.name).open ? 'ring-2 ring-primary/30 border-primary shadow-sm' : 'shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)]'">
                                                            <span class="text-text-primary truncate" x-text="getLinkMode(field.name) === 'page' ? 'Page' : 'Custom'"></span>
                                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 opacity-60" :class="getSelect('mode-' + field.name).open ? 'rotate-180' : ''">
                                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="getSelect('mode-' + field.name).open" x-cloak
                                                            class="absolute z-50 top-full mt-1.5 left-0 right-0 bg-white rounded-xl shadow-lg overflow-hidden"
                                                            @click.away="closeSelect('mode-' + field.name)">
                                                            <div class="py-1">
                                                                <template x-for="opt in [{value:'page',label:'Page'},{value:'custom',label:'Custom'}]">
                                                                    <button type="button" @click="setLinkMode(field.name, opt.value); closeSelect('mode-' + field.name)"
                                                                        class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm text-left transition-colors duration-75 hover:bg-primary/10"
                                                                        :class="getLinkMode(field.name) === opt.value ? 'text-primary font-medium' : 'text-gray-700'">
                                                                        <span class="truncate" x-text="opt.label"></span>
                                                                        <svg x-show="getLinkMode(field.name) === opt.value" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-primary">
                                                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                                        </svg>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- Page select --}}
                                                    <template x-if="getLinkMode(field.name) === 'page'">
                                                        <div class="relative flex-1 min-w-0" @keydown="selectKeydown($event, 'page-' + field.name)">
                                                            <button type="button" @click.stop="toggleSelect('page-' + field.name)"
                                                                 class="w-full flex items-center justify-between gap-2 bg-white border border-gray-300 text-sm rounded-lg px-3 h-10 cursor-pointer transition-shadow duration-150 hover:shadow-sm"
                                                                 :class="getSelect('page-' + field.name).open ? 'ring-2 ring-primary/30 border-primary shadow-sm' : 'shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)]'">
                                                                 <span class="truncate" :class="getField(field.name) ? 'text-gray-900' : 'text-gray-400'">
                                                                     <template x-if="getField(field.name)">
                                                                         <span x-text="pages.find(p => p.route === getField(field.name))?.title || getField(field.name)"></span>
                                                                     </template>
                                                                     <template x-if="!getField(field.name)">Select a page...</template>
                                                                 </span>
                                                                 <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 opacity-60" :class="getSelect('page-' + field.name).open ? 'rotate-180' : ''">
                                                                     <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                 </svg>
                                                             </button>
                                                              <div x-show="getSelect('page-' + field.name).open" x-cloak
                                                                  class="absolute z-50 top-full mt-1.5 left-0 right-0 bg-white rounded-xl shadow-lg overflow-hidden"
                                                                 @click.away="closeSelect('page-' + field.name)">
                                                                  <div class="flex items-center gap-2 px-3 py-1 border-b border-gray-200">
                                                                      <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 opacity-50 text-gray-400">
                                                                          <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                                                                      </svg>
                                                                      <input type="text" :id="'sel-search-page-' + field.name"
                                                                          @input="getSelect('page-' + field.name).search = $event.target.value; getSelect('page-' + field.name).highlight = 0"
                                                                          @click.stop
                                                                          placeholder="Search..."
                                                                          class="w-full bg-transparent text-sm text-gray-900 placeholder:text-gray-400 outline-none border-0 focus:ring-0">
                                                                 </div>
                                                                 <div class="max-h-60 overflow-y-auto py-1 [scrollbar-width:thin]" x-ref="list">
                                                                     <template x-for="(page, pi) in pages.filter(p => !getSelect('page-' + field.name).search || p.title.toLowerCase().includes(getSelect('page-' + field.name).search.toLowerCase()) || p.route.toLowerCase().includes(getSelect('page-' + field.name).search.toLowerCase()))" :key="page.id">
                                                                         <button type="button" @click="linkFieldValue(field.name, page.route); closeSelect('page-' + field.name)"
                                                                             class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm text-left transition-colors duration-75 hover:bg-primary/10"
                                                                             :class="getField(field.name) === page.route ? 'text-primary font-medium' : 'text-gray-700'">
                                                                             <span class="truncate" x-text="page.title"></span>
                                                                             <svg x-show="getField(field.name) === page.route" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-primary">
                                                                                 <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                                             </svg>
                                                                         </button>
                                                                     </template>
                                                                     <template x-if="pages.filter(p => !getSelect('page-' + field.name).search || p.title.toLowerCase().includes(getSelect('page-' + field.name).search.toLowerCase()) || p.route.toLowerCase().includes(getSelect('page-' + field.name).search.toLowerCase())).length === 0">
                                                                         <div class="px-3 py-6 text-sm text-gray-400 text-center">No results found</div>
                                                                     </template>
                                                                 </div>
                                                             </div>
                                                        </div>
                                                    </template>
                                                    <template x-if="getLinkMode(field.name) === 'custom'">
                                                        <input type="text"
                                                            :value="getField(field.name)"
                                                            @input="linkFieldValue(field.name, $event.target.value)"
                                                            placeholder="https://example.com"
                                                            class="flex-1 min-w-0 rounded-lg border border-gray-300 bg-white px-3 h-10 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                                    </template>
                                                </div>
                                    </div>
                                </template>

                                {{-- tags --}}
                                <template x-if="field.type === 'tags'">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                        <div class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus-within:ring-2 focus-within:ring-primary/30 focus-within:border-primary">
                                            <div class="flex flex-wrap gap-1 mb-1">
                                                <template x-for="(tag, ti) in parseTags(getField(field.name))" :key="ti">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-panel-bg rounded text-xs">
                                                        <span x-text="tag"></span>
                                                        <button @click="removeTag(field.name, ti)" class="text-danger hover:text-danger/70 leading-none">&times;</button>
                                                    </span>
                                                </template>
                                            </div>
                                            <input type="text" placeholder="Type and press Enter..." @keydown.enter.prevent="addTag(field.name, $event.target.value); $event.target.value = ''"
                                                class="w-full border-0 p-0 text-sm text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-0">
                                        </div>
                                    </div>
                                </template>

                                {{-- object (drill-in or list) --}}
                                        <template x-if="field.type === 'object'">
                                            <div :data-field-target="field.name">
                                                <template x-if="!field.list">
                                                    <button @click="drillIn(field.name)"
                                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-left text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] flex items-center justify-between"
                                                    >
                                                        <span class="font-semibold" x-text="field.label"></span>
                                                        <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                                    </button>
                                                </template>
                                                <template x-if="field.list">
                                                    <div>
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="text-sm font-semibold text-text-primary" x-text="field.label"></span>
                                                            <button type="button" @click="addListItem(field.name)" class="text-xs text-primary hover:text-primary/80 font-medium">+ Add <span x-text="field.label.toLowerCase()"></span></button>
                                                </div>
                                                <template x-if="getList(field.name).length > 0">
                                                    <div class="space-y-0.5" data-sortable-list :data-field-name="field.name"
                                                         x-init="$nextTick(() => initListSortable($el))"
                                                    >
                                                        <template x-for="(item, ci) in getList(field.name)" :key="item._key">
                                                            <div class="flex rounded-lg shadow-sm bg-content-bg mb-0.5 group overflow-hidden">
                                                                <div class="w-6 shrink-0 flex items-center justify-center cursor-grab active:cursor-grabbing opacity-70 hover:opacity-100 touch-none transition-opacity text-text-muted/70">
                                                                    <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                                                                        <circle cx="8" cy="6" r="2.5" /><circle cx="16" cy="6" r="2.5" />
                                                                        <circle cx="8" cy="12" r="2.5" /><circle cx="16" cy="12" r="2.5" />
                                                                        <circle cx="8" cy="18" r="2.5" /><circle cx="16" cy="18" r="2.5" />
                                                                    </svg>
                                                                </div>
                                                                <div class="flex flex-1 min-w-0 items-center px-1.5 py-2.5 text-xs leading-normal">
                                                                    <div class="flex min-w-0 flex-1 items-center gap-1.5">
                                                                        <template x-if="item.icon">
                                                                            <i :class="item.icon" class="size-3 shrink-0 text-text-muted"></i>
                                                                        </template>
                                                                        <span class="text-sm font-semibold text-text-heading group-hover:text-primary truncate leading-normal transition-colors" x-text="cardLabel(item, field)"></span>
                                                                    </div>
                                                                    <div class="flex items-center gap-0.5 shrink-0 ml-1">
                                                                        <button @click="drillIn(field.name, ci)" class="p-1 text-text-muted/60 hover:text-primary group-hover:text-primary transition-colors rounded hover:bg-text-primary/10" title="Edit">
                                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                                                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                                            </svg>
                                                                        </button>
                                                                        <button @click="removeListItem(field.name, ci)" class="p-1 text-text-muted/60 hover:text-danger transition-colors rounded hover:bg-text-primary/10" title="Remove">
                                                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                        </template>
                                    </div>
                                </template>

                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>



        {{-- Footer --}}
        <div class="shrink-0 px-4 py-3 border-t border-content-border bg-body-bg flex items-center justify-end gap-2">
            <button type="button" @click="reset()" x-bind:disabled="!dirty" class="px-4 py-1.5 text-sm font-medium text-text-primary bg-content-bg border border-content-border rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Reset
            </button>
            <button
                type="button"
                @click="save()"
                x-bind:disabled="!dirty"
                class="px-4 py-1.5 text-sm font-medium text-white bg-primary rounded-lg hover:opacity-90 transition-opacity disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2"
            >
                <span x-show="!isSaving">Save &amp; Publish</span>
                <span x-show="isSaving" class="inline-flex items-center gap-2">
                    <svg class="animate-spin size-4" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Saving...
                </span>
            </button>
        </div>
    </div>

    {{-- Preview pane --}}
    <div class="flex-1 flex flex-col min-w-0 h-full relative items-center justify-start overflow-visible">
        {{-- Frame wrapper --}}
        <div class="relative flex-1 w-full h-full flex justify-center overflow-visible">
            <div
                class="relative flex flex-col"
                :class="{ 'transition-none select-none': isResizing, 'transition-[width] duration-150 ease-out': !isResizing }"
                :style="{ width: previewWidth, maxWidth: '100%', minWidth: '320px' }"
                x-ref="previewFrame"
            >
                {{-- Drag Handle Grid in Center-Left --}}
                <div
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1/2 z-50 cursor-ew-resize group flex items-center justify-center"
                    @mousedown="startResize($event)"
                    @touchstart="startResize($event)"
                    title="Drag left/right to resize screen to mobile or desktop"
                >
                    <div class="w-[16px] h-[96px] rounded-md bg-white border border-gray-300 shadow-md group-hover:bg-gray-100 group-hover:border-gray-400 transition-all flex items-center justify-center cursor-ew-resize">
                        <svg class="w-2.5 h-10 text-gray-400 group-hover:text-gray-600 transition-colors" fill="currentColor" viewBox="0 0 10 38">
                            <circle cx="3" cy="4" r="1" />
                            <circle cx="7" cy="4" r="1" />
                            <circle cx="3" cy="10" r="1" />
                            <circle cx="7" cy="10" r="1" />
                            <circle cx="3" cy="16" r="1" />
                            <circle cx="7" cy="16" r="1" />
                            <circle cx="3" cy="22" r="1" />
                            <circle cx="7" cy="22" r="1" />
                            <circle cx="3" cy="28" r="1" />
                            <circle cx="7" cy="28" r="1" />
                            <circle cx="3" cy="34" r="1" />
                            <circle cx="7" cy="34" r="1" />
                        </svg>
                    </div>
                </div>

                <div class="flex-1 overflow-hidden preview-shell h-full bg-white rounded-2xl border border-[#e8eaed] shadow-[0_1px_2px_rgba(16,24,40,0.04),0_8px_24px_-12px_rgba(16,24,40,0.12)]">
                    <iframe id="preview-iframe" class="w-full h-full min-h-full border-0 block bg-white" :class="{ 'pointer-events-none': isResizing }"></iframe>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

<style>
    .sortable-ghost,
    .sortable-drag {
        opacity: 0.3 !important;
        box-shadow: none !important;
        border: none !important;
    }
</style>

@push('scripts')
<script>
    function pageEditor() {
        return {
            sections: [],
            schemas: {},
            blockList: [],
            slug: '',
            pages: [],
            entryData: {},
            linkModes: {},
            active: null,
            crumbs: [],
            homeGlobals: {},
            originalSections: [],
            _focusField: null,
            dirty: false,
            isSaving: false,
            previewTimer: null,
            iconPickerOpen: false,
            iconSearch: '',
            iconLoading: false,
            faIcons: window.FA_ICONS || [],
            sidebarOpen: true,
            previewWidth: '100%',
            isResizing: false,

            get previewWidthStyle() {
                return this.previewWidth === '100%' ? '100%' : this.previewWidth;
            },

            setPreviewWidth(width) {
                this.previewWidth = width;
            },

            startResize(e) {
                e.preventDefault();
                this.isResizing = true;
                document.body.style.userSelect = 'none';
                document.body.style.cursor = 'ew-resize';

                const startX = e.touches ? e.touches[0].clientX : e.clientX;
                const container = this.$refs.previewFrame ? this.$refs.previewFrame.parentElement : null;
                if (!container) return;
                const containerRect = container.getBoundingClientRect();
                const initialWidth = this.$refs.previewFrame.offsetWidth;

                const onMove = (moveEvt) => {
                    if (!this.isResizing) return;
                    const currentX = moveEvt.touches ? moveEvt.touches[0].clientX : moveEvt.clientX;
                    const deltaX = startX - currentX;
                    let newWidth = initialWidth + (deltaX * 2);
                    if (newWidth >= containerRect.width - 25) {
                        this.previewWidth = '100%';
                    } else {
                        newWidth = Math.max(320, Math.min(containerRect.width, newWidth));
                        this.previewWidth = Math.round(newWidth) + 'px';
                    }
                };

                const onEnd = () => {
                    this.isResizing = false;
                    document.body.style.userSelect = '';
                    document.body.style.cursor = '';
                    window.removeEventListener('mousemove', onMove);
                    window.removeEventListener('mouseup', onEnd);
                    window.removeEventListener('touchmove', onMove);
                    window.removeEventListener('touchend', onEnd);
                };

                window.addEventListener('mousemove', onMove);
                window.addEventListener('mouseup', onEnd);
                window.addEventListener('touchmove', onMove);
                window.addEventListener('touchend', onEnd);
            },

            init(sections, schemas, blockList, slug, pages, homeGlobals) {
                if (!sections) sections = [];
                if (!schemas) schemas = {};
                if (!blockList) blockList = [];
                if (!slug) slug = '';
                if (!pages) pages = [];
                if (!homeGlobals) homeGlobals = {};
                this.sections = JSON.parse(JSON.stringify(sections));
                this.ensureSectionKeys();
                this.originalSections = JSON.parse(JSON.stringify(this.sections));
                this.schemas = schemas;
                this.blockList = blockList;
                this.slug = slug;
                this.pages = pages;
                this.homeGlobals = homeGlobals;
                this.entryData = window.editorEntryData || {};
                this.$nextTick(() => this.initSectionSortable());
                this.refreshPreview();
            },

            ensureSectionKeys() {
                const addKeys = (obj) => {
                    if (!obj || typeof obj !== 'object') return;
                    if (Array.isArray(obj)) {
                        obj.forEach(item => {
                            if (item && typeof item === 'object') {
                                if (!item._key) {
                                    item._key = crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2);
                                }
                                addKeys(item);
                            }
                        });
                    } else {
                        Object.values(obj).forEach(val => addKeys(val));
                    }
                };

                for (const section of this.sections) {
                    if (!section._key) {
                        section._key = crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2);
                    }
                    if (section.data) {
                        addKeys(section.data);
                    }
                }
            },

            initSectionSortable() {
                if (this._sectionSortable) {
                    try { this._sectionSortable.destroy(); } catch (e) {}
                    this._sectionSortable = null;
                }
                const el = this.$refs?.sectionList;
                if (!el) return;
                this._sectionSortable = new Sortable(el, {
                    handle: '.cursor-grab',
                    animation: 200,
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    ghostClass: 'sortable-ghost',
                    onStart: (evt) => {
                        evt.item._prevSibling = evt.item.previousElementSibling;
                    },
                    onEnd: (evt) => {
                        const cleanup = () => {
                            delete evt.item._prevSibling;
                            setTimeout(() => this.initSectionSortable(), 0);
                        };

                        if (evt.oldIndex === evt.newIndex || evt.oldIndex === undefined || evt.newIndex === undefined) {
                            cleanup();
                            return;
                        }

                        // Revert Sortable DOM changes so Alpine can handle the DOM update
                        const itemEl = evt.item;
                        if (itemEl._prevSibling && itemEl._prevSibling.parentElement === evt.from) {
                            itemEl._prevSibling.after(itemEl);
                        } else if (evt.from) {
                            evt.from.prepend(itemEl);
                        }

                        let oldIdx = evt.oldDraggableIndex;
                        let newIdx = evt.newDraggableIndex;
                        if (oldIdx === undefined || newIdx === undefined) {
                            const offset = (evt.from.children[0] && evt.from.children[0].tagName === 'TEMPLATE') ? 1 : 0;
                            oldIdx = evt.oldIndex - offset;
                            newIdx = evt.newIndex - offset;
                        }

                        if (oldIdx >= 0 && oldIdx < this.sections.length && newIdx >= 0 && newIdx < this.sections.length) {
                            const item = this.sections.splice(oldIdx, 1)[0];
                            if (item !== undefined) {
                                this.sections.splice(newIdx, 0, item);
                                this.sections = [...this.sections];
                                this.dirty = true;
                                this.schedulePreview();
                            }
                        }
                        cleanup();
                    },
                });
            },

            initListSortable(el) {
                if (!el) return;
                if (el._sortable) {
                    try { el._sortable.destroy(); } catch (e) {}
                    delete el._sortable;
                }
                el._sortable = new Sortable(el, {
                    handle: '.cursor-grab',
                    animation: 200,
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    ghostClass: 'sortable-ghost',
                    onStart: (evt) => {
                        evt.item._prevSibling = evt.item.previousElementSibling;
                    },
                    onEnd: (evt) => {
                        const cleanup = () => {
                            delete evt.item._prevSibling;
                            setTimeout(() => this.initListSortable(el), 0);
                        };

                        if (evt.oldIndex === evt.newIndex || evt.oldIndex === undefined || evt.newIndex === undefined) {
                            cleanup();
                            return;
                        }

                        // Revert Sortable DOM changes so Alpine can handle the DOM update
                        const itemEl = evt.item;
                        if (itemEl._prevSibling && itemEl._prevSibling.parentElement === evt.from) {
                            itemEl._prevSibling.after(itemEl);
                        } else if (evt.from) {
                            evt.from.prepend(itemEl);
                        }

                        const name = el.dataset.fieldName;
                        if (!name) {
                            cleanup();
                            return;
                        }

                        let oldIdx = evt.oldDraggableIndex;
                        let newIdx = evt.newDraggableIndex;
                        if (oldIdx === undefined || newIdx === undefined) {
                            const offset = (evt.from.children[0] && evt.from.children[0].tagName === 'TEMPLATE') ? 1 : 0;
                            oldIdx = evt.oldIndex - offset;
                            newIdx = evt.newIndex - offset;
                        }

                        const list = this.getList(name);
                        if (oldIdx < 0 || oldIdx >= list.length || newIdx < 0 || newIdx >= list.length) {
                            cleanup();
                            return;
                        }

                        const item = list.splice(oldIdx, 1)[0];
                        if (item === undefined) {
                            cleanup();
                            return;
                        }
                        list.splice(newIdx, 0, item);

                        // Re-assign list array reference to force Alpine reactivity update
                        if (this.active !== null && this.sections[this.active] && this.sections[this.active].data) {
                            let d = this.sections[this.active].data;
                            let valid = true;
                            for (const crumb of this.crumbs) {
                                if (!d || !crumb || !crumb.key) { valid = false; break; }
                                d = d[crumb.key];
                                if (!d) { valid = false; break; }
                                if (crumb.index !== undefined) {
                                    d = d[crumb.index];
                                    if (!d) { valid = false; break; }
                                }
                            }
                            if (valid && d) {
                                d[name] = [...list];
                            }
                        }

                        this.dirty = true;
                        this.schedulePreview();
                        cleanup();
                    },
                });
            },

            initListSortables() {
                setTimeout(() => {
                    document.querySelectorAll('[data-sortable-list]').forEach(el => {
                        this.initListSortable(el);
                    });
                }, 0);
            },

            moveSection(from, to) {
                if (from < 0 || from >= this.sections.length) return;
                if (to < 0 || to >= this.sections.length) return;

                if (this._sectionSortable) {
                    try { this._sectionSortable.destroy(); } catch (e) {}
                    this._sectionSortable = null;
                }

                const item = this.sections.splice(from, 1)[0];
                this.sections.splice(to, 0, item);
                this.sections = [...this.sections];
                this.dirty = true;
                this.schedulePreview();
                this.$nextTick(() => this.initSectionSortable());
            },

            sectionLabel(section) {
                const s = this.schemas[section.name];
                return section.data?.headline || section.data?.title || section.data?.heading || (s ? s.label : section.name) || section.name;
            },

            editorTitle() {
                if (this.crumbs.length > 0) {
                    return this.crumbs[this.crumbs.length - 1].key;
                }
                const section = this.sections[this.active];
                if (!section) return '';
                return this.sectionLabel(section);
            },

            isChecked(val) {
                return val === 'true' || val === true;
            },

            currentFields() {
                if (this.active === null) return [];
                const section = this.sections[this.active];
                if (!section) return [];
                let fields = this.schemas[section.name] || [];
                let current = { fields, data: section.data };
                for (const crumb of this.crumbs) {
                    const f = (current.fields || []).find(f2 => f2.name === crumb.key);
                    if (!f || f.type !== 'object') return [];
                    current = { fields: f.fields || [], data: (current.data || {})[crumb.key] };
                    if (crumb.index !== undefined && current.data) {
                        current.data = current.data[crumb.index] || {};
                    }
                }
                return current.fields || [];
            },

            currentData() {
                if (this.active === null) return {};
                let d = this.sections[this.active].data || {};
                for (const crumb of this.crumbs) {
                    d = d[crumb.key] || {};
                    if (crumb.index !== undefined) {
                        d = d[crumb.index] || {};
                    }
                }
                return d;
            },

            getField(name) {
                const fields = this.currentFields();
                const fieldDef = fields?.find(f => f.name === name);
                if (fieldDef?.source && this.entryData[fieldDef.source] !== undefined && this.entryData[fieldDef.source] !== '') {
                    return this.entryData[fieldDef.source];
                }
                return this.currentData()[name] ?? '';
            },

            isSourceField(name) {
                const fields = this.currentFields();
                const fieldDef = fields?.find(f => f.name === name);
                return !!(fieldDef?.source && this.entryData[fieldDef.source] !== undefined && this.entryData[fieldDef.source] !== '');
            },

            getLinkMode(name) {
                if (!(name in this.linkModes)) {
                    const val = this.getField(name);
                    this.linkModes[name] = (!val || this.pages?.some(p => p.route === val)) ? 'page' : 'custom';
                }
                return this.linkModes[name];
            },

            setLinkMode(name, mode) {
                this.linkModes[name] = mode;
            },

            linkFieldValue(name, linkValue) {
                this.setField(name, linkValue);
            },

            syncPreviewField(name, value) {
                const iframe = document.getElementById('preview-iframe');
                if (!iframe) return false;

                const doc = iframe.contentDocument || iframe.contentWindow?.document;
                if (!doc) return false;

                let context = doc;
                const container = doc.getElementById('preview-content');
                if (container && this.active !== null && container.children[this.active]) {
                    context = container.children[this.active];
                }

                if (this.crumbs && this.crumbs.length > 0) {
                    for (const crumb of this.crumbs) {
                        const listElements = Array.from(context.querySelectorAll(`[data-list="${crumb.key}"]`));
                        if (crumb.index !== undefined && listElements[crumb.index]) {
                            context = listElements[crumb.index];
                        } else if (listElements.length > 0) {
                            context = listElements[0];
                        }
                    }
                }

                const target = context.querySelector(`[data-edit="${name}"]`);
                if (!target) return false;

                const isImgTag = target.tagName === 'IMG';
                let imgEl = isImgTag ? target : target.querySelector('img');
                const isImageField = name.toLowerCase().includes('image') || isImgTag || !!imgEl;

                if (isImageField) {
                    if (!imgEl && !isImgTag) {
                        imgEl = doc.createElement('img');
                        imgEl.className = 'absolute inset-0 w-full h-full object-cover';
                        target.appendChild(imgEl);
                    }

                    const activeImg = imgEl || target;
                    if (value && typeof value === 'string') {
                        activeImg.setAttribute('src', value);
                        activeImg.classList.remove('hidden');
                        activeImg.style.display = '';
                        target.classList.remove('hidden');
                        target.style.display = '';
                        if (!isImgTag) {
                            target.querySelectorAll('span, p, label').forEach(el => el.classList.add('hidden'));
                        }
                    } else {
                        activeImg.classList.add('hidden');
                        if (!isImgTag) {
                            target.querySelectorAll('span, p, label').forEach(el => el.classList.remove('hidden'));
                        }
                    }
                    return true;
                }

                const text = value == null || value === undefined ? '' : String(value);
                const looksLikeHtml = typeof text === 'string' && /<\/?[a-z][\s\S]*>/i.test(text);

                if (looksLikeHtml) {
                    if (window.morphdom) {
                        const wrapper = doc.createElement('div');
                        wrapper.innerHTML = text;
                        window.morphdom(target, wrapper, { childrenOnly: true });
                    } else {
                        target.innerHTML = text;
                    }
                } else {
                    target.textContent = text;
                }

                return true;
            },

            setField(name, value) {
                this.setNested(name, value);
                this.dirty = true;
                const synced = this.syncPreviewField(name, value);
                if (!synced) {
                    this.schedulePreview();
                }
            },

            setNested(name, value) {
                if (this.active === null) return;
                let d = this.sections[this.active]?.data;
                if (!d) return;
                for (const crumb of this.crumbs) {
                    if (!d || !crumb || !crumb.key) return;
                    d = d[crumb.key];
                    if (!d) return;
                    if (crumb.index !== undefined) {
                        d = d[crumb.index];
                        if (!d) return;
                    }
                }
                d[name] = value;
            },

            getList(name) {
                const val = this.currentData()[name];
                return Array.isArray(val) ? val : [];
            },

            drillIn(key, index) {
                const val = this.currentData()[key];
                if (typeof val === 'string') {
                    try {
                        const parsed = JSON.parse(val);
                        if (typeof parsed === 'object' && parsed !== null) {
                            this.setNested(key, parsed);
                        }
                    } catch (e) {}
                }
                this.crumbs.push({ key, index: index ?? undefined });
                this.initListSortables();
            },

            currentSection() {
                if (this.active === null) return null;
                return this.sections[this.active] ?? null;
            },

            toggleEnabled() {
                const section = this.currentSection();
                if (!section) return;
                section.enabled = section.enabled === false ? true : false;
                this.dirty = true;
                this.schedulePreview();
            },

            exit() {
                if (this.crumbs.length > 0) {
                    this.destroyNestedSortables();
                    this.crumbs.pop();
                } else {
                    this.active = null;
                    this.$nextTick(() => this.initSectionSortable());
                }
            },

            destroyNestedSortables() {
                document.querySelectorAll('[data-sortable-list]').forEach(el => {
                    if (el._sortable) {
                        el._sortable.destroy();
                        delete el._sortable;
                    }
                });
            },

            edit(i, focusField) {
                this.active = i;
                this.crumbs = [];
                this._focusField = focusField || null;
                if (this._focusField) {
                    this.$nextTick(() => this.focusFirstField());
                }
            },

            focusFirstField() {
                const targetName = this._focusField ? this.resolveFieldName(this._focusField) : null;
                const candidates = document.querySelectorAll('[data-field-target]');

                if (targetName) {
                    const el = document.querySelector(`[data-field-target="${targetName}"]`);
                    if (el) {
                        const proseMirror = el.classList.contains('ProseMirror') ? el : el.querySelector('.ProseMirror');
                        if (proseMirror) { proseMirror.focus(); } else { try { el.focus(); } catch {} }
                        try { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                        return;
                    }
                }

                for (const el of candidates) {
                    const proseMirror = el.classList.contains('ProseMirror') ? el : el.querySelector('.ProseMirror');
                    if (proseMirror) {
                        proseMirror.focus();
                        try { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                        return;
                    }
                    if (el.matches('input, textarea, button, select, [tabindex]')) {
                        try { el.focus(); } catch {}
                        try { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                        return;
                    }
                }
            },

            resolveFieldName(type) {
                if (this.active === null) return null;
                const section = this.sections[this.active];
                if (!section) return null;
                const schema = this.schemas[section.name] || [];
                if (type === 'title') {
                    for (const name of ['headline', 'title', 'heading']) {
                        if (schema.some(f => f.name === name)) return name;
                    }
                    const f = schema.find(f => f.type === 'string' && !f.multiline);
                    return f ? f.name : null;
                }
                if (type === 'description') {
                    for (const name of ['description', 'subheading']) {
                        if (schema.some(f => f.name === name)) return name;
                    }
                    const f = schema.find(f => (f.type === 'string' && f.multiline) || f.type === 'text');
                    return f ? f.name : null;
                }
                return null;
            },

            sectionDescription(section) {
                return section.data?.description || section.data?.subheading || '';
            },

            addSection(name) {
                const section = this.createDefault(name);
                if (section) {
                    this.sections.push(section);
                    this.sections = [...this.sections];
                    this.dirty = true;
                    this.schedulePreview();
                    this.$nextTick(() => this.initSectionSortable());
                }
            },

            createDefault(name) {
                const schema = this.schemas[name];
                if (!schema) return null;
                const homeGlobal = this.homeGlobals.find?.(g => g.name === name);
                return {
                    _key: crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2),
                    name: name,
                    enabled: true,
                    data: homeGlobal ? JSON.parse(JSON.stringify(homeGlobal.data)) : this.buildDefaultData(schema),
                };
            },

            buildDefaultData(fields) {
                const out = {};
                for (const f of fields) {
                    if (f.type === 'object') {
                        if (f.list) {
                            out[f.name] = [];
                            const count = f.defaultCount || 0;
                            for (let i = 0; i < count; i++) {
                                out[f.name].push(this.buildListItem(f));
                            }
                        } else {
                            out[f.name] = this.buildDefaultData(f.fields || []);
                        }
                    } else {
                        out[f.name] = f.defaultValue ?? '';
                    }
                }
                return out;
            },

            buildListItem(field) {
                return { _key: crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2), ...this.buildDefaultData(field.fields || []) };
            },

            reset() {
                this.sections = JSON.parse(JSON.stringify(this.originalSections));
                this.ensureSectionKeys();
                this.active = null;
                this.crumbs = [];
                this.dirty = false;
                this.closeAllSelects();
                this.$nextTick(() => {
                    this.initSectionSortable();
                    this.refreshPreview();
                });
            },

            removeSection(i) {
                // Destroy Sortable BEFORE mutating the array so it doesn't
                // interfere with Alpine's DOM diffing (otherwise the wrong
                // element gets removed from the DOM).
                if (this._sectionSortable) {
                    try { this._sectionSortable.destroy(); } catch (e) {}
                    this._sectionSortable = null;
                }
                this.sections.splice(i, 1);
                this.sections = [...this.sections];
                if (this.active === i || this.active >= this.sections.length) { this.active = null; this.crumbs = []; }
                this.dirty = true;
                this.schedulePreview();
                this.$nextTick(() => this.initSectionSortable());
            },

            addListItem(name) {
                const field = this.findField(name);
                if (!field) return;
                if (this.active === null) return;
                let d = this.sections[this.active].data;
                for (const crumb of this.crumbs) {
                    d = d[crumb.key];
                    if (crumb.index !== undefined) d = d[crumb.index];
                }
                if (!Array.isArray(d[name])) d[name] = [];
                d[name].push(this.buildListItem(field));
                d[name] = [...d[name]];
                this.dirty = true;
                this.schedulePreview();
                this.initListSortables();
            },

            removeListItem(name, index) {
                if (this.active === null) return;
                let d = this.sections[this.active].data;
                for (const crumb of this.crumbs) {
                    d = d[crumb.key];
                    if (crumb.index !== undefined) d = d[crumb.index];
                }
                if (Array.isArray(d[name])) {
                    d[name] = d[name].filter((_, idx) => idx !== index);
                    this.dirty = true;
                    this.schedulePreview();
                    this.initListSortables();
                }
            },

            findField(name) {
                if (this.active === null) return null;
                const section = this.sections[this.active];
                let fields = this.schemas[section.name] || [];
                for (const crumb of this.crumbs) {
                    const f = fields.find(f2 => f2.name === crumb.key);
                    if (!f) return null;
                    fields = f.fields || [];
                }
                return fields.find(f => f.name === name) || null;
            },

            cardLabel(item, field) {
                const candidates = ['title', 'label', 'name', 'heading', 'text'];
                for (const c of candidates) {
                    if (item[c] && typeof item[c] === 'string') return item[c].slice(0, 40);
                }
                return (field.itemLabel || field.label || 'Item') + ' ' + ((item._key || '').slice(0, 6));
            },

            parseTags(val) {
                if (!val) return [];
                try { return JSON.parse(val); } catch { return []; }
            },

            addTag(name, value) {
                const v = value.trim();
                if (!v) return;
                const current = this.parseTags(this.getField(name));
                current.push(v);
                this.setField(name, JSON.stringify(current));
            },

            removeTag(name, index) {
                const current = this.parseTags(this.getField(name));
                current.splice(index, 1);
                this.setField(name, JSON.stringify(current));
            },

            get filteredIcons() {
                let icons = this.faIcons;
                if (this.iconSearch.trim()) {
                    const q = this.iconSearch.toLowerCase();
                    icons = icons.filter(i => i.l.toLowerCase().includes(q) || i.c.toLowerCase().includes(q));
                }
                return icons.slice(0, 2000);
            },

            iconLabel(cls) {
                const found = this.faIcons.find(i => i.c === cls);
                return found ? found.l : cls;
            },

            schedulePreview() {
                if (window.__isResizingImage || window.__skipNextPreviewRefresh) {
                    return;
                }
                clearTimeout(this.previewTimer);
                this.previewTimer = setTimeout(() => this.refreshPreview(), 250);
            },

            refreshPreview() {
                const iframe = document.getElementById('preview-iframe');
                if (!iframe) return;
                if (this.sections.length === 0) {
                    const doc = iframe.contentDocument || iframe.contentWindow?.document;
                    if (doc) doc.body.innerHTML = '';
                    return;
                }
                const payload = { sections: this.sections, entry_data: this.entryData };
                if (window.editorPostId) payload.post_id = window.editorPostId;
                fetch('{{ route('admin.preview') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload),
                })
                .then(r => { if (!r.ok) throw new Error('Preview response ' + r.status); return r.json(); })
                .then(data => { this.updateIframeContent(iframe, data.html); })
                .catch(e => console.error('Preview fetch failed:', e));
            },

            updateIframeContent(iframe, html) {
                const doc = iframe.contentDocument || iframe.contentWindow?.document;
                if (!doc) return;

                doc.body.className = 'antialiased text-gray-800 m-0 p-0 min-h-full bg-white preview-shell';
                let container = doc.getElementById('preview-content');
                if (!container) {
                    doc.body.innerHTML = '<div id="preview-content">' + html + '</div>';
                } else {
                    if (window.morphdom) {
                        window.morphdom(container, '<div id="preview-content">' + html + '</div>');
                    } else {
                        const parser = new DOMParser();
                        const newDoc = parser.parseFromString(html, 'text/html');
                        const newNodes = Array.from(newDoc.body.children);
                        const currentNodes = Array.from(container.children);

                        if (newNodes.length === currentNodes.length && newNodes.length > 0) {
                            newNodes.forEach((node, idx) => {
                                const cur = currentNodes[idx];
                                if (cur && cur.outerHTML !== node.outerHTML) {
                                    cur.replaceWith(doc.importNode(node, true));
                                }
                            });
                        } else if (container.innerHTML !== html) {
                            container.innerHTML = html;
                        }
                    }
                }

                if (!doc.head.querySelector('[data-preview-styles]')) {
                    const styleMarker = doc.createElement('meta');
                    styleMarker.setAttribute('data-preview-styles', 'true');
                    doc.head.appendChild(styleMarker);

                    const vp = doc.createElement('meta');
                    vp.name = 'viewport';
                    vp.content = 'width=device-width, initial-scale=1';
                    doc.head.appendChild(vp);

                    document.querySelectorAll('link[rel="stylesheet"], style').forEach(el => {
                        doc.head.appendChild(el.cloneNode(true));
                    });

                    const baseStyle = doc.createElement('style');
                    baseStyle.textContent = 'html, body { margin: 0; padding: 0; min-height: 100%; background-color: #ffffff; }\n' +
                        '::-webkit-scrollbar { width: 6px; height: 6px; }\n' +
                        '::-webkit-scrollbar-track { background: transparent; }\n' +
                        '::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.4); border-radius: 9999px; }\n' +
                        '::-webkit-scrollbar-thumb:hover { background: rgba(107, 114, 128, 0.7); }\n' +
                        '* { scrollbar-width: thin; scrollbar-color: rgba(156, 163, 175, 0.4) transparent; }\n' +
                        '[data-edit], [data-list], [data-section-index] { cursor: pointer !important; }\n' +
                        '[data-edit]:hover { outline: 1px dashed #3b82f6 !important; position: relative; z-index: 50; }\n' +
                        '[data-edit]:not(img):not([data-edit-button]):hover { background-color: rgba(59, 130, 246, 0.07) !important; }\n' +
                        '[data-section-index]:hover { outline: 1px dashed #3b82f6 !important; border-radius: 4px; }\n' +
                        '[data-list]:hover { outline: 1px dashed #3b82f6 !important; }\n' +
                        '[data-package-block]:hover { outline: 1px dashed #3b82f6 !important; }';
                    doc.head.appendChild(baseStyle);
                }

                this.attachPreviewListeners(doc);
            },

            attachPreviewListeners(doc) {
                doc.addEventListener('click', (e) => {
                    const sectionEl = e.target.closest('[data-section-index]');
                    if (!sectionEl) return;
                    const link = e.target.closest('a');
                    if (link) { e.preventDefault(); e.stopPropagation(); }

                    const idx = parseInt(sectionEl.getAttribute('data-section-index'), 10);
                    if (isNaN(idx) || idx < 0 || idx >= this.sections.length) return;

                    const path = this.buildFieldPath(e.target);
                    this.focusField(path, idx);
                });
            },

            buildFieldPath(target) {
                const fieldEl = target.closest('[data-edit]');
                const listEl = !fieldEl ? target.closest('[data-list]') : null;

                if (!fieldEl && !listEl) return '_root';

                let leaf = '';
                let startEl = fieldEl;
                if (fieldEl) {
                    leaf = fieldEl.getAttribute('data-edit') || '';
                    if (!leaf) { startEl = null; leaf = ''; }
                } else if (listEl) {
                    startEl = listEl;
                }

                const listParts = [];
                let current = startEl;
                while (current) {
                    const listName = current.getAttribute('data-list');
                    if (listName) {
                        const explicitIndex = current.getAttribute('data-list-index');
                        if (explicitIndex !== null && explicitIndex !== '') {
                            listParts.unshift(`${listName}:${explicitIndex}`);
                        } else {
                            const parent = current.parentElement;
                            if (parent) {
                                const siblings = Array.from(parent.querySelectorAll(`[data-list="${listName}"]`));
                                const index = siblings.indexOf(current);
                                if (index >= 0) listParts.unshift(`${listName}:${index}`);
                            }
                        }
                    }
                    current = current.parentElement?.closest('[data-list]') ?? null;
                }

                if (leaf) {
                    return [...listParts, leaf].join('/');
                }
                return listParts.join('/') + '/';
            },

            focusField(cmd, sectionIdx) {
                if (sectionIdx !== undefined) this.active = sectionIdx;

                const raw = cmd.split('#')[0];
                if (raw === '_root') { this.crumbs = []; return; }

                const segs = raw.split('/');
                const leaf = segs.pop() || '';
                const newCrumbs = [];
                let curFields = this.schemas[this.sections[this.active]?.name] || [];
                let curData = this.sections[this.active]?.data || {};

                for (const s of segs) {
                    if (!s) continue;
                    const [key, idxStr] = s.split(':');
                    const def = curFields.find(f => f.name === key && f.type === 'object');
                    if (!def) break;
                    if (idxStr !== undefined) {
                        const index = parseInt(idxStr, 10);
                        if (!isNaN(index)) {
                            newCrumbs.push({ key, index });
                            curData = curData?.[key]?.[index] || {};
                        }
                    } else {
                        newCrumbs.push({ key });
                        curData = curData?.[key] || {};
                    }
                    curFields = def.fields || [];
                }

                this.crumbs = newCrumbs;

                this.$nextTick(() => {
                    const fieldEl = document.querySelector(`[data-field-target="${leaf}"]`);
                    if (fieldEl && fieldEl.offsetParent !== null) {
                        const proseMirror = fieldEl.classList.contains('ProseMirror') ? fieldEl : fieldEl.querySelector('.ProseMirror');
                        if (proseMirror) {
                            proseMirror.focus();
                        } else if (fieldEl.matches('input, textarea, button, select, [tabindex]')) {
                            try { fieldEl.focus(); } catch {}
                        } else {
                            this.highlightField(fieldEl);
                        }
                        try { fieldEl.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                    } else if (leaf) {
                        const scrollEl = document.querySelector(`[data-field-scroll="${leaf}"]`);
                        if (scrollEl) {
                            try { scrollEl.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                            const target = document.querySelector(`[data-field-target="${leaf}"]`);
                            if (target) this.highlightField(target);
                        }
                    }
                });
            },

            highlightField(el) {
                el.style.boxShadow = '0 0 0 3px rgba(59,130,246,0.5)';
                el.style.borderRadius = el.style.borderRadius || '8px';
                clearTimeout(el._highlightTimer);
                el._highlightTimer = setTimeout(() => { el.style.boxShadow = ''; }, 2000);
            },

            selects: {},

            getSelect(id) {
                if (!this.selects[id]) this.selects[id] = { open: false, search: '', highlight: -1 };
                return this.selects[id];
            },

            toggleSelect(id) {
                const s = this.getSelect(id);
                const wasOpen = s.open;
                this.closeAllSelects();
                if (!wasOpen) {
                    s.open = true;
                    this.$nextTick(() => {
                        if (this.selects[id]?.searchable) {
                            const input = document.getElementById('sel-search-' + id);
                            input?.focus();
                        }
                    });
                }
            },

            closeSelect(id) {
                const s = this.selects[id];
                if (s) { s.open = false; s.search = ''; s.highlight = -1; }
            },

            closeAllSelects() {
                Object.keys(this.selects).forEach(k => this.closeSelect(k));
            },

            selectKeydown(e, id) {
                const s = this.getSelect(id);
                if (!s.open) {
                    if (['Enter', ' ', 'ArrowDown'].includes(e.key)) { e.preventDefault(); s.open = true; }
                    return;
                }
                const list = s.searchable ? s.filtered : s.options;
                switch (e.key) {
                    case 'Escape': e.preventDefault(); s.open = false; break;
                    case 'ArrowDown': e.preventDefault(); s.highlight = Math.min(s.highlight + 1, (list?.length || 1) - 1); break;
                    case 'ArrowUp': e.preventDefault(); s.highlight = Math.max(s.highlight - 1, 0); break;
                    case 'Enter': e.preventDefault(); if (list?.[s.highlight]) { s.onSelect(list[s.highlight].value); s.open = false; } break;
                }
            },

            async save() {
                this.isSaving = true;
                const route = window.editorSaveRoute || '{{ route('admin.collections.entries.update-sections', [$collection, $entry]) }}';
                try {
                    const r = await fetch(route, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ sections: this.sections }),
                    });
                    if (r.ok) {
                        this.originalSections = JSON.parse(JSON.stringify(this.sections));
                        this.dirty = false;
                        this.isSaving = false;
                    } else {
                        this.isSaving = false;
                        alert('Save failed.');
                    }
                } catch {
                    this.isSaving = false;
                    alert('Save failed.');
                }
            }
        };
    }
</script>

@endpush
