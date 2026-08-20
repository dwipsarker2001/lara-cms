{{-- tags input --}}
<template x-if="field.type === 'tags'">
    <div>
        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
        <div class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus-within:ring-2 focus-within:ring-primary/30 focus-within:border-primary">
            <div class="flex flex-wrap gap-1 mb-1">
                <template x-for="(tag, ti) in parseTags(getField(field.name))" :key="ti">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-panel-bg rounded text-xs">
                        <span x-text="tag"></span>
                        <button @click="removeTag(field.name, ti)" class="text-danger hover:text-danger/70 leading-none">&times;</button>
                    </span>
                </template>
            </div>
            <input type="text" placeholder="Type and press Enter..."
                @keydown.enter.prevent="addTag(field.name, $event.target.value); $event.target.value = ''"
                class="w-full border-0 p-0 text-sm text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-0">
        </div>
    </div>
</template>
