{{--
    Source picker popover.
    Requires `showSourcePicker` (boolean) in the parent x-data scope,
    and `field` (object with .name) in the Alpine loop context.
--}}
<div x-show="showSourcePicker" @click.outside="showSourcePicker = false"
    class="absolute right-0 z-30 mt-1 min-w-[240px] rounded-xl border border-gray-200 bg-white py-1 shadow-xl ring-1 ring-black/5 text-xs"
    x-transition
>
    {{-- Header: title + unlink button --}}
    <div class="px-3 py-1.5 border-b border-gray-100 flex items-center justify-between font-semibold text-xs text-gray-700">
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

    {{-- Collection group selector (only shown when multiple collections exist) --}}
    <template x-if="(groupedCollectionFields || []).length > 1">
        <div class="px-3 py-1.5 border-b border-gray-100 bg-gray-50/70">
            <div class="relative" x-data="{ groupPickerOpen: false }">
                <button type="button" @click="groupPickerOpen = !groupPickerOpen"
                    class="w-full flex items-center justify-between gap-2 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-300 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary cursor-pointer"
                    :class="groupPickerOpen ? 'border-primary ring-1 ring-primary' : ''"
                >
                    <span class="truncate" x-text="(groupedCollectionFields.find(g => g.collection_id == selectedCollectionGroup) || groupedCollectionFields[0])?.name || 'Select collection'"></span>
                    <svg class="size-3.5 shrink-0 text-gray-400 transition-transform duration-200" :class="groupPickerOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>

                <div x-show="groupPickerOpen" @click.outside="groupPickerOpen = false"
                    class="absolute inset-x-0 top-full z-40 mt-1 max-h-48 overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 shadow-xl ring-1 ring-black/5"
                    x-transition
                >
                    <template x-for="group in groupedCollectionFields" :key="'g_' + group.collection_id">
                        <button type="button" @click="selectedCollectionGroup = group.collection_id; groupPickerOpen = false"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 text-left text-xs transition-colors cursor-pointer"
                            :class="selectedCollectionGroup == group.collection_id ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:bg-gray-50'"
                        >
                            <span class="truncate pr-2" x-text="group.name"></span>
                            <svg x-show="selectedCollectionGroup == group.collection_id" class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </template>

    {{-- Field list --}}
    <div class="max-h-48 overflow-y-auto py-1">
        <template x-for="group in (groupedCollectionFields || [])" :key="group.collection_id">
            <div x-show="(groupedCollectionFields || []).length <= 1 || selectedCollectionGroup == group.collection_id">
                <template x-for="cf in group.fields" :key="group.collection_id + '_' + cf.key">
                    <button type="button" @click="setFieldSource(field.name, cf.key); showSourcePicker = false"
                        class="w-full flex items-center justify-between px-3 py-1.5 text-left text-xs transition-colors cursor-pointer"
                        :class="getSourceKey(field.name) === cf.key ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:bg-gray-50'"
                    >
                        <span class="truncate pr-2" x-text="cf.label"></span>
                        <span class="text-[10px] font-mono text-gray-400 shrink-0" x-text="cf.key"></span>
                    </button>
                </template>
            </div>
        </template>
        <template x-if="(!groupedCollectionFields || groupedCollectionFields.length === 0)">
            <div class="px-3 py-2 text-gray-400 italic text-center text-xs">No entry fields available</div>
        </template>
    </div>
</div>
