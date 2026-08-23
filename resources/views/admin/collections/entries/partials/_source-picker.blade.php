{{--
    Source picker popover.
    Allows binding any input to:
    1) Current Page fields
    2) Site Settings fields
    3) Any Collection Entry's fields (Packages, Destinations, Blogs, etc.)
--}}
<div x-show="showSourcePicker" @click.outside="showSourcePicker = false"
    x-data="{
        pickerMode: 'current_page',
        selectedEntryId: null,
        entrySearch: '',
        modePickerOpen: false,
        entryPickerOpen: false,
        init() {
            this.sync();
            this.$watch('showSourcePicker', value => {
                if (value) {
                    this.sync();
                }
            });
        },
        sync() {
            const currentSrc = getSourceKey(field.name);
            if (currentSrc) {
                if (currentSrc.startsWith('entry:')) {
                    const parts = currentSrc.split(':');
                    this.selectedEntryId = parts[1];
                    let foundSlug = null;
                    const matched = (pages || []).find(p => String(p.id) === String(parts[1]));
                    if (matched && matched.collection_slug) {
                        foundSlug = matched.collection_slug;
                    }
                    if (!foundSlug) {
                        const cols = window.editorAllCollections || (typeof allCollections !== 'undefined' ? allCollections : []);
                        for (const c of cols) {
                            if (c.entries && c.entries.some(e => String(e.id) === String(parts[1]))) {
                                foundSlug = c.slug;
                                break;
                            }
                        }
                    }
                    this.pickerMode = foundSlug || (getLinkCollections()[0]?.slug || 'current_page');
                } else if (currentSrc.startsWith('term:')) {
                    const parts = currentSrc.split(':');
                    this.selectedEntryId = parts[1];
                    const allTaxes = window.editorAllTaxonomies || [];
                    for (const t of allTaxes) {
                        if (t.terms && t.terms.some(item => String(item.id) === String(parts[1]))) {
                            this.pickerMode = 'tax:' + t.slug;
                            break;
                        }
                    }
                } else {
                    const settingsGroup = (groupedCollectionFields || []).find(g => g.collection_id === 'site_settings');
                    if (settingsGroup && settingsGroup.fields.some(f => f.key === currentSrc)) {
                        this.pickerMode = 'site_settings';
                    } else {
                        this.pickerMode = 'current_page';
                    }
                }
            } else {
                let scopeEntryId = null;
                let scopeColSlug = null;
                const candidates = ['package_id', 'entry_id', 'deal_id', 'collection_entry_id'];
                for (const cKey of candidates) {
                    const val = typeof getField === 'function' ? getField(cKey) : null;
                    if (val) {
                        scopeEntryId = String(val);
                        break;
                    }
                }
                if (scopeEntryId) {
                    this.selectedEntryId = scopeEntryId;
                    const cols = window.editorAllCollections || (typeof allCollections !== 'undefined' ? allCollections : []);
                    for (const c of cols) {
                        if (c.entries && c.entries.some(e => String(e.id) === String(scopeEntryId))) {
                            scopeColSlug = c.slug;
                            break;
                        }
                    }
                    this.pickerMode = scopeColSlug || (getLinkCollections()[0]?.slug || 'current_page');
                } else {
                    const taxCandidates = ['destination_id', 'term_id'];
                    let scopeTermId = null;
                    for (const tKey of taxCandidates) {
                        const val = typeof getField === 'function' ? getField(tKey) : null;
                        if (val) {
                            scopeTermId = String(val);
                            break;
                        }
                    }
                    if (scopeTermId) {
                        this.selectedEntryId = scopeTermId;
                        const allTaxes = window.editorAllTaxonomies || [];
                        for (const t of allTaxes) {
                            if (t.terms && t.terms.some(item => String(item.id) === String(scopeTermId))) {
                                this.pickerMode = 'tax:' + t.slug;
                                break;
                            }
                        }
                    } else {
                        this.pickerMode = 'current_page';
                        this.selectedEntryId = null;
                    }
                }
            }
        },
        getModeLabel() {
            if (this.pickerMode === 'current_page') return 'Current Page';
            if (this.pickerMode === 'site_settings') return 'Site Settings';
            if (this.pickerMode.startsWith('tax:')) {
                const taxSlug = this.pickerMode.substring(4);
                const tax = (window.editorAllTaxonomies || []).find(t => t.slug === taxSlug);
                return tax ? ('Taxonomy: ' + tax.title) : 'Taxonomy';
            }
            const col = getLinkCollections().find(c => c.slug === this.pickerMode);
            return col ? col.name : this.pickerMode;
        },
        getSelectedEntryTitle() {
            if (!this.selectedEntryId) return 'Select item...';
            if (this.pickerMode.startsWith('tax:')) {
                const taxSlug = this.pickerMode.substring(4);
                const tax = (window.editorAllTaxonomies || []).find(t => t.slug === taxSlug);
                const term = (tax?.terms || []).find(item => String(item.id) === String(this.selectedEntryId));
                return term ? term.title : 'Select item...';
            }
            let matched = (pages || []).find(p => String(p.id) === String(this.selectedEntryId));
            if (!matched) {
                const cols = window.editorAllCollections || (typeof allCollections !== 'undefined' ? allCollections : []);
                for (const c of cols) {
                    if (c.entries) {
                        const found = c.entries.find(e => String(e.id) === String(this.selectedEntryId));
                        if (found) { matched = found; break; }
                    }
                }
            }
            return matched ? matched.title : 'Select item...';
        },
        getFilteredEntries() {
            if (this.pickerMode.startsWith('tax:')) {
                const taxSlug = this.pickerMode.substring(4);
                const tax = (window.editorAllTaxonomies || []).find(t => t.slug === taxSlug);
                const list = tax?.terms || [];
                if (!this.entrySearch) return list;
                return list.filter(p => (p.title || '').toLowerCase().includes(this.entrySearch.toLowerCase()));
            }
            const list = getLinkEntries(this.pickerMode);
            if (!this.entrySearch) return list;
            return list.filter(p => (p.title || '').toLowerCase().includes(this.entrySearch.toLowerCase()));
        },
        getCurrentPageFields() {
            const list = [
                { key: 'title', label: 'Title' },
                { key: 'slug', label: 'Slug' },
                { key: 'created_at', label: 'Created At' },
                { key: 'updated_at', label: 'Updated At' },
                { key: 'created_by', label: 'Created By / Author' },
            ];
            const pageGroup = (groupedCollectionFields || []).find(g => g.collection_id !== 'site_settings');
            if (pageGroup && Array.isArray(pageGroup.fields)) {
                for (const f of pageGroup.fields) {
                    if (!list.some(existing => existing.key === f.key)) {
                        list.push(f);
                    }
                }
            }
            if (entryData && typeof entryData === 'object') {
                for (const [k, v] of Object.entries(entryData)) {
                    if (k && !k.startsWith('_') && !list.some(existing => existing.key === k)) {
                        list.push({ key: k, label: k.replace(/[_-]+/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) });
                    }
                }
            }
            return list;
        },
        getSiteSettingsFields() {
            const list = [];
            const settingsGroup = (groupedCollectionFields || []).find(g => g.collection_id === 'site_settings');
            if (settingsGroup && Array.isArray(settingsGroup.fields)) {
                for (const f of settingsGroup.fields) {
                    list.push(f);
                }
            }
            if (siteSettings && typeof siteSettings === 'object') {
                for (const [k, v] of Object.entries(siteSettings)) {
                    if (k && !k.startsWith('_') && !list.some(existing => existing.key === k)) {
                        list.push({ key: k, label: k.replace(/[_-]+/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) });
                    }
                }
            }
            return list;
        }
    }"
    class="absolute right-0 z-50 mt-1 min-w-[280px] w-72 rounded-xl border border-gray-200 bg-white py-1 shadow-xl ring-1 ring-black/5 text-xs text-left"
    x-transition
