{{-- boolean toggle switch --}}
<template x-if="field.type === 'boolean'">
    <div class="flex items-center justify-between gap-3">
        <label class="text-sm font-semibold text-text-primary" x-text="field.label"></label>
        <button type="button" role="switch"
            :aria-checked="isChecked(getField(field.name))"
            @click="setField(field.name, isChecked(getField(field.name)) ? 'false' : 'true')"
            :class="isChecked(getField(field.name)) ? 'bg-primary' : 'bg-gray-300'"
            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/30 focus:ring-offset-1"
            :data-field-target="field.name"
        >
            <span aria-hidden="true"
                :class="isChecked(getField(field.name)) ? 'translate-x-5' : 'translate-x-0'"
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
            ></span>
        </button>
    </div>
</template>
