{{-- AI Models Management Tab Panel --}}
<div x-show="activeTab === 'ai'" x-cloak x-data="aiModelsManager()" x-init="init()">
    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
        {{-- Top Navigation & Filter Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-3 border-b border-content-border text-sm font-medium text-text-heading">
            {{-- Tab Title with Icon --}}
            <div class="flex items-center gap-2 text-sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-text-muted shrink-0">
                    <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                    <path d="M5 3v4"/>
                    <path d="M19 17v4"/>
                    <path d="M3 5h4"/>
                    <path d="M17 19h4"/>
                </svg>
                <span class="text-text-heading font-medium">AI Models & Providers</span>
            </div>

            <div class="flex items-center gap-2 flex-nowrap shrink-0">
                {{-- Search Input --}}
                <div class="relative shrink-0">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-text-muted">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Search models..."
                        aria-label="Search models"
                        class="h-8 w-44 sm:w-56 rounded-lg border border-content-border bg-content-bg pl-8 pr-3 text-[12px] text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/10 shadow-sm"
                    >
                </div>

                {{-- Provider Filter Dropdown with Icons --}}
                <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                    <button type="button"
                        @click="open = !open"
                        class="flex h-8 items-center gap-1.5 whitespace-nowrap rounded-lg border border-content-border bg-white px-3 text-[12px] font-medium text-text-heading hover:bg-body-bg shadow-sm transition-colors cursor-pointer">
                        <template x-if="providerFilter !== 'all'">
                            <img :src="getProviderLogo(providerFilter)" class="size-3.5 object-contain shrink-0" alt="">
                        </template>
                        <template x-if="providerFilter === 'all'">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-text-muted shrink-0">
                                <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                            </svg>
                        </template>
                        <span x-text="providerFilter === 'all' ? 'All Providers' : providerFilter.toUpperCase()" class="whitespace-nowrap">All Providers</span>
                        <svg class="size-3 text-text-muted shrink-0 transition-transform ml-0.5" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak
                        class="absolute right-0 top-full mt-2 min-w-[14rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 space-y-0.5 z-[100]">
                        <button type="button" @click="providerFilter = 'all'; page = 1; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="providerFilter === 'all' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                            <span>All Providers</span>
                            <span x-show="providerFilter === 'all'" class="font-bold">✓</span>
                        </button>
                        <button type="button" @click="providerFilter = 'openai'; page = 1; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="providerFilter === 'openai' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                            <span class="flex items-center gap-2">
                                <img src="/images/ai-providers/openai.svg" class="size-3.5 object-contain" alt="">
                                <span>OpenAI / ChatGPT</span>
                            </span>
                            <span x-show="providerFilter === 'openai'" class="font-bold">✓</span>
                        </button>
                        <button type="button" @click="providerFilter = 'anthropic'; page = 1; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="providerFilter === 'anthropic' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                            <span class="flex items-center gap-2">
                                <img src="/images/ai-providers/claude.svg" class="size-3.5 object-contain" alt="">
                                <span>Anthropic / Claude</span>
                            </span>
                            <span x-show="providerFilter === 'anthropic'" class="font-bold">✓</span>
                        </button>
                        <button type="button" @click="providerFilter = 'deepseek'; page = 1; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="providerFilter === 'deepseek' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                            <span class="flex items-center gap-2">
                                <img src="/images/ai-providers/deepseek.svg" class="size-3.5 object-contain" alt="">
                                <span>DeepSeek</span>
                            </span>
                            <span x-show="providerFilter === 'deepseek'" class="font-bold">✓</span>
                        </button>
                        <button type="button" @click="providerFilter = 'grok'; page = 1; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="providerFilter === 'grok' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                            <span class="flex items-center gap-2">
                                <img src="/images/ai-providers/grok.svg" class="size-3.5 object-contain" alt="">
                                <span>xAI / Grok</span>
                            </span>
                            <span x-show="providerFilter === 'grok'" class="font-bold">✓</span>
                        </button>
                        <button type="button" @click="providerFilter = 'google'; page = 1; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="providerFilter === 'google' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                            <span class="flex items-center gap-2">
                                <img src="/images/ai-providers/google.svg" class="size-3.5 object-contain" alt="">
                                <span>Google Gemini</span>
                            </span>
                            <span x-show="providerFilter === 'google'" class="font-bold">✓</span>
                        </button>
                        <button type="button" @click="providerFilter = 'qwen'; page = 1; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="providerFilter === 'qwen' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                            <span class="flex items-center gap-2">
                                <img src="/images/ai-providers/qwen.svg" class="size-3.5 object-contain" alt="">
                                <span>Qwen (Alibaba)</span>
                            </span>
                            <span x-show="providerFilter === 'qwen'" class="font-bold">✓</span>
                        </button>
                        <button type="button" @click="providerFilter = 'groq'; page = 1; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="providerFilter === 'groq' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                            <span class="flex items-center gap-2">
                                <img src="/images/ai-providers/groq.png" class="size-3.5 object-contain" alt="">
                                <span>Groq</span>
                            </span>
                            <span x-show="providerFilter === 'groq'" class="font-bold">✓</span>
                        </button>
                        <button type="button" @click="providerFilter = 'custom'; page = 1; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="providerFilter === 'custom' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                            <span class="flex items-center gap-2">
                                <img src="/images/ai-providers/custom.png" class="size-3.5 object-contain" alt="">
                                <span>Custom / Ollama</span>
                            </span>
                            <span x-show="providerFilter === 'custom'" class="font-bold">✓</span>
                        </button>
                    </div>
                </div>

                {{-- Add AI Model Button in Toolbar --}}
                <button
                    type="button"
                    @click="openCreateModal()"
                    class="inline-flex items-center justify-center whitespace-nowrap shrink-0 font-medium text-sm px-4 py-2 rounded-lg bg-primary hover:opacity-90 text-white shadow-sm cursor-pointer transition-opacity"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4 mr-2">
                        <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                    </svg>
                    Add AI Model
                </button>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="px-1.5 pb-2 pt-2">
            <template x-if="loading">
                <div class="flex items-center justify-center py-20">
                    <div class="text-sm text-text-muted">Loading AI models...</div>
                </div>
            </template>

            <template x-if="!loading && filteredModels.length === 0">
                <div class="flex flex-col items-center justify-center py-12 text-center px-6">
                    <img src="/empty-collection.svg" alt="No items" class="size-28 mb-3 opacity-60">
                    <p class="text-sm font-medium text-text-heading">No AI models configured yet</p>
                    <p class="text-[12px] text-text-muted mt-1">Add your preferred AI models to enable AI assistance.</p>
                </div>
            </template>

            <template x-if="!loading && filteredModels.length > 0">
                <div class="rounded-xl ring-1 ring-content-border bg-content-bg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto table-scrollbar">
                        <table class="w-full min-w-full border-separate border-spacing-y-0 text-left text-[13px]">
                            <thead>
                                <tr class="bg-[#f9fafb]">
                                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">AI Model</th>
                                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">API Key</th>
                                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border text-center">Default</th>
                                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border sticky right-0 bg-[#f9fafb] z-20 text-right rounded-tr-xl w-24">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="m in paginatedModels" :key="m.id">
                                    <tr class="group hover:bg-[#f9fafb] transition-colors">
                                        {{-- Model Name & Provider (With Official SVG Icon) --}}
                                        <td class="px-4 py-3.5 text-text-primary whitespace-nowrap border-b border-content-border group-last:border-b-0 group-last:rounded-bl-xl">
                                            <div class="flex items-center gap-3.5">
                                                <div class="size-9 rounded-xl flex items-center justify-center shrink-0 border border-content-border bg-white p-1.5 shadow-2xs">
                                                    <img :src="getProviderLogo(m.provider)" class="size-full object-contain pointer-events-none" :alt="m.provider">
                                                </div>
                                                <div>
                                                    <div>
                                                        <button
                                                            type="button"
                                                            @click="openEditModal(m)"
                                                            class="text-text-heading font-semibold cursor-pointer normal-nums select-none hover:text-primary text-start text-[13px]"
                                                            x-text="m.name"
                                                        ></button>
                                                    </div>
                                                    <p class="text-[11px] text-text-muted truncate max-w-sm mt-0.5" x-text="m.description || m.base_url || m.effective_base_url"></p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Compact Gray API Key Badge with Copy Action --}}
                                        <td class="px-4 py-3.5 whitespace-nowrap border-b border-content-border group-last:border-b-0">
                                            <template x-if="m.has_api_key">
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100/90 hover:bg-gray-200/80 border border-gray-200/80 text-gray-700 text-[11px] font-mono transition-colors shadow-2xs group/key select-none">
                                                    <span class="truncate max-w-[120px]" x-text="m.masked_api_key || '••••••••••••'"></span>
                                                    <button
                                                        type="button"
                                                        @click.stop="copyApiKey(m)"
                                                        class="size-4 flex items-center justify-center text-gray-400 hover:text-gray-800 transition-colors cursor-pointer shrink-0"
                                                        :title="copiedModelId === m.id ? 'Copied!' : 'Copy API Key'"
                                                    >
                                                        <template x-if="copiedModelId === m.id">
                                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-emerald-600">
                                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                            </svg>
                                                        </template>
                                                        <template x-if="copiedModelId !== m.id">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
                                                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                                                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                                            </svg>
                                                        </template>
                                                    </button>
                                                </div>
                                            </template>
                                            <template x-if="!m.has_api_key">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium text-gray-400 bg-gray-50 border border-gray-200/60">
                                                    None
                                                </span>
                                            </template>
                                        </td>

                                        {{-- Default Model Badge / Action --}}
                                        <td class="px-4 py-3.5 text-center whitespace-nowrap border-b border-content-border group-last:border-b-0">
                                            <template x-if="m.is_default">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-primary/10 text-primary border border-primary/20">
                                                    Default
                                                </span>
                                            </template>
                                            <template x-if="!m.is_default">
                                                <button
                                                    type="button"
                                                    @click="setDefault(m)"
                                                    class="text-[12px] text-text-muted hover:text-primary transition-colors cursor-pointer hover:underline font-medium"
                                                >
                                                    Set as default
                                                </button>
                                            </template>
                                        </td>

                                        {{-- Actions (3-Dots Action Button) --}}
                                        <td class="sticky right-0 bg-white group-hover:bg-[#f9fafb] group-last:rounded-br-xl z-10 px-4 py-3.5 text-right whitespace-nowrap transition-colors border-b border-content-border group-last:border-b-0">
                                            <button
                                                type="button"
                                                @click="openActionMenu($event, m)"
                                                class="inline-flex size-7 items-center justify-center rounded-md text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors cursor-pointer"
                                                title="Actions"
                                            >
                                                <svg viewBox="0 0 16 3" class="size-4" fill="currentColor" aria-hidden="true">
                                                    <circle cx="2" cy="1.5" r="1.5" />
                                                    <circle cx="8" cy="1.5" r="1.5" />
                                                    <circle cx="14" cy="1.5" r="1.5" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer Pagination Bar --}}
        <footer class="flex justify-between flex-wrap items-center gap-3 px-[18px] py-3 border-t border-content-border rounded-b-2xl antialiased">
            <div class="flex items-center gap-3 flex-wrap">
                <div class="text-xs text-text-muted font-medium">
                    <span x-text="filteredModels.length > 0 ? `${(page - 1) * perPage + 1}–${Math.min(page * perPage, filteredModels.length)} of ${filteredModels.length} models` : '0 models'"></span>
                </div>
            </div>

            <div class="flex items-center gap-4 flex-wrap">
                {{-- Pagination Controls --}}
                <div class="flex items-center gap-1" x-show="filteredModels.length > perPage">
                    <button type="button" @click="if (page > 1) page--" :disabled="page <= 1" class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-text-heading disabled:opacity-40 transition-colors cursor-pointer shadow-2xs">
                        <svg viewBox="0 0 15 15" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M8.842 3.135a.5.5 0 01.023.707L5.435 7.5l3.43 3.658a.5.5 0 01-.73.684l-3.75-4a.5.5 0 010-.684l3.75-4a.5.5 0 01.707-.023" clip-rule="evenodd" /></svg>
                    </button>
                    <span class="inline-flex items-center justify-center px-2.5 h-7 rounded-lg bg-white border border-gray-200 text-text-heading text-xs font-semibold shadow-2xs" x-text="`${page} / ${totalPages}`">1 / 1</span>
                    <button type="button" @click="if (page < totalPages) page++" :disabled="page >= totalPages" class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-text-heading disabled:opacity-40 transition-colors cursor-pointer shadow-2xs">
                        <svg viewBox="0 0 15 15" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M6.158 3.135a.5.5 0 01-.023.707L9.565 7.5l-3.43 3.658a.5.5 0 00.73.684l3.75-4a.5.5 0 000-.684l-3.75-4a.5.5 0 00-.707-.023" clip-rule="evenodd" /></svg>
                    </button>
                </div>

                {{-- Per Page Dropdown --}}
                <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                    <div class="flex items-center gap-2 text-xs text-text-muted font-medium">
                        <span>Per Page</span>
                        <button type="button" @click="open = !open"
                            class="inline-flex items-center justify-between gap-1.5 bg-white border border-content-border text-text-primary text-xs font-semibold rounded-lg px-2.5 py-1 h-7 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-2xs">
                            <span x-text="perPage">10</span>
                            <svg class="size-3 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180 text-primary' : ''" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <div x-show="open" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute bottom-full right-0 mb-1 z-[100] min-w-[5.5rem] rounded-xl border border-gray-200 bg-white shadow-xl p-1 space-y-0.5"
                    >
                        <template x-for="n in [10, 25, 50, 100]" :key="n">
                            <button type="button" @click="perPage = n; page = 1; open = false"
                                class="flex w-full items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-colors cursor-pointer"
                                :class="perPage === n ? 'bg-primary/10 text-primary font-bold' : 'text-text-primary hover:bg-gray-100'"
                            >
                                <span x-text="n"></span>
                                <span x-show="perPage === n" class="font-bold text-primary">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {{-- Action Menu Popup --}}
    <div
        x-show="actionModel"
        x-cloak
        class="fixed inset-0 z-[1000]"
        @click="actionModel = null"
        @keydown.escape.window="actionModel = null"
    >
        <div
            class="absolute min-w-[13rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 space-y-0.5"
            :style="`left: ${actionMenuX}px; top: ${actionMenuY}px;`"
            @click.stop
        >
            <button type="button" role="menuitem" @click="testRowConnection(actionModel); actionModel = null"
                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Test Connection</span>
            </button>
            <button type="button" role="menuitem" @click="openEditModal(actionModel); actionModel = null"
                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" /><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" /></svg>
                <span>Edit Model</span>
            </button>
            <button type="button" role="menuitem" @click="setDefault(actionModel); actionModel = null"
                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Set as Default</span>
            </button>
            <template x-if="actionModel?.has_api_key">
                <button type="button" role="menuitem" @click="copyApiKey(actionModel); actionModel = null"
                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                        <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                        <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                    </svg>
                    <span>Copy API Key</span>
                </button>
            </template>
            <hr class="my-1 border-content-border">
            <button type="button" role="menuitem"
                @click="confirmDelete(actionModel); actionModel = null"
                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-red-500"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
                <span>Delete Model</span>
            </button>
        </div>
    </div>

    {{-- Include Modals --}}
    @include('admin.settings.partials.ai._model_modal')
    @include('admin.settings.partials.ai._delete_modal')
</div>
