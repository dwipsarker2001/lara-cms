{{-- object (drill-in or list of items) --}}
<template x-if="field.type === 'object'">
    <div :data-field-target="field.name">
        {{-- Single nested object (drill-in button) --}}
        <template x-if="!field.list">
            <button @click="drillIn(field.name)"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-left text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] flex items-center justify-between"
            >
                <span class="font-semibold" x-text="field.label"></span>
                <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </template>

        {{-- List of nested objects --}}
        <template x-if="field.list">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-text-primary" x-text="field.label"></span>
                    <button type="button" @click="addListItem(field.name)" class="text-xs text-primary hover:text-primary/80 font-medium">
                        + Add <span x-text="field.label.toLowerCase()"></span>
                    </button>
                </div>
                <template x-if="getList(field.name).length > 0">
                    <div class="space-y-0.5" data-sortable-list :data-field-name="field.name"
                         x-init="$nextTick(() => initListSortable($el))">
                        <template x-for="(item, ci) in getList(field.name)" :key="item._key">
                            <div class="flex rounded-lg shadow-sm bg-content-bg mb-0.5 group overflow-hidden">
                                <div class="w-6 shrink-0 flex items-center justify-center cursor-grab active:cursor-grabbing opacity-70 hover:opacity-100 touch-none transition-opacity text-text-muted/70">
                                    <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                                        <circle cx="8" cy="6" r="2.5" /><circle cx="16" cy="6" r="2.5" />
                                        <circle cx="8" cy="12" r="2.5" /><circle cx="16" cy="12" r="2.5" />
                                        <circle cx="8" cy="18" r="2.5" /><circle cx="16" cy="18" r="2.5" />
                                    </svg>
                                </div>
                                <div class="flex flex-1 min-w-0 items-center px-1.5 py-2.5 text-xs leading-normal">
                                    <div class="flex min-w-0 flex-1 items-center gap-1.5">
                                        <template x-if="item.icon">
                                            <i :class="item.icon" class="size-3 shrink-0 text-text-muted"></i>
                                        </template>
                                        <span class="text-sm font-semibold text-text-heading group-hover:text-primary truncate leading-normal transition-colors" x-text="cardLabel(item, field, ci)"></span>
                                    </div>
                                    <div class="flex items-center gap-0.5 shrink-0 ml-1">
                                        <button @click="drillIn(field.name, ci)" class="p-1 text-text-muted/60 hover:text-primary group-hover:text-primary transition-colors rounded hover:bg-text-primary/10" title="Edit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>
                                        <button @click="removeListItem(field.name, ci)" class="p-1 text-text-muted/60 hover:text-danger transition-colors rounded hover:bg-text-primary/10" title="Remove">
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </div>
</template>
