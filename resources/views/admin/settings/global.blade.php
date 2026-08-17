@extends('admin.layout')

@section('title', 'Settings')
@section('breadcrumb', 'Settings')

@section('content')
    <script>
        window.settingsCustomFields = @json($settings->custom_fields ?? []);
        window.settingsCustomValues = @json($settings->custom_values ?? (object)[]);
    </script>
    <div
        class="max-w-5xl mx-auto px-2 sm:px-0"
        style="max-width: 64rem;"
        x-data="globalSettingsForm()"
    >
        {{-- ==================== Preferences Form ==================== --}}
        <form method="POST" action="{{ route('admin.settings') }}" id="settings-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="custom_fields" :value="JSON.stringify(fields)">

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-6 shrink-0 text-text-muted">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.51a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    Settings
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <button type="button" @click="openFieldModal()" x-show="activeTab === 'general'"
                        class="inline-flex items-center justify-center gap-1.5 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 px-3 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200 text-sm">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                            <path d="M8 3v10M3 8h10" />
                        </svg>
                        Add Input
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        Save Settings
                    </button>
                </div>
            </header>

            {{-- Settings Navigation Tabs --}}
            <div class="flex items-center gap-1 border-b border-content-border mb-6 px-2 sm:px-0">
                <button type="button" @click="activeTab = 'general'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 cursor-pointer -mb-px"
                    :class="activeTab === 'general' ? 'border-primary text-primary font-semibold' : 'border-transparent text-text-muted hover:text-text-primary'">
                    General
                </button>
                <button type="button" @click="activeTab = 'recaptcha'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 cursor-pointer -mb-px"
                    :class="activeTab === 'recaptcha' ? 'border-primary text-primary font-semibold' : 'border-transparent text-text-muted hover:text-text-primary'">
                    reCAPTCHA
                </button>
            </div>

            {{-- Settings Content --}}
            <div class="space-y-6">
                {{-- General Settings Tab Panel --}}
                <div x-show="activeTab === 'general'">
                    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                        <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">General Settings</div>
                        <p class="px-[18px] pb-3 text-sm text-text-muted">Configure default preferences for your application.</p>
                        <div class="px-1.5 pb-2">
                            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                                <div>

                                    {{-- Language --}}
                                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-sm font-medium text-text-heading">Language</label>
                                            <div class="text-sm text-text-muted">The preferred language for the control panel.</div>
                                        </div>
                                        <div>
                                            <div x-data="{
                                                open: false,
                                                selected: @js(old('language', $settings->language ?? 'en')),
                                                options: [
                                                    { value: 'en', label: 'English' },
                                                    { value: 'fr', label: 'French' },
                                                    { value: 'de', label: 'German' },
                                                    { value: 'es', label: 'Spanish' },
                                                    { value: 'nl', label: 'Dutch' }
                                                ],
                                                get selectedLabel() {
                                                    return this.options.find(o => o.value === this.selected)?.label ?? 'English';
                                                },
                                                select(val) {
                                                    this.selected = val;
                                                    this.open = false;
                                                }
                                            }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-10 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                                                    <span class="truncate font-medium" x-text="selectedLabel"></span>
                                                    <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180 text-primary' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" x-cloak
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="absolute z-50 top-full mt-1 left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-xl p-1 max-h-60 overflow-y-auto space-y-0.5"
                                                >
                                                    <template x-for="opt in options" :key="opt.value">
                                                        <button type="button" @click="select(opt.value)" class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-md transition-colors cursor-pointer" :class="opt.value === selected ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-gray-100'">
                                                            <span x-text="opt.label"></span>
                                                            <svg x-show="opt.value === selected" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </template>
                                                </div>
                                                <input type="hidden" name="language" :value="selected">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Currency --}}
                                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5" :class="fields.length > 0 ? 'border-b border-content-border' : ''">
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-sm font-medium text-text-heading">Currency</label>
                                            <div class="text-sm text-text-muted">The default currency for your site.</div>
                                        </div>
                                        <div>
                                            <div x-data="{
                                                open: false,
                                                selected: @js(old('currency', $settings->currency ?? 'USD')),
                                                options: [
                                                    { value: 'USD', label: 'USD ($)' },
                                                    { value: 'EUR', label: 'EUR (€)' },
                                                    { value: 'GBP', label: 'GBP (£)' },
                                                    { value: 'BDT', label: 'BDT (৳)' },
                                                    { value: 'INR', label: 'INR (₹)' },
                                                    { value: 'CAD', label: 'CAD (C$)' },
                                                    { value: 'AUD', label: 'AUD (A$)' },
                                                    { value: 'JPY', label: 'JPY (¥)' },
                                                    { value: 'CNY', label: 'CNY (¥)' },
                                                    { value: 'SAR', label: 'SAR (﷼)' },
                                                    { value: 'AED', label: 'AED (د.إ)' }
                                                ],
                                                get selectedLabel() {
                                                    return this.options.find(o => o.value === this.selected)?.label ?? 'USD ($)';
                                                },
                                                select(val) {
                                                    this.selected = val;
                                                    this.open = false;
                                                }
                                            }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-10 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                                                    <span class="truncate font-medium" x-text="selectedLabel"></span>
                                                    <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180 text-primary' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" x-cloak
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="absolute z-50 top-full mt-1 left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-xl p-1 max-h-60 overflow-y-auto space-y-0.5"
                                                >
                                                    <template x-for="opt in options" :key="opt.value">
                                                        <button type="button" @click="select(opt.value)" class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-md transition-colors cursor-pointer" :class="opt.value === selected ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-gray-100'">
                                                            <span x-text="opt.label"></span>
                                                            <svg x-show="opt.value === selected" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </template>
                                                </div>
                                                <input type="hidden" name="currency" :value="selected">
                                            </div>
                                            @error('currency') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                {{-- Dynamic Custom Inputs in General Settings --}}
                                <div x-show="fields.length > 0" id="sortable-custom-fields">
                                    <template x-for="(field, index) in fields" :key="field._key || field.template || index">
                                        <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 group/row bg-white hover:bg-gray-50/60 transition-colors cursor-grab active:cursor-grabbing select-none"
                                            :class="index < fields.length - 1 ? 'border-b border-content-border' : ''">
                                            <div class="flex flex-col gap-1.5 min-w-0">
                                                <label class="text-sm font-medium text-text-heading cursor-grab" x-text="field.title"></label>
                                                <div class="text-sm text-text-muted" x-text="field.description || 'Custom setting parameter.'"></div>
                                            </div>

                                            <div class="flex items-start gap-3">
                                                <div class="flex-1 min-w-0">
                                                    {{-- Text Field --}}
                                                    <template x-if="field.type === 'text'">
                                                        <input type="text"
                                                            :name="`custom_values[${field.template}]`"
                                                            x-model="customValues[field.template]"
                                                            :placeholder="field.title"
                                                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                    </template>

                                                    {{-- Textarea Field --}}
                                                    <template x-if="field.type === 'textarea'">
                                                        <textarea
                                                            :name="`custom_values[${field.template}]`"
                                                            x-model="customValues[field.template]"
                                                            rows="3"
                                                            :placeholder="field.title"
                                                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                                                    </template>

                                                    {{-- Number Field --}}
                                                    <template x-if="field.type === 'number'">
                                                        <input type="number"
                                                            :name="`custom_values[${field.template}]`"
                                                            x-model="customValues[field.template]"
                                                            :placeholder="field.title"
                                                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                    </template>

                                                    {{-- Color Field --}}
                                                    <template x-if="field.type === 'color'">
                                                        <div class="flex items-center gap-2">
                                                            <input type="color"
                                                                x-model="customValues[field.template]"
                                                                class="h-9 w-12 rounded border border-gray-300 p-0.5 cursor-pointer bg-white">
                                                            <input type="text"
                                                                :name="`custom_values[${field.template}]`"
                                                                x-model="customValues[field.template]"
                                                                placeholder="#000000"
                                                                class="w-full block bg-white border border-gray-300 text-text-primary font-mono text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                        </div>
                                                    </template>

                                                    {{-- Toggle / Boolean Field --}}
                                                    <template x-if="field.type === 'toggle' || field.type === 'boolean'">
                                                        <div class="flex items-center gap-3">
                                                            <button type="button" role="switch"
                                                                :aria-checked="!!customValues[field.template]"
                                                                :data-state="customValues[field.template] ? 'checked' : 'unchecked'"
                                                                @click="customValues[field.template] = !customValues[field.template]"
                                                                class="relative flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/30 data-[state=checked]:border-emerald-500 data-[state=checked]:bg-emerald-500 data-[state=unchecked]:!border-gray-300 data-[state=unchecked]:bg-gray-200">
                                                                <span :data-state="customValues[field.template] ? 'checked' : 'unchecked'"
                                                                    class="my-auto flex items-center justify-center size-5 rounded-full bg-white text-xs shadow transition-transform data-[state=checked]:translate-x-[20px] data-[state=unchecked]:translate-x-0"></span>
                                                            </button>
                                                            <input type="hidden" :name="`custom_values[${field.template}]`" :value="customValues[field.template] ? '1' : '0'">
                                                            <span class="text-xs font-medium text-text-muted" x-text="customValues[field.template] ? 'Enabled' : 'Disabled'"></span>
                                                        </div>
                                                    </template>

                                                    {{-- Select Field --}}
                                                    <template x-if="field.type === 'select'">
                                                        <select :name="`custom_values[${field.template}]`"
                                                            x-model="customValues[field.template]"
                                                            class="w-full block bg-white border border-gray-300 text-text-primary shadow-xs text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                            <option value="">Select option...</option>
                                                            <template x-for="opt in (field.options ? field.options.split(',').map(s => s.trim()).filter(Boolean) : [])" :key="opt">
                                                                <option :value="opt" x-text="opt"></option>
                                                            </template>
                                                        </select>
                                                    </template>

                                                    {{-- Image Asset Field --}}
                                                    <template x-if="field.type === 'image'">
                                                        <div class="rounded-lg border border-gray-300 bg-white overflow-hidden shadow-xs">
                                                            <input type="hidden" :name="`custom_values[${field.template}]`" :value="customValues[field.template] || ''">
                                                            <div class="flex items-center gap-3 px-2.5 py-2 bg-white">
                                                                <button
                                                                    type="button"
                                                                    @click="pickImageForField(field.template)"
                                                                    class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-8 text-xs leading-tight px-3 bg-white hover:bg-gray-50 text-text-primary border border-gray-200 shadow-xs"
                                                                >
                                                                    <svg viewBox="0 0 24 24" fill="none" class="size-3.5 shrink-0">
                                                                        <path d="M3 7a2 2 0 0 1 2-2h3.5l2 2H19a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                                                    </svg>
                                                                    Browse Assets
                                                                </button>
                                                                <div class="flex items-center gap-1.5 text-xs text-text-muted min-w-0 flex-1">
                                                                    <span class="truncate" x-text="customValues[field.template] ? '1 image selected' : 'Choose an image asset'"></span>
                                                                </div>
                                                            </div>
                                                            <div class="border-t border-gray-100 px-2.5 py-2 flex items-center gap-3 bg-gray-50/50" x-show="customValues[field.template]">
                                                                <div class="size-8 rounded-md overflow-hidden bg-white flex items-center justify-center shrink-0 border border-gray-200">
                                                                    <img :src="customValues[field.template]" alt="Selected preview" class="size-full object-cover">
                                                                </div>
                                                                <span class="flex-1 min-w-0 truncate text-xs text-text-primary font-medium" x-text="getImageName(customValues[field.template])"></span>
                                                                <button
                                                                    type="button"
                                                                    @click="customValues[field.template] = ''"
                                                                    class="shrink-0 flex size-6 items-center justify-center rounded-md text-text-muted hover:bg-red-50 hover:text-danger transition-colors"
                                                                    title="Remove image"
                                                                >
                                                                    <svg viewBox="0 0 24 24" fill="none" class="size-4">
                                                                        <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    {{-- Location Field --}}
                                                    <template x-if="field.type === 'location'">
                                                        <div class="space-y-2">
                                                            <template x-if="field.enable_country !== false">
                                                                <input type="text"
                                                                    :name="`custom_values[${field.template}][country]`"
                                                                    :value="getLocationSubfield(field.template, 'country')"
                                                                    @input="setLocationSubfield(field.template, 'country', $event.target.value)"
                                                                    placeholder="Country"
                                                                    class="w-full block bg-white border border-gray-300 text-text-primary text-xs rounded-lg px-3 py-1.5 h-8">
                                                            </template>
                                                            <template x-if="field.enable_state !== false">
                                                                <input type="text"
                                                                    :name="`custom_values[${field.template}][state]`"
                                                                    :value="getLocationSubfield(field.template, 'state')"
                                                                    @input="setLocationSubfield(field.template, 'state', $event.target.value)"
                                                                    placeholder="State / Province"
                                                                    class="w-full block bg-white border border-gray-300 text-text-primary text-xs rounded-lg px-3 py-1.5 h-8">
                                                            </template>
                                                            <template x-if="field.enable_city !== false">
                                                                <input type="text"
                                                                    :name="`custom_values[${field.template}][city]`"
                                                                    :value="getLocationSubfield(field.template, 'city')"
                                                                    @input="setLocationSubfield(field.template, 'city', $event.target.value)"
                                                                    placeholder="City"
                                                                    class="w-full block bg-white border border-gray-300 text-text-primary text-xs rounded-lg px-3 py-1.5 h-8">
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>

                                                {{-- Field Actions Dropdown --}}
                                                <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                                                    <button
                                                        type="button"
                                                        @click="open = !open"
                                                        class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-200 bg-white text-text-muted hover:text-text-primary hover:bg-gray-50 transition-colors shadow-xs cursor-pointer"
                                                        title="Actions"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                                            <circle cx="12" cy="5" r="2" />
                                                            <circle cx="12" cy="12" r="2" />
                                                            <circle cx="12" cy="19" r="2" />
                                                        </svg>
                                                    </button>
                                                    <div
                                                        x-show="open"
                                                        x-cloak
                                                        x-transition:enter="transition ease-out duration-100"
                                                        x-transition:enter-start="opacity-0 scale-95"
                                                        x-transition:enter-end="opacity-100 scale-100"
                                                        x-transition:leave="transition ease-in duration-75"
                                                        x-transition:leave-start="opacity-100 scale-100"
                                                        x-transition:leave-end="opacity-0 scale-95"
                                                        style="z-index: 9999; display: none;"
                                                        class="absolute right-0 top-full mt-1 min-w-[12rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5"
                                                    >
                                                        <button
                                                            type="button"
                                                            @click="open = false; editField(index)"
                                                            class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil size-4 shrink-0 text-text-muted">
                                                                <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                                                                <path d="m15 5 4 4"/>
                                                            </svg>
                                                            <span>Edit</span>
                                                        </button>
                                                        <hr class="my-1 border-content-border">
                                                        <button
                                                            type="button"
                                                            @click="open = false; promptDeleteField(index)"
                                                            class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2 size-4 shrink-0 text-red-500">
                                                                <path d="M3 6h18"/>
                                                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                                            </svg>
                                                            <span>Delete</span>
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
                </div> {{-- End General Tab Panel --}}

                {{-- reCAPTCHA Settings Tab Panel --}}
                <div x-show="activeTab === 'recaptcha'" style="display: none;">
                    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                        <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">reCAPTCHA v3 Protection</div>
                        <p class="px-[18px] pb-3 text-sm text-text-muted">Configure Google reCAPTCHA v3 credentials to protect your admin login page from automated brute-force attacks.</p>
                        <div class="px-1.5 pb-2">
                            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                                <div>
                                    {{-- reCAPTCHA Site Key --}}
                                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-sm font-medium text-text-heading">reCAPTCHA Site Key</label>
                                            <div class="text-sm text-text-muted">Google reCAPTCHA v3 public site key for login protection.</div>
                                        </div>
                                        <div>
                                            <input type="text" name="recaptcha_site_key" value="{{ old('recaptcha_site_key', $settings->recaptcha_site_key ?? '') }}"
                                                placeholder="6L..."
                                                class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                                            @error('recaptcha_site_key') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    {{-- reCAPTCHA Secret Key --}}
                                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-sm font-medium text-text-heading">reCAPTCHA Secret Key</label>
                                            <div class="text-sm text-text-muted">Google reCAPTCHA v3 private secret key for server verification.</div>
                                        </div>
                                        <div>
                                            <input type="password" name="recaptcha_secret_key" value="{{ old('recaptcha_secret_key', $settings->recaptcha_secret_key ?? '') }}"
                                                placeholder="6L..."
                                                class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                                            @error('recaptcha_secret_key') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== System Updates Card ==================== --}}
                <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                    <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">System Updates</div>
                    <p class="px-[18px] pb-3 text-sm text-text-muted">Check for the latest Lara CMS updates and upgrade your system in one click (no Git required).</p>
                    <div class="px-1.5 pb-2">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-4 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <div class="text-sm font-medium text-text-heading">
                                        Current Version: <span class="font-bold text-primary" x-text="`v${currentVersion}`"></span>
                                    </div>
                                    <div class="text-xs text-text-muted mt-1">
                                        <template x-if="updateState === 'idle'">
                                            <span>Click below to check for available system updates.</span>
                                        </template>
                                        <template x-if="updateState === 'checking'">
                                            <span class="text-primary font-medium flex items-center gap-1.5">
                                                <svg class="animate-spin size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                Checking update server...
                                            </span>
                                        </template>
                                        <template x-if="updateState === 'up_to_date'">
                                            <span class="text-emerald-600 font-medium">✓ Your CMS is up to date!</span>
                                        </template>
                                        <template x-if="updateState === 'found'">
                                            <span class="text-amber-600 font-semibold" x-text="`New version v${latestVersion} is available for update!`"></span>
                                        </template>
                                        <template x-if="updateState === 'updating'">
                                            <span class="text-primary font-medium flex items-center gap-1.5">
                                                <svg class="animate-spin size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                Downloading & installing update package...
                                            </span>
                                        </template>
                                        <template x-if="updateState === 'done'">
                                            <span class="text-emerald-600 font-semibold">✓ Update complete! System updated to v<span x-text="currentVersion"></span></span>
                                        </template>
                                        <template x-if="updateState === 'error'">
                                            <span class="text-red-600 font-medium" x-text="updateError || 'Update encountered an error.'"></span>
                                        </template>
                                        <template x-if="updateState === 'check_failed'">
                                            <span class="text-amber-600 font-medium">⚠ Could not verify the latest version. Please try again or check your connection.</span>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button"
                                        @click="checkForUpdates()"
                                        :disabled="updateState === 'checking' || updateState === 'updating'"
                                        class="inline-flex items-center justify-center gap-1.5 h-9 px-3 rounded-lg border border-content-border bg-white text-xs font-medium text-text-heading hover:bg-body-bg shadow-xs transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-text-muted"><path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H4.75a.75.75 0 00-.75.75v3.473a.75.75 0 001.5 0v-2.004l.527.527A7 7 0 0017 11.424a.75.75 0 00-1.688 0zM4.688 8.576a5.5 5.5 0 019.201-2.466l.312.311h-2.433a.75.75 0 000 1.5h3.475a.75.75 0 00.75-.75V3.698a.75.75 0 00-1.5 0v2.004l-.527-.527A7 7 0 003 8.576a.75.75 0 001.688 0z" clip-rule="evenodd" /></svg>
                                        <span>Check for Updates</span>
                                    </button>
                                    <button type="button"
                                        x-show="updateState === 'found'"
                                        @click="runUpdate()"
                                        :disabled="updateState === 'updating'"
                                        class="inline-flex items-center justify-center gap-1.5 h-9 px-3.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold text-white shadow-sm transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v8.69l3.22-3.22a.75.75 0 111.06 1.06l-4.5 4.5a.75.75 0 01-1.06 0l-4.5-4.5a.75.75 0 111.06-1.06l3.22 3.22V3.75A.75.75 0 0110 3z" clip-rule="evenodd" /></svg>
                                        <span>Update Now</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Terminal-style Update Console Logs --}}
                            <template x-if="updateLogs.length > 0">
                                <div x-ref="logConsole" class="mt-3 bg-gray-950 text-gray-100 rounded-xl p-3.5 font-mono text-xs space-y-1.5 max-h-60 overflow-y-auto border border-gray-800 shadow-inner">
                                    <div class="text-[11px] font-semibold text-gray-400 border-b border-gray-800 pb-1.5 flex items-center justify-between sticky top-0 bg-gray-950/95 backdrop-blur-xs z-10">
                                        <span>Update Process Log</span>
                                        <span class="size-2.5 rounded-full" :class="updateState === 'updating' ? 'bg-amber-400 animate-ping' : 'bg-emerald-400'"></span>
                                    </div>
                                    <template x-for="(log, idx) in updateLogs" :key="idx">
                                        <div class="leading-relaxed py-0.5" :class="log.includes('[ERROR]') ? 'text-red-400 font-bold' : (log.includes('✓') ? 'text-emerald-400 font-bold text-[13px] bg-emerald-950/40 p-1.5 rounded border border-emerald-500/30' : 'text-gray-200')" x-text="log"></div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

        </form>

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
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0" :class="fieldForm.type === 'location' ? 'text-primary' : 'text-text-muted'"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
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
    </div>
