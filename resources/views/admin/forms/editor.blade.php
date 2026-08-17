@extends('admin.layout')

@section('title', 'Form Editor — '.$form->title)
@section('breadcrumb', 'Form Editor')

@section('content-full')
<script>
    window.formFields = @json($form->fields ?? []);
    window.formFieldList = @json($fieldList);
    window.formSaveRoute = @json(route('admin.forms.update-fields', $form));
</script>


<div class="flex h-full gap-3 p-3"
    x-data="formEditor()"
    x-init="init(window.formFields)"
    x-on:field-selected.window="addField($event.detail.name)"
>
    {{-- Sidepanel --}}
    <div class="w-[420px] min-w-[320px] shrink-0 bg-white h-full flex flex-col rounded-2xl border border-[#e8eaed] shadow-[0_1px_2px_rgba(16,24,40,0.04),0_8px_24px_-12px_rgba(16,24,40,0.12)] overflow-hidden">
        <div class="flex-1 overflow-y-auto px-3 pt-3 pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div class="bg-gray-100 rounded-2xl p-[7px]">
                {{-- Field list mode --}}
                <div x-show="activeField === null">
                    <div class="flex items-center justify-between pr-3 py-3 text-sm font-medium text-text-heading">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.forms.index') }}" aria-label="Back" class="size-7 shrink-0 flex items-center justify-center rounded-full border border-gray-300 bg-white text-text-primary hover:bg-gray-100 transition-colors">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3">
                                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <div class="font-bold">Form Fields</div>
                        </div>
                        <button
                            type="button"
                            @click="window.dispatchEvent(new CustomEvent('open-field-picker'))"
                            class="size-6 flex items-center justify-center rounded-full bg-white text-text-primary border border-content-border hover:bg-gray-50 transition-colors"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[14px]">
                                <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    <div x-show="fields.length === 0" class="flex flex-col items-center justify-center py-12 text-center px-6">
                        <img src="/empty-collection.svg" alt="No items" class="size-32 mb-4 opacity-60">
                        <p class="text-sm font-medium text-text-heading">No fields yet.</p>
                        <p class="text-xs text-text-muted mt-1">Add a field using the + button above.</p>
                    </div>
                    <div x-show="fields.length > 0" class="space-y-0.5" x-ref="fieldList">
                        <template x-for="(field, i) in fields" :key="field._key">
                            <x-admin::sortable-item @click="editField(i)">
                                <x-slot:label>
                                    <div @click="editField(i)" class="flex items-center gap-2 min-w-0 cursor-pointer">
                                        <span class="text-[11px] font-medium text-text-muted bg-panel-bg px-1.5 py-0.5 rounded shrink-0" x-text="field.type"></span>
                                        <span class="text-sm font-semibold text-text-heading group-hover:text-primary truncate leading-normal transition-colors" x-text="field.label"></span>
                                    </div>
                                </x-slot:label>
                                <x-slot:edit>
                                    <button type="button" @click.stop="editField(i)" class="p-1 text-text-muted/60 hover:text-primary transition-colors rounded hover:bg-text-primary/10" title="Edit field">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot:edit>
                                <x-slot:remove>
                                    <button type="button" @click.stop="removeField(i)" class="p-1 text-text-muted/60 hover:text-danger transition-colors rounded hover:bg-text-primary/10" title="Remove field">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot:remove>
                            </x-admin::sortable-item>
                        </template>
                    </div>
                </div>

                {{-- Field editor mode --}}
                <div x-show="activeField !== null">
                    <div class="flex items-center gap-2 mb-3">
                        <button @click="exitField()" aria-label="Back" class="size-7 shrink-0 flex items-center justify-center rounded-full border border-gray-300 bg-white text-text-primary hover:bg-gray-100 transition-colors">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3">
                                <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="grow truncate text-sm font-bold text-text-heading">
                            <span x-text="currentField()?.label || 'Edit Field'"></span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <template x-for="prop in currentFieldSchema()" :key="prop.name">
                            <div>
                                <template x-if="prop.type === 'string'">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="prop.label"></label>
                                        <input type="text" :value="currentField()[prop.name] || ''" @input="setFieldProp(prop.name, $event.target.value)"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                    </div>
                                </template>
                                <template x-if="prop.type === 'boolean'">
                                    <div class="flex items-center justify-between py-2">
                                        <label class="text-sm font-semibold text-text-primary" x-text="prop.label"></label>
                                        <button type="button" role="switch"
                                            :aria-checked="currentField()[prop.name] === true"
                                            @click="setFieldProp(prop.name, !currentField()[prop.name])"
                                            :class="currentField()[prop.name] ? 'bg-primary' : 'bg-gray-300'"
                                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/30 focus:ring-offset-1"
                                        >
                                            <span aria-hidden="true"
                                                :class="currentField()[prop.name] ? 'translate-x-5' : 'translate-x-0'"
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            ></span>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="prop.type === 'tags'">
                                    <div>
                                        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="prop.label"></label>
                                        <div class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus-within:ring-2 focus-within:ring-primary/30 focus-within:border-primary">
                                            <div class="flex flex-wrap gap-1 mb-1">
                                                <template x-for="(tag, ti) in (currentField()[prop.name] || [])" :key="ti">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-panel-bg rounded text-xs">
                                                        <span x-text="tag"></span>
                                                        <button @click="removeTag(prop.name, ti)" class="text-danger hover:text-danger/70 leading-none">&times;</button>
                                                    </span>
                                                </template>
                                            </div>
                                            <input type="text" placeholder="Type and press Enter..." @keydown.enter.prevent="addTag(prop.name, $event.target.value); $event.target.value = ''"
                                                class="w-full border-0 p-0 text-sm text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-0">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <div class="shrink-0 px-4 py-3 border-t border-content-border bg-body-bg flex items-center justify-end gap-2">
            <button type="button" @click="reset()" :disabled="!dirty" class="px-4 py-1.5 text-sm font-medium text-text-primary bg-content-bg border border-content-border rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Reset
            </button>
            <button type="button" @click="save()" :disabled="!dirty || isSaving" class="px-4 py-1.5 text-sm font-medium text-white bg-primary rounded-lg hover:opacity-90 transition-opacity disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2">
                <span x-show="!isSaving">Save &amp; Publish</span>
                <span x-show="isSaving" class="inline-flex items-center gap-2">
                    <svg class="animate-spin size-4" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Saving...
                </span>
            </button>
        </div>
    </div>

    {{-- Form area --}}
    <div class="flex-1 min-w-0 overflow-y-auto bg-white rounded-2xl border border-[#e8eaed] shadow-[0_1px_2px_rgba(16,24,40,0.04),0_8px_24px_-12px_rgba(16,24,40,0.12)]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6">
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
            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm w-[500px] mx-auto px-6 py-6">
                <div class="mb-5">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-base font-semibold text-text-heading">Build Form</p>
                    </div>
                    <p class="text-xs text-text-muted mt-1.5">Build your form structure here and then generate ui for that form.</p>
                </div>

                {{-- Live preview --}}
                <div x-show="fields.length > 0" class="pt-3 space-y-0.5">
                    <template x-for="(field, i) in fields" :key="field._key">
                        <div :data-field-index="i" @click="editField(i)"
                            class="rounded-lg p-2 -mx-3 cursor-pointer transition-all duration-150 hover:border-blue-400 hover:bg-blue-500/10 border border-transparent hover:border-dashed"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-sm font-semibold text-text-primary" x-text="field.label + (field.required ? ' *' : '')"></label>
                            </div>
                            <template x-if="field.type === 'checkbox' || field.type === 'radio'">
                                <div>
                                    <template x-for="(opt, oi) in (field.options || [])" :key="oi">
                                        <label class="flex items-center gap-2 py-1 text-sm text-text-primary cursor-pointer">
                                            <input :type="field.type" disabled class="accent-primary">
                                            <span x-text="opt"></span>
                                        </label>
                                    </template>
                                </div>
                            </template>
                            <template x-if="field.type === 'select'">
                                <div>
                                    <select disabled class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)]">
                                        <option value="" x-text="field.placeholder || 'Select...'"></option>
                                        <template x-for="(opt, oi) in (field.options || [])" :key="oi">
                                            <option x-text="opt"></option>
                                        </template>
                                    </select>
                                </div>
                            </template>
                            <template x-if="field.type === 'textarea'">
                                <div>
                                    <textarea disabled :placeholder="field.placeholder || ''" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] resize-none" rows="3"></textarea>
                                </div>
                            </template>
                            <template x-if="!['checkbox','radio','select','textarea'].includes(field.type)">
                                <div>
                                    <input :type="field.type" disabled :placeholder="field.placeholder || ''" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)]">
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <x-admin::field-picker />
</div>
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
            originalFields: [],
            activeField: null,
            dirty: false,
            isSaving: false,
            _fieldSortable: null,

            fieldSchemas: {
                text: [
                    { name: 'label', type: 'string', label: 'Label' },
                    { name: 'column_name', type: 'string', label: 'Column Name' },
                    { name: 'name', type: 'string', label: 'Field Key' },
                    { name: 'placeholder', type: 'string', label: 'Placeholder' },
                    { name: 'required', type: 'boolean', label: 'Required' },
                ],
                email: [
                    { name: 'label', type: 'string', label: 'Label' },
                    { name: 'column_name', type: 'string', label: 'Column Name' },
                    { name: 'name', type: 'string', label: 'Field Key' },
                    { name: 'placeholder', type: 'string', label: 'Placeholder' },
                    { name: 'required', type: 'boolean', label: 'Required' },
                ],
                phone: [
                    { name: 'label', type: 'string', label: 'Label' },
                    { name: 'column_name', type: 'string', label: 'Column Name' },
                    { name: 'name', type: 'string', label: 'Field Key' },
                    { name: 'placeholder', type: 'string', label: 'Placeholder' },
                    { name: 'required', type: 'boolean', label: 'Required' },
                ],
                number: [
                    { name: 'label', type: 'string', label: 'Label' },
                    { name: 'column_name', type: 'string', label: 'Column Name' },
                    { name: 'name', type: 'string', label: 'Field Key' },
                    { name: 'placeholder', type: 'string', label: 'Placeholder' },
                    { name: 'required', type: 'boolean', label: 'Required' },
                ],
                textarea: [
                    { name: 'label', type: 'string', label: 'Label' },
                    { name: 'column_name', type: 'string', label: 'Column Name' },
                    { name: 'name', type: 'string', label: 'Field Key' },
                    { name: 'placeholder', type: 'string', label: 'Placeholder' },
                    { name: 'required', type: 'boolean', label: 'Required' },
                ],
                select: [
                    { name: 'label', type: 'string', label: 'Label' },
                    { name: 'column_name', type: 'string', label: 'Column Name' },
                    { name: 'name', type: 'string', label: 'Field Key' },
                    { name: 'options', type: 'tags', label: 'Options' },
                    { name: 'required', type: 'boolean', label: 'Required' },
                ],
                checkbox: [
                    { name: 'label', type: 'string', label: 'Label' },
                    { name: 'column_name', type: 'string', label: 'Column Name' },
                    { name: 'name', type: 'string', label: 'Field Key' },
                    { name: 'options', type: 'tags', label: 'Options' },
                    { name: 'required', type: 'boolean', label: 'Required' },
                ],
                radio: [
                    { name: 'label', type: 'string', label: 'Label' },
                    { name: 'column_name', type: 'string', label: 'Column Name' },
                    { name: 'name', type: 'string', label: 'Field Key' },
                    { name: 'options', type: 'tags', label: 'Options' },
                    { name: 'required', type: 'boolean', label: 'Required' },
                ],
                date: [
                    { name: 'label', type: 'string', label: 'Label' },
                    { name: 'column_name', type: 'string', label: 'Column Name' },
                    { name: 'name', type: 'string', label: 'Field Key' },
                    { name: 'placeholder', type: 'string', label: 'Placeholder' },
                    { name: 'required', type: 'boolean', label: 'Required' },
                ],
                time: [
                    { name: 'label', type: 'string', label: 'Label' },
                    { name: 'column_name', type: 'string', label: 'Column Name' },
                    { name: 'name', type: 'string', label: 'Field Key' },
                    { name: 'placeholder', type: 'string', label: 'Placeholder' },
                    { name: 'required', type: 'boolean', label: 'Required' },
                ],
                file: [
                    { name: 'label', type: 'string', label: 'Label' },
                    { name: 'column_name', type: 'string', label: 'Column Name' },
                    { name: 'name', type: 'string', label: 'Field Key' },
                    { name: 'required', type: 'boolean', label: 'Required' },
                ],
            },

            init(fields) {
                this.fields = (fields || []).map(f => ({
                    ...f,
                    column_name: f.column_name || f.label || '',
                    _nameEdited: true,
                    _columnEdited: true,
                    _placeholderEdited: true,
                }));
                this.originalFields = JSON.parse(JSON.stringify(this.fields));
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

                        // Revert Sortable DOM changes so Alpine can handle the DOM update
                        const itemEl = evt.item;
                        const nextSibling = evt.from.children[evt.oldIndex > evt.newIndex ? evt.oldIndex + 1 : evt.oldIndex];
                        evt.from.insertBefore(itemEl, nextSibling);

                        const offset = (evt.from.children[0] && evt.from.children[0].tagName === 'TEMPLATE') ? 1 : 0;
                        const oldIdx = evt.oldIndex - offset;
                        const newIdx = evt.newIndex - offset;

                        if (oldIdx >= 0 && oldIdx < this.fields.length && newIdx >= 0 && newIdx < this.fields.length) {
                            const item = this.fields.splice(oldIdx, 1)[0];
                            if (item !== undefined) {
                                this.fields.splice(newIdx, 0, item);
                                this.dirty = true;
                            }
                        }
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
                    time: { label: 'Time', type: 'time', placeholder: 'HH:MM' },
                    file: { label: 'File upload', type: 'file' },
                };

                const def = defaults[name];
                if (!def) return;

                const field = {
                    _key: crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2),
                    type: def.type,
                    label: def.label,
                    column_name: def.label,
                    name: def.type + '_' + Math.random().toString(36).slice(2, 6),
                    placeholder: def.placeholder || '',
                    required: false,
                    _nameEdited: false,
                    _columnEdited: false,
                    _placeholderEdited: false,
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
                if (this.activeField === i) this.activeField = null;
                else if (this.activeField > i) this.activeField--;
                this.$nextTick(() => this.initFieldSortable());
            },

            currentField() {
                if (this.activeField === null || !this.fields[this.activeField]) return null;
                return this.fields[this.activeField];
            },

            currentFieldSchema() {
                const field = this.currentField();
                if (!field) return [];
                return this.fieldSchemas[field.type] || [];
            },

            editField(i) {
                this.activeField = i;
            },

            exitField() {
                this.activeField = null;
            },

            setFieldProp(name, value) {
                const field = this.currentField();
                if (!field) return;
                field[name] = value;
                this.dirty = true;

                if (name === 'name') {
                    field._nameEdited = true;
                } else if (name === 'column_name') {
                    field._columnEdited = true;
                } else if (name === 'placeholder') {
                    field._placeholderEdited = true;
                } else if (name === 'label') {
                    const cleanLabel = (value || '').trim();
                    const lower = cleanLabel.toLowerCase();

                    // Smart auto-generation of Column Name if not manually locked
                    if (!field._columnEdited) {
                        field.column_name = cleanLabel;
                    }

                    // Smart auto-generation of Field Key (name) if not manually locked
                    if (!field._nameEdited) {
                        const generatedKey = lower
                            .replace(/[^a-z0-9]+/g, '_')
                            .replace(/^_+|_+$/g, '');
                        if (generatedKey) {
                            field.name = generatedKey;
                        }
                    }

                    // Smart auto-generation of Placeholder if not manually locked
                    if (!field._placeholderEdited) {
                        if (field.type === 'email' || lower.includes('email')) {
                            field.placeholder = 'you@example.com';
                        } else if (field.type === 'phone' || lower.includes('phone') || lower.includes('tel') || lower.includes('mobile')) {
                            field.placeholder = '+1 (555) 000-0000';
                        } else if (field.type === 'date' || lower.includes('date') || lower.includes('dob') || lower.includes('birthday')) {
                            field.placeholder = 'YYYY-MM-DD';
                        } else if (field.type === 'time' || lower.includes('time')) {
                            field.placeholder = 'HH:MM';
                        } else if (field.type === 'number' || lower.includes('quantity') || lower.includes('amount') || lower.includes('age')) {
                            field.placeholder = '0';
                        } else if (cleanLabel) {
                            field.placeholder = 'Enter ' + lower + '…';
                        }
                    }
                }
            },

            addTag(name, value) {
                const field = this.currentField();
                if (!field) return;
                const tag = value.trim();
                if (!tag) return;
                if (!field[name]) field[name] = [];
                field[name].push(tag);
                this.dirty = true;
            },

            removeTag(name, i) {
                const field = this.currentField();
                if (!field) return;
                if (!field[name]) return;
                field[name].splice(i, 1);
                this.dirty = true;
            },

            reset() {
                this.fields = JSON.parse(JSON.stringify(this.originalFields));
                this.activeField = null;
                this.dirty = false;
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
