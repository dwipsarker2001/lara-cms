{{-- General Settings Tab Panel --}}
<div x-show="activeTab === 'general'">
    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
        <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">General Settings</div>
        <p class="px-[18px] pb-3 text-xs text-text-muted">Configure default preferences for your application.</p>
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
                                <input type="hidden" name="currency" :value="selected">
                            </div>
                            @error('currency') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                {{-- Dynamic Custom Inputs in General Settings --}}
                <div x-show="fields.length > 0" id="sortable-custom-fields">
                    <template x-for="(field, index) in fields" :key="field._key || field.template || index">
                        <div data-custom-field-row class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 group/row bg-white hover:bg-gray-50/60 transition-colors cursor-grab active:cursor-grabbing select-none"
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
