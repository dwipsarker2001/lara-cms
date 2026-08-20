{{-- map (URL + image preview or Google Maps embed) --}}
<template x-if="field.type === 'map'">
    <div x-data="{ showSourcePicker: false }">
        @include('admin.collections.entries.partials._field-label')

        <template x-if="isSourceField(field.name)">
            <input type="text" :value="getField(field.name)" disabled
                class="w-full rounded-lg border border-primary/30 bg-primary/5 px-3 py-2 text-sm text-text-primary cursor-not-allowed opacity-75">
        </template>

        <template x-if="!isSourceField(field.name)">
            <div class="space-y-2">
                <div class="flex gap-1.5">
                    <input type="text" :value="getField(field.name)" @input="setField(field.name, $event.target.value)"
                        :data-field-target="field.name"
                        placeholder="Map Image URL or Google Maps embed URL"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                    >
                    <button type="button"
                        @click="window.dispatchEvent(new CustomEvent('open-asset-picker', { detail: { callback: (url) => { setField(field.name, url) } } }))"
                        class="shrink-0 px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-xs font-semibold text-text-primary flex items-center gap-1 transition-colors"
                        title="Select Map Image from Assets"
                    >
                        <svg class="size-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Select</span>
                    </button>
                </div>

                <div class="relative w-full h-28 rounded-lg border border-gray-200 bg-gray-50 overflow-hidden flex items-center justify-center">
                    <template x-if="getField(field.name) && (getField(field.name).includes('google.com/maps') || getField(field.name).includes('maps.app') || getField(field.name).includes('embed') || getField(field.name).includes('<iframe'))">
                        <div class="flex flex-col items-center justify-center p-3 text-center text-xs text-primary">
                            <svg class="size-6 mb-1 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="font-medium truncate max-w-[220px]" x-text="getField(field.name)"></span>
                            <span class="text-[10px] text-gray-400 mt-0.5">Google Maps Link / Embed</span>
                        </div>
                    </template>
                    <template x-if="getField(field.name) && !getField(field.name).includes('google.com/maps') && !getField(field.name).includes('maps.app') && !getField(field.name).includes('embed') && !getField(field.name).includes('<iframe')">
                        <img :src="getField(field.name)" alt="Map Preview" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!getField(field.name)">
                        <div class="flex flex-col items-center justify-center text-gray-400 text-xs">
                            <svg class="size-6 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <span>No map selected</span>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</template>
