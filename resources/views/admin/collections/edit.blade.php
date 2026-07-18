@extends('admin.layout')

@section('title', 'Edit Collection')
@section('breadcrumb', 'Edit Collection')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0" x-data="collectionForm({{ Js::from($collection->fields ?? []) }})">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
                    class="size-6 shrink-0 text-text-muted">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>
                Edit Collection
            </h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button type="button" @click="openFieldModal()"
                    class="inline-flex items-center justify-center gap-1.5 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 px-3 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200 text-sm">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="size-4">
                        <path d="M8 3v10M3 8h10" />
                    </svg>
                    Add Input
                </button>
                <button type="submit" form="collection-form"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 shadow-sm bg-primary hover:opacity-90 text-white">
                    <span>Update Collection</span>
                </button>
            </div>
        </header>

        <form id="collection-form" method="POST" action="{{ route('admin.collections.update', $collection) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="fields" :value="JSON.stringify(fields)">

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="flex items-center justify-between px-[18px] pt-3 pb-1">
                    <div>
                        <div class="text-sm font-medium text-text-heading">Collection Details</div>
                        <p class="text-sm text-text-muted mt-1 mb-2">Configure the name and icon for this collection.</p>
                    </div>
                </div>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div>
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-name" class="text-sm font-medium text-text-heading">Name</label>
                                    <div class="text-sm text-text-muted">A descriptive name for this collection.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input id="field-name" type="text" name="name" x-model="name"
                                            value="{{ old('name', $collection->name) }}" placeholder="Products"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                </div>
                            </div>

                            <div
                                class="border-t border-content-border grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading">Icon</label>
                                    <div class="text-sm text-text-muted">Choose an icon for this collection.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 flex items-center gap-2">
                                        <input type="hidden" name="icon" x-model="selectedIcon">
                                        <div class="relative flex-1">
                                            <button type="button"
                                                @click="iconPickerOpen = !iconPickerOpen; if(iconPickerOpen) { iconLoading = true; iconSearch = ''; $nextTick(() => iconLoading = false); }"
                                                class="flex items-center gap-2 w-full rounded-lg border border-gray-300 hover:border-gray-400 px-3 py-2 text-sm transition-colors bg-white h-9">
                                                <template x-if="selectedIcon">
                                                    <i :class="selectedIcon" class="text-base w-5 text-center"></i>
                                                </template>
                                                <template x-if="!selectedIcon">
                                                    <span class="text-gray-400 w-5 text-center">?</span>
                                                </template>
                                                <span class="text-text-primary"
                                                    x-text="selectedIcon ? iconLabel(selectedIcon) : 'Choose icon'"></span>
                                                <svg class="ml-auto size-3 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                            <template x-if="iconPickerOpen">
                                                <div
                                                    class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg">
                                                    <div class="p-2 border-b border-gray-100">
                                                        <input type="text" x-model="iconSearch"
                                                            placeholder="Search icons..."
                                                            class="w-full rounded-md border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                                    </div>
                                                    <div x-show="iconLoading"
                                                        class="flex items-center justify-center py-4 text-sm text-gray-400">
                                                        <svg class="animate-spin size-4 mr-2" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4" />
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                                        </svg>
                                                        Loading icons...
                                                    </div>
                                                    <div x-show="!iconLoading"
                                                        class="p-2 max-h-72 overflow-y-auto grid grid-cols-8 gap-1">
                                                        <template x-for="icon in filteredIcons" :key="icon.c">
                                                            <button type="button"
                                                                @click="selectedIcon = icon.c; iconPickerOpen = false; iconSearch = ''"
                                                                class="flex items-center justify-center size-8 rounded-md border transition-colors text-sm"
                                                                :class="selectedIcon === icon.c ?
                                                                    'border-primary bg-primary/10 ring-1 ring-primary' :
                                                                    'border-gray-200 hover:border-gray-300 bg-white'"
                                                                :title="icon.l">
                                                                <i :class="icon.c"></i>
                                                            </button>
                                                        </template>
                                                        <template x-if="filteredIcons.length === 0">
                                                            <div class="col-span-8 py-4 text-center text-sm text-gray-400">
                                                                No icons found</div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <button type="button" x-show="selectedIcon" @click="selectedIcon = ''"
                                            class="size-9 shrink-0 flex items-center justify-center rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-text-muted transition-colors">
                                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="size-4">
                                                <path d="M4 4l8 8M12 4l-8 8" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div x-show="fields.length > 0" class="px-[18px] py-4">
                                <div id="sortable-fields" class="space-y-1">
                                    <template x-for="(field, index) in fields" :key="field._key || index">
                                        <div class="flex rounded-lg shadow-sm bg-content-bg border border-content-border group overflow-hidden px-2">
                                            <div class="w-6 shrink-0 flex items-center justify-center cursor-grab active:cursor-grabbing opacity-70 hover:opacity-100 touch-none transition-opacity text-text-muted/70" data-drag-handle>
                                                <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                                                    <circle cx="8" cy="6" r="2.5" /><circle cx="16" cy="6" r="2.5" />
                                                    <circle cx="8" cy="12" r="2.5" /><circle cx="16" cy="12" r="2.5" />
                                                    <circle cx="8" cy="18" r="2.5" /><circle cx="16" cy="18" r="2.5" />
                                                </svg>
                                            </div>
                                            <div class="flex flex-1 min-w-0 items-center px-1.5 py-2.5 text-xs leading-normal">
                                                <div class="flex min-w-0 flex-1 items-center gap-1.5">
                                                    <span class="text-sm font-semibold text-text-heading group-hover:text-primary truncate leading-normal transition-colors" x-text="field.title"></span>
                                                </div>
                                                <div class="flex items-center gap-0.5 shrink-0 ml-1">
                                                    <button type="button" @click="editField(index)" class="p-1 text-text-muted/60 hover:text-primary transition-colors rounded hover:bg-text-primary/10" title="Edit">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                        </svg>
                                                    </button>
                                                    <button type="button" @click="fields.splice(index, 1)" class="p-1 text-text-muted/60 hover:text-danger transition-colors rounded hover:bg-text-primary/10" title="Remove">
                                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-1.5 pb-2 pt-2">
                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                    <div class="divide-y divide-content-border">
                        <div class="grid md:grid-cols-2 items-center px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                            <div class="flex flex-col justify-center gap-1.5">
                                <div class="text-sm font-medium text-text-heading">Delete Collection</div>
                                <div class="text-sm text-text-muted">Permanently delete this collection and all its
                                    entries.</div>
                            </div>
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this collection? All entries will be lost.')"
                                    class="inline-flex">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-3 bg-red-500 hover:bg-red-600 text-white shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            <line x1="10" y1="11" x2="10" y2="17" />
                                            <line x1="14" y1="11" x2="14" y2="17" />
                                        </svg>
                                        <span>Delete collection</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showFieldModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40"
            @click.self="showFieldModal = false" style="display: none;">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
                <div class="flex items-center justify-between px-6 pt-5 pb-3">
                    <h3 class="text-lg font-semibold text-text-heading"
                        x-text="editingFieldIndex !== null ? 'Edit Field' : 'Add Field'"></h3>
                    <button type="button" @click="showFieldModal = false"
                        class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-text-muted transition-colors">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="size-4">
                            <path d="M4 4l8 8M12 4l-8 8" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 pb-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Title</label>
                        <input type="text" x-model="fieldForm.title" @input="generateTemplate"
                            placeholder="Full Name"
                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Description</label>
                        <input type="text" x-model="fieldForm.description" placeholder="Enter your full name"
                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Input Type</label>
                        <select x-model="fieldForm.type"
                            class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="number">Number</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Template String</label>
                        <input type="text" x-model="fieldForm.template" readonly
                            class="w-full block bg-gray-50 border border-gray-200 text-text-muted text-sm rounded-lg px-3 py-2 h-9 cursor-not-allowed">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <button type="button" @click="showFieldModal = false"
                        class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-primary shadow-sm border border-gray-200">
                        Cancel
                    </button>
                    <button type="button" @click="saveField"
                        class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                        <span x-text="editingFieldIndex !== null ? 'Update' : 'Add'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function collectionForm(existingFields) {
        const initialName = @json($collection->name);
        const initialIcon = @json($collection->icon ?? '');
        const initialFields = JSON.parse(JSON.stringify(existingFields || []));

        return {
            name: initialName,
            selectedIcon: initialIcon,
            iconPickerOpen: false,
            iconSearch: '',
            iconLoading: false,
            faIcons: window.FA_ICONS || [],
            fields: (existingFields || []).map(f => ({ ...f, _key: f._key || crypto.randomUUID() })),
            showFieldModal: false,
            editingFieldIndex: null,
            fieldForm: {
                title: '',
                description: '',
                type: 'text',
                template: ''
            },

            get filteredIcons() {
                let icons = this.faIcons;
                if (this.iconSearch.trim()) {
                    const q = this.iconSearch.toLowerCase();
                    icons = icons.filter(i => i.l.toLowerCase().includes(q) || i.c.toLowerCase().includes(q));
                }
                return icons.slice(0, 2000);
            },
            get isDirty() {
                return this.name !== initialName ||
                    this.selectedIcon !== initialIcon ||
                    JSON.stringify(this.fields.filter(Boolean).map(({ _key, ...rest }) => rest)) !== JSON.stringify(initialFields);
            },
            iconLabel(cls) {
                const found = this.faIcons.find(i => i.c === cls);
                return found ? found.l : cls;
            },

            generateTemplate() {
                const slug = this.fieldForm.title.replace(/[^a-zA-Z0-9]+/g, '_').replace(/^_|_$/g, '').toLowerCase();
                this.fieldForm.template = slug ? '@{{' + slug + '@}}' : '';
            },

            openFieldModal() {
                this.editingFieldIndex = null;
                this.fieldForm = {
                    title: '',
                    description: '',
                    type: 'text',
                    template: ''
                };
                this.showFieldModal = true;
            },

            editField(index) {
                this.editingFieldIndex = index;
                this.fieldForm = {
                    ...this.fields[index]
                };
                this.showFieldModal = true;
            },

            saveField() {
                if (!this.fieldForm.title.trim()) return;
                if (this.editingFieldIndex !== null) {
                    this.fields[this.editingFieldIndex] = { ...this.fieldForm, _key: this.fields[this.editingFieldIndex]._key };
                    this.fields = [...this.fields]; // Force Alpine reactivity update
                } else {
                    this.fields.push({ ...this.fieldForm, _key: crypto.randomUUID() });
                    this.$nextTick(() => {
                        const el = document.getElementById('sortable-fields');
                        if (!el || el._sortable) return;
                        el._sortable = new Sortable(el, {
                            handle: '[data-drag-handle]',
                            animation: 200,
                            easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                            onEnd: (evt) => {
                                if (evt.oldIndex === evt.newIndex) return;
                                const item = this.fields.splice(evt.oldIndex, 1)[0];
                                this.fields.splice(evt.newIndex, 0, item);
                            },
                        });
                    });
                }
                this.showFieldModal = false;
            },

            init() {
                const el = document.getElementById('sortable-fields');
                if (!el || el._sortable) return;
                el._sortable = new Sortable(el, {
                    handle: '[data-drag-handle]',
                    animation: 200,
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    onEnd: (evt) => {
                        if (evt.oldIndex === evt.newIndex) return;
                        const item = this.fields.splice(evt.oldIndex, 1)[0];
                        this.fields.splice(evt.newIndex, 0, item);
                    },
                });
            },
        };
    }
</script>

<style>
    #sortable-fields .sortable-ghost {
        opacity: 0 !important;
    }

    #sortable-fields .sortable-drag {
        opacity: 0.9 !important;
        box-shadow: none !important;
        border-radius: 0.75rem !important;
        background: var(--color-content-bg, #fff) !important;
    }
</style>
@endpush