>
    {{-- Header: Title + Unlink button --}}
    <div class="px-3 py-2 border-b border-gray-100 flex items-center justify-between font-semibold text-xs text-gray-700">
        <span>Bind Input</span>
        <template x-if="isSourceField(field.name)">
            <button type="button" @click="clearFieldSource(field.name); showSourcePicker = false"
                class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-[11px] font-medium transition-colors cursor-pointer"
                title="Unlink field"
            >
                <svg class="size-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="m18.84 12.25 1.72-1.71h0a5 5 0 0 0-.7-7.07l-.14-.13a5 5 0 0 0-7.07 0l-1.72 1.71"/>
                    <path d="m5.16 11.75-1.72 1.71h0a5 5 0 0 0 .7 7.07l.14.13a5 5 0 0 0 7.07 0l1.72-1.71"/>
                    <line x1="2" y1="2" x2="22" y2="22"/>
                </svg>
                <span>Unlink</span>
            </button>
        </template>
    </div>

    {{-- Source Selector --}}
    <div class="px-3 py-2 border-b border-gray-100 bg-gray-50/70">
        <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Source</label>
        <div class="relative">
            <button type="button" @click="modePickerOpen = !modePickerOpen"
                class="w-full flex items-center justify-between gap-2 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-300 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary cursor-pointer"
                :class="modePickerOpen ? 'border-primary ring-1 ring-primary' : ''"
            >
                <span class="truncate" x-text="getModeLabel()"></span>
                <svg class="size-3.5 shrink-0 text-gray-400 transition-transform duration-200" :class="modePickerOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>

            <div x-show="modePickerOpen" @click.outside="modePickerOpen = false"
                class="absolute inset-x-0 top-full z-50 mt-1 max-h-48 overflow-y-auto scrollbar-thin rounded-lg border border-gray-200 bg-white py-1 shadow-xl ring-1 ring-black/5"
                x-transition
            >
                {{-- 1. Current Page --}}
                <button type="button" @click="pickerMode = 'current_page'; selectedEntryId = null; modePickerOpen = false"
                    class="w-full flex items-center justify-between px-2.5 py-1.5 text-left text-xs transition-colors cursor-pointer"
                    :class="pickerMode === 'current_page' ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:bg-gray-50'"
                >
                    <span>Current Page</span>
                    <svg x-show="pickerMode === 'current_page'" class="size-3.5 shrink-0 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </button>

                {{-- 2. Site Settings --}}
                <button type="button" @click="pickerMode = 'site_settings'; selectedEntryId = null; modePickerOpen = false"
                    class="w-full flex items-center justify-between px-2.5 py-1.5 text-left text-xs transition-colors cursor-pointer"
                    :class="pickerMode === 'site_settings' ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:bg-gray-50'"
                >
                    <span>Site Settings</span>
                    <svg x-show="pickerMode === 'site_settings'" class="size-3.5 shrink-0 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </button>

                <div class="h-px bg-gray-100 my-1"></div>
                <div class="px-2.5 py-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Collections</div>

                {{-- 3...N. Collections --}}
                <template x-for="col in getLinkCollections()" :key="col.slug">
                    <button type="button" @click="pickerMode = col.slug; selectedEntryId = null; modePickerOpen = false"
                        class="w-full flex items-center justify-between px-2.5 py-1.5 text-left text-xs transition-colors cursor-pointer"
                        :class="pickerMode === col.slug ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:bg-gray-50'"
                    >
                        <span class="truncate" x-text="col.name"></span>
                        <svg x-show="pickerMode === col.slug" class="size-3.5 shrink-0 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                    </button>
                </template>

                <div class="h-px bg-gray-100 my-1"></div>
                <div class="px-2.5 py-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Taxonomies</div>

                {{-- Taxonomies --}}
                <template x-for="tax in (window.editorAllTaxonomies || [])" :key="tax.slug">
                    <button type="button" @click="pickerMode = 'tax:' + tax.slug; selectedEntryId = null; modePickerOpen = false"
                        class="w-full flex items-center justify-between px-2.5 py-1.5 text-left text-xs transition-colors cursor-pointer"
                        :class="pickerMode === ('tax:' + tax.slug) ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:bg-gray-50'"
                    >
                        <span class="truncate" x-text="tax.title"></span>
                        <svg x-show="pickerMode === ('tax:' + tax.slug)" class="size-3.5 shrink-0 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- Mode 1: Current Page Fields --}}
    <template x-if="pickerMode === 'current_page'">
        <div class="max-h-56 overflow-y-auto py-1 scrollbar-thin">
            <template x-for="cf in getCurrentPageFields()" :key="'cp_' + cf.key">
                <button type="button" @click="setFieldSource(field.name, cf.key); showSourcePicker = false"
                    class="w-full flex items-center justify-between px-3 py-1.5 text-left text-xs transition-colors cursor-pointer hover:bg-primary/10"
                    :class="getSourceKey(field.name) === cf.key ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700'"
                >
                    <span class="truncate pr-2" x-text="cf.label"></span>
                    <span class="text-[10px] font-mono text-gray-400 shrink-0" x-text="cf.key"></span>
                </button>
            </template>
            <template x-if="getCurrentPageFields().length === 0">
                <div class="px-3 py-4 text-gray-400 italic text-center text-xs">No page fields available</div>
            </template>
        </div>
    </template>

    {{-- Mode 2: Site Settings Fields --}}
    <template x-if="pickerMode === 'site_settings'">
        <div class="max-h-56 overflow-y-auto py-1 scrollbar-thin">
            <template x-for="cf in getSiteSettingsFields()" :key="'ss_' + cf.key">
                <button type="button" @click="setFieldSource(field.name, cf.key); showSourcePicker = false"
                    class="w-full flex items-center justify-between px-3 py-1.5 text-left text-xs transition-colors cursor-pointer hover:bg-primary/10"
                    :class="getSourceKey(field.name) === cf.key ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700'"
                >
                    <span class="truncate pr-2" x-text="cf.label"></span>
                    <span class="text-[10px] font-mono text-gray-400 shrink-0" x-text="cf.key"></span>
                </button>
            </template>
            <template x-if="getSiteSettingsFields().length === 0">
                <div class="px-3 py-4 text-gray-400 italic text-center text-xs">No settings fields available</div>
            </template>
        </div>
    </template>

    {{-- Mode 3: Collection or Taxonomy Item & Fields --}}
    <template x-if="pickerMode !== 'current_page' && pickerMode !== 'site_settings'">
        <div>
            {{-- Item Picker --}}
            <div class="px-3 py-2 border-b border-gray-100">
                <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-1" x-text="pickerMode.startsWith('tax:') ? 'Taxonomy Term' : 'Collection Item'"></label>
                <div class="relative">
                    <button type="button" @click="entryPickerOpen = !entryPickerOpen"
                        class="w-full flex items-center justify-between gap-2 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-300 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary cursor-pointer"
                        :class="entryPickerOpen ? 'border-primary ring-1 ring-primary' : (selectedEntryId ? 'text-primary font-semibold' : '')"
                    >
                        <span class="truncate" x-text="getSelectedEntryTitle()"></span>
                        <svg class="size-3.5 shrink-0 text-gray-400 transition-transform duration-200" :class="entryPickerOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div x-show="entryPickerOpen" @click.outside="entryPickerOpen = false"
                        class="absolute inset-x-0 top-full z-50 mt-1 max-h-52 overflow-y-auto scrollbar-thin rounded-lg border border-gray-200 bg-white shadow-xl ring-1 ring-black/5"
                        x-transition
                    >
                        <div class="p-1.5 border-b border-gray-100">
                            <input type="text" x-model="entrySearch" @click.stop placeholder="Search item..."
                                class="w-full rounded border border-gray-200 px-2 py-1 text-xs text-gray-800 placeholder:text-gray-400 focus:border-primary focus:ring-0 outline-none">
                        </div>
                        <div class="py-1 max-h-40 overflow-y-auto scrollbar-thin">
                            <template x-for="item in getFilteredEntries()" :key="item.id">
                                <button type="button" @click="selectedEntryId = item.id; entryPickerOpen = false"
                                    class="w-full flex items-center justify-between px-2.5 py-1.5 text-left text-xs transition-colors cursor-pointer hover:bg-primary/10"
                                    :class="String(selectedEntryId) === String(item.id) ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700'"
                                >
                                    <span class="truncate" x-text="item.title"></span>
                                    <svg x-show="String(selectedEntryId) === String(item.id)" class="size-3.5 shrink-0 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>
                                </button>
                            </template>
                            <template x-if="getFilteredEntries().length === 0">
                                <div class="px-2.5 py-3 text-center text-xs text-gray-400">No items found</div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fields of selected item --}}
            <div class="max-h-56 overflow-y-auto py-1 scrollbar-thin">
                <template x-if="selectedEntryId">
                    <div>
                        <div class="px-3 py-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Select Field to Bind</div>
                        <template x-for="cf in (pickerMode.startsWith('tax:') ? getTermSourceFields(selectedEntryId) : getEntrySourceFields(selectedEntryId))" :key="cf.key">
                            <button type="button" @click="setFieldSource(field.name, (pickerMode.startsWith('tax:') ? 'term:' : 'entry:') + selectedEntryId + ':' + cf.key); showSourcePicker = false"
                                class="w-full flex items-center justify-between px-3 py-1.5 text-left text-xs transition-colors cursor-pointer hover:bg-primary/10"
                                :class="getSourceKey(field.name) === ((pickerMode.startsWith('tax:') ? 'term:' : 'entry:') + selectedEntryId + ':' + cf.key) ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700'"
                            >
                                <span class="truncate pr-2" x-text="cf.label"></span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="text-[10px] font-mono text-gray-400" x-text="cf.key"></span>
                                    <svg x-show="getSourceKey(field.name) === ((pickerMode.startsWith('tax:') ? 'term:' : 'entry:') + selectedEntryId + ':' + cf.key)" class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>
                                </div>
                            </button>
                        </template>
                    </div>
                </template>
                <template x-if="!selectedEntryId">
                    <div class="px-3 py-6 text-center text-xs text-gray-400 italic">Select an item above to choose its fields</div>
                </template>
            </div>
        </div>
    </template>
</div>
