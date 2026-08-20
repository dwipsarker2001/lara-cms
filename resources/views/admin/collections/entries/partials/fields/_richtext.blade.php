{{-- rich-text (TipTap WYSIWYG editor) --}}
<template x-if="field.type === 'rich-text'">
    <div x-data="{ showSourcePicker: false }">
        @include('admin.collections.entries.partials._field-label')

        <template x-if="isSourceField(field.name)">
            <div class="rounded-lg border border-primary/30 bg-primary/5 p-3 text-sm text-text-primary opacity-80 min-h-[100px] prose prose-sm max-w-none" x-html="getField(field.name)"></div>
        </template>

        <template x-if="!isSourceField(field.name)">
            <div class="tt-wrapper border border-gray-300 rounded-lg overflow-hidden"
                :data-field-target="field.name"
                x-init="mountTipTap(field.name, $el, getField(field.name) || '', (html) => setField(field.name, html))">

                {{-- Toolbar --}}
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

                    {{-- Single action buttons --}}
                    <button type="button" data-tt-cmd="prompt-link" class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors" title="Link">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                        <span class="tt-label">Link</span>
                    </button>
                    <button type="button" data-tt-cmd="prompt-image" class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors" title="Image">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                        <span class="tt-label">Image</span>
                    </button>

                    <span class="w-px h-5 bg-gray-300 shrink-0"></span>

                    <button type="button" data-tt-cmd="toggleBlockquote" class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors" title="Blockquote">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10 11h-4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v2c0 2.5-2 5-4 5"/><path d="M20 11h-4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v2c0 2.5-2 5-4 5"/></svg>
                        <span class="tt-label">Quote</span>
                    </button>
                    <button type="button" data-tt-cmd="toggleCodeBlock" class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors" title="Code Block">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m16 18 6-6-6-6"/><path d="m8 6-6 6 6 6"/></svg>
                        <span class="tt-label">Code block</span>
                    </button>

                    <span class="w-px h-5 bg-gray-300 shrink-0"></span>

                    <button type="button" data-tt-cmd="undo" class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 rounded transition-colors" title="Undo">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 10h13a4 4 0 0 1 0 8H7"/><path d="m7 6-4 4 4 4"/></svg>
                        <span class="tt-label">Undo</span>
                    </button>
                    <button type="button" data-tt-cmd="redo" class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 rounded transition-colors" title="Redo">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10H8a4 4 0 0 0 0 8h9"/><path d="m17 6 4 4-4 4"/></svg>
                        <span class="tt-label">Redo</span>
                    </button>

                    <div class="relative tt-dropdown tt-overflow-dd shrink-0 ml-auto" style="display:none">
                        <button type="button" data-tt-cmd="toggle-dropdown" data-tt-dropdown="overflow" class="tt-btn px-1.5 py-1.5 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors" title="More tools">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
                        </button>
                        <div data-tt-panel="overflow" class="bg-white rounded-lg shadow-lg border border-gray-200 p-1 min-w-[170px] hidden"></div>
                    </div>
                </div>

                <div class="tt-editor px-4 py-3 min-h-[200px] max-h-[480px] overflow-y-auto prose prose-sm max-w-none focus:outline-none"></div>
            </div>
        </template>
    </div>
</template>
