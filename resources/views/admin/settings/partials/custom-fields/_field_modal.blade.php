{{-- ==================== ADD / EDIT CUSTOM FIELD MODAL ==================== --}}
<div x-show="showFieldModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40"
    @click.self="showFieldModal = false" style="display: none;">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 relative">
        <div class="px-6 pt-5 pb-3">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-text-heading"
                    x-text="editingFieldIndex !== null ? 'Edit Custom Field' : 'Add Custom Field'"></h3>
                <button type="button" @click="showFieldModal = false"
                    class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-text-muted transition-colors">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="size-4">
                        <path d="M4 4l8 8M12 4l-8 8" />
                    </svg>
                </button>
            </div>
            <p class="text-sm text-text-muted mt-1">Configure global custom inputs for your site settings.</p>
        </div>
        <div class="px-6 pb-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Title</label>
                <input type="text" x-model="fieldForm.title" @input="generateTemplate"
                    placeholder="e.g. Support Hotline, Footer Copyright, Announcement Banner"
                    class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Description</label>
                <input type="text" x-model="fieldForm.description"
                    placeholder="e.g. Phone number displayed in header and footer"
                    class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
            <div class="relative z-30" x-data="{ open: false }" @click.outside="open = false">
                <label class="block text-sm font-medium text-text-heading mb-1.5">Input Type</label>
                <button type="button" @click="open = !open"
                    class="flex items-center justify-between gap-2 w-full rounded-lg border border-gray-300 hover:border-gray-400 bg-white px-3 py-2 text-sm text-text-primary h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer shadow-xs">
                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                        <template x-if="fieldForm.type === 'text'">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>
                                <span class="font-medium">Text</span>
                            </div>
                        </template>
                        <template x-if="fieldForm.type === 'textarea'">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/><line x1="9" y1="9" x2="10" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                                <span class="font-medium">Textarea</span>
                            </div>
                        </template>
                        <template x-if="fieldForm.type === 'number'">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>
                                <span class="font-medium">Number</span>
                            </div>
                        </template>
                        <template x-if="fieldForm.type === 'image'">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span class="font-medium">Image (Asset Picker)</span>
                            </div>
                        </template>
                        <template x-if="fieldForm.type === 'color'">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                                <span class="font-medium">Color</span>
                            </div>
                        </template>
                        <template x-if="fieldForm.type === 'toggle' || fieldForm.type === 'boolean'">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><rect x="1" y="5" width="22" height="14" rx="7" ry="7"/><circle cx="16" cy="12" r="3"/></svg>
                                <span class="font-medium">Toggle Switch (Boolean)</span>
                            </div>
                        </template>
                        <template x-if="fieldForm.type === 'select'">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                                <span class="font-medium">Select Dropdown</span>
                            </div>
                        </template>
                        <template x-if="fieldForm.type === 'location'">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-primary shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span class="font-medium">Location</span>
                            </div>
                        </template>
                    </div>
                    <svg :class="open ? 'rotate-180 text-primary' : 'text-gray-400'" class="size-4 transition-transform duration-150 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-50 top-full mt-1 left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-xl p-1.5 space-y-0.5 max-h-60 overflow-y-auto"
                    style="display: none;">
                    <button type="button" @click="fieldForm.type = 'text'; open = false"
                        class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100 transition-colors"
                        :class="fieldForm.type === 'text' ? 'bg-primary/10 text-primary font-medium' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'text' ? 'text-primary' : 'text-text-muted'"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>
                            <span>Text</span>
                        </div>
                        <svg x-show="fieldForm.type === 'text'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button type="button" @click="fieldForm.type = 'textarea'; open = false"
                        class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100 transition-colors"
                        :class="fieldForm.type === 'textarea' ? 'bg-primary/10 text-primary font-medium' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'textarea' ? 'text-primary' : 'text-text-muted'"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/><line x1="9" y1="9" x2="10" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                            <span>Textarea</span>
                        </div>
                        <svg x-show="fieldForm.type === 'textarea'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button type="button" @click="fieldForm.type = 'number'; open = false"
                        class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100 transition-colors"
                        :class="fieldForm.type === 'number' ? 'bg-primary/10 text-primary font-medium' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'number' ? 'text-primary' : 'text-text-muted'"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>
                            <span>Number</span>
                        </div>
                        <svg x-show="fieldForm.type === 'number'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button type="button" @click="fieldForm.type = 'image'; open = false"
                        class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100 transition-colors"
                        :class="fieldForm.type === 'image' ? 'bg-primary/10 text-primary font-medium' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'image' ? 'text-primary' : 'text-text-muted'"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <span>Image (Asset Picker)</span>
                        </div>
                        <svg x-show="fieldForm.type === 'image'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button type="button" @click="fieldForm.type = 'color'; open = false"
                        class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100 transition-colors"
                        :class="fieldForm.type === 'color' ? 'bg-primary/10 text-primary font-medium' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'color' ? 'text-primary' : 'text-text-muted'"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                            <span>Color</span>
                        </div>
                        <svg x-show="fieldForm.type === 'color'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button type="button" @click="fieldForm.type = 'toggle'; open = false"
                        class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100 transition-colors"
                        :class="fieldForm.type === 'toggle' ? 'bg-primary/10 text-primary font-medium' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'toggle' ? 'text-primary' : 'text-text-muted'"><rect x="1" y="5" width="22" height="14" rx="7" ry="7"/><circle cx="16" cy="12" r="3"/></svg>
                            <span>Toggle Switch</span>
                        </div>
                        <svg x-show="fieldForm.type === 'toggle'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button type="button" @click="fieldForm.type = 'select'; open = false"
                        class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100 transition-colors"
                        :class="fieldForm.type === 'select' ? 'bg-primary/10 text-primary font-medium' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'select' ? 'text-primary' : 'text-text-muted'"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                            <span>Select Dropdown</span>
                        </div>
                        <svg x-show="fieldForm.type === 'select'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button type="button" @click="fieldForm.type = 'location'; open = false"
                        class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100 transition-colors"
                        :class="fieldForm.type === 'location' ? 'bg-primary/10 text-primary font-medium' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'location' ? 'text-primary' : 'text-text-muted'"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>Location</span>
                        </div>
                        <svg x-show="fieldForm.type === 'location'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Select Options config --}}
            <div x-show="fieldForm.type === 'select'" style="display: none;">
                <label class="block text-sm font-medium text-text-heading mb-1">Dropdown Options (Comma separated)</label>
                <input type="text" x-model="fieldForm.options" placeholder="e.g. Red, Blue, Green"
                    class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>

            {{-- Location Sub-Fields Config --}}
            <div x-show="fieldForm.type === 'location'" style="display: none;" class="flex items-center flex-wrap gap-5 pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-sm text-text-heading">
                    <input type="checkbox" x-model="fieldForm.enable_country" class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="font-medium">Country</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-sm text-text-heading">
                    <input type="checkbox" x-model="fieldForm.enable_state" class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="font-medium">State / Province</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-sm text-text-heading">
                    <input type="checkbox" x-model="fieldForm.enable_city" class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="font-medium">City</span>
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-text-heading mb-1">Storage Key</label>
                <input type="text" x-model="fieldForm.template" @input="onKeyInput"
                    placeholder="e.g. support_phone, footer_copyright"
                    class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-mono">
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl">
            <button type="button" @click="showFieldModal = false"
                class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-primary shadow-xs border border-gray-200">
                Cancel
            </button>
            <button type="button" @click="saveField()"
                class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-xs">
                <span x-text="editingFieldIndex !== null ? 'Update Field' : 'Add Field'"></span>
            </button>
        </div>
    </div>
</div>

{{-- ==================== DELETE CUSTOM FIELD CONFIRMATION MODAL ==================== --}}
<x-admin::delete-modal
    show="showDeleteModal"
    title="Delete Custom Input"
    confirm-action="confirmDeleteField()"
>
    Are you sure you want to delete <span class="font-medium text-text-heading" x-text="deletingFieldIndex !== null && fields[deletingFieldIndex] ? '“' + fields[deletingFieldIndex].title + '”' : 'this input'"></span>? This action will remove the field from your settings.
</x-admin::delete-modal>
