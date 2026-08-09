@extends('admin.layout')

@section('title', 'Edit Taxonomy — ' . $taxonomy->title)
@section('breadcrumb', 'Edit Taxonomy')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0" x-data="taxonomyForm({{ Js::from($taxonomy->fields ?? []) }})">
        <form method="POST" action="{{ route('admin.taxonomies.update', $taxonomy) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="fields" :value="JSON.stringify(fields)">

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <div>
                    <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                            <line x1="4" y1="9" x2="20" y2="9" />
                            <line x1="4" y1="15" x2="20" y2="15" />
                            <line x1="10" y1="3" x2="8" y2="21" />
                            <line x1="16" y1="3" x2="14" y2="21" />
                        </svg>
                        Edit Taxonomy Settings
                    </h1>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <button type="button" @click="openFieldModal()"
                        class="inline-flex items-center justify-center gap-1.5 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 px-3 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200 text-sm">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                            <path d="M8 3v10M3 8h10" />
                        </svg>
                        Add Input
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                        <span>Update Taxonomy</span>
                    </button>
                </div>
            </header>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Taxonomy Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Update this taxonomy group configuration and input fields.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-title" class="text-sm font-medium text-text-heading">Title</label>
                                    <div class="text-sm text-text-muted">A descriptive name for this taxonomy group.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-title"
                                            type="text"
                                            name="title"
                                            x-model="title"
                                            value="{{ old('title', $taxonomy->title) }}"
                                            placeholder="e.g. Categories, Topics, Regions"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-slug" class="text-sm font-medium text-text-heading">Slug</label>
                                    <div class="text-sm text-text-muted">The URL-friendly identifier.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-slug"
                                            type="text"
                                            name="slug"
                                            x-model="slug"
                                            value="{{ old('slug', $taxonomy->slug) }}"
                                            placeholder="e.g. categories"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-mono"
                                        >
                                        @error('slug') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div x-show="fields.length > 0" class="px-[18px] py-4">
                                <div class="text-sm font-medium text-text-heading mb-2">Custom Term Fields</div>
                                <div id="sortable-fields" class="space-y-1">
                                    <template x-for="(field, index) in fields" :key="field._key || index">
                                        <div class="flex rounded-lg shadow-sm bg-gray-50 border border-gray-200 group overflow-hidden px-2 hover:bg-gray-100/60 transition-colors">
                                            <div class="w-6 shrink-0 flex items-center justify-center cursor-grab active:cursor-grabbing opacity-70 hover:opacity-100 touch-none transition-opacity text-text-muted/70" data-drag-handle>
                                                <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                                                    <circle cx="8" cy="6" r="2.5" /><circle cx="16" cy="6" r="2.5" />
                                                    <circle cx="8" cy="12" r="2.5" /><circle cx="16" cy="12" r="2.5" />
                                                    <circle cx="8" cy="18" r="2.5" /><circle cx="16" cy="18" r="2.5" />
                                                </svg>
                                            </div>
                                            <div class="flex flex-1 min-w-0 items-center justify-between px-1.5 py-2.5 text-xs leading-normal gap-2">
                                                <div class="flex min-w-0 flex-1 items-center gap-2">
                                                    <span class="text-sm font-semibold text-text-heading group-hover:text-primary truncate transition-colors" x-text="field.title"></span>
                                                    <span class="text-[11px] font-mono text-primary/80 bg-primary/10 px-1.5 py-0.5 rounded" x-text="field.type"></span>
                                                </div>
                                                <div class="flex items-center gap-2.5 shrink-0 ml-1">
                                                    <span class="text-[11px] font-mono text-text-muted bg-white px-1.5 py-0.5 rounded border border-gray-200" x-text="field.template"></span>
                                                    <div class="flex items-center gap-0.5">
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
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- ADD / EDIT FIELD MODAL --}}
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
                    <p class="text-sm text-text-muted mt-1">Configure custom input fields for items under this taxonomy.</p>
                </div>
                <div class="px-6 pb-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Title</label>
                        <input type="text" x-model="fieldForm.title" @input="generateTemplate"
                            placeholder="e.g. Featured Image, Icon, Color Code, Subtitle"
                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Description</label>
                        <input type="text" x-model="fieldForm.description"
                            placeholder="e.g. Upload or paste featured image URL for this item"
                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div class="relative z-30">
                        <label class="block text-sm font-medium text-text-heading mb-1">Input Type</label>
                        <select x-model="fieldForm.type"
                            class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="number">Number</option>
                            <option value="image">Image</option>
                            <option value="color">Color</option>
                            <option value="select">Select</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Field Key</label>
                        <input type="text" x-model="fieldForm.template" @input="onKeyInput"
                            placeholder="e.g. image, icon, color"
                            class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-mono">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl">
                    <button type="button" @click="showFieldModal = false"
                        class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-primary shadow-sm border border-gray-200">
                        Cancel
                    </button>
                    <button type="button" @click="saveField"
                        class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                        <span x-text="editingFieldIndex !== null ? 'Update Field' : 'Add Field'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function taxonomyForm(existingFields) {
        return {
            title: @json(old('title', $taxonomy->title)),
            slug: @json(old('slug', $taxonomy->slug)),
            fields: (existingFields || []).map(f => ({
                ...f,
                template: (f.template || '').replace(/[^a-zA-Z0-9_]+/g, ''),
                _key: f._key || crypto.randomUUID()
            })),
            showFieldModal: false,
            editingFieldIndex: null,
            fieldForm: {
                title: '',
                description: '',
                type: 'text',
                template: ''
            },
            isKeyManuallyEdited: false,

            generateTemplate() {
                if (this.isKeyManuallyEdited) return;
                const slug = this.fieldForm.title.replace(/[^a-zA-Z0-9]+/g, '_').replace(/^_|_$/g, '').toLowerCase();
                this.fieldForm.template = slug;
            },
            onKeyInput() {
                this.isKeyManuallyEdited = true;
                if (this.fieldForm.template) {
                    this.fieldForm.template = this.fieldForm.template.replace(/[^a-zA-Z0-9_]+/g, '');
                }
            },
            openFieldModal() {
                this.editingFieldIndex = null;
                this.isKeyManuallyEdited = false;
                this.fieldForm = {
                    title: '',
                    type: 'text',
                    template: ''
                };
                this.showFieldModal = true;
            },
            editField(index) {
                this.editingFieldIndex = index;
                this.isKeyManuallyEdited = true;
                const field = { ...this.fields[index] };
                if (field.template) {
                    field.template = field.template.replace(/[^a-zA-Z0-9_]+/g, '');
                }
                this.fieldForm = field;
                this.showFieldModal = true;
            },
            saveField() {
                if (!this.fieldForm.title.trim()) return;
                if (!this.fieldForm.template || !this.fieldForm.template.trim()) {
                    this.fieldForm.template = this.fieldForm.title.replace(/[^a-zA-Z0-9]+/g, '_').replace(/^_|_$/g, '').toLowerCase();
                } else {
                    this.fieldForm.template = this.fieldForm.template.replace(/[^a-zA-Z0-9_]+/g, '').toLowerCase();
                }
                if (this.editingFieldIndex !== null) {
                    this.fields[this.editingFieldIndex] = { ...this.fieldForm, _key: this.fields[this.editingFieldIndex]._key };
                    this.fields = [...this.fields];
                } else {
                    this.fields.push({ ...this.fieldForm, _key: crypto.randomUUID() });
                }
                this.showFieldModal = false;
            }
        };
    }
</script>
@endpush