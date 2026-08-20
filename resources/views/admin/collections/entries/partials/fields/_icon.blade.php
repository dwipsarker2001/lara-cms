{{-- icon picker (Font Awesome) --}}
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
