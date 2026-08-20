@extends('admin.layout')

@section('title', 'Editor — '.$entry->title)
@section('breadcrumb', 'Editor')

@section('content-full')
<script src="https://cdn.jsdelivr.net/npm/morphdom@2.7.4/dist/morphdom-umd.min.js"></script>
<script>
    {{-- Blade-injected runtime data (routes + JSON data — must stay server-side) --}}
    window.editorSections               = @json($syncedSections ?? $entry->sections ?? []);
    window.editorSchemas                = @json($blockSchemas);
    window.editorBlockList              = @json($blockList);
    window.editorSlug                   = '{{ Str::slug($entry->title) }}';
    window.editorPages                  = @json($pages);
    window.editorHomeGlobals            = @json($homeGlobals);
    window.editorEntryData              = @json($entryData ?? (object)[]);
    window.editorCollectionFields       = @json($collectionFields ?? []);
    window.editorGroupedCollectionFields = @json($groupedCollectionFields ?? []);
    window.editorSettingsCustomValues   = @json($settingsCustomValues ?? (object)[]);
    window.editorSaveRoute              = '{{ route('admin.collections.entries.update-sections', [$collection, $entry]) }}';
    window.editorPreviewRoute           = '{{ route('admin.preview') }}';
    window.editorCsrfToken              = '{{ csrf_token() }}';
    window.editorPostId                 = null;
    window.editorForms                  = @json($availableForms ?? []);
</script>

<div class="flex h-full p-3 relative" id="page-editor-root" style="--sb-w: 420px;"
    x-data="pageEditor()"
    x-init="init(window.editorSections, window.editorSchemas, window.editorBlockList, window.editorSlug, window.editorPages, window.editorHomeGlobals)"
    x-on:section-selected.window="addSection($event.detail.name)"
