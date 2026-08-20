{{-- form key picker --}}
<template x-if="field.type === 'form'">
    <div>
        <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-semibold text-text-primary" x-text="field.label"></label>
            <span class="text-[10px] uppercase font-mono text-primary bg-primary/10 px-1.5 py-0.5 rounded font-medium">Form Key</span>
        </div>
        <div>
            <template x-if="getFormKeyOptions(field).length > 0">
                <select :value="getField(field.name)" @change="setField(field.name, $event.target.value)"
                    :data-field-target="field.name"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    <option value="">-- Select Form Key --</option>
                    <template x-for="opt in getFormKeyOptions(field)" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label" :selected="getField(field.name) === opt.value"></option>
                    </template>
                </select>
            </template>
            <template x-if="getFormKeyOptions(field).length === 0">
                <div class="space-y-1">
                    <input type="text" :value="getField(field.name)" @input="setField(field.name, $event.target.value)"
                        :placeholder="field.defaultValue || 'e.g. full_name'"
                        :data-field-target="field.name"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    <p class="text-[11px] text-text-muted">Select a Form in the block settings to pick from its keys.</p>
                </div>
            </template>
        </div>
    </div>
</template>
