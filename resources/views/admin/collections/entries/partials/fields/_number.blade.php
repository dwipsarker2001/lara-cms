{{-- number input --}}
<template x-if="field.type === 'number'">
    <div x-data="{ showSourcePicker: false }">
        @include('admin.collections.entries.partials._field-label')
        <template x-if="isSourceField(field.name)">
            <input type="number" :value="getField(field.name)" disabled
                class="w-full rounded-lg border border-primary/30 bg-primary/5 px-3 py-2 text-sm text-text-primary cursor-not-allowed opacity-75">
        </template>
        <template x-if="!isSourceField(field.name)">
            <input type="number" :value="getField(field.name)" @input="setField(field.name, parseFloat($event.target.value) || '')"
                :data-field-target="field.name"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
        </template>
    </div>
</template>
