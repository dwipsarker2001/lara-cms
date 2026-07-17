@extends('admin.layout')

@section('title', 'Form Editor — '.$form->title)
@section('breadcrumb', 'Form Editor')

@section('content')
<script>
    window.formFields = @json($form->fields ?? []);
    window.formFieldList = @json($fieldList);
    window.formSaveRoute = @json(route('admin.forms.update-fields', $form));
</script>

<div
    class="max-w-5xl mx-auto px-2 sm:px-0"
    x-data="formEditor()"
    x-init="init(window.formFields)"
    x-on:field-selected.window="addField($event.detail.name)"
>
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="12" y1="18" x2="12" y2="12" />
                <line x1="9" y1="15" x2="15" y2="15" />
            </svg>
            Form Editor
        </h1>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <button type="button"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-gradient-to-b from-content-bg to-gray-50 hover:to-gray-100 text-text-primary border border-content-border shadow-sm"
            >
                <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                Generate UI
            </button>
            <button type="button"
                @click="save()"
                x-bind:disabled="isSaving || !dirty"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <template x-if="!isSaving">
                    <span class="inline-flex items-center gap-2">
                        <i class="fa-regular fa-floppy-disk"></i>
                        Save
                    </span>
                </template>
                <template x-if="isSaving">
                    <span class="inline-flex items-center gap-2">
                        <svg class="animate-spin size-4" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        Saving...
                    </span>
                </template>
            </button>
        </div>
    </header>

    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
        <div class="p-1.5">
            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm w-[500px] mx-auto px-6 py-5">
                <div class="mb-5">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-base font-semibold text-text-heading">Build Form</p>
                        <button
                            type="button"
                            @click="window.dispatchEvent(new CustomEvent('open-field-picker'))"
                            class="size-6 flex items-center justify-center rounded-full bg-white text-text-primary border border-content-border hover:bg-gray-50 transition-colors"
                            title="Add field"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[14px]">
                                <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-text-muted mt-1.5">Build your form structure here and then generate ui for that form.</p>
                </div>

                {{-- Empty state --}}
                <div x-show="fields.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-12 text-gray-300 mb-4">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="12" y1="18" x2="12" y2="12"/>
                        <line x1="9" y1="15" x2="15" y2="15"/>
                    </svg>
                    <p class="text-xs text-text-muted">No fields yet</p>
                </div>

                {{-- Field list (matches page builder section list rows) --}}
                <div x-show="fields.length > 0" class="space-y-0.5" x-ref="fieldList" style="display: none;">
                    <template x-for="(field, i) in fields" :key="field._key">
                        <div class="flex rounded-lg shadow-sm bg-content-bg mb-0.5 group overflow-hidden border border-content-border/50">
                            <div class="w-6 shrink-0 flex items-center justify-center cursor-grab active:cursor-grabbing opacity-70 hover:opacity-100 touch-none transition-opacity text-text-muted/70">
                                <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                                    <circle cx="8" cy="6" r="2.5" />
                                    <circle cx="16" cy="6" r="2.5" />
                                    <circle cx="8" cy="12" r="2.5" />
                                    <circle cx="16" cy="12" r="2.5" />
                                    <circle cx="8" cy="18" r="2.5" />
                                    <circle cx="16" cy="18" r="2.5" />
                                </svg>
                            </div>
                            <div class="flex flex-1 min-w-0 flex-col px-1.5 py-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-sm font-semibold text-text-heading group-hover:text-primary truncate leading-normal transition-colors" x-text="field.label"></span>
                                    <span class="text-[11px] text-text-muted shrink-0" x-text="field.type"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-0.5 shrink-0 ml-auto pr-1">
                                <button
                                    type="button"
                                    @click.stop="removeField(i)"
                                    class="p-1 text-text-muted/60 hover:text-danger transition-colors rounded hover:bg-text-primary/10"
                                    title="Remove field"
                                >
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <x-admin::field-picker />
</div>
@endsection

<style>
    .sortable-ghost,
    .sortable-drag {
        opacity: 0.3 !important;
        box-shadow: none !important;
        border: none !important;
    }
</style>

@push('scripts')
<script>
    function formEditor() {
        return {
            fields: [],
            dirty: false,
            isSaving: false,
            _fieldSortable: null,

            init(fields) {
                this.fields = JSON.parse(JSON.stringify(fields || []));
                this.$nextTick(() => this.initFieldSortable());
            },

            initFieldSortable() {
                if (this._fieldSortable) {
                    try { this._fieldSortable.destroy(); } catch (e) {}
                    this._fieldSortable = null;
                }
                const el = this.$refs?.fieldList;
                if (!el || typeof Sortable === 'undefined') return;
                this._fieldSortable = new Sortable(el, {
                    handle: '.cursor-grab',
                    animation: 200,
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    ghostClass: 'sortable-ghost',
                    onEnd: (evt) => {
                        if (evt.oldIndex === evt.newIndex) return;
                        const item = this.fields.splice(evt.oldIndex, 1)[0];
                        this.fields.splice(evt.newIndex, 0, item);
                        this.dirty = true;
                    },
                });
            },

            addField(name) {
                const defaults = {
                    text: { label: 'Text', type: 'text', placeholder: 'Enter text…' },
                    email: { label: 'Email', type: 'email', placeholder: 'you@example.com' },
                    phone: { label: 'Phone', type: 'phone', placeholder: '+1 (555) 000-0000' },
                    number: { label: 'Number', type: 'number', placeholder: '0' },
                    textarea: { label: 'Textarea', type: 'textarea', placeholder: 'Write something…' },
                    select: { label: 'Select', type: 'select', options: ['Option 1', 'Option 2', 'Option 3'] },
                    checkbox: { label: 'Checkbox', type: 'checkbox', options: ['Option 1', 'Option 2', 'Option 3'] },
                    radio: { label: 'Radio', type: 'radio', options: ['Option 1', 'Option 2', 'Option 3'] },
                    date: { label: 'Date', type: 'date', placeholder: 'YYYY-MM-DD' },
                    file: { label: 'File upload', type: 'file' },
                };

                const def = defaults[name];
                if (!def) return;

                const field = {
                    _key: crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2),
                    type: def.type,
                    label: def.label,
                    name: def.type + '_' + Math.random().toString(36).slice(2, 6),
                    placeholder: def.placeholder || '',
                    required: false,
                };

                if (def.options) {
                    field.options = def.options.slice();
                }

                this.fields.push(field);
                this.dirty = true;
                this.$nextTick(() => this.initFieldSortable());
            },

            removeField(i) {
                this.fields.splice(i, 1);
                this.dirty = true;
                this.$nextTick(() => this.initFieldSortable());
            },

            async save() {
                if (this.isSaving) return;
                this.isSaving = true;
                try {
                    const res = await fetch(window.formSaveRoute, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ fields: this.fields }),
                    });

                    if (!res.ok) {
                        throw new Error('Save failed');
                    }

                    this.dirty = false;
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Form fields saved.', type: 'success' },
                    }));
                } catch (e) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Could not save form fields.', type: 'error' },
                    }));
                } finally {
                    this.isSaving = false;
                }
            },
        };
    }
</script>
@endpush
