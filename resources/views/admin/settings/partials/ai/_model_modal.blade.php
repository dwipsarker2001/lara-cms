{{-- Modal for Add / Edit AI Model --}}
<div
    x-show="showModal"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    @keydown.escape.window="closeModal()"
>
    {{-- Backdrop --}}
    <div
        x-show="showModal"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50"
        @click="closeModal()"
    ></div>

    {{-- Dialog Box --}}
    <div
        x-show="showModal"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-lg bg-white rounded-2xl border border-gray-200/90 shadow-2xl z-10 font-sans"
    >
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-gray-50 border border-gray-200/90 flex items-center justify-center p-2 shadow-2xs shrink-0">
                    <img :src="getProviderLogo(form.provider)" class="size-full object-contain pointer-events-none" :alt="form.provider">
                </div>
                <div>
                    <h3 class="text-base font-semibold text-text-heading leading-tight" x-text="isEditing ? 'Edit AI Model' : 'Add AI Model'"></h3>
                    <p class="text-xs text-text-muted mt-0.5">Configure model provider, credentials, and endpoint.</p>
                </div>
            </div>
            <button
                type="button"
                @click="closeModal()"
                class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-text-muted hover:text-text-heading transition-colors cursor-pointer"
                title="Close"
            >
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                    <path d="M4 4l8 8M12 4l-8 8" />
                </svg>
            </button>
        </div>

        {{-- Modal Form --}}
        <div @keydown.enter.prevent="saveModel()" class="rounded-b-2xl">
            <div class="px-6 py-4 space-y-3.5">
                {{-- Modern Provider Custom Dropdown --}}
                <div class="relative" @click.outside="providerDropdownOpen = false" @keydown.escape.window="providerDropdownOpen = false">
                    <label class="block text-xs font-semibold text-text-heading mb-1.5">AI Provider</label>
                    <button
                        type="button"
                        @click="providerDropdownOpen = !providerDropdownOpen"
                        class="w-full h-10 rounded-xl border border-gray-300 hover:border-gray-400 bg-white px-3 flex items-center justify-between text-xs text-text-heading focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-2xs cursor-pointer select-none"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="size-6 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center p-1 shadow-2xs shrink-0">
                                <img :src="getProviderLogo(form.provider)" class="size-full object-contain pointer-events-none" :alt="form.provider">
                            </div>
                            <span class="font-semibold text-xs truncate text-text-heading" x-text="currentProviderObj?.name || form.provider"></span>
                            <span class="text-[11px] text-text-muted hidden sm:inline truncate" x-text="`— ${currentProviderObj?.tagline || ''}`"></span>
                        </div>
                        <svg class="size-4 text-text-muted transition-transform duration-150 shrink-0 ml-2" :class="providerDropdownOpen ? 'rotate-180 text-primary' : ''" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    {{-- Provider Popover Menu --}}
                    <div
                        x-show="providerDropdownOpen"
                        x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                        class="absolute z-50 top-full mt-1.5 left-0 w-full rounded-xl border border-gray-200 bg-white shadow-2xl p-1 space-y-0.5"
                    >
                        <template x-for="p in providersList" :key="p.id">
                            <button
                                type="button"
                                @click="selectProvider(p.id)"
                                class="w-full flex items-center justify-between gap-2.5 px-2.5 py-1.5 rounded-lg text-left transition-colors cursor-pointer select-none"
                                :class="form.provider === p.id ? 'bg-primary/10 text-primary font-semibold' : 'hover:bg-gray-50 text-text-heading'"
                            >
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="size-6 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center p-1 shadow-2xs shrink-0">
                                        <img :src="getProviderLogo(p.id)" class="size-full object-contain pointer-events-none" :alt="p.name">
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-semibold leading-tight truncate" x-text="p.name"></div>
                                        <div class="text-[11px] text-text-muted mt-0.5 truncate" x-text="p.tagline"></div>
                                    </div>
                                </div>
                                <template x-if="form.provider === p.id">
                                    <span class="size-4 rounded-full bg-primary text-white flex items-center justify-center text-[10px] shrink-0 font-bold shadow-2xs">✓</span>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- PRESET PROVIDER MODE: Only Model Selection --}}
                <div x-show="form.provider !== 'custom'">
                    {{-- Model Selector Dropdown --}}
                    <div class="relative" @click.outside="modelPresetDropdownOpen = false" @keydown.escape.window="modelPresetDropdownOpen = false">
                        <label class="block text-xs font-semibold text-text-heading mb-1.5">Model</label>
                        <button
                            type="button"
                            @click="modelPresetDropdownOpen = !modelPresetDropdownOpen"
                            class="w-full h-10 rounded-xl border border-gray-300 hover:border-gray-400 bg-white px-3 flex items-center justify-between text-xs text-text-heading focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-2xs cursor-pointer select-none"
                        >
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="font-semibold text-xs truncate text-text-heading" x-text="form.name || 'Select a Model'"></span>
                                <span class="text-[11px] text-text-muted font-mono" x-show="form.model_id" x-text="`(${form.model_id})`"></span>
                            </div>
                            <svg class="size-4 text-text-muted transition-transform duration-150 shrink-0 ml-2" :class="modelPresetDropdownOpen ? 'rotate-180 text-primary' : ''" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        {{-- Model Presets Popover Menu --}}
                        <div
                            x-show="modelPresetDropdownOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                            class="absolute z-40 top-full mt-1.5 left-0 w-full rounded-xl border border-gray-200 bg-white shadow-2xl p-1 space-y-0.5"
                        >
                            <template x-for="item in currentProviderModels" :key="item.model_id">
                                <button
                                    type="button"
                                    @click="applyModelPreset(item); modelPresetDropdownOpen = false"
                                    class="w-full flex items-center justify-between gap-2.5 px-2.5 py-1.5 rounded-lg text-left transition-colors cursor-pointer select-none"
                                    :class="form.model_id === item.model_id ? 'bg-primary/10 text-primary font-semibold' : 'hover:bg-gray-50 text-text-heading'"
                                >
                                    <div class="min-w-0">
                                        <div class="text-xs font-semibold leading-tight truncate" x-text="item.name"></div>
                                        <div class="text-[11px] text-text-muted mt-0.5 truncate" x-text="item.description || item.model_id"></div>
                                    </div>
                                    <template x-if="form.model_id === item.model_id">
                                        <span class="size-4 rounded-full bg-primary text-white flex items-center justify-center text-[10px] shrink-0 font-bold shadow-2xs">✓</span>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- CUSTOM PROVIDER MODE: Display Name, Model ID, Base URL, Description --}}
                <div x-show="form.provider === 'custom'" class="space-y-3.5">
                    {{-- Display Name & Model ID --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-text-heading mb-1.5">Display Name</label>
                            <input
                                type="text"
                                x-model="form.name"
                                placeholder="e.g. Local LLaMA 3.2"
                                class="w-full h-10 rounded-xl border border-gray-300 bg-white px-3.5 text-xs text-text-heading placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-2xs"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-heading mb-1.5 font-mono">Model ID</label>
                            <input
                                type="text"
                                x-model="form.model_id"
                                placeholder="e.g. llama3.2"
                                class="w-full h-10 rounded-xl border border-gray-300 bg-white px-3.5 text-xs font-mono text-text-heading placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-2xs"
                            >
                        </div>
                    </div>

                    {{-- Base URL --}}
                    <div>
                        <label class="block text-xs font-semibold text-text-heading mb-1.5">API Base URL</label>
                        <input
                            type="text"
                            x-model="form.base_url"
                            placeholder="https://tabitoken.com/v1, http://localhost:11434/v1, or any custom URL"
                            class="w-full h-10 rounded-xl border border-gray-300 bg-white px-3.5 text-xs font-mono text-text-heading placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-2xs"
                        >
                        <p class="text-[11px] text-text-muted mt-1">Supports domain roots, <code>/v1</code> prefixes, or direct endpoints.</p>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-semibold text-text-heading mb-1.5">Description (Optional)</label>
                        <input
                            type="text"
                            x-model="form.description"
                            placeholder="e.g. Local self-hosted model"
                            class="w-full h-10 rounded-xl border border-gray-300 bg-white px-3.5 text-xs text-text-heading placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-2xs"
                        >
                    </div>
                </div>

                {{-- API Key --}}
                <div>
                    <label class="block text-xs font-semibold text-text-heading mb-1.5">API Key</label>
                    <div class="relative">
                        <input
                            :type="showApiKey ? 'text' : 'password'"
                            x-model="form.api_key"
                            :placeholder="isEditing && form.has_api_key ? '•••••••••••••••• (Leave blank to keep unchanged)' : (form.provider === 'custom' ? 'Optional for local Ollama / sk-...' : 'sk-...')"
                            class="w-full h-10 rounded-xl border border-gray-300 bg-white pl-3.5 pr-10 text-xs font-mono text-text-heading placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-2xs"
                        >
                        <button
                            type="button"
                            @click="showApiKey = !showApiKey"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 size-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer"
                            tabindex="-1"
                        >
                            <svg x-show="!showApiKey" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg x-show="showApiKey" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                <line x1="2" x2="22" y1="2" y2="22"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Toggles --}}
                <div class="pt-2">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" x-model="form.is_default" class="size-4 rounded border-gray-300 text-primary focus:ring-primary">
                        <div>
                            <span class="font-medium text-text-heading text-xs">Set as Default AI Model</span>
                            <p class="text-[11px] text-text-muted">Will be selected automatically in AI Copilot</p>
                        </div>
                    </label>
                </div>

                {{-- Test Connection Result Message --}}
                <div x-show="modalTestMessage" x-cloak class="p-3 rounded-xl text-xs" :class="modalTestSuccess ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200'">
                    <div class="flex items-center gap-1.5 font-semibold">
                        <span x-text="modalTestSuccess ? '✓ Connection Verified' : (modalErrorTitle || '✕ Error')"></span>
                    </div>
                    <p class="mt-0.5 text-[11px] opacity-90" x-text="modalTestMessage"></p>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl">
                <button
                    type="button"
                    @click="testModalConnection()"
                    :disabled="isTestingModal"
                    class="h-10 px-4 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer inline-flex items-center justify-center gap-1.5 shadow-2xs"
                >
                    <svg x-show="!isTestingModal" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 text-emerald-600">
                        <path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <svg x-show="isTestingModal" x-cloak class="animate-spin size-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isTestingModal ? 'Testing...' : 'Test Connection'"></span>
                </button>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="closeModal()"
                        class="h-10 px-4 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer text-center"
                    >Cancel</button>

                    <button
                        type="button"
                        @click="saveModel()"
                        :disabled="isSaving"
                        class="h-10 px-5 rounded-xl bg-primary text-xs font-semibold text-white hover:opacity-90 active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed shadow-sm shadow-primary/20 transition-all cursor-pointer text-center inline-flex items-center justify-center gap-1.5"
                    >
                        <svg x-show="isSaving" x-cloak class="animate-spin size-3.5 text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSaving ? 'Saving...' : 'Save Model'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
