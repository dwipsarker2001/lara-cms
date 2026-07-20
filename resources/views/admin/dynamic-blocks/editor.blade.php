@extends('admin.layout')

@section('title', 'Edit Block — '.$block->label)
@section('breadcrumb', 'Edit Block')

@section('content-full')
<script>
    window.aiSystemPromptTemplate = @json(file_get_contents(resource_path('prompts/block-generator.txt')));

    function blockGenerator() {
            return {
                tab: 'generate',
                prompt: '',
                generating: false,
                logs: '',
                provider: localStorage.getItem('lara_cms_ai_provider') || 'opencode',
                selectedCustomProviderId: localStorage.getItem('lara_cms_ai_provider_id') || '',
                selectedCustomModelId: localStorage.getItem('lara_cms_ai_model_id') || '',
                
                // Modals
                showConnectModal: false,
                showApiKeyModal: false,
                showCustomFormModal: false,
                showAddCustomModal: false,
                showCustomForm: true, // Show form fields inside custom modal by default

                // Active connection setup state
                connectingProvider: '',
                tempApiKey: '',

                // Configurations
                geminiKey: localStorage.getItem('lara_cms_gemini_key') || '',
                openrouterKey: localStorage.getItem('lara_cms_openrouter_key') || '',
                zaiKey: localStorage.getItem('lara_cms_zai_key') || '',
                customProviders: JSON.parse(localStorage.getItem('lara_cms_custom_providers') || '[]'),

                // Sub-form temp state
                newProvider: { id: '', name: '', baseUrl: '', apiKey: '', models: [] },
                newModel: { id: '', name: '' },

                // Attachment wireframe state
                attachmentName: null,
                attachmentBase64: null,
                attachmentMime: null,

                providerLabel() {
                    if (this.provider === 'opencode') return 'block-cms/gemini-2.0-flash';
                    if (this.provider === 'gemini') return 'gemini/gemini-2.0-flash';
                    if (this.provider === 'openrouter') return 'OpenRouter (' + (this.selectedCustomModelId ? this.selectedCustomModelId.split('/').pop() : 'Claude 3.5 Sonnet') + ')';
                    if (this.provider === 'zai') return 'z.ai (GLM 5.2 Free)';
                    
                    const p = this.customProviders.find(cp => cp.id === this.selectedCustomProviderId);
                    if (p) {
                        const m = p.models.find(cm => cm.id === this.selectedCustomModelId);
                        return p.name + ' (' + (m ? m.name : 'Default') + ')';
                    }
                    return 'custom/api-provider';
                },

                selectProviderModel(providerType, providerId, modelId) {
                    this.provider = providerType;
                    this.selectedCustomProviderId = providerId || '';
                    this.selectedCustomModelId = modelId || '';
                    localStorage.setItem('lara_cms_ai_provider', providerType);
                    localStorage.setItem('lara_cms_ai_provider_id', this.selectedCustomProviderId);
                    localStorage.setItem('lara_cms_ai_model_id', this.selectedCustomModelId);
                },

                startConnectPredefined(providerName) {
                    this.connectingProvider = providerName;
                    if (providerName === 'gemini') this.tempApiKey = this.geminiKey;
                    else if (providerName === 'openrouter') this.tempApiKey = this.openrouterKey;
                    else if (providerName === 'zai') this.tempApiKey = this.zaiKey;

                    this.showConnectModal = false;
                    this.showApiKeyModal = true;
                },

                saveApiKey() {
                    const val = this.tempApiKey.trim();
                    if (this.connectingProvider === 'gemini') {
                        this.geminiKey = val;
                        localStorage.setItem('lara_cms_gemini_key', val);
                        this.selectProviderModel('gemini', null, 'gemini-2.0-flash');
                    } else if (this.connectingProvider === 'openrouter') {
                        this.openrouterKey = val;
                        localStorage.setItem('lara_cms_openrouter_key', val);
                        this.selectProviderModel('openrouter', null, 'anthropic/claude-3.5-sonnet');
                    } else if (this.connectingProvider === 'zai') {
                        this.zaiKey = val;
                        localStorage.setItem('lara_cms_zai_key', val);
                        this.selectProviderModel('zai', null, 'z-ai/glm-5.2-free');
                    }

                    this.showApiKeyModal = false;
                    window.showToast?.('✓ Connected successfully.', 'success');
                },

                disconnectProvider(providerName) {
                    if (providerName === 'gemini') {
                        this.geminiKey = '';
                        localStorage.removeItem('lara_cms_gemini_key');
                        if (this.provider === 'gemini') this.selectProviderModel('opencode', null, null);
                    } else if (providerName === 'openrouter') {
                        this.openrouterKey = '';
                        localStorage.removeItem('lara_cms_openrouter_key');
                        if (this.provider === 'openrouter') this.selectProviderModel('opencode', null, null);
                    } else if (providerName === 'zai') {
                        this.zaiKey = '';
                        localStorage.removeItem('lara_cms_zai_key');
                        if (this.provider === 'zai') this.selectProviderModel('opencode', null, null);
                    }
                    window.showToast?.('✓ Disconnected ' + providerName, 'success');
                },

                addModelToNewProvider() {
                    if (!this.newModel.id || !this.newModel.name) {
                        window.showToast?.('Please specify Model ID and Name.', 'danger');
                        return;
                    }
                    this.newProvider.models.push({
                        id: this.newModel.id.trim(),
                        name: this.newModel.name.trim()
                    });
                    this.newModel = { id: '', name: '' };
                },

                removeModelFromNewProvider(index) {
                    this.newProvider.models.splice(index, 1);
                },

                addCustomProvider() {
                    if (!this.newProvider.id || !this.newProvider.name || !this.newProvider.baseUrl) {
                        window.showToast?.('Please fill in Provider ID, Name, and Base URL.', 'danger');
                        return;
                    }
                    
                    const idPattern = /^[a-z0-9-_]+$/;
                    if (!idPattern.test(this.newProvider.id)) {
                        window.showToast?.('Provider ID must contain only lowercase letters, hyphens, or underscores.', 'danger');
                        return;
                    }

                    if (this.customProviders.some(cp => cp.id === this.newProvider.id)) {
                        window.showToast?.('Provider ID must be unique.', 'danger');
                        return;
                    }

                    const savedProvider = {
                        id: this.newProvider.id.trim(),
                        name: this.newProvider.name.trim(),
                        baseUrl: this.newProvider.baseUrl.trim(),
                        apiKey: this.newProvider.apiKey.trim(),
                        models: [...this.newProvider.models]
                    };

                    this.customProviders.push(savedProvider);
                    this.newProvider = { id: '', name: '', baseUrl: '', apiKey: '', models: [] };
                    this.newModel = { id: '', name: '' };
                    
                    localStorage.setItem('lara_cms_custom_providers', JSON.stringify(this.customProviders));
                    
                    // Automatically select this new custom provider
                    const defaultModelId = savedProvider.models.length > 0 ? savedProvider.models[0].id : null;
                    this.selectProviderModel('custom', savedProvider.id, defaultModelId);

                    this.showCustomFormModal = false;
                    window.showToast?.('✓ Custom provider saved to list.', 'success');
                },

                removeCustomProvider(index) {
                    const p = this.customProviders[index];
                    this.customProviders.splice(index, 1);
                    localStorage.setItem('lara_cms_custom_providers', JSON.stringify(this.customProviders));
                    
                    if (this.provider === 'custom' && this.selectedCustomProviderId === p.id) {
                        this.selectProviderModel('opencode', null, null);
                    }
                    window.showToast?.('✓ Custom provider removed.', 'success');
                },

                handleFileSelect(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    this.attachmentName = file.name;
                    this.attachmentMime = file.type;

                    const reader = new FileReader();
                    reader.onload = (event) => {
                        this.attachmentBase64 = event.target.result.split(',')[1];
                    };
                    reader.readAsDataURL(file);
                },

                removeAttachment() {
                    this.attachmentName = null;
                    this.attachmentBase64 = null;
                    this.attachmentMime = null;
                    document.getElementById('ai-file-input').value = '';
                },

                async runAiGeneration() {
                    if (this.generating) return;
                    this.generating = true;
                    this.logs = 'Connecting to AI provider...';

                    try {
                        let fields = [];
                        let template = '';

                        if (this.provider === 'opencode') {
                            this.logs = 'OpenCode is generating templates...';
                            await new Promise(resolve => setTimeout(resolve, 1500));

                            const promptLower = this.prompt.toLowerCase();
                            if (promptLower.includes('pricing') || promptLower.includes('card')) {
                                fields = [
                                    { name: 'title', label: 'Title', type: 'string', defaultValue: 'Starter Plan' },
                                    { name: 'price', label: 'Price', type: 'string', defaultValue: '$29/mo' },
                                    { name: 'features', label: 'Features (comma separated)', type: 'string', defaultValue: '10 Projects, 5GB Storage, 24/7 Support' }
                                ];
                                template = `<div class="p-6 max-w-sm mx-auto bg-white rounded-xl shadow-md border border-gray-100 text-center">
  <h3 class="text-lg font-bold text-gray-900">@{{ title }}</h3>
  <div class="mt-4 text-3xl font-extrabold text-emerald-600">@{{ price }}</div>
  <ul class="mt-4 text-sm text-gray-500 space-y-2">
    <li>@{{ features }}</li>
  </ul>
  <button class="mt-6 w-full bg-emerald-500 text-white rounded-lg py-2 font-semibold hover:bg-emerald-600">Get Started</button>
</div>`;
                            } else if (promptLower.includes('hero') || promptLower.includes('banner')) {
                                fields = [
                                    { name: 'heading', label: 'Heading', type: 'string', defaultValue: 'Discover the World' },
                                    { name: 'subheading', label: 'Subheading', type: 'string', defaultValue: 'Explore tailored packages for your next journey.' },
                                    { name: 'cta_text', label: 'CTA Text', type: 'string', defaultValue: 'Book Now' }
                                ];
                                template = `<div class="relative py-24 px-6 text-center bg-gray-900 text-white rounded-2xl overflow-hidden shadow-lg">
  <div class="relative z-10 max-w-2xl mx-auto">
    <h1 class="text-4xl font-extrabold tracking-tight">@{{ heading }}</h1>
    <p class="mt-4 text-lg text-gray-300">@{{ subheading }}</p>
    <a href="#" class="mt-8 inline-block bg-emerald-500 px-8 py-3 rounded-lg font-medium text-white hover:opacity-90 transition-opacity">@{{ cta_text }}</a>
  </div>
</div>`;
                            } else if (promptLower.includes('faq') || promptLower.includes('accordion')) {
                                fields = [
                                    { name: 'question', label: 'Question', type: 'string', defaultValue: 'What is included in the tour?' },
                                    { name: 'answer', label: 'Answer', type: 'string', defaultValue: 'All transfers, accommodation, and entry fees are fully covered.' }
                                ];
                                template = `<div class="max-w-xl mx-auto p-5 bg-white border border-content-border rounded-xl">
  <details class="group">
    <summary class="flex justify-between items-center font-medium cursor-pointer list-none">
      <span class="text-text-heading">@{{ question }}</span>
      <span class="transition group-open:rotate-180">
        <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
      </span>
    </summary>
    <p class="text-text-muted mt-3 group-open:animate-fadeIn">@{{ answer }}</p>
  </details>
</div>`;
                            } else {
                                fields = [
                                                    <template x-if="!att.preview">
                                                        <div class="flex flex-col items-center justify-center p-0.5 text-center">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4 opacity-75">
                                                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                                            </svg>
                                                            <span class="text-[7px] truncate max-w-[40px] text-text-muted" x-text="att.name"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Text --}}
                                    <div class="text-xs leading-relaxed whitespace-pre-wrap" x-text="msg.text"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Fixed Bottom Input Area --}}
                    <div class="shrink-0 border-t border-gray-150 bg-white p-3 flex flex-col">
                        {{-- Input container panel --}}
                        <div class="w-full bg-white rounded-2xl border border-gray-200 shadow-sm p-3 flex flex-col">
                            
                                    <template x-for="(att, idx) in attachments" :key="idx">
                                        <div class="relative">
                                            <template x-if="att.preview">
                                                <div class="relative size-12">
                                                    <img :src="att.preview" class="size-12 rounded-lg object-cover border border-gray-200">
                                                    <button type="button" @click="removeAttachment(idx)" class="absolute top-0.5 right-0.5 size-3.5 rounded-full bg-gray-900 text-white flex items-center justify-center hover:bg-gray-700 shadow-sm">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="size-2">
                                                            <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <template x-if="!att.preview">
                                                <div class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-200 px-2 py-0.5 rounded-md text-[10px] text-text-primary h-12">
                                                    <span class="max-w-[80px] truncate font-medium" x-text="att.name"></span>
                                                    <button type="button" @click="removeAttachment(idx)" class="text-text-muted hover:text-danger">×</button>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Textarea prompt input --}}
                            <textarea
                                id="chat-prompt-input"
                                x-model="prompt"
                                rows="2"
                                @keydown.enter.prevent="if (prompt.trim() && !generating) runAiGeneration()"
                                class="w-full bg-transparent border-0 text-text-primary placeholder:text-text-muted text-xs resize-none focus:ring-0 focus:outline-none"
                                placeholder="Ask AI builder to generate..."
                            ></textarea>

                            {{-- Control Bar --}}
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Text --}}
                                    <div class="text-xs leading-relaxed whitespace-pre-wrap" x-text="msg.text"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Fixed Bottom Input Area --}}
                    <div class="shrink-0 border-t border-gray-150 bg-white p-3 flex flex-col">
                        {{-- Input container panel --}}
                                <div class="flex items-center gap-1.5">
                                    {{-- Model Select button --}}
                                    <div class="relative" x-data="{ openModelMenu: false }">
                                        <button
                                            type="button"
                                            @click="openModelMenu = !openModelMenu"
                                            class="flex h-7 items-center gap-1 px-2 rounded-lg border border-gray-200 bg-gray-50 text-[10px] font-semibold text-text-primary cursor-pointer"
                                        >
                                            <span class="max-w-[70px] truncate" x-text="currentModelName()"></span>
                                            <svg class="size-2.5 text-text-muted" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                        <div x-show="openModelMenu" @click.outside="openModelMenu = false" style="display: none;" class="absolute right-0 bottom-full mb-1 z-50 min-w-[180px] bg-content-bg border border-content-border rounded-xl shadow-lg p-1 max-h-48 overflow-y-auto">
                                            {{-- Default connected models --}}
                                            <template x-if="geminiKey">
                                                <button type="button" @click="selectProviderModel('gemini', null, 'gemini-2.0-flash'); openModelMenu = false;" class="w-full text-left px-2 py-1.5 text-[10px] rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-1.5" :class="provider === 'gemini' ? 'font-bold text-primary' : ''">
                                                    <span class="size-1.5 rounded-full bg-blue-400"></span>
                                                    <span>Gemini 2.0 Flash</span>
                                                </button>
                                            </template>
                                            <button type="button" @click="selectProviderModel('openrouter', null, 'anthropic/claude-3.5-sonnet'); openModelMenu = false;" class="w-full text-left px-2 py-1.5 text-[10px] rounded-lg flex items-center gap-1.5" :class="openrouterKey ? 'text-text-primary hover:bg-body-bg font-medium' : 'text-text-muted opacity-50'">
                                                <span class="size-1.5 rounded-full bg-orange-400" :class="openrouterKey ? '' : 'opacity-30'"></span>
                                                <span>Claude 3.5 Sonnet</span>
                                            </button>
                                            <template x-if="zaiKey">
                                                <button type="button" @click="selectProviderModel('zai', null, 'z-ai/glm-5.2-free'); openModelMenu = false;" class="w-full text-left px-2 py-1.5 text-[10px] rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-1.5" :class="provider === 'zai' ? 'font-bold text-primary' : ''">
                                                    <span class="size-1.5 rounded-full bg-indigo-400"></span>
                                                    <span>GLM 5.2 Free</span>
                                                </button>
                                            </template>

                                            {{-- Pinned --}}
                                            <template x-if="pinnedModels.length > 0">
                                                <div class="border-t border-content-border mt-1 pt-1 space-y-0.5">
                                                    <template x-for="pm in pinnedModels" :key="pm.id">
                                                        <button type="button" @click="selectProviderModel(pm.provider, pm.provider === 'openrouter' || pm.provider === 'gemini' || pm.provider === 'zai' ? null : pm.provider, pm.id); openModelMenu = false;" class="w-full text-left px-2 py-1 text-[10px] rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-1.5" :class="selectedCustomModelId === pm.id ? 'bg-gray-150 font-bold text-primary' : ''">
                                                            <span class="size-1.5 rounded-full" :class="pm.provider === 'openrouter' ? 'bg-orange-400' : (pm.provider === 'gemini' ? 'bg-blue-400' : 'bg-purple-400')"></span>
                                                            <span x-text="pm.name"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </template>

                                responseText = resJson.candidates?.[0]?.content?.parts?.[0]?.text || '';
                            } else {
                                responseText = resJson.choices?.[0]?.message?.content || '';
                            }

                            responseText = responseText.replace(/```json/gi, '').replace(/```/g, '').trim();

                            const parsed = JSON.parse(responseText);
                            if (!parsed.fields || !parsed.template) {
                                throw new Error('AI returned an invalid JSON schema.');
                            }
                            fields = parsed.fields;
                            template = parsed.template;
                        }

                        const fieldsStr = JSON.stringify(fields, null, 2);
                        window.updateEditorContent('field-fields', fieldsStr);
                        window.updateEditorContent('field-template', template);

                        window.showToast?.('✓ Block generated successfully!', 'success');

                        this.tab = 'preview';
                        this.$nextTick(() => window.renderPreview());

                        this.prompt = '';
                        this.removeAttachment();
                    } catch (err) {
                        this.logs = '[ERROR] ' + err.message;
                        window.showToast?.('✗ Generation failed: ' + err.message, 'danger');
                    } finally {
                        this.generating = false;
                    }
                }
            };
        }

    window.blockGenerator = blockGenerator;

    if (window.Alpine) {
        window.Alpine.data('blockGenerator', blockGenerator);
    } else {
        document.addEventListener('alpine:init', function () {
            window.Alpine.data('blockGenerator', blockGenerator);
        });
    }
