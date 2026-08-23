{{-- link (Collection / Custom URL picker) --}}
<template x-if="field.type === 'link'">
    <div :data-field-target="field.name" x-data="{
        modeOpen: false,
        entryOpen: false,
        search: '',
        get currentVal() {
            return String(getField(field.name) || '');
        },
        get currentMode() {
            return getLinkMode(field.name);
        },
        get collections() {
            return getLinkCollections();
        },
        get currentEntries() {
            return getLinkEntries(this.currentMode);
        },
        get filteredEntries() {
            if (!this.search.trim()) return this.currentEntries;
            const q = this.search.toLowerCase();
            return this.currentEntries.filter(p => (p.title || '').toLowerCase().includes(q) || (p.route || '').toLowerCase().includes(q) || (p.slug || '').toLowerCase().includes(q));
        },
        selectedLabel() {
            const val = this.currentVal;
            if (!val) return 'Select ' + (getLinkModeLabel(this.currentMode)) + '...';
            const found = this.currentEntries.find(p => p.route === val || String(p.id) === val || p.slug === val);
            if (found) return found.title;
            for (const col of this.collections) {
                const entries = getLinkEntries(col.slug);
                const match = entries.find(p => p.route === val || String(p.id) === val || p.slug === val);
                if (match) return match.title;
            }
            const matchPage = (pages || []).find(p => p.route === val || p.slug === val);
            if (matchPage) return matchPage.title;
            return val;
        },
        selectEntry(item) {
            linkFieldValue(field.name, item.route || item.slug || String(item.id));
            this.entryOpen = false;
            this.search = '';
        },
        changeMode(mode) {
            setLinkMode(field.name, mode);
            this.modeOpen = false;
            this.entryOpen = false;
            this.search = '';
        }
    }">
        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
        <div class="flex gap-2">
            {{-- Mode / Collection selector --}}
            <div class="relative w-40 shrink-0">
                <button type="button" @click="modeOpen = !modeOpen; entryOpen = false"
                    class="w-full flex items-center justify-between gap-2 bg-white border border-gray-300 text-sm rounded-lg px-3 h-10 cursor-pointer transition-shadow duration-150 hover:shadow-sm"
                    :class="modeOpen ? 'ring-2 ring-primary/30 border-primary shadow-sm' : 'shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)]'">
                    <span class="text-text-primary truncate font-medium" x-text="getLinkModeLabel(currentMode)"></span>
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 opacity-60 transition-transform duration-150" :class="modeOpen ? 'rotate-180' : ''">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="modeOpen" x-cloak
                    class="absolute z-50 top-full mt-1.5 left-0 right-0 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
                    @click.outside="modeOpen = false">
                    <div class="py-1 max-h-56 overflow-y-auto [scrollbar-width:thin]">
                        {{-- Custom Option --}}
                        <button type="button" @click="changeMode('custom')"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm text-left transition-colors duration-75 hover:bg-primary/10"
                            :class="currentMode === 'custom' ? 'text-primary font-semibold bg-primary/5' : 'text-gray-700'">
                            <span class="truncate">Custom</span>
                            <svg x-show="currentMode === 'custom'" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-primary">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div class="h-px bg-gray-100 my-1"></div>

                        {{-- Dynamic Collection Names --}}
                        <template x-for="col in collections" :key="col.slug">
                            <button type="button" @click="changeMode(col.slug)"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm text-left transition-colors duration-75 hover:bg-primary/10"
                                :class="currentMode === col.slug ? 'text-primary font-semibold bg-primary/5' : 'text-gray-700'">
                                <span class="truncate" x-text="col.name"></span>
                                <svg x-show="currentMode === col.slug" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-primary">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Collection Entry Item Picker (when a collection is selected) --}}
            <template x-if="currentMode !== 'custom'">
                <div class="relative flex-1 min-w-0">
                    <button type="button" @click="entryOpen = !entryOpen; modeOpen = false; if (entryOpen) search = ''"
                         class="w-full flex items-center justify-between gap-2 bg-white border border-gray-300 text-sm rounded-lg px-3 h-10 cursor-pointer transition-shadow duration-150 hover:shadow-sm"
                         :class="entryOpen ? 'ring-2 ring-primary/30 border-primary shadow-sm' : 'shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)]'">
                         <span class="truncate" :class="currentVal ? 'text-gray-900 font-medium' : 'text-gray-400'">
                             <span x-text="selectedLabel()"></span>
                         </span>
                         <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 opacity-60 transition-transform duration-150" :class="entryOpen ? 'rotate-180' : ''">
                             <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                         </svg>
                    </button>
                    <div x-show="entryOpen" x-cloak
                         class="absolute z-50 top-full mt-1.5 left-0 right-0 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
                        @click.outside="entryOpen = false">
                        <div class="flex items-center gap-2 px-3 py-1.5 border-b border-gray-200">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 opacity-50 text-gray-400">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                            </svg>
                            <input type="text"
                                x-model="search"
                                @click.stop
                                placeholder="Search..."
                                class="w-full bg-transparent text-sm text-gray-900 placeholder:text-gray-400 outline-none border-0 focus:ring-0">
                        </div>
                        <div class="max-h-60 overflow-y-auto py-1 [scrollbar-width:thin]">
                            <template x-for="item in filteredEntries" :key="item.id">
                                <button type="button" @click="selectEntry(item)"
                                    class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm text-left transition-colors duration-75 hover:bg-primary/10"
                                    :class="(currentVal === item.route || currentVal === item.slug) ? 'text-primary font-semibold bg-primary/5' : 'text-gray-700'">
                                    <span class="truncate" x-text="item.title"></span>
                                    <svg x-show="currentVal === item.route || currentVal === item.slug" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-primary">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>
                            <template x-if="filteredEntries.length === 0">
                                <div class="px-3 py-6 text-sm text-gray-400 text-center">No items found in this collection</div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Custom URL input (when Custom is selected) --}}
            <template x-if="currentMode === 'custom'">
                <input type="text"
                    :value="currentVal"
                    @input="linkFieldValue(field.name, $event.target.value)"
                    placeholder="https://example.com or /my-page"
                    class="flex-1 min-w-0 rounded-lg border border-gray-300 bg-white px-3 h-10 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </template>
        </div>
    </div>
</template>