@endsection

@push('scripts')
<script>
    function globalSettingsForm() {
        const initialFields = window.settingsCustomFields || [];
        const initialValues = window.settingsCustomValues || {};
        return {
            activeTab: 'general',
            fields: (initialFields || []).map(f => ({
                ...f,
                template: (f.template || '').replace(/[^a-zA-Z0-9_]+/g, ''),
                _key: f._key || crypto.randomUUID()
            })),
            customValues: initialValues || {},

            showFieldModal: false,
            editingFieldIndex: null,
            isKeyManuallyEdited: false,

            showDeleteModal: false,
            deletingFieldIndex: null,
            fieldForm: {
                title: '',
                description: '',
                type: 'text',
                template: '',
                options: '',
                enable_country: true,
                enable_state: true,
                enable_city: true
            },

            // System updates state
            updateState: 'idle',
            currentVersion: '{{ $currentVersion }}',
            latestVersion: null,
            updateLogs: [],
            updateError: null,

            init() {
                this.$nextTick(() => this.initSortable());
            },

            getImageName(url) {
                if (!url) return '';
                try {
                    const path = url.split('?')[0];
                    return decodeURIComponent(path.split('/').pop() || path);
                } catch (e) {
                    return url;
                }
            },

            pickImageForField(fieldKey) {
                window.dispatchEvent(new CustomEvent('open-asset-picker', {
                    detail: {
                        callback: (url) => {
                            this.customValues[fieldKey] = url;
                        }
                    }
                }));
            },

            getLocationSubfield(fieldKey, subKey) {
                if (!this.customValues[fieldKey] || typeof this.customValues[fieldKey] !== 'object') {
                    return '';
                }
                return this.customValues[fieldKey][subKey] || '';
            },

            setLocationSubfield(fieldKey, subKey, val) {
                if (!this.customValues[fieldKey] || typeof this.customValues[fieldKey] !== 'object') {
                    this.customValues[fieldKey] = {};
                }
                this.customValues[fieldKey][subKey] = val;
            },

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
                    description: '',
                    type: 'text',
                    template: '',
                    options: '',
                    enable_country: true,
                    enable_state: true,
                    enable_city: true
                };
                this.showFieldModal = true;
            },

            editField(index) {
                this.editingFieldIndex = index;
                this.isKeyManuallyEdited = true;
                const raw = this.fields[index] || {};
                this.fieldForm = {
                    title: raw.title || '',
                    description: raw.description || '',
                    type: raw.type || 'text',
                    template: (raw.template || '').replace(/[^a-zA-Z0-9_]+/g, ''),
                    options: raw.options || '',
                    enable_country: raw.enable_country !== undefined ? !!raw.enable_country : true,
                    enable_state: raw.enable_state !== undefined ? !!raw.enable_state : true,
                    enable_city: raw.enable_city !== undefined ? !!raw.enable_city : true,
                };
                this.showFieldModal = true;
            },

            saveField() {
                if (!this.fieldForm.title.trim()) return;
                if (!this.fieldForm.template || !this.fieldForm.template.trim()) {
                    this.fieldForm.template = this.fieldForm.title.replace(/[^a-zA-Z0-9]+/g, '_').replace(/^_|_$/g, '').toLowerCase();
                } else {
                    this.fieldForm.template = this.fieldForm.template.replace(/[^a-zA-Z0-9_]+/g, '').toLowerCase();
                }

                if (this.fieldForm.type === 'location') {
                    if (!this.fieldForm.enable_country && !this.fieldForm.enable_state && !this.fieldForm.enable_city) {
                        this.fieldForm.enable_country = true;
                        this.fieldForm.enable_state = true;
                        this.fieldForm.enable_city = true;
                    }
                }

                if (this.editingFieldIndex !== null) {
                    const oldTemplate = this.fields[this.editingFieldIndex].template;
                    if (oldTemplate && oldTemplate !== this.fieldForm.template && this.customValues[oldTemplate] !== undefined) {
                        this.customValues[this.fieldForm.template] = this.customValues[oldTemplate];
                        delete this.customValues[oldTemplate];
                    }
                    this.fields[this.editingFieldIndex] = { ...this.fieldForm, _key: this.fields[this.editingFieldIndex]._key };
                    this.fields = [...this.fields];
                } else {
                    this.fields.push({ ...this.fieldForm, _key: crypto.randomUUID() });
                    this.$nextTick(() => this.initSortable());
                }

                this.showFieldModal = false;
            },

            promptDeleteField(index) {
                this.deletingFieldIndex = index;
                this.showDeleteModal = true;
            },

            confirmDeleteField() {
                if (this.deletingFieldIndex !== null) {
                    this.removeField(this.deletingFieldIndex);
                }
                this.deletingFieldIndex = null;
                this.showDeleteModal = false;
            },

            removeField(index) {
                const f = this.fields[index];
                if (f && f.template && this.customValues[f.template] !== undefined) {
                    delete this.customValues[f.template];
                }
                this.fields.splice(index, 1);
            },

            initSortable() {
                const el = document.getElementById('sortable-custom-fields');
                if (!el || typeof Sortable === 'undefined') return;
                if (el._sortable) {
                    try { el._sortable.destroy(); } catch (e) {}
                    delete el._sortable;
                }
                el._sortable = new Sortable(el, {
                    animation: 200,
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    filter: 'input, textarea, select, button, a, [role="switch"]',
                    preventOnFilter: false,
                    onEnd: (evt) => {
                        if (evt.oldIndex === undefined || evt.newIndex === undefined || evt.oldIndex === evt.newIndex) return;
                        const item = this.fields.splice(evt.oldIndex, 1)[0];
                        if (item) {
                            this.fields.splice(evt.newIndex, 0, item);
                            this.fields = [...this.fields];
                        }
                    }
                });
            },

            checkForUpdates() {
                this.updateState = 'checking';
                this.updateLogs = [];
                this.updateError = null;

                fetch('{{ route('admin.updates.check') }}?force=1', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    this.currentVersion = data.current_version;
                    this.latestVersion = data.latest_version;

                    if (data.status === 'check_failed') {
                        this.updateState = 'check_failed';
                        this.updateError = data.message || 'Unable to reach the update server.';
                    } else {
                        this.updateState = data.update_available ? 'found' : 'up_to_date';
                    }
                })
                .catch(() => {
                    this.updateState = 'error';
                    this.updateError = 'Failed to reach the update server. Check your internet connection.';
                });
            },

            runUpdate() {
                this.updateState = 'updating';
                this.updateLogs = ['Preparing update process...'];

                fetch('{{ route('admin.updates.run') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                })
                .then(r => r.json().then(data => ({ ok: r.ok, data })))
                .then(({ ok, data }) => {
                    if (!ok || !data.success) throw new Error(data.error || data.message || 'Update failed.');
                    let delay = 200;
                    data.logs.forEach(log => {
                        setTimeout(() => {
                            this.updateLogs.push(log);
                            this.$nextTick(() => {
                                if (this.$refs.logConsole) {
                                    this.$refs.logConsole.scrollTop = this.$refs.logConsole.scrollHeight;
                                }
                            });
                        }, delay += 400);
                    });
                    setTimeout(() => {
                        this.currentVersion = data.version;
                        this.updateState = 'done';
                        this.$nextTick(() => {
                            if (this.$refs.logConsole) {
                                this.$refs.logConsole.scrollTop = this.$refs.logConsole.scrollHeight;
                            }
                        });
                    }, delay + 500);
                })
                .catch(err => {
                    this.updateLogs.push('[ERROR] ' + err.message);
                    this.updateError = err.message;
                    this.updateState = 'error';
                    this.$nextTick(() => {
                        if (this.$refs.logConsole) {
                            this.$refs.logConsole.scrollTop = this.$refs.logConsole.scrollHeight;
                        }
                    });
                });
            }
        };
    }
</script>

<style>
    #sortable-custom-fields .sortable-ghost {
        opacity: 0.35 !important;
        background: #ffffff !important;
    }

    #sortable-custom-fields .sortable-drag {
        opacity: 1 !important;
        background: #ffffff !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        border-radius: 0.5rem !important;
    }
</style>
@endpush
