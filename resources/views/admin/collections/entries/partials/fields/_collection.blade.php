{{-- collection entry picker --}}
<template x-if="field.type === 'collection' || field.type === 'collectionEntry'">
    <div x-data="{
        open: false,
        search: '',
        get allOptions() {
            const targetSlugOrId = field.collection || '';
            const allCols = window.editorAllCollections || [];
            let entries = [];
            if (targetSlugOrId) {
                const found = allCols.find(c => String(c.slug) === String(targetSlugOrId) || String(c.id) === String(targetSlugOrId));
                if (found && found.entries) {
                    entries = found.entries;
                }
            } else {
                allCols.forEach(c => {
                    if (c.entries) {
                        c.entries.forEach(e => {
                            entries.push({ ...e, title: (c.name ? c.name + ' — ' : '') + e.title });
                        });
                    }
                });
            }
            return entries;
        },
        get filteredOptions() {
            if (!this.search.trim()) return this.allOptions;
            const q = this.search.toLowerCase();
            return this.allOptions.filter(o => (o.title || '').toLowerCase().includes(q) || (o.slug || '').toLowerCase().includes(q));
        },
        selectedLabel() {
            const val = String(getField(field.name) || '');
            if (!val) return 'Select item...';
            const found = this.allOptions.find(o => String(o.id) === val);
            return found ? found.title : 'Select item...';
        },
        selectItem(entry) {
            const val = entry ? String(entry.id) : '';
            setField(field.name, val);
            this.open = false;
            this.search = '';
        }
    }">
        <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-semibold text-text-primary" x-text="field.label"></label>
            <span class="text-[10px] uppercase font-mono text-primary bg-primary/10 px-1.5 py-0.5 rounded font-medium">Collection Item</span>
        </div>
        <div class="relative">
            <button type="button" @click="open = !open; if (open) search = ''"
                :data-field-target="field.name"
                class="w-full flex items-center justify-between gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors cursor-pointer"
            >
                <div class="flex items-center gap-2 truncate">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span class="truncate" :class="getField(field.name) ? 'font-medium text-text-primary' : 'text-text-muted'" x-text="selectedLabel()"></span>
                </div>
                <svg class="size-4 text-text-muted transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-cloak @click.outside="open = false"
                class="absolute z-30 mt-1 w-full rounded-xl border border-gray-200 bg-white shadow-xl ring-1 ring-black/5 max-h-72 overflow-hidden flex flex-col"
                x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            >
                <div class="p-2 border-b border-gray-100 bg-gray-50/50" x-show="allOptions.length > 5">
                    <input type="text" x-model="search" placeholder="Search..."
                        class="w-full rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                </div>

                <div class="p-1 space-y-0.5 overflow-y-auto max-h-56 [scrollbar-width:thin]">
                    <button type="button" @click="selectItem(null)"
                        class="w-full flex items-center px-3 py-2 text-xs rounded-lg text-left transition-colors text-text-muted hover:bg-gray-100">
                        <span>-- None (Clear) --</span>
                    </button>
                    <template x-for="opt in filteredOptions" :key="opt.id">
                        <button type="button" @click="selectItem(opt)"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg text-left transition-colors cursor-pointer"
                            :class="String(getField(field.name)) === String(opt.id) ? 'bg-primary/10 text-primary font-semibold' : 'text-text-primary hover:bg-gray-100/80'"
                        >
                            <span class="truncate" x-text="opt.title"></span>
                            <template x-if="String(getField(field.name)) === String(opt.id)">
                                <svg class="size-4 ml-2 text-primary shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </template>
                        </button>
                    </template>
                    <template x-if="filteredOptions.length === 0">
                        <div class="px-3 py-3 text-xs text-text-muted text-center">No items found</div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
