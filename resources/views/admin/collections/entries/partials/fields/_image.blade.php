{{-- image upload / picker --}}
<template x-if="field.type === 'image'">
    <div x-data="{ showSourcePicker: false }">
        @include('admin.collections.entries.partials._field-label')

        {{-- Linked state --}}
        <template x-if="isSourceField(field.name)">
            <div class="relative w-full h-32 rounded-lg border border-primary/30 bg-primary/5 p-1 overflow-hidden">
                <template x-if="getField(field.name)">
                    <img :src="getField(field.name)" alt="" class="w-full h-full object-cover rounded-md opacity-85">
                </template>
                <template x-if="!getField(field.name)">
                    <div class="flex flex-col items-center justify-center w-full h-full text-primary/60 text-xs font-medium">
                        <svg class="size-6 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <span>Linked to entry field (<span x-text="getSourceKey(field.name)"></span>)</span>
                    </div>
                </template>
            </div>
        </template>

        {{-- Editable state --}}
        <template x-if="!isSourceField(field.name)">
            <div>
                <div
                    :data-field-target="field.name"
                    @click="window.dispatchEvent(new CustomEvent('open-asset-picker', { detail: { callback: (url) => { setField(field.name, url) } } }))"
                    @dragover.prevent="$event.currentTarget.classList.add('border-gray-400', 'bg-gray-50')"
                    @dragleave.prevent="$event.currentTarget.classList.remove('border-gray-400', 'bg-gray-50')"
                    @drop.prevent="
                        $event.currentTarget.classList.remove('border-gray-400', 'bg-gray-50');
                        const file = $event.dataTransfer.files[0];
                        if (file && file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = (e) => { setField(field.name, e.target.result); };
                            reader.readAsDataURL(file);
                        }
                    "
                    class="relative w-full h-32 rounded-lg border-2 border-dashed cursor-pointer transition-colors bg-white overflow-hidden border-gray-300 hover:border-gray-400"
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
                            class="absolute top-1 right-1 text-[11px] font-medium text-white rounded px-2 py-0.5 transition-colors"
                            style="background: rgba(220,38,38,0.8);"
                            onmouseover="this.style.background='rgb(220,38,38)'"
                            onmouseout="this.style.background='rgba(220,38,38,0.8)'"
                        >Remove</button>
                    </template>
                </div>
                <input type="file" accept="image/*" class="hidden"
                    @change="const file = $event.target.files[0]; if (file && file.type.startsWith('image/')) { const reader = new FileReader(); reader.onload = (e) => { setField(field.name, e.target.result); }; reader.readAsDataURL(file); } $event.target.value = '';"
                >
            </div>
        </template>
    </div>
</template>