</script>
<div class="bg-content-bg h-[calc(100%-8px)] mx-2 overflow-hidden mt-2 rounded-t-2xl border border-content-border border-b-0 relative flex flex-col" style="container-type: inline-size;"
     x-data="blockGenerator()"
>
    {{-- Header bar --}}
    <div class="shrink-0 flex items-center justify-between pl-0 border-b border-content-border bg-white">
        {{-- Tab strip --}}
        <div class="flex items-end">
            <a href="{{ route('admin.dynamic-blocks.index') }}" class="flex items-center justify-center size-9 -mb-px text-text-muted hover:text-text-heading transition-colors border-r border-content-border">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <button type="button" @click="tab = 'generate'"
                :class="tab === 'generate' ? 'bg-gray-100 text-text-heading border-content-border border-t-0' : 'bg-transparent text-text-muted border-transparent hover:bg-gray-50 hover:text-text-heading'"
                class="flex items-center gap-2 px-6 h-10 text-xs font-medium border border-r-0 -mb-px -ml-px transition-colors hover:border-b-gray-300"
            >
                <span class="flex items-center justify-center size-5 rounded bg-emerald-50 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3 text-emerald-500">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </span>
                Generate
            </button>
            <button type="button" @click="tab = 'fields'"
                :class="tab === 'fields' ? 'bg-gray-100 text-text-heading border-content-border border-t-0' : 'bg-transparent text-text-muted border-transparent hover:bg-gray-50 hover:text-text-heading'"
                class="flex items-center gap-2 px-6 h-10 text-xs font-medium border -mb-px -ml-px transition-colors hover:border-b-gray-300"
            >
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Status log --}}
                        <template x-if="generating">
                            <div class="mt-1.5 flex items-center justify-center gap-1.5 text-[10px] text-emerald-600 font-medium animate-pulse">
                                <svg class="animate-spin size-3 text-emerald-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="logs"></span>
                            </div>
                        </template>
                    </div>

    </div>

    {{-- Connect Provider Modal (Shows popular list) --}}
    <div x-show="showConnectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @keydown.escape.window="showConnectModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden flex flex-col" @click.outside="showConnectModal = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-content-border">
                <h3 class="text-base font-semibold text-text-heading">Connect AI Provider</h3>
                <button @click="showConnectModal = false" class="p-1 text-text-muted hover:text-text-primary transition-colors">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 1 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 1-1.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
            
            <button type="button" onclick="window.formatDocument()"
                class="flex items-center gap-2 px-5 h-10 text-xs font-medium border -mb-px transition-colors text-text-muted hover:text-text-heading hover:bg-gray-50 border-transparent hover:border-b-gray-300"
            >Format</button>
            <button type="submit" form="editor-form"
                class="flex items-center gap-2 px-5 h-10 text-xs font-medium border -mb-px transition-colors text-white bg-emerald-600 hover:bg-emerald-700 border-emerald-600 hover:border-emerald-700"
            >Save Document</button>
        </div>
    </div>

    {{-- Editor area --}}
    <div class="flex-1 min-h-0 bg-content-bg relative">
        <form id="editor-form" method="POST" action="{{ route('admin.dynamic-blocks.update-editor', $block) }}" class="h-full">
            @csrf @method('PUT')

            {{-- Dynamic AI Generator UI Tab --}}
            <div x-show="tab === 'generate'" x-cloak class="absolute inset-0 overflow-y-auto bg-white flex flex-col justify-center items-center px-4">
                
                {{-- Central Container --}}
                <div class="relative z-10 w-full max-w-2xl flex flex-col items-center">
                    
                    {{-- Input container panel --}}
                    <div class="w-full bg-white rounded-3xl border border-gray-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-shadow p-4 flex flex-col gap-2">
                        
                        {{-- Text input field --}}
                        <textarea
                            x-model="prompt"
                            rows="2"
                            @keydown.enter.prevent="if (prompt.trim() && !generating) runAiGeneration()"
                            class="w-full bg-transparent border-0 text-text-primary placeholder:text-text-muted text-sm resize-none focus:ring-0 focus:outline-none [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                            placeholder="Ask anything, / for commands, @ for context..."
                        ></textarea>

                        {{-- Attachment file indicator --}}
                        <template x-if="attachmentName">
                            <div class="inline-flex items-center gap-2 self-start bg-gray-50 border border-gray-200 px-3 py-1 rounded-lg text-xs text-text-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 text-text-muted">
                                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                </svg>
                                <span class="max-w-[200px] truncate font-medium" x-text="attachmentName"></span>
                                <button type="button" @click="removeAttachment()" class="text-text-muted hover:text-danger ml-1">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                        </template>

                        {{-- Loading / generating status text --}}
                        <template x-if="generating">
                            <div class="flex items-center gap-2 text-xs text-emerald-600 font-medium">
                                <svg class="animate-spin size-4 text-emerald-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="logs"></span>
                            </div>
                        </template>

                        {{-- Control Bar --}}
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3 mt-1">
                            
                            <div class="flex items-center gap-2">
                                {{-- Plus/Attach Button --}}
                                <button
                                    type="button"
                                    @click="document.getElementById('ai-file-input').click()"
                                    class="size-8 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 text-text-muted hover:text-text-heading flex items-center justify-center transition-colors cursor-pointer"
                                    title="Upload wireframe / screenshot"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4">
                                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                </button>
                                <input id="ai-file-input" type="file" accept="image/*" class="hidden" @change="handleFileSelect($event)">

                                {{-- Provider Selector Dropdown --}}
                                <div class="relative" x-data="{ openProvider: false }">
                                    <button
                                        type="button"
                                        @click="openProvider = !openProvider"
                                        class="flex h-8 items-center gap-1.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 px-3 text-xs text-text-primary font-medium transition-colors cursor-pointer"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 text-emerald-500 shrink-0">
                                            <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                                            <polyline points="2 17 12 22 22 17"/>
                                            <polyline points="2 12 12 17 22 12"/>
                                        </svg>
                                        <span class="max-w-[240px] truncate" x-text="providerLabel()"></span>
                                        <svg class="size-3 text-text-muted shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    <div
                                        x-show="openProvider"
                                        @click.outside="openProvider = false"
                                        style="display: none;"
                                        class="absolute left-0 bottom-full mb-1.5 z-50 min-w-[280px] bg-content-bg border border-content-border rounded-xl shadow-xl p-1 max-h-64 overflow-y-auto"
                                    >
                                        <div class="px-2.5 py-1 text-[10px] font-bold text-text-muted uppercase tracking-wider">Simulation</div>
                                        <button type="button" @click="selectProviderModel('opencode', null, null); openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-2.5 font-medium">
                                            <span class="size-2 rounded-full bg-emerald-400"></span>
                                            <span>Block CMS (Free AI Simulation)</span>
                                        </button>

                                        <div class="px-2.5 py-1 mt-2 border-t border-content-border pt-2 text-[10px] font-bold text-text-muted uppercase tracking-wider">Pre-configured APIs</div>
                                        
                                        {{-- Google Gemini Option (only if connected) --}}
                                        <button type="button" @click="selectProviderModel('gemini', null, 'gemini-2.0-flash'); openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-2.5">
                                            <span class="size-2 rounded-full bg-blue-400" :class="geminiKey ? '' : 'opacity-30'"></span>
                                            <span :class="geminiKey ? 'text-text-primary font-medium' : 'text-text-muted opacity-50'">Google Gemini (gemini-2.0-flash)</span>
                                        </button>
                                        
                                        {{-- OpenRouter Options (only if connected) --}}
                                        <button type="button" @click="selectProviderModel('openrouter', null, 'anthropic/claude-3.5-sonnet'); openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-2.5">
                                            <span class="size-2 rounded-full bg-orange-400" :class="openrouterKey ? '' : 'opacity-30'"></span>
                                            <span :class="openrouterKey ? 'text-text-primary font-medium' : 'text-text-muted opacity-50'">OpenRouter (Claude 3.5 Sonnet)</span>
                                        </button>
                                        <button type="button" @click="selectProviderModel('openrouter', null, 'google/gemini-2.0-flash-exp'); openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-2.5">
                                            <span class="size-2 rounded-full bg-orange-400" :class="openrouterKey ? '' : 'opacity-30'"></span>
                                            <span :class="openrouterKey ? 'text-text-primary font-medium' : 'text-text-muted opacity-50'">OpenRouter (Gemini 2.0 Flash)</span>
                                        </button>
                                        
                                        {{-- z.ai Option (only if connected) --}}
                                        <button type="button" @click="selectProviderModel('zai', null, 'z-ai/glm-5.2-free'); openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-2.5">
                                            <span class="size-2 rounded-full bg-indigo-400" :class="zaiKey ? '' : 'opacity-30'"></span>
                                            <span :class="zaiKey ? 'text-text-primary font-medium' : 'text-text-muted opacity-50'">z.ai (GLM 5.2 Free)</span>
                                        </button>

                                        {{-- Dynamic Custom Providers List --}}
                                        <template x-if="customProviders.length > 0">
                                            <div>
                                                <div class="px-2.5 py-1 mt-2 border-t border-content-border pt-2 text-[10px] font-bold text-text-muted uppercase tracking-wider">Custom Providers</div>
                                                <template x-for="p in customProviders" :key="p.id">
                                                    <div class="space-y-0.5">
                                                        <template x-if="p.models.length === 0">
                                                            <button type="button" @click="selectProviderModel('custom', p.id, null); openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-2.5">
                                                                <span class="size-2 rounded-full bg-purple-400"></span>
                                                                <span x-text="p.name + ' (Default)'"></span>
                                                            </button>
                                                        </template>
                                                        <template x-for="m in p.models" :key="m.id">
                                                            <button type="button" @click="selectProviderModel('custom', p.id, m.id); openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-2.5">
                                                                <span class="size-2 rounded-full bg-purple-400"></span>
                                                                <span x-text="p.name + ' (' + m.name + ')'"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <hr class="my-1 border-content-border">
                                        <button type="button" @click="showConnectModal = true; openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-primary hover:bg-body-bg flex items-center gap-2.5 font-semibold">
                // Sub-form temp state
                newProvider: { id: '', name: '', baseUrl: '', apiKey: '', models: [] },
                newModel: { id: '', name: '' },

                // Attachment wireframe state
                attachmentName: null,
                attachmentBase64: null,
                attachmentMime: null,

                providerLabel() {
                    if (this.provider === 'opencode') return 'block-cms/gemini-2.0-flash';
                    if (this.provider === 'gemini') return 'gemini/gemini-2.0-flash';
                    if (this.provider === 'openrouter') return 'OpenRouter (' + (this.selectedCustomModelId ? this.selectedCustomModelId.split('/').pop() : 'Claude 3.5 Sonnet') + ')';
                    if (this.provider === 'zai') return 'z.ai (GLM 5.2 Free)';
                    
                    const p = this.customProviders.find(cp => cp.id === this.selectedCustomProviderId);
                    if (p) {
                        const m = p.models.find(cm => cm.id === this.selectedCustomModelId);
                        return p.name + ' (' + (m ? m.name : 'Default') + ')';
                    }
            </div>

            <div x-show="tab === 'fields'" x-cloak class="h-full">
                <div id="editor-fields" data-cm-editor data-cm-lang="json" data-cm-textarea="field-fields" class="h-full"></div>
                <textarea id="field-fields" name="fields" class="sr-only">{{ old('fields', json_encode($block->fields, JSON_PRETTY_PRINT)) }}</textarea>
            </div>

            <div x-show="tab === 'template'" x-cloak class="h-full">
                <div id="editor-template" data-cm-editor data-cm-lang="html" data-cm-textarea="field-template" class="h-full"></div>
                <textarea id="field-template" name="template" class="sr-only">{{ old('template', $block->template) }}</textarea>
            </div>

            <div x-show="tab === 'preview'" x-cloak class="h-full bg-white">
                <iframe id="preview-frame" class="w-full h-full border-0"></iframe>
            </div>

            <div x-show="tab === 'readme'" x-cloak class="h-full overflow-y-auto p-6 text-sm text-text-primary leading-relaxed space-y-6">
                <div>
                    <h2 class="text-base font-semibold text-text-heading mb-2">Creating Dynamic Blocks</h2>
                    <p class="text-text-muted">Dynamic blocks let you define custom content fields and render them with a template.</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-text-heading mb-1.5">fields.json</h3>
                    <p class="text-text-muted mb-2">Define an array of field objects. Each field supports:</p>
                    <div class="bg-gray-50 rounded-lg p-4 font-mono text-xs space-y-1">
                        <div><span class="text-amber-700">name</span> — Field identifier (used in template as <code class="text-primary">@{{ name }}</code>)</div>
                        <div><span class="text-amber-700">label</span> — Human-readable label shown in the editor</div>
                        <div><span class="text-amber-700">type</span> — Field type: <code class="text-primary">string</code>, <code class="text-primary">text</code>, <code class="text-primary">image</code>, <code class="text-primary">link</code>, <code class="text-primary">boolean</code>, <code class="text-primary">select</code></div>
                        <div><span class="text-amber-700">defaultValue</span> — Default value when block is first added</div>
                        <div><span class="text-amber-700">multiline</span> — Set to <code class="text-primary">true</code> for a textarea (string type only)</div>
                        <div><span class="text-amber-700">options</span> — Array of <code class="text-primary">{ label, value }</code> objects (select type only)</div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-text-heading mb-1.5">Example</h3>
                    <pre class="bg-gray-50 rounded-lg p-4 text-xs overflow-x-auto"><span class="text-amber-700">[</span>
    <span class="text-amber-700">{</span>
        <span class="text-sky-600">"name"</span>: <span class="text-green-700">"heading"</span>,
        <span class="text-sky-600">"label"</span>: <span class="text-green-700">"Heading"</span>,
        <span class="text-sky-600">"type"</span>: <span class="text-green-700">"string"</span>,
        <span class="text-sky-600">"defaultValue"</span>: <span class="text-green-700">"Hello"</span>
    <span class="text-amber-700">}</span>,
    <span class="text-amber-700">{</span>
        <span class="text-sky-600">"name"</span>: <span class="text-green-700">"description"</span>,
        <span class="text-sky-600">"label"</span>: <span class="text-green-700">"Description"</span>,
        <span class="text-sky-600">"type"</span>: <span class="text-green-700">"string"</span>,
        <span class="text-sky-600">"multiline"</span>: <span class="text-blue-600">true</span>,
        <span class="text-sky-600">"defaultValue"</span>: <span class="text-green-700">"World"</span>
    <span class="text-amber-700">}</span>,
    <span class="text-amber-700">{</span>
        <span class="text-sky-600">"name"</span>: <span class="text-green-700">"link"</span>,
        <span class="text-sky-600">"label"</span>: <span class="text-green-700">"Link"</span>,
        <span class="text-sky-600">"type"</span>: <span class="text-green-700">"link"</span>,
        <span class="text-sky-600">"defaultValue"</span>: <span class="text-green-700">""</span>
    <span class="text-amber-700">}</span>
<span class="text-amber-700">]</span></pre>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-text-heading mb-1.5">template.html</h3>
                    <p class="text-text-muted mb-2">Use <code class="text-primary">@{{ fieldName }}</code> to render field values. For nested fields like <code class="text-primary">link</code>, access properties with dot notation:</p>
                    <pre class="bg-gray-50 rounded-lg p-4 text-xs overflow-x-auto"><span class="text-gray-500">&lt;</span><span class="text-red-600">div</span> <span class="text-purple-600">class</span>=<span class="text-green-700">"p-8 text-center bg-gray-50 rounded-xl"</span><span class="text-gray-500">&gt;</span>
    <span class="text-gray-500">&lt;</span><span class="text-red-600">h2</span><span class="text-gray-500">&gt;</span><span class="text-amber-700">@{{ heading }}</span><span class="text-gray-500">&lt;/</span><span class="text-red-600">h2</span><span class="text-gray-500">&gt;</span>
    <span class="text-gray-500">&lt;</span><span class="text-red-600">p</span><span class="text-gray-500">&gt;</span><span class="text-amber-700">@{{ description }}</span><span class="text-gray-500">&lt;/</span><span class="text-red-600">p</span><span class="text-gray-500">&gt;</span>
    <span class="text-gray-500">&lt;</span><span class="text-red-600">a</span> <span class="text-purple-600">href</span>=<span class="text-green-700">"@{{ link.url }}"</span><span class="text-gray-500">&gt;</span><span class="text-amber-700">@{{ link.label }}</span><span class="text-gray-500">&lt;/</span><span class="text-red-600">a</span><span class="text-gray-500">&gt;</span>
<span class="text-gray-500">&lt;/</span><span class="text-red-600">div</span><span class="text-gray-500">&gt;</span></pre>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-text-heading mb-1.5">Field Types</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="border-b border-content-border">

        function blockGenerator() {
            return {
                tab: 'preview',
                init() {
                    this.$nextTick(() => {
                        if (window.renderPreview) {
                            window.renderPreview();
                        }
                    });
                },
                prompt: '',
                generating: false,
                logs: '',
                messages: [],
                provider: localStorage.getItem('lara_cms_ai_provider') || 'opencode',
                selectedCustomProviderId: localStorage.getItem('lara_cms_ai_provider_id') || '',
                selectedCustomModelId: localStorage.getItem('lara_cms_ai_model_id') || '',
                
                // Modals
                showConnectModal: false,
                showApiKeyModal: false,
                showCustomFormModal: false,
                showAddCustomModal: false,
                showPinModal: false,
                showCustomForm: true, // Show form fields inside custom modal by default
                pinnedModels: JSON.parse(localStorage.getItem('lara_cms_pinned_models') || '[]'),

                // Active connection setup state
                connectingProvider: '',
                tempApiKey: '',

                // Configurations
                geminiKey: localStorage.getItem('lara_cms_gemini_key') || '',
                openrouterKey: localStorage.getItem('lara_cms_openrouter_key') || '',
                zaiKey: localStorage.getItem('lara_cms_zai_key') || '',
    <script>
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.querySelector('#editor-form button[type="submit"]').click();
            }
        });

        function blockGenerator() {
            return {
                                if (this.attachmentBase64 && this.attachmentMime) {
                                    parts.push({
                                        inline_data: {
                                            mime_type: this.attachmentMime,
                                            data: this.attachmentBase64
                                        }
                                    });
                                }

                                response = await fetch(url, {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({
                                        contents: [{ parts: parts }]
                                    })
                                });
                            } else {
                                // OpenAI compatible standard format
                                response = await fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        ...(apiKey ? { 'Authorization': 'Bearer ' + apiKey } : {}),
                                        ...(this.provider === 'openrouter' ? {
                                            'HTTP-Referer': 'http://localhost:8000',
                                            'X-Title': 'Lara CMS'
                                        } : {})
                                    },
                                    body: JSON.stringify({
                                        model: modelId,
                                        messages: [{ role: 'user', content: systemPrompt }],
                                        temperature: 0.2
                                    })
                                });
                            }

                            if (!response.ok) {
                                throw new Error('API request failed with status ' + response.status);
                            }

                            const resJson = await response.json();
                            let responseText = '';

                            if (this.provider === 'gemini') {
                                responseText = resJson.candidates?.[0]?.content?.parts?.[0]?.text || '';
                            } else {
                                responseText = resJson.choices?.[0]?.message?.content || '';
                            }

                            responseText = responseText.replace(/```json/gi, '').replace(/```/g, '').trim();
            <div class="flex items-center justify-end gap-2 p-5 border-t border-content-border shrink-0 bg-gray-50">
                <button type="button" @click="showAddCustomModal = false; showCustomFormModal = true;" class="px-4 py-1.5 border border-content-border rounded-lg text-xs font-semibold text-text-primary bg-white hover:bg-gray-50 cursor-pointer">Back</button>
                <button type="button" @click="addCustomProvider()" class="px-4 py-1.5 rounded-lg text-xs font-semibold text-white bg-primary hover:opacity-90 cursor-pointer">Save Provider</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .cm-editor,
    .cm-scroller { height: 100% !important; }
    .cm-editor.cm-focused { outline: none; }
    .cm-scroller { font-family: 'JetBrains Mono', 'Fira Code', monospace; font-size: 13px; }
    .cm-editor .cm-content { padding: 16px; }
    .cm-editor .cm-gutters { border-right: 1px solid #e5e7eb; background: #f9fafb; }
    .cm-editor .cm-activeLineGutter { background: #e5e7eb; }
    .editor-tab { border-right-color: #d1d5db !important; }
    .editor-tab + .editor-tab { border-left-color: #d1d5db !important; }
    .editor-tab:last-of-type { border-right-color: transparent !important; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.2s ease-out forwards;
    }
</style>
@endpush

@push('scripts')
    @vite('resources/js/block-editor.js')
    <script>
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.querySelector('#editor-form button[type=submit]').click();
            }
        });
    </script>
@endpush

                    return { fields: filteredFields, template: fixedTemplate };
                },
                prompt: '',
                generating: false,
                logs: '',
                messages: [],
                provider: localStorage.getItem('lara_cms_ai_provider') || 'opencode',
                selectedCustomProviderId: localStorage.getItem('lara_cms_ai_provider_id') || '',
                selectedCustomModelId: localStorage.getItem('lara_cms_ai_model_id') || '',
                
                // Modals
                showConnectModal: false,
                showApiKeyModal: false,
                showCustomFormModal: false,
                showAddCustomModal: false,
                showPinModal: false,
                showCustomForm: true, // Show form fields inside custom modal by default
                pinnedModels: JSON.parse(localStorage.getItem('lara_cms_pinned_models') || '[]'),

                // Active connection setup state
                connectingProvider: '',
                tempApiKey: '',

                // Configurations
                geminiKey: localStorage.getItem('lara_cms_gemini_key') || '',
                openrouterKey: localStorage.getItem('lara_cms_openrouter_key') || '',
                zaiKey: localStorage.getItem('lara_cms_zai_key') || '',
                customProviders: JSON.parse(localStorage.getItem('lara_cms_custom_providers') || '[]'),

                // Sub-form temp state
                newProvider: { id: '', name: '', baseUrl: '', apiKey: '', models: [] },
                newModel: { id: '', name: '' },

                // Attachment wireframe state
                attachments: [],

                providerLabel() {
                    if (this.provider === 'opencode') return 'block-cms/gemini-2.0-flash';
                    if (this.provider === 'gemini') return 'gemini/gemini-2.0-flash';
                    if (this.provider === 'openrouter') return 'OpenRouter (' + (this.selectedCustomModelId ? this.selectedCustomModelId.split('/').pop() : 'Claude 3.5 Sonnet') + ')';
                    if (this.provider === 'zai') return 'z.ai (GLM 5.2 Free)';
                    
                    const p = this.customProviders.find(cp => cp.id === this.selectedCustomProviderId);
                    if (p) {
                        const m = p.models.find(cm => cm.id === this.selectedCustomModelId);
                        return p.name + ' (' + (m ? m.name : 'Default') + ')';
                    }
                    return 'custom/api-provider';
                },

                currentModelName() {
                    const label = this.providerLabel();
                    if (label.includes('(')) {
                        return label.substring(label.indexOf('(') + 1, label.indexOf(')'));
                    }
                    return label.split('/').pop();
                },

                selectProviderModel(providerType, providerId, modelId) {
                    this.provider = providerType;
                    this.selectedCustomProviderId = providerId || '';
                    this.selectedCustomModelId = modelId || '';
                    localStorage.setItem('lara_cms_ai_provider', providerType);
                    localStorage.setItem('lara_cms_ai_provider_id', this.selectedCustomProviderId);
                    localStorage.setItem('lara_cms_ai_model_id', this.selectedCustomModelId);
                },

                togglePinModel(id, name, provider) {
                    const idx = this.pinnedModels.findIndex(pm => pm.id === id);
                    if (idx > -1) {
                        this.pinnedModels.splice(idx, 1);
                    } else {
                        this.pinnedModels.push({ id, name, provider });
                    }
                    localStorage.setItem('lara_cms_pinned_models', JSON.stringify(this.pinnedModels));
                },

                isPinned(id) {
                    return this.pinnedModels.some(pm => pm.id === id);
                },

                startConnectPredefined(providerName) {
                    this.connectingProvider = providerName;
                    if (providerName === 'gemini') this.tempApiKey = this.geminiKey;
                    else if (providerName === 'openrouter') this.tempApiKey = this.openrouterKey;
                    else if (providerName === 'zai') this.tempApiKey = this.zaiKey;

                    this.showConnectModal = false;
                    this.showApiKeyModal = true;
                },

                saveApiKey() {
                    const val = this.tempApiKey.trim();
                    if (this.connectingProvider === 'gemini') {
                        this.geminiKey = val;
                        localStorage.setItem('lara_cms_gemini_key', val);
                        this.selectProviderModel('gemini', null, 'gemini-2.0-flash');
                    } else if (this.connectingProvider === 'openrouter') {
                        this.openrouterKey = val;
                        localStorage.setItem('lara_cms_openrouter_key', val);
                        this.selectProviderModel('openrouter', null, 'anthropic/claude-3.5-sonnet');
                    } else if (this.connectingProvider === 'zai') {
                        this.zaiKey = val;
                        localStorage.setItem('lara_cms_zai_key', val);
                        this.selectProviderModel('zai', null, 'z-ai/glm-5.2-free');
                    }

                    this.showApiKeyModal = false;
                    window.showToast?.('✓ Connected successfully.', 'success');
                },

                disconnectProvider(providerName) {
                    if (providerName === 'gemini') {
                        this.geminiKey = '';
                        localStorage.removeItem('lara_cms_gemini_key');
                        if (this.provider === 'gemini') this.selectProviderModel('opencode', null, null);
                    } else if (providerName === 'openrouter') {
                        this.openrouterKey = '';
                        localStorage.removeItem('lara_cms_openrouter_key');
                        if (this.provider === 'openrouter') this.selectProviderModel('opencode', null, null);
                    } else if (providerName === 'zai') {
                        this.zaiKey = '';
                        localStorage.removeItem('lara_cms_zai_key');
                        if (this.provider === 'zai') this.selectProviderModel('opencode', null, null);
                    }
                    window.showToast?.('✓ Disconnected ' + providerName, 'success');
                },

                addModelToNewProvider() {
                    if (!this.newModel.id || !this.newModel.name) {
                        window.showToast?.('Please specify Model ID and Name.', 'danger');
                        return;
                    }
                    this.newProvider.models.push({
                        id: this.newModel.id.trim(),
                        name: this.newModel.name.trim()
                    });
                    this.newModel = { id: '', name: '' };
                },

                removeModelFromNewProvider(index) {
                    this.newProvider.models.splice(index, 1);
                },

                addCustomProvider() {
                    if (!this.newProvider.id || !this.newProvider.name || !this.newProvider.baseUrl) {
                        window.showToast?.('Please fill in Provider ID, Name, and Base URL.', 'danger');
                        return;
                    }
                    
                    const idPattern = /^[a-z0-9-_]+$/;
                    if (!idPattern.test(this.newProvider.id)) {
                        window.showToast?.('Provider ID must contain only lowercase letters, hyphens, or underscores.', 'danger');
                        return;
                    }

                    if (this.customProviders.some(cp => cp.id === this.newProvider.id)) {
                        window.showToast?.('Provider ID must be unique.', 'danger');
                        return;
                    }

                    const savedProvider = {
                        id: this.newProvider.id.trim(),
                        name: this.newProvider.name.trim(),
                        baseUrl: this.newProvider.baseUrl.trim(),
                        apiKey: this.newProvider.apiKey.trim(),
                        models: [...this.newProvider.models]
                    };

                    this.customProviders.push(savedProvider);
                    this.newProvider = { id: '', name: '', baseUrl: '', apiKey: '', models: [] };
                    this.newModel = { id: '', name: '' };
                    
                    localStorage.setItem('lara_cms_custom_providers', JSON.stringify(this.customProviders));
                    
                    // Automatically select this new custom provider
                    const defaultModelId = savedProvider.models.length > 0 ? savedProvider.models[0].id : null;
                    this.selectProviderModel('custom', savedProvider.id, defaultModelId);

                    this.showCustomFormModal = false;
                    window.showToast?.('✓ Custom provider saved to list.', 'success');
                },

                removeCustomProvider(index) {
                    const p = this.customProviders[index];
                    this.customProviders.splice(index, 1);
                    localStorage.setItem('lara_cms_custom_providers', JSON.stringify(this.customProviders));
                    
                    if (this.provider === 'custom' && this.selectedCustomProviderId === p.id) {
                        this.selectProviderModel('opencode', null, null);
                    }
                    window.showToast?.('✓ Custom provider removed.', 'success');
                },

                handleFileSelect(e) {
                    const files = Array.from(e.target.files);
                    if (!files.length) return;

                    const spaceLeft = 3 - this.attachments.length;
                    const filesToAdd = files.slice(0, spaceLeft);

                    filesToAdd.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            const base64 = event.target.result.split(',')[1];
                            const preview = file.type.startsWith('image/') ? event.target.result : null;

                            this.attachments.unshift({
                                name: file.name,
                                mime: file.type,
                                base64: base64,
                                preview: preview
                            });
                        };
                        reader.readAsDataURL(file);
                    });

                    e.target.value = '';
                },

                removeAttachment(index = null) {
                    if (index !== null) {
                        this.attachments.splice(index, 1);
                    } else {
                        this.attachments = [];
                    }
                    document.getElementById('ai-file-input').value = '';
                },

                textareaFocus() {
                    this.$nextTick(() => {
                        const el = document.getElementById('chat-prompt-input');
                        if (el) el.focus();
                    });
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.chatContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                async runAiGeneration() {
                    if (this.generating) return;
                    
                    const promptText = this.prompt.trim();
                    if (!promptText) return;

                    const attachmentsCopy = [...this.attachments];

                    this.messages.push({
                        sender: 'user',
                        text: promptText,
                        attachments: attachmentsCopy,
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    });

                    this.prompt = '';
                    this.removeAttachment();
                    this.scrollToBottom();

                    const aiMsgIdx = this.messages.length;
                    this.messages.push({
                        sender: 'ai',
                        text: 'Thinking...',
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    });

                    this.generating = true;
                    this.logs = 'Thinking...';
                    this.scrollToBottom();

                    const updateAiMessageText = (txt) => {
                        this.messages[aiMsgIdx] = {
                            ...this.messages[aiMsgIdx],
                            text: txt
                        };
                        this.scrollToBottom();
                    };

                    try {
                        let fields = null;
                        let template = null;
                        let aiMessage = '';

                        if (this.provider === 'opencode') {
                            updateAiMessageText('Thinking...');
                            this.logs = 'Thinking...';
                            await new Promise(resolve => setTimeout(resolve, 1000));

                            const promptLower = promptText.toLowerCase();
                            const isGreeting = promptLower.includes('how are you') || 
                                               promptLower.includes('are you well') || 
                                               promptLower.includes('hello') || 
                                               promptLower.includes('hi') || 
                                               promptLower.includes('who are you') || 
                                               promptLower.includes('what can you do') ||
                                               promptLower.includes('help');

                            if (isGreeting) {
                                aiMessage = "Hello! I am doing great, thank you. I am your AI Block Assistant for Lara CMS. I can help you design, generate, or modify content blocks such as Hero sections, Pricing tables, Contact forms, Testimonials, and FAQs. How can I assist you with your project today?";
                            } else if (promptLower.includes('pricing') || promptLower.includes('card') || promptLower.includes('travel') || promptLower.includes('deal')) {
                                fields = [
                                    { name: 'headline', label: 'Section Headline', type: 'string', defaultValue: 'Exclusive Travel Deals' },
                                    { name: 'description', label: 'Section Description', type: 'string', multiline: true, defaultValue: 'Handpicked packages with special discounts.' },
                                    {
                                        name: 'cards',
                                        label: 'Travel Deal Cards',
                                        type: 'object',
                                        list: true,
                                        defaultCount: 3,
                                        fields: [
                                            { name: 'image', label: 'Card Image', type: 'image', defaultValue: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80' },
                                            { name: 'title', label: 'Deal Title', type: 'string', defaultValue: 'Maldives Overwater Resort' },
                                            { name: 'badge', label: 'Badge', type: 'string', defaultValue: '25% OFF' },
                                            { name: 'description', label: 'Description', type: 'string', multiline: true, defaultValue: '5 nights luxury stay with breakfast and speedboat transfers.' },
                                            { name: 'price', label: 'Discounted Price ($)', type: 'string', defaultValue: '1,299' },
                                            { name: 'originalPrice', label: 'Original Price ($)', type: 'string', defaultValue: '1,799' },
                                            { name: 'buttonLabel', label: 'Button Label', type: 'string', defaultValue: 'Book Deal' }
                                        ]
                                    }
                                ];
                                template = `<section class="py-16 relative overflow-hidden bg-gray-50">
  <div data-edit="background" class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-color: @{{ background.color }}; background-image: url(@{{ background.image }}); opacity: calc(@{{ background.opacity }} / 100)"></div>
  <div class="relative max-w-6xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-12">
      <h2 data-edit="headline" class="text-3xl font-extrabold text-gray-900 tracking-tight">@{{ headline }}</h2>
      <p data-edit="description" class="mt-3 text-base text-gray-500">@{{ description }}</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      {% for card in cards %}
        <div data-list="cards" class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-transform duration-300 hover:-translate-y-1">
          <div data-edit="image" class="relative h-52 overflow-hidden rounded-t-2xl bg-gray-100 flex items-center justify-center">
            <img src="@{{ card.image }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
            {% if card.badge != '' %}
              <span data-edit="badge" class="absolute top-3 right-3 rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white shadow-xs">@{{ card.badge }}</span>
            {% endif %}
          </div>
          <div class="flex flex-col flex-1 p-5">
            <h3 data-edit="title" class="text-lg font-bold text-gray-900 group-hover:text-primary transition-colors">@{{ card.title }}</h3>
            <p data-edit="description" class="mt-2 text-sm text-gray-500 leading-relaxed flex-1">@{{ card.description }}</p>
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
              <div>
                {% if card.originalPrice != '' %}
                  <span data-edit="originalPrice" class="text-xs text-gray-400 line-through mr-1">$@{{ card.originalPrice }}</span>
                {% endif %}
                <span data-edit="price" class="text-xl font-extrabold text-emerald-600">$@{{ card.price }}</span>
              </div>
              <span data-edit="buttonLabel" class="inline-flex items-center gap-1.5 bg-gray-900 text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-gray-800 transition-colors">
                @{{ card.buttonLabel }}
              </span>
            </div>
          </div>
        </div>
      {% endfor %}
    </div>
  </div>
</section>`;
                                aiMessage = "I have generated a fully functional Travel Deals block section with repeatable, addable, and removeable deal cards and editable images.";
                            } else if (promptLower.includes('hero') || promptLower.includes('banner')) {
                                fields = [
                                    { name: 'heading', label: 'Heading', type: 'string', defaultValue: 'Discover the World' },
                                    { name: 'subheading', label: 'Subheading', type: 'string', defaultValue: 'Explore tailored packages for your next journey.' },
                                    { name: 'cta_text', label: 'CTA Text', type: 'string', defaultValue: 'Book Now' }
                                ];
                                template = `<div class="relative py-24 px-6 text-center bg-gray-900 text-white rounded-2xl overflow-hidden shadow-lg">
  <div class="relative z-10 max-w-2xl mx-auto">
    <h1 class="text-4xl font-extrabold tracking-tight">@{{ heading }}</h1>
    <p class="mt-4 text-lg text-gray-300">@{{ subheading }}</p>
    <a href="#" class="mt-8 inline-block bg-emerald-500 px-8 py-3 rounded-lg font-medium text-white hover:opacity-90 transition-opacity">@{{ cta_text }}</a>
  </div>
</div>`;
                                aiMessage = "I have created a hero banner block with a main heading, subheading, and CTA button.";
                            } else if (promptLower.includes('faq') || promptLower.includes('accordion')) {
                                fields = [
                                    { name: 'question', label: 'Question', type: 'string', defaultValue: 'What is included in the tour?' },
                                    { name: 'answer', label: 'Answer', type: 'string', defaultValue: 'All transfers, accommodation, and entry fees are fully covered.' }
                                ];
                                template = `<div class="max-w-xl mx-auto p-5 bg-white border border-content-border rounded-xl">
  <details class="group">
    <summary class="flex justify-between items-center font-medium cursor-pointer list-none">
      <span class="text-text-heading">@{{ question }}</span>
      <span class="transition group-open:rotate-180">
        <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
      </span>
    </summary>
    <p class="text-text-muted mt-3 group-open:animate-fadeIn">@{{ answer }}</p>
  </details>
</div>`;
                                aiMessage = "I have built an accordion FAQ block component for your content.";
                            } else {
                                fields = [
                                    { name: 'title', label: 'Title', type: 'string', defaultValue: 'Feature Title' },
                                    { name: 'desc', label: 'Description', type: 'string', defaultValue: 'Feature description goes here.' }
                                ];
                                template = `<div class="p-6 bg-content-bg rounded-xl border border-content-border shadow-sm max-w-md mx-auto">
  <h2 class="text-lg font-semibold text-text-heading">@{{ title }}</h2>
  <p class="text-text-muted mt-2">@{{ desc }}</p>
</div>`;
                                aiMessage = `I have updated the block based on your request: "${promptText}".`;
                            }
                        } else {
                            let apiKey = '';
                            let url = '';
                            let modelId = '';

                            if (this.provider === 'gemini') {
                                apiKey = this.geminiKey;
                                if (!apiKey) {
                                    this.showConnectModal = true;
                                    throw new Error('Please connect Google Gemini API first.');
                                }
                                url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' + apiKey;

                                contents.push({ role: 'user', parts: currentParts });

                                response = await fetch(url, {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({
                                        contents: contents
                                    })
                                });
                            } else {
                                const apiMessages = [
                                    { role: 'system', content: systemPrompt }
                                ];
                                recentHistory.forEach(h => {
                                    apiMessages.push({ role: h.role, content: h.text });
                                });
                                apiMessages.push({ role: 'user', content: promptContextText });

                                response = await fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        ...(apiKey ? { 'Authorization': 'Bearer ' + apiKey } : {}),
                                        ...(this.provider === 'openrouter' ? {
                                            'HTTP-Referer': 'http://localhost:8000',
                                            'X-Title': 'Lara CMS'
                                        } : {})
                                    },
                                    body: JSON.stringify({
                                        model: modelId,
                                        messages: apiMessages,
                                        temperature: 0.2
                                    })
                                });
                            }

                            if (!response.ok) {
                                throw new Error('API request failed with status ' + response.status);
                            }

                            const resJson = await response.json();
                            let responseText = '';

                            if (this.provider === 'gemini') {
                                responseText = resJson.candidates?.[0]?.content?.parts?.[0]?.text || '';
                            } else {
                                responseText = resJson.choices?.[0]?.message?.content || '';
                            }

                            responseText = responseText.replace(/```json/gi, '').replace(/```/g, '').trim();

                            try {
                                const parsed = JSON.parse(responseText);
                                aiMessage = parsed.message || 'Response received.';
                                if (parsed.fields && parsed.template && Array.isArray(parsed.fields) && typeof parsed.template === 'string') {
                                    fields = parsed.fields;
                                    template = parsed.template;
                                }
                            } catch (pErr) {
                                // Fallback if AI output non-JSON text
                                aiMessage = responseText;
                            }
                        }

                        // Update fields & template ONLY if code was actually returned/generated
                        if (fields !== null && template !== null) {
                            const fieldsStr = JSON.stringify(fields, null, 2);
                            window.updateEditorContent('field-fields', fieldsStr);
                            window.updateEditorContent('field-template', template);

                            window.showToast?.('✓ Block updated successfully!', 'success');
                            this.$nextTick(() => window.renderPreview?.());
                        }

                        updateAiMessageText(aiMessage || '✓ Processed successfully.');
                    } catch (err) {
                        this.logs = '[ERROR] ' + err.message;
                        updateAiMessageText('✗ Generation failed: ' + err.message);
                        window.showToast?.('✗ Generation failed: ' + err.message, 'danger');
                    } finally {
                        this.generating = false;
                    }
                }
            };
                                apiMessages.push({ role: 'user', content: promptContextText });

                                response = await fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        ...(apiKey ? { 'Authorization': 'Bearer ' + apiKey } : {}),
                                        ...(this.provider === 'openrouter' ? {
                                            'HTTP-Referer': 'http://localhost:8000',
                                            'X-Title': 'Lara CMS'
                                        } : {})
                                    },
                                    body: JSON.stringify({
                                        model: modelId,
                                        messages: apiMessages,
                                        temperature: 0.2
                                    })
                                });
                            }

                            if (!response.ok) {
                                throw new Error('API request failed with status ' + response.status);
                            }

                            const resJson = await response.json();
                            let responseText = '';

                            if (this.provider === 'gemini') {
                                responseText = resJson.candidates?.[0]?.content?.parts?.[0]?.text || '';
                            } else {
                                responseText = resJson.choices?.[0]?.message?.content || '';
                            }

                            responseText = responseText.replace(/```json/gi, '').replace(/```/g, '').trim();

                            try {
                                const parsed = JSON.parse(responseText);
                                aiMessage = parsed.message || 'Response received.';
                                if (parsed.fields && parsed.template && Array.isArray(parsed.fields) && typeof parsed.template === 'string') {
                                    fields = parsed.fields;
    .cm-editor .cm-gutters { border-right: 1px solid #e5e7eb; background: #f9fafb; }
    .cm-editor .cm-activeLineGutter { background: #e5e7eb; }
    .editor-tab { border-right-color: #d1d5db !important; }
    .editor-tab + .editor-tab { border-left-color: #d1d5db !important; }
    .editor-tab:last-of-type { border-right-color: transparent !important; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.2s ease-out forwards;
    }
</style>
@endpush

@push('scripts')
    @vite('resources/js/block-editor.js')
    <script>
        document.addEventListener('keydown', function (e) {
    </script>
@endpush
            return {
                parseMarkdown(txt) {
                    if (window.marked && typeof window.marked.parse === 'function') {
                        try {
                            return window.marked.parse(txt);
                        } catch (e) {
                            console.error('Marked parsing error:', e);
                        }
                    }
                            return window.marked.parse(txt);
                        } catch (e) {
                            console.error('Marked parsing error:', e);
                        }
                    }
                    let html = txt
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');
                    html = html.replace(/^### (.*$)/gim, '<h3 class="font-bold text-xs mt-2 mb-1 text-gray-900">$1</h3>');
                        } catch (e) {
                            console.error('Marked parsing error:', e);
                        }
                    }
                    let html = txt
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');
                    html = html.replace(/^### (.*$)/gim, '<h3 class="font-bold text-xs mt-2 mb-1 text-gray-900">$1</h3>');
                    html = html.replace(/^## (.*$)/gim, '<h2 class="font-bold text-xs mt-2 mb-1 text-gray-900">$1</h2>');
                    html = html.replace(/^# (.*$)/gim, '<h1 class="font-bold text-sm mt-2 mb-1 text-gray-900">$1</h1>');
                    html = html.replace(/```([\s\S]*?)```/g, '<pre class="bg-gray-800 text-gray-100 p-2 rounded-lg text-[10px] my-1.5 font-mono overflow-x-auto"><code>$1</code></pre>');
                    html = html.replace(/`([^`]+)`/g, '<code class="bg-gray-200 text-gray-800 px-1 py-0.5 rounded text-[10px] font-mono">$1</code>');
                    html = html.replace(/\*\*([^*]+)\*\*/g, '<strong class="font-semibold text-gray-900">$1</strong>');
                    html = html.replace(/\*([^*]+)\*/g, '<em>$1</em>');
                    html = html.replace(/^\s*[-*]\s+(.*)$/gim, '<li class="ml-3 list-disc">$1</li>');
                    html = html.replace(/^\s*(\d+)\.\s+(.*)$/gim, '<li class="ml-3 list-decimal">$1</li>');
                    html = html.replace(/\n/g, '<br>');
                    return html;
                },
                                // Fallback if AI output non-JSON text
                                aiMessage = responseText;
                            }
                        }

                        // Update fields & template ONLY if code was actually returned/generated
                        if (fields !== null && template !== null) {
                            const sanitized = this.sanitizeBlockDefinition(fields, template);
                            fields = sanitized.fields;
                            template = sanitized.template;
                        updateAiMessageText(aiMessage || '✓ Processed successfully.');
                    } catch (err) {
                        this.logs = '[ERROR] ' + err.message;
                        updateAiMessageText('✗ Generation failed: ' + err.message);
                        window.showToast?.('✗ Generation failed: ' + err.message, 'danger');
                    } finally {
                        this.generating = false;
                    }
                }
            };
        }

        if (window.Alpine) {
            window.Alpine.data('blockGenerator', blockGenerator);
        } else {
            document.addEventListener('alpine:init', function () {
                window.Alpine.data('blockGenerator', blockGenerator);
            });
        }
    </script>
@endpush
@endpush
                window.Alpine.data('blockGenerator', blockGenerator);
            });
        }
    </script>
            });
        }
    </script>
        }
    </script>
@endpush