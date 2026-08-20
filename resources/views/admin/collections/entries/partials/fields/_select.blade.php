{{-- select (colour swatch picker) --}}
<template x-if="field.type === 'select'">
    <div>
        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open"
                :data-field-target="field.name"
                class="w-full flex items-center gap-2.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
            >
                <span class="inline-block size-4 rounded border border-gray-200 shrink-0" :style="'background-color: ' + (getField(field.name) || '#ffffff')"></span>
                <span class="flex-1 text-left" x-text="filteredSelectOptions(field).find(o => o.value === getField(field.name))?.label || 'Select...'"></span>
                <svg class="size-4 text-text-muted transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="open" x-cloak @click.outside="open = false"
                class="absolute z-20 mt-1 w-full rounded-xl border border-gray-200 bg-white py-1 shadow-lg ring-1 ring-black/5 max-h-60 overflow-y-auto"
                x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            >
                <template x-for="opt in filteredSelectOptions(field)" :key="(opt.collection || '') + '__' + opt.value">
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
