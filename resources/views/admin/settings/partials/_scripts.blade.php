<script>
    function generateUUID() {
        if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
            return crypto.randomUUID();
        }
        return 'f_' + Math.random().toString(36).substring(2, 11) + '_' + Date.now().toString(36);
    }

    function globalSettingsForm() {
        const initialFields = window.settingsCustomFields || [];
        const initialValues = window.settingsCustomValues || {};
        return {
            activeTab: 'general',
            showAiKey: false,
            showUnsplashKey: false,
            showPexelsKey: false,
            showPixabayKey: false,
            fields: (initialFields || []).map(f => ({
                ...f,
                template: (f.template || '').replace(/[^a-zA-Z0-9_]+/g, ''),
                _key: f._key || generateUUID()
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
                    this.fields.push({ ...this.fieldForm, _key: generateUUID() });
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
                this.$nextTick(() => this.initSortable());
            },

            initSortable() {
                const el = document.getElementById('sortable-custom-fields');
                if (!el || typeof Sortable === 'undefined') return;
                if (el._sortable) {
                    try { el._sortable.destroy(); } catch (e) {}
                    delete el._sortable;
                }
                el._sortable = new Sortable(el, {
                    draggable: '[data-custom-field-row]',
                    animation: 200,
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    filter: 'input, textarea, select, button, a, [role="switch"], .no-drag',
                    preventOnFilter: false,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onStart: (evt) => {
                        evt.item._prevSibling = evt.item.previousElementSibling;
                    },
                    onEnd: (evt) => {
                        const cleanup = () => {
                            delete evt.item._prevSibling;
                            setTimeout(() => this.initSortable(), 0);
                        };

                        if (evt.oldIndex === evt.newIndex || evt.oldIndex === undefined || evt.newIndex === undefined) {
                            cleanup();
                            return;
                        }

                        // Revert Sortable DOM changes so Alpine can handle the DOM update cleanly
                        const itemEl = evt.item;
                        if (itemEl._prevSibling && itemEl._prevSibling.parentElement === evt.from) {
                            itemEl._prevSibling.after(itemEl);
                        } else if (evt.from) {
                            evt.from.prepend(itemEl);
                        }

                        let oldIdx = evt.oldDraggableIndex;
                        let newIdx = evt.newDraggableIndex;
                        if (oldIdx === undefined || newIdx === undefined) {
                            const offset = (evt.from.children[0] && evt.from.children[0].tagName === 'TEMPLATE') ? 1 : 0;
                            oldIdx = evt.oldIndex - offset;
                            newIdx = evt.newIndex - offset;
                        }

                        if (oldIdx >= 0 && oldIdx < this.fields.length && newIdx >= 0 && newIdx < this.fields.length) {
                            const item = this.fields.splice(oldIdx, 1)[0];
                            if (item !== undefined) {
                                this.fields.splice(newIdx, 0, item);
                                this.fields = [...this.fields];

                                // Auto-save reordered fields immediately
                                fetch('{{ route("admin.settings.reorder_custom_fields") }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        custom_fields: this.fields.map(f => {
                                            const { _key, ...rest } = f;
                                            return rest;
                                        })
                                    })
                                });
                            }
                        }
                        cleanup();
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

    function aiModelsManager() {
        return {
            models: [],
            loading: true,
            search: '',
            providerFilter: 'all',
            page: 1,
            perPage: 10,
            showModal: false,
            isEditing: false,
            isSaving: false,
            isTestingModal: false,
            modalTestMessage: '',
            modalTestSuccess: null,
            modalErrorTitle: '',
            showApiKey: false,
            copiedModelId: null,
            showDeleteModal: false,
            deletingModel: null,
            actionModel: null,
            actionMenuX: 0,
            actionMenuY: 0,
            providerDropdownOpen: false,
            modelPresetDropdownOpen: false,

            providersList: [
                {
                    id: 'openai',
                    name: 'OpenAI / ChatGPT',
                    tagline: 'GPT-4o, GPT-4o Mini, o1, o3-mini',
                    baseUrl: 'https://api.openai.com/v1',
                    models: [
                        { name: 'GPT-4o', model_id: 'gpt-4o', description: 'Flagship multimodal model' },
                        { name: 'GPT-4o Mini', model_id: 'gpt-4o-mini', description: 'Fast & lightweight' },
                        { name: 'o1', model_id: 'o1', description: 'Deep reasoning' },
                        { name: 'o3-mini', model_id: 'o3-mini', description: 'Fast reasoning' }
                    ]
                },
                {
                    id: 'anthropic',
                    name: 'Anthropic / Claude',
                    tagline: 'Claude 3.5 Sonnet, Claude 3.5 Haiku',
                    baseUrl: 'https://api.anthropic.com/v1',
                    models: [
                        { name: 'Claude 3.5 Sonnet', model_id: 'claude-3-5-sonnet-20241022', description: 'Coding & analysis' },
                        { name: 'Claude 3.5 Haiku', model_id: 'claude-3-5-haiku-20241022', description: 'Fast & efficient' },
                        { name: 'Claude 3 Opus', model_id: 'claude-3-opus-20240229', description: 'Complex reasoning' }
                    ]
                },
                {
                    id: 'deepseek',
                    name: 'DeepSeek',
                    tagline: 'DeepSeek V3 & R1 Chain-of-Thought',
                    baseUrl: 'https://api.deepseek.com',
                    models: [
                        { name: 'DeepSeek V3', model_id: 'deepseek-chat', description: 'General coding & reasoning' },
                        { name: 'DeepSeek R1', model_id: 'deepseek-reasoner', description: 'Reasoning & thinking' }
                    ]
                },
                {
                    id: 'grok',
                    name: 'xAI / Grok',
                    tagline: 'Grok 2, Grok 2 Vision, Grok Beta',
                    baseUrl: 'https://api.x.ai/v1',
                    models: [
                        { name: 'Grok 2', model_id: 'grok-2-latest', description: 'Flagship reasoning & coding' },
                        { name: 'Grok 2 Vision', model_id: 'grok-2-vision-1212', description: 'Multimodal vision model' },
                        { name: 'Grok Beta', model_id: 'grok-beta', description: 'High-speed reasoning' }
                    ]
                },
                {
                    id: 'google',
                    name: 'Google Gemini',
                    tagline: 'Gemini 1.5 Pro, Flash, 2.0 Flash',
                    baseUrl: 'https://generativelanguage.googleapis.com/v1beta',
                    models: [
                        { name: 'Gemini 1.5 Pro', model_id: 'gemini-1.5-pro', description: 'Multimodal reasoning' },
                        { name: 'Gemini 1.5 Flash', model_id: 'gemini-1.5-flash', description: 'Fast multimodal' },
                        { name: 'Gemini 2.0 Flash', model_id: 'gemini-2.0-flash', description: 'Real-time multimodal' }
                    ]
                },
                {
                    id: 'qwen',
                    name: 'Qwen / Alibaba',
                    tagline: 'Qwen 2.5 72B, Plus, Max, Coder',
                    baseUrl: 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1',
                    models: [
                        { name: 'Qwen 2.5 72B', model_id: 'qwen-plus', description: 'Open-weight flagship' },
                        { name: 'Qwen 2.5 Max', model_id: 'qwen-max', description: 'Enterprise reasoning' },
                        { name: 'Qwen 2.5 Turbo', model_id: 'qwen-turbo', description: 'Low latency' },
                        { name: 'Qwen 2.5 Coder 32B', model_id: 'qwen-coder-plus', description: 'Code generation' }
                    ]
                },
                {
                    id: 'groq',
                    name: 'Groq',
                    tagline: 'Ultra-fast LPU inference (LLaMA 3.3)',
                    baseUrl: 'https://api.groq.com/openai/v1',
                    models: [
                        { name: 'Groq LLaMA 3.3 70B', model_id: 'llama-3.3-70b-versatile', description: 'High-speed LPU' },
                        { name: 'Groq LLaMA 3.1 8B', model_id: 'llama-3.1-8b-instant', description: 'Instant inference' },
                        { name: 'Groq Mixtral 8x7B', model_id: 'mixtral-8x7b-32768', description: 'MoE model' }
                    ]
                },
                {
                    id: 'custom',
                    name: 'Custom / Local Ollama',
                    tagline: 'Self-hosted Ollama, vLLM, LM Studio',
                    baseUrl: 'http://localhost:11434/v1',
                    models: [
                        { name: 'Ollama LLaMA 3.2', model_id: 'llama3.2', description: 'Local model' },
                        { name: 'Ollama DeepSeek R1', model_id: 'deepseek-r1:8b', description: 'Local reasoning' },
                        { name: 'Ollama Mistral', model_id: 'mistral', description: 'Local Mistral' }
                    ]
                }
            ],

            form: {
                id: null,
                name: '',
                model_id: '',
                provider: 'openai',
                base_url: 'https://api.openai.com/v1',
                api_key: '',
                is_active: true,
                is_default: false,
                description: '',
                has_api_key: false
            },

            init() {
                this.fetchModels();
            },

            get currentProviderObj() {
                return this.providersList.find(p => p.id === this.form.provider) || this.providersList[0];
            },

            get currentProviderModels() {
                return this.currentProviderObj?.models || [];
            },

            get filteredModels() {
                return this.models.filter(m => {
                    const matchesSearch = !this.search ||
                        m.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        m.model_id.toLowerCase().includes(this.search.toLowerCase()) ||
                        m.provider.toLowerCase().includes(this.search.toLowerCase());
                    const matchesProvider = this.providerFilter === 'all' || m.provider === this.providerFilter;
                    return matchesSearch && matchesProvider;
                });
            },

            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredModels.length / this.perPage));
            },

            get paginatedModels() {
                const start = (this.page - 1) * this.perPage;
                return this.filteredModels.slice(start, start + this.perPage);
            },

            openActionMenu(e, m) {
                this.actionModel = m;
                const rect = e.currentTarget.getBoundingClientRect();
                const menuWidth = 208;
                let left = rect.right - menuWidth;
                if (left < 10) left = 10;
                this.actionMenuX = left;
                this.actionMenuY = rect.bottom + window.scrollY + 4;
            },

            providerBadgeClasses(provider) {
                switch (provider) {
                    case 'deepseek':
                        return 'bg-blue-50 border-blue-200 text-blue-600';
                    case 'openai':
                        return 'bg-emerald-50 border-emerald-200 text-emerald-600';
                    case 'anthropic':
                        return 'bg-amber-50 border-amber-200 text-amber-600';
                    case 'grok':
                    case 'xai':
                        return 'bg-neutral-100 border-neutral-300 text-neutral-900';
                    case 'qwen':
                        return 'bg-purple-50 border-purple-200 text-purple-600';
                    case 'google':
                        return 'bg-sky-50 border-sky-200 text-sky-600';
                    case 'groq':
                        return 'bg-orange-50 border-orange-200 text-orange-600';
                    default:
                        return 'bg-gray-100 border-gray-200 text-gray-700';
                }
            },

            getProviderLogo(provider) {
                switch (provider) {
                    case 'deepseek':
                        return '/images/ai-providers/deepseek.svg';
                    case 'openai':
                        return '/images/ai-providers/openai.svg';
                    case 'anthropic':
                    case 'claude':
                        return '/images/ai-providers/claude.svg';
                    case 'grok':
                    case 'xai':
                        return '/images/ai-providers/grok.svg';
                    case 'qwen':
                        return '/images/ai-providers/qwen.svg';
                    case 'google':
                        return '/images/ai-providers/google.svg';
                    case 'groq':
                        return '/images/ai-providers/groq.png';
                    default:
                        return '/images/ai-providers/custom.png';
                }
            },

            selectProvider(providerId) {
                this.form.provider = providerId;
                this.providerDropdownOpen = false;
                const provider = this.providersList.find(p => p.id === providerId);
                if (provider) {
                    this.form.base_url = provider.baseUrl;
                    if (provider.models && provider.models.length > 0) {
                        this.form.name = provider.models[0].name;
                        this.form.model_id = provider.models[0].model_id;
                        this.form.description = provider.models[0].description || '';
                    }
                }
            },

            applyModelPreset(preset) {
                this.form.name = preset.name;
                this.form.model_id = preset.model_id;
                if (preset.description) {
                    this.form.description = preset.description;
                }
            },

            async fetchModels() {
                this.loading = true;
                try {
                    const res = await fetch('/admin/ai-models', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                        }
                    });
                    const data = await res.json();
                    if (data.success && Array.isArray(data.models)) {
                        this.models = data.models;
                    }
                } catch (e) {
                    console.error('Failed to fetch AI models:', e);
                } finally {
                    this.loading = false;
                }
            },

            openCreateModal() {
                this.isEditing = false;
                this.modalTestMessage = '';
                this.modalTestSuccess = null;
                this.modalErrorTitle = '';
                this.showApiKey = false;
                this.providerDropdownOpen = false;
                this.modelPresetDropdownOpen = false;
                this.form = {
                    id: null,
                    name: '',
                    model_id: '',
                    provider: 'openai',
                    base_url: 'https://api.openai.com/v1',
                    api_key: '',
                    is_active: true,
                    is_default: false,
                    description: '',
                    has_api_key: false
                };
                this.selectProvider('openai');
                this.showModal = true;
            },

            openEditModal(m) {
                this.isEditing = true;
                this.modalTestMessage = '';
                this.modalTestSuccess = null;
                this.modalErrorTitle = '';
                this.showApiKey = false;
                this.providerDropdownOpen = false;
                this.modelPresetDropdownOpen = false;
                this.form = {
                    id: m.id,
                    name: m.name,
                    model_id: m.model_id,
                    provider: m.provider,
                    base_url: m.base_url || '',
                    api_key: '',
                    is_active: !!m.is_active,
                    is_default: !!m.is_default,
                    description: m.description || '',
                    has_api_key: !!m.has_api_key
                };
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
                this.providerDropdownOpen = false;
                this.modelPresetDropdownOpen = false;
            },

            onProviderChange() {
                this.selectProvider(this.form.provider);
            },

            async saveModel() {
                if (this.isSaving) return;
                this.isSaving = true;
                this.modalTestMessage = '';
                this.modalErrorTitle = '';

                const url = this.isEditing ? `/admin/ai-models/${this.form.id}` : '/admin/ai-models';
                const method = this.isEditing ? 'PUT' : 'POST';

                try {
                    const res = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await res.json();
                    if (!res.ok || !data.success) {
                        let errMsg = data.message || 'Failed to save model';
                        if (data.errors) {
                            errMsg = Object.values(data.errors).flat().join(' ');
                        }
                        throw new Error(errMsg);
                    }

                    this.showModal = false;
                    await this.fetchModels();
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: data.message || (this.isEditing ? 'AI model updated successfully.' : 'AI model added successfully.'),
                            type: 'success'
                        }
                    }));
                } catch (err) {
                    this.modalTestSuccess = false;
                    this.modalErrorTitle = '✕ Save Failed';
                    this.modalTestMessage = err.message;
                } finally {
                    this.isSaving = false;
                }
            },

            async toggleActive(m) {
                try {
                    const res = await fetch(`/admin/ai-models/${m.id}/toggle-active`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        m.is_active = data.is_active;
                        m.is_default = data.is_default;
                    }
                } catch (e) {
                    console.error('Failed to toggle model active status:', e);
                }
            },

            async setDefault(m) {
                try {
                    const res = await fetch(`/admin/ai-models/${m.id}/set-default`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.models.forEach(model => model.is_default = (model.id === m.id));
                        m.is_active = true;
                    }
                } catch (e) {
                    console.error('Failed to set default model:', e);
                }
            },

            confirmDelete(m) {
                this.deletingModel = m;
                this.showDeleteModal = true;
            },

            async executeDelete() {
                if (!this.deletingModel) return;
                try {
                    const res = await fetch(`/admin/ai-models/${this.deletingModel.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showDeleteModal = false;
                        this.deletingModel = null;
                        await this.fetchModels();
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                message: data.message || 'AI model deleted successfully.',
                                type: 'success'
                            }
                        }));
                    }
                } catch (e) {
                    console.error('Failed to delete model:', e);
                }
            },

            async testModalConnection() {
                if (this.isTestingModal) return;
                this.isTestingModal = true;
                this.modalTestMessage = '';
                this.modalTestSuccess = null;
                this.modalErrorTitle = '';

                try {
                    const res = await fetch('/admin/ai-models/test-connection', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await res.json();
                    this.modalTestSuccess = data.success;
                    this.modalErrorTitle = data.success ? '' : '✕ Connection Failed';
                    this.modalTestMessage = data.message;
                } catch (err) {
                    this.modalTestSuccess = false;
                    this.modalErrorTitle = '✕ Connection Failed';
                    this.modalTestMessage = err.message;
                } finally {
                    this.isTestingModal = false;
                }
            },

            async copyApiKey(m) {
                const keyToCopy = m.api_key;
                if (!keyToCopy) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: 'No API key configured for this model.',
                            type: 'error'
                        }
                    }));
                    return;
                }

                try {
                    await navigator.clipboard.writeText(keyToCopy);
                } catch (err) {
                    const el = document.createElement('textarea');
                    el.value = keyToCopy;
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand('copy');
                    document.body.removeChild(el);
                }

                this.copiedModelId = m.id;
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        message: `API key for ${m.name} copied to clipboard!`,
                        type: 'success'
                    }
                }));

                setTimeout(() => {
                    if (this.copiedModelId === m.id) {
                        this.copiedModelId = null;
                    }
                }, 2000);
            },

            async testRowConnection(m) {
                try {
                    const res = await fetch('/admin/ai-models/test-connection', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: m.id,
                            provider: m.provider,
                            model_id: m.model_id,
                            base_url: m.base_url || m.effective_base_url
                        })
                    });

                    const data = await res.json();
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: data.success ? `✓ ${m.name}: ${data.message}` : `✕ ${m.name}: ${data.message}`,
                            type: data.success ? 'success' : 'error'
                        }
                    }));
                } catch (err) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: `✕ ${m.name} connection error: ${err.message}`,
                            type: 'error'
                        }
                    }));
                }
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
