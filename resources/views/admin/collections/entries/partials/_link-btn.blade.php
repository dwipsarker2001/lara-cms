{{--
    Collection field binding button + source picker popover.
    Requires `showSourcePicker` (boolean) in the parent x-data scope,
    and `field` (object with .name) in the Alpine loop context.
--}}
<div class="relative">
    <button type="button" @click="showSourcePicker = !showSourcePicker"
        class="flex items-center gap-1 px-1.5 py-0.5 text-xs font-medium rounded transition-colors"
        :class="isSourceField(field.name) ? 'bg-primary/10 text-primary border border-primary/20' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100'"
        :title="isSourceField(field.name) ? getSourceKeyTitle(field.name) : 'Link to Collection Field'"
    >
        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
        </svg>
        <span x-text="isSourceField(field.name) ? getSourceKeyLabel(field.name) : 'Link'"></span>
    </button>

    @include('admin.collections.entries.partials._source-picker')
</div>
