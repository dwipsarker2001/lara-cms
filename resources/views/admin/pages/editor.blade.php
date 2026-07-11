@extends('admin.layout')

@section('title', 'Editor — '.$page->title)
@section('breadcrumb', 'Editor')

@section('content-full')
<script>
    window.editorSections = @json($page->sections ?? []);
    window.editorSchemas = @json($blockSchemas);
    window.editorBlockList = @json($blockList);
    window.editorSlug = '{{ $page->slug }}';
</script>
<div class="flex h-full gap-3 p-3" x-data="pageEditor()" x-init="init(window.editorSections, window.editorSchemas, window.editorBlockList, window.editorSlug)">
    {{-- Editor panel --}}
    <div class="w-[420px] min-w-[320px] shrink-0 bg-white h-full flex flex-col rounded-2xl border border-[#e8eaed] shadow-[0_1px_2px_rgba(16,24,40,0.04),0_8px_24px_-12px_rgba(16,24,40,0.12)] overflow-hidden">
        <div class="flex-1 overflow-y-auto px-3 pt-3 pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div class="bg-gray-100 rounded-2xl p-[7px]">
                {{-- Section list mode --}}
                    <div x-show="active === null">
                        <div class="flex items-center justify-between px-3 py-3 text-sm font-medium text-text-heading">
                            <div class="font-bold">Sections</div>
                            <button
                                type="button"
                                @click="showPicker = true"
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
                                    <div
                                        @click="edit(i)"
                                        role="button"
                                        tabindex="0"
                                        class="flex flex-1 min-w-0 items-center px-1.5 py-2.5 text-xs leading-normal text-left cursor-pointer"
                                    >
                                        <div class="flex min-w-0 flex-1 items-center">
                                            <span class="text-sm font-semibold text-text-heading group-hover:text-primary truncate leading-normal transition-colors" x-text="sectionLabel(section)"></span>
                                        </div>
                                        <div class="flex items-center gap-0.5 shrink-0 ml-1">
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
                    </div>
                    <div class="flex flex-col gap-3 p-0.5">
                        <template x-for="field in currentFields()" :key="field.name">
                            <div :data-field-scroll="field.name">
                                {{-- string (input) --}}
                                <template x-if="field.type === 'string' && !field.multiline">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                        <input type="text" :value="getField(field.name)" @input="setField(field.name, $event.target.value)"
                                            :data-field-target="field.name"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    </div>
                                </template>

                                {{-- string (textarea) --}}
                                <template x-if="field.type === 'string' && field.multiline">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                        <textarea :value="getField(field.name)" @input="setField(field.name, $event.target.value)"
                                            :data-field-target="field.name"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-y min-h-[80px]" rows="3"></textarea>
                                    </div>
                                </template>

                                {{-- number --}}
                                <template x-if="field.type === 'number'">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                        <input type="number" :value="getField(field.name)" @input="setField(field.name, parseFloat($event.target.value) || '')"
                                            :data-field-target="field.name"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
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

                                {{-- image --}}
                                <template x-if="field.type === 'image'">
                                    <div :data-field-target="field.name">
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                        <div
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
                                                    class="absolute top-1 right-1 text-[11px] font-medium text-white bg-danger/80 hover:bg-danger rounded px-2 py-0.5 transition-colors"
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

                                {{-- rich-text --}}
                                        <template x-if="field.type === 'rich-text'">
                                            <div>
                                                <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                                <textarea :value="getField(field.name)" @input="setField(field.name, $event.target.value)"
                                                    :data-field-target="field.name"
                                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary font-mono resize-y min-h-[100px]" rows="4"></textarea>
                                    </div>
                                </template>

                                {{-- link --}}
                                        <template x-if="field.type === 'link'">
                                            <div>
                                                <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
                                                <input type="text" :value="getField(field.name)" @input="setField(field.name, $event.target.value)"
                                                    placeholder="/page-url or https://..."
                                                    :data-field-target="field.name"
                                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
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
                                                    <div class="rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)]">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="text-sm font-semibold text-text-primary" x-text="field.label"></span>
                                                            <button type="button" @click="addListItem(field.name)" class="text-xs text-primary hover:text-primary/80 font-medium">+ Add <span x-text="field.label.toLowerCase()"></span></button>
                                                </div>
                                                <template x-if="getList(field.name).length > 0">
                                                    <div class="space-y-0.5">
                                                        <template x-for="(item, ci) in getList(field.name)" :key="item._key">
                                                            <div class="flex rounded-lg shadow-sm bg-gray-50 mb-0.5 group overflow-hidden">
                                                                <div class="w-6 shrink-0 flex items-center justify-center cursor-grab active:cursor-grabbing opacity-70 hover:opacity-100 touch-none transition-opacity text-text-muted/70">
                                                                    <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                                                                        <circle cx="8" cy="6" r="2.5" /><circle cx="16" cy="6" r="2.5" />
                                                                        <circle cx="8" cy="12" r="2.5" /><circle cx="16" cy="12" r="2.5" />
                                                                        <circle cx="8" cy="18" r="2.5" /><circle cx="16" cy="18" r="2.5" />
                                                                    </svg>
                                                                </div>
                                                                <div class="flex flex-1 min-w-0 items-center px-1.5 py-2 text-xs leading-normal">
                                                                    <span class="text-sm font-semibold text-text-heading truncate flex-1" x-text="cardLabel(item, field)"></span>
                                                                    <div class="flex items-center gap-0.5 shrink-0 ml-1">
                                                                        <button @click="drillIn(field.name, ci)" class="p-1 text-text-muted/60 hover:text-primary transition-colors rounded hover:bg-text-primary/10" title="Edit">
                                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5">
                                                                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                                            </svg>
                                                                        </button>
                                                                        <button @click="removeListItem(field.name, ci)" class="p-1 text-text-muted/60 hover:text-danger transition-colors rounded hover:bg-text-primary/10" title="Remove">
                                                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5">
                                                                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                {{-- background --}}
                                <template x-if="field.type === 'background'">
                                    <div :data-field-target="field.name">
                                        <label class="block text-sm font-semibold text-text-primary mb-1">Background</label>
                                        <button @click="drillIn(field.name)"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-left text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] flex items-center justify-between"
                                        >
                                            <span x-text="getField(field.name) ? 'Configured' : 'Not configured'" :class="getField(field.name) ? '' : 'text-text-muted'"></span>
                                            <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                        </button>
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
            <button type="button" class="px-4 py-1.5 text-sm font-medium text-text-primary bg-content-bg border border-content-border rounded-lg hover:bg-gray-50 transition-colors">
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
    <div class="flex-1 bg-white rounded-2xl border border-[#e8eaed] shadow-[0_1px_2px_rgba(16,24,40,0.04),0_8px_24px_-12px_rgba(16,24,40,0.12)] overflow-hidden flex flex-col">
        <div class="shrink-0 px-4 py-2 border-b border-content-border bg-panel-bg flex items-center gap-2 text-[13px] text-text-muted">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            <span>Preview</span>
            <span x-show="dirty" class="text-xs text-primary font-medium ml-1">(unsaved)</span>
        </div>
        <div class="flex-1 overflow-y-auto preview-shell">
            <div id="preview-content" class="min-h-full"></div>
        </div>
    </div>

    {{-- Add-section picker modal --}}
    <div x-show="showPicker" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center" @click.self="showPicker = false">
        <div class="bg-content-bg rounded-xl shadow-xl w-[420px] max-h-[80vh] overflow-y-auto p-6" @click.stop>
            <div class="flex items-center justify-between gap-2 mb-4">
                <h3 class="text-lg font-bold text-text-heading">Components</h3>
                <button @click="showPicker = false" class="text-sm text-text-muted hover:text-text-heading">Cancel</button>
            </div>
            <div class="space-y-2.5">
                <template x-for="item in blockList" :key="item.name">
                    <button
                        @click="addSection(item.name); showPicker = false"
                        class="group block w-full min-w-0 cursor-pointer overflow-hidden rounded-lg bg-content-bg ring-1 ring-content-border/60 shadow-sm transition-shadow hover:shadow-md text-left"
                    >
                        <div class="flex items-center gap-2 px-3 py-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-panel-bg text-text-muted">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-5">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-text-primary group-hover:text-primary transition-colors" x-text="item.label"></div>
                                <div class="text-[11px] text-text-muted" x-text="item.name"></div>
                            </div>
                            <span class="ml-auto shrink-0 inline-flex size-6 items-center justify-center rounded-full bg-content-border/40 text-text-muted transition-colors group-hover:bg-primary group-hover:text-white">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[14px]">
                                    <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                                </svg>
                            </span>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function pageEditor() {
        return {
            sections: [],
            schemas: {},
            blockList: [],
            slug: '',
            active: null,
            crumbs: [],
            dirty: false,
            showPicker: false,
            isSaving: false,
            previewTimer: null,
            iconPickerOpen: false,
            iconSearch: '',
            iconLoading: false,
            faIcons: window.FA_ICONS || [],

            init(sections, schemas, blockList, slug) {
                this.sections = JSON.parse(JSON.stringify(sections));
                this.schemas = schemas;
                this.blockList = blockList;
                this.slug = slug;
                this.$nextTick(() => this.initSectionSortable());
                this.refreshPreview();
            },

            initSectionSortable() {
                if (this._sectionSortable) this._sectionSortable.destroy();
                const el = this.$refs?.sectionList;
                if (!el) return;
                this._sectionSortable = new Sortable(el, {
                    handle: '.cursor-grab',
                    animation: 200,
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    onEnd: (evt) => {
                        if (evt.oldIndex === evt.newIndex) return;
                        const item = this.sections.splice(evt.oldIndex, 1)[0];
                        this.sections.splice(evt.newIndex, 0, item);
                        this.dirty = true;
                        this.schedulePreview();
                    },
                });
            },

            moveSection(from, to) {
                const item = this.sections.splice(from, 1)[0];
                this.sections.splice(to, 0, item);
                this.dirty = true;
                this.schedulePreview();
            },

            sectionLabel(section) {
                const s = this.schemas[section.name];
                return section.data?.title || section.data?.heading || section.data?.headline || (s ? s.label : section.name) || section.name;
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
                return this.currentData()[name] ?? '';
            },

            setField(name, value) {
                this.setNested(name, value);
                this.dirty = true;
                this.schedulePreview();
            },

            setNested(name, value) {
                if (this.active === null) return;
                let d = this.sections[this.active].data;
                for (const crumb of this.crumbs) {
                    d = d[crumb.key];
                    if (crumb.index !== undefined) d = d[crumb.index];
                }
                d[name] = value;
            },

            getList(name) {
                const val = this.currentData()[name];
                return Array.isArray(val) ? val : [];
            },

            drillIn(key, index) {
                this.crumbs.push({ key, index: index ?? undefined });
            },

            exit() {
                if (this.crumbs.length > 0) {
                    this.crumbs.pop();
                } else {
                    this.active = null;
                    this.$nextTick(() => this.initSectionSortable());
                }
            },

            edit(i) {
                this.active = i;
                this.crumbs = [];
            },

            addSection(name) {
                const section = this.createDefault(name);
                if (section) {
                    this.sections.push(section);
                    this.dirty = true;
                    this.schedulePreview();
                    this.$nextTick(() => this.initSectionSortable());
                }
            },

            createDefault(name) {
                const schema = this.schemas[name];
                if (!schema) return null;
                return {
                    _key: crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2),
                    name: name,
                    data: this.buildDefaultData(schema),
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

            removeSection(i) {
                this.sections.splice(i, 1);
                if (this.active === i) { this.active = null; this.crumbs = []; }
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
                this.dirty = true;
                this.schedulePreview();
            },

            removeListItem(name, index) {
                if (this.active === null) return;
                let d = this.sections[this.active].data;
                for (const crumb of this.crumbs) {
                    d = d[crumb.key];
                    if (crumb.index !== undefined) d = d[crumb.index];
                }
                if (Array.isArray(d[name])) {
                    d[name].splice(index, 1);
                    this.dirty = true;
                    this.schedulePreview();
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
                clearTimeout(this.previewTimer);
                this.previewTimer = setTimeout(() => this.refreshPreview(), 150);
            },

            refreshPreview() {
                const el = document.getElementById('preview-content');
                if (!el) return;
                fetch('{{ route('admin.preview') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ sections: this.sections }),
                })
                .then(r => r.json())
                .then(data => { el.innerHTML = data.html; this.attachPreviewListeners(el); })
                .catch(() => {});
            },

            attachPreviewListeners(el) {
                el.addEventListener('click', (e) => {
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
                        const parent = current.parentElement;
                        if (parent) {
                            const siblings = Array.from(parent.querySelectorAll(`[data-list="${listName}"]`));
                            const index = siblings.indexOf(current);
                            if (index >= 0) listParts.unshift(`${listName}:${index}`);
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
                        try { fieldEl.focus(); } catch {}
                        try { fieldEl.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                    } else if (leaf) {
                        const scrollEl = document.querySelector(`[data-field-scroll="${leaf}"]`);
                        if (scrollEl) {
                            try { scrollEl.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                            const target = document.querySelector(`[data-field-target="${leaf}"]`);
                            if (target) {
                                target.style.boxShadow = '0 0 0 3px rgba(59,130,246,0.5)';
                                setTimeout(() => { target.style.boxShadow = ''; }, 2000);
                            }
                        }
                    }
                });
            },

            async save() {
                this.isSaving = true;
                try {
                    const r = await fetch('{{ route('admin.pages.update-sections', $page) }}', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ sections: this.sections }),
                    });
                    if (r.ok) {
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