>
    {{-- ===================================================
         Editor sidebar panel
    =================================================== --}}
    <div class="shrink-0 bg-white h-full flex flex-col rounded-2xl border border-[#e8eaed] shadow-[0_1px_2px_rgba(16,24,40,0.04),0_8px_24px_-12px_rgba(16,24,40,0.12)] overflow-hidden"
        style="width: var(--sb-w); min-width: 280px;"
        x-show="sidebarOpen"
    >
        <div class="flex-1 overflow-y-auto px-3 pt-3 pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div class="bg-gray-100 rounded-2xl p-[7px]">

                {{-- -----------------------------------------------
                     Section list mode
                ----------------------------------------------- --}}
                <div x-show="active === null">
                    <div class="flex items-center justify-between pr-3 py-3 text-sm font-medium text-text-heading">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.collections.entries.index', $collection) }}" aria-label="Back"
                                class="size-7 shrink-0 flex items-center justify-center rounded-full border border-gray-300 bg-white text-text-primary hover:bg-gray-100 transition-colors">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3">
                                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <div class="font-bold">Sections</div>
                        </div>
                        <button type="button"
                            @click="window.dispatchEvent(new CustomEvent('open-section-picker'))"
                            class="size-6 flex items-center justify-center rounded-full bg-white text-text-primary border border-content-border hover:bg-gray-50 transition-colors">
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
                                        <circle cx="8" cy="6" r="2.5" /><circle cx="16" cy="6" r="2.5" />
                                        <circle cx="8" cy="12" r="2.5" /><circle cx="16" cy="12" r="2.5" />
                                        <circle cx="8" cy="18" r="2.5" /><circle cx="16" cy="18" r="2.5" />
                                    </svg>
                                </div>
                                <div class="flex flex-1 min-w-0 flex-col px-1.5 py-2 cursor-pointer">
                                    <div @click="edit(i, 'title')" class="flex items-center gap-1.5 min-w-0">
                                        <span class="text-sm font-semibold text-text-heading group-hover:text-primary truncate leading-normal transition-colors" x-text="sectionLabel(section)"></span>
                                        <span x-show="isGlobal(section)" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-primary/10 text-primary shrink-0">Global</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-0.5 shrink-0 ml-auto pr-1">
                                    <button @click.stop="edit(i)" class="p-1 text-text-muted/60 hover:text-primary group-hover:text-primary transition-colors rounded hover:bg-text-primary/10" title="Edit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>
                                    <button @click.stop="removeSection(i)" class="p-1 text-text-muted/60 hover:text-danger transition-colors rounded hover:bg-text-primary/10" title="Remove section">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- -----------------------------------------------
                     Field editor mode
                ----------------------------------------------- --}}
                <div x-show="active !== null">
                    <div class="flex items-center gap-2 mb-3">
                        <button @click="exit()" aria-label="Back"
                            class="size-7 shrink-0 flex items-center justify-center rounded-full border border-gray-300 bg-white text-text-primary hover:bg-gray-100 transition-colors">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3">
                                <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="grow truncate text-sm font-bold text-text-heading">
                            <span x-text="editorTitle()"></span>
                        </div>
                    </div>

                    {{-- Field list --}}
                    <div class="flex flex-col gap-3 p-0.5">
                        <template x-for="field in currentFields()" :key="field.name">
                            <div :data-field-scroll="field.name">
                                @include('admin.collections.entries.partials.fields._string')
                                @include('admin.collections.entries.partials.fields._textarea')
                                @include('admin.collections.entries.partials.fields._number')
                                @include('admin.collections.entries.partials.fields._boolean')
                                @include('admin.collections.entries.partials.fields._devices')
                                @include('admin.collections.entries.partials.fields._select')
                                @include('admin.collections.entries.partials.fields._form')
                                @include('admin.collections.entries.partials.fields._image')
                                @include('admin.collections.entries.partials.fields._icon')
                                @include('admin.collections.entries.partials.fields._map')
                                @include('admin.collections.entries.partials.fields._richtext')
                                @include('admin.collections.entries.partials.fields._date')
                                @include('admin.collections.entries.partials.fields._link')
                                @include('admin.collections.entries.partials.fields._tags')
                                @include('admin.collections.entries.partials.fields._object')
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <div class="shrink-0 px-4 py-3 border-t border-content-border bg-body-bg flex items-center justify-end gap-2">
            <button type="button" @click="reset()" x-bind:disabled="!dirty"
                class="px-4 py-1.5 text-sm font-medium text-text-primary bg-content-bg border border-content-border rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Reset
            </button>
            <button type="button" @click="save()" x-bind:disabled="!dirty"
                class="px-4 py-1.5 text-sm font-medium text-white bg-primary rounded-lg hover:opacity-90 transition-opacity disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2">
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

    {{-- ===================================================
         Sidebar ↔ Preview resize handle
    =================================================== --}}
    <div x-show="sidebarOpen"
        class="relative shrink-0 h-full flex items-center justify-center cursor-col-resize select-none group z-10"
        style="width: 10px;"
        @mousedown="startSidebarResize($event)"
    >
        <div class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-px bg-transparent group-hover:bg-gray-300 transition-colors duration-150 pointer-events-none"></div>
        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-150 pointer-events-none flex flex-col gap-[4px] items-center justify-center bg-white border border-gray-200 rounded-full px-[3px] py-[6px] shadow-sm">
            <span class="block w-[3px] h-[3px] rounded-full bg-gray-400"></span>
            <span class="block w-[3px] h-[3px] rounded-full bg-gray-400"></span>
            <span class="block w-[3px] h-[3px] rounded-full bg-gray-400"></span>
            <span class="block w-[3px] h-[3px] rounded-full bg-gray-400"></span>
        </div>
    </div>

    {{-- ===================================================
         Preview iframe pane
    =================================================== --}}
    <div class="flex-1 flex flex-col min-w-0 h-full relative items-center justify-start overflow-visible">
        <div class="relative flex-1 w-full h-full flex justify-center overflow-visible">
            <div class="relative flex flex-col w-full h-full" x-ref="previewFrame">
                <div class="flex-1 overflow-hidden preview-shell h-full bg-white rounded-2xl border border-[#e8eaed] shadow-[0_1px_2px_rgba(16,24,40,0.04),0_8px_24px_-12px_rgba(16,24,40,0.12)]">
                    <iframe id="preview-iframe" class="w-full h-full min-h-full border-0 block bg-white"></iframe>
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

@push('styles')
<script src="/js/page-editor.js?v={{ filemtime(public_path('js/page-editor.js')) }}"></script>
@endpush
