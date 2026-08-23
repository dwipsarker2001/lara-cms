/**
 * Lara-CMS Autonomous AI Agent Controller
 * Integrates DeepSeek V4 Flash with Lara-CMS Visual Editor & Live Typewriter Animations
 */
function aiAgent() {
    return {
        isOpen: false,
        isExpanded: false,
        isDragging: false,
        dragMoved: false,
        dragStartX: 0,
        dragStartY: 0,
        initialPosX: 0,
        initialPosY: 0,
        posX: 0,
        posY: 0,
        prompt: '',
        messages: [],
        isLoading: false,
        isProcessingActions: false,
        statusMessage: '',
        activeTab: 'chat', // 'chat' | 'assets'
        availableAssets: [],
        assetsLoading: false,
        undoStack: {},
        showToast: false,
        toastMessage: '',
        toastTimer: null,
        suggestions: [
            '🎯 Optimize hook & headlines (3 angles)',
            '✍️ Rewrite section copy for high conversion',
            '✨ Generate a modern Hero section with CTA',
            '🖼️ Auto-pick & assign relevant images to blocks',
            '👥 Add Team Members with roles and avatars',
            '💬 Add Client Testimonials block',
            '❓ Add FAQ Section with questions & answers',
            '💾 Save & Publish current page',
        ],

        get avatarSrc() {
            // Content editing mode (typewriter applying changes into editor):
            if (this.isProcessingActions) {
                return '/images/ai-agent-editing.svg';
            }
            // Active AI response thinking/generating:
            if (this.isLoading) {
                return '/images/ai-agent-curious.svg';
            }
            if (this.isOpen) {
                return '/images/ai-agent-excited.svg';
            }
            return '/images/ai-agent-idle.svg';
        },

        init() {
            this.loadSavedPosition();
            this.loadAssets();
            this.initWelcomeMessage();

            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && (e.key === ' ' || e.key.toLowerCase() === 'j')) {
                    e.preventDefault();
                    this.toggleChat();
                } else if (e.key === 'Escape' && this.isOpen) {
                    this.closeChat();
                }
            });

            window.addEventListener('resize', () => {
                this.clampPosition();
            });

            // Global safety net for pointer releases anywhere
            window.addEventListener('pointerup', (e) => {
                if (this.isDragging) {
                    this.endDrag(e);
                }
            });
            window.addEventListener('pointercancel', (e) => {
                if (this.isDragging) {
                    this.endDrag(e);
                }
            });
        },

        loadSavedPosition() {
            const savedX = localStorage.getItem('lara_cms_ai_pos_x');
            const savedY = localStorage.getItem('lara_cms_ai_pos_y');

            const vpWidth = window.innerWidth;
            const vpHeight = window.innerHeight;

            if (savedX !== null && savedY !== null) {
                this.posX = Math.max(12, Math.min(vpWidth - 84, parseInt(savedX, 10)));
                this.posY = Math.max(12, Math.min(vpHeight - 84, parseInt(savedY, 10)));
            } else {
                // Default: bottom right corner
                this.posX = Math.max(12, vpWidth - 100);
                this.posY = Math.max(12, vpHeight - 110);
            }
        },

        savePosition() {
            localStorage.setItem('lara_cms_ai_pos_x', this.posX);
            localStorage.setItem('lara_cms_ai_pos_y', this.posY);
        },

        clampPosition() {
            const vpWidth = window.innerWidth;
            const vpHeight = window.innerHeight;
            this.posX = Math.max(12, Math.min(vpWidth - 84, this.posX));
            this.posY = Math.max(12, Math.min(vpHeight - 84, this.posY));
            this.savePosition();
        },

        initWelcomeMessage() {
            if (this.messages.length === 0) {
                this.messages.push({
                    id: 'welcome',
                    role: 'assistant',
                    content: `How can I help you with this page?

You can ask me to draft or polish copy, add or reorganize sections, or find and place images from your media library.`,
                    actions: [],
                    timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                });
            }
        },

        async loadAssets() {
            this.assetsLoading = true;
            try {
                const res = await fetch('/admin/ai/assets', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    this.availableAssets = data.assets || [];
                }
            } catch (e) {
                console.warn('AI Agent asset load error:', e);
            } finally {
                this.assetsLoading = false;
            }
        },

        startDrag(e) {
            // Only primary button
            if (e.pointerType === 'mouse' && e.button !== 0) return;

            this.isDragging = true;
            this.dragMoved = false;
            this.dragStartX = e.clientX;
            this.dragStartY = e.clientY;
            this.initialPosX = this.posX;
            this.initialPosY = this.posY;

            // Capture all pointer events directly to this element
            if (e.currentTarget && typeof e.currentTarget.setPointerCapture === 'function') {
                try {
                    e.currentTarget.setPointerCapture(e.pointerId);
                } catch (err) {}
            }

            // Disable pointer events on all iframes so iframe never captures cursor or mouseup
            document.querySelectorAll('iframe').forEach(f => {
                f._prevPointerEvents = f.style.pointerEvents;
                f.style.pointerEvents = 'none';
            });

            document.body.style.userSelect = 'none';
            document.body.style.cursor = 'grabbing';
        },

        onDragMove(e) {
            if (!this.isDragging) return;

            const dx = e.clientX - this.dragStartX;
            const dy = e.clientY - this.dragStartY;

            if (Math.abs(dx) > 5 || Math.abs(dy) > 5) {
                this.dragMoved = true;
            }

            const vpWidth = window.innerWidth;
            const vpHeight = window.innerHeight;

            this.posX = Math.max(12, Math.min(vpWidth - 84, this.initialPosX + dx));
            this.posY = Math.max(12, Math.min(vpHeight - 84, this.initialPosY + dy));
        },

        endDrag(e) {
            if (!this.isDragging) return;

            this.isDragging = false;
            this.savePosition();

            // Release pointer capture
            if (e && e.currentTarget && typeof e.currentTarget.hasPointerCapture === 'function' && e.currentTarget.hasPointerCapture(e.pointerId)) {
                try {
                    e.currentTarget.releasePointerCapture(e.pointerId);
                } catch (err) {}
            }

            // Restore iframes pointer events
            document.querySelectorAll('iframe').forEach(f => {
                f.style.pointerEvents = f._prevPointerEvents || '';
            });

            document.body.style.userSelect = '';
            document.body.style.cursor = '';

            // If pointer did not move significantly, treat as click to open/toggle chatbox
            if (!this.dragMoved) {
                this.toggleChat();
            }
        },

        handleAvatarClick() {
            // Handled directly in endDrag if not moved
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                    const input = this.$refs.promptInput;
                    if (input) input.focus();
                });
            }
        },

        openChat() {
            this.isOpen = true;
            this.$nextTick(() => {
                this.scrollToBottom();
                const input = this.$refs.promptInput;
                if (input) input.focus();
            });
        },

        closeChat() {
            this.isOpen = false;
        },

        toggleExpand() {
            this.isExpanded = !this.isExpanded;
        },

        clearChat() {
            if (confirm('Clear chat history?')) {
                this.messages = [];
                this.initWelcomeMessage();
            }
        },

        useSuggestion(text) {
            this.prompt = text;
            this.sendMessage();
        },

        get editor() {
            return window.__pageEditor || null;
        },

        scrollToBottom() {
            const container = this.$refs.chatContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        setFloatingToast(message, duration = 4000) {
            this.toastMessage = message;
            this.showToast = true;
            clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => {
                this.showToast = false;
            }, duration);
        },

        async sendMessage() {
            const text = this.prompt.trim();
            if (!text || this.isLoading || this.isProcessingActions) return;

            const userMsgId = 'msg_' + Date.now();
            this.messages.push({
                id: userMsgId,
                role: 'user',
                content: text,
                timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            });

            this.prompt = '';
            this.isLoading = true;
            this.statusMessage = 'Thinking & Crafting...';

            this.$nextTick(() => this.scrollToBottom());

            const editor = this.editor;
            const currentSections = editor ? JSON.parse(JSON.stringify(editor.sections || [])) : [];
            const schemas = editor?.schemas || window.editorSchemas || {};
            const blockList = editor?.blockList || window.editorBlockList || [];
            const entryData = editor?.entryData || window.editorEntryData || {};

            const payloadMessages = this.messages
                .filter(m => m.id !== 'welcome')
                .map(m => ({
                    role: m.role,
                    content: m.content
                }));

            const assistantMsgId = 'asst_' + Date.now();

            const activeSectionIndex = (editor && editor.active !== null && editor.active !== undefined) ? editor.active : null;
            const activeSectionName = (activeSectionIndex !== null && editor.sections[activeSectionIndex]) ? editor.sections[activeSectionIndex].name : null;
            const activeSectionData = (activeSectionIndex !== null && editor.sections[activeSectionIndex]) ? editor.sections[activeSectionIndex].data : null;

            try {
                const response = await fetch('/admin/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.editorCsrfToken || document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        messages: payloadMessages,
                        sections: currentSections,
                        schemas: schemas,
                        blockList: blockList,
                        entryData: entryData,
                        assets: this.availableAssets,
                        activeSectionIndex: activeSectionIndex,
                        activeSectionName: activeSectionName,
                        activeSectionData: activeSectionData
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'AI request failed');
                }

                const hasActions = data.actions && Array.isArray(data.actions) && data.actions.length > 0;

                if (hasActions) {
                    // 1. Auto-close modal
                    this.isOpen = false;

                    // 2. Wait for modal fade-out to finish (300ms) so user sees work start in clear view!
                    await this.sleep(300);

                    // Save snapshot for undo capability before applying actions
                    if (editor) {
                        this.undoStack[assistantMsgId] = JSON.parse(JSON.stringify(editor.sections));
                    }

                    this.isProcessingActions = true;
                    const executedActions = [];

                    // 3. Execute typewriter actions
                    for (const act of data.actions) {
                        const status = await this.executeActionWithTypewriter(act);
                        executedActions.push({ ...act, executionStatus: status });
                    }

                    // 4. Finished actions: clear status, show toast, and small crisp delay (500ms)
                    this.statusMessage = '';
                    this.isProcessingActions = false;
                    this.setFloatingToast('✨ ' + (data.message || 'Changes applied successfully!'), 4000);

                    await this.sleep(500);

                    // 5. Reopen chat modal promptly!
                    this.isOpen = true;

                    const asstMsg = {
                        id: assistantMsgId,
                        role: 'assistant',
                        content: '',
                        thought: data.thought || '',
                        actions: executedActions,
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                        canUndo: executedActions.length > 0,
                        undone: false,
                    };
                    this.messages.push(asstMsg);

                    this.$nextTick(() => {
                        this.scrollToBottom();
                        const input = this.$refs.promptInput;
                        if (input) input.focus();
                    });

                    await this.typewriteChatMessage(assistantMsgId, data.message || 'I have completed the requested changes.');

                } else {
                    // Conversational / advice / general response: keep chat window open and typewrite response!
                    this.isOpen = true;
                    this.isLoading = false; // Hide thinking indicator as text starts writing

                    const asstMsg = {
                        id: assistantMsgId,
                        role: 'assistant',
                        content: '',
                        thought: data.thought || '',
                        actions: [],
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                        canUndo: false,
                        undone: false,
                    };
                    this.messages.push(asstMsg);

                    await this.typewriteChatMessage(assistantMsgId, data.message || 'How else can I help?');

                    this.$nextTick(() => {
                        this.scrollToBottom();
                        const input = this.$refs.promptInput;
                        if (input) input.focus();
                    });
                }

            } catch (err) {
                console.error('AI chat error:', err);
                this.isOpen = true;
                this.messages.push({
                    id: assistantMsgId,
                    role: 'assistant',
                    content: `⚠️ **Error:** ${err.message || 'Unable to connect to AI agent.'}`,
                    error: true,
                    actions: [],
                    timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                });
                this.$nextTick(() => this.scrollToBottom());
                this.setFloatingToast('⚠️ AI Error: ' + (err.message || 'Failed'), 4000);
            } finally {
                this.isLoading = false;
                this.isProcessingActions = false;
                this.statusMessage = '';
            }
        },

        async executeActionWithTypewriter(action) {
            const editor = this.editor;
            if (!editor) {
                console.warn('Page editor instance not available for action:', action);
                return 'skipped';
            }

            try {
                switch (action.action) {
                    case 'add_section': {
                        const blockName = action.name;
                        this.statusMessage = `Adding block: ${blockName}...`;
                        const defaultSec = editor.createDefault(blockName);
                        if (!defaultSec) {
                            console.warn(`Block schema not found for "${blockName}"`);
                            return 'failed';
                        }

                        const pos = (typeof action.position === 'number' && action.position >= 0)
                            ? Math.min(action.position, editor.sections.length)
                            : editor.sections.length;

                        // Insert empty/default skeleton section first
                        editor.sections.splice(pos, 0, defaultSec);
                        editor.sections = [...editor.sections];
                        editor.dirty = true;
                        editor.schedulePreview();
                        editor.$nextTick(() => editor.initSectionSortable());

                        // Focus this new section in sidebar
                        if (typeof editor.focusField === 'function') {
                            editor.focusField('_root', pos);
                        }

                        // Animate typing into string fields, and deep copy objects/arrays
                        if (action.data && typeof action.data === 'object') {
                            for (let [key, val] of Object.entries(action.data)) {
                                val = this.cleanFieldValue(key, val);
                                if (typeof val === 'string' && val.length > 0 && !val.startsWith('http') && !val.startsWith('/storage/')) {
                                    this.statusMessage = `Typing ${key}...`;
                                    if (typeof editor.focusField === 'function') {
                                        editor.focusField(key, pos);
                                    }
                                    await this.typewriteField(editor.sections[pos].data, key, val);
                                } else {
                                    editor.sections[pos].data[key] = (typeof val === 'object' && val !== null) ? JSON.parse(JSON.stringify(val)) : val;
                                    editor.schedulePreview();
                                }
                            }
                        }

                        editor.ensureSectionKeys();
                        editor.sections = [...editor.sections];
                        editor.schedulePreview();
                        return 'success';
                    }

                    case 'update_section': {
                        const idx = action.section_index;
                        if (idx === undefined || !editor.sections[idx]) return 'not_found';

                        this.statusMessage = `Updating section #${idx + 1}...`;
                        if (typeof editor.focusField === 'function') {
                            editor.focusField('_root', idx);
                        }

                        if (action.data && typeof action.data === 'object') {
                            for (let [key, val] of Object.entries(action.data)) {
                                val = this.cleanFieldValue(key, val);
                                if (typeof val === 'string' && val.length > 0 && !val.startsWith('http') && !val.startsWith('/storage/')) {
                                    this.statusMessage = `Writing ${key}...`;
                                    if (typeof editor.focusField === 'function') {
                                        editor.focusField(key, idx);
                                    }
                                    await this.typewriteField(editor.sections[idx].data, key, val);
                                } else {
                                    editor.sections[idx].data[key] = (typeof val === 'object' && val !== null) ? JSON.parse(JSON.stringify(val)) : val;
                                    editor.schedulePreview();
                                }
                            }
                        }

                        editor.ensureSectionKeys();
                        editor.sections = [...editor.sections];
                        editor.dirty = true;
                        editor.schedulePreview();
                        return 'success';
                    }

                    case 'update_field': {
                        const idx = action.section_index;
                        if (idx === undefined || !editor.sections[idx]) return 'not_found';

                        const path = action.field_path;
                        let val = this.cleanFieldValue(path, action.value);

                        this.statusMessage = `Writing ${path}...`;
                        if (typeof editor.focusField === 'function') {
                            editor.focusField(path, idx);
                        }

                        if (typeof val === 'string' && val.length > 0 && !val.startsWith('http') && !val.startsWith('/storage/')) {
                            await this.typewriteNestedField(editor.sections[idx].data, path, val);
                        } else {
                            this.setNestedValue(editor.sections[idx].data, path, val);
                        }

                        editor.sections = [...editor.sections];
                        editor.dirty = true;
                        editor.schedulePreview();
                        return 'success';
                    }

                    case 'set_image': {
                        const idx = action.section_index;
                        if (idx === undefined || !editor.sections[idx]) return 'not_found';

                        this.statusMessage = `Setting image for ${action.field_path || 'block'}...`;
                        this.setNestedValue(editor.sections[idx].data, action.field_path, action.image_url);

                        if (typeof editor.focusField === 'function') {
                            editor.focusField(action.field_path, idx);
                        }

                        editor.sections = [...editor.sections];
                        editor.dirty = true;
                        editor.schedulePreview();
                        await this.sleep(200);
                        return 'success';
                    }

                    case 'remove_section': {
                        const idx = action.section_index;
                        if (idx !== undefined && editor.sections[idx]) {
                            this.statusMessage = `Removing section #${idx + 1}...`;
                            editor.removeSection(idx);
                            await this.sleep(200);
                            return 'success';
                        }
                        return 'not_found';
                    }

                    case 'reorder_sections': {
                        if (Array.isArray(action.order)) {
                            this.statusMessage = 'Reordering sections...';
                            const newSections = [];
                            for (const i of action.order) {
                                if (editor.sections[i]) newSections.push(editor.sections[i]);
                            }
                            if (newSections.length > 0) {
                                editor.sections = newSections;
                                editor.dirty = true;
                                editor.schedulePreview();
                                editor.$nextTick(() => editor.initSectionSortable());
                                await this.sleep(200);
                                return 'success';
                            }
                        }
                        return 'failed';
                    }

                    case 'replace_all_sections': {
                        if (Array.isArray(action.sections)) {
                            this.statusMessage = 'Applying new page layout...';
                            editor.sections = JSON.parse(JSON.stringify(action.sections));
                            editor.ensureSectionKeys();
                            editor.dirty = true;
                            editor.schedulePreview();
                            editor.$nextTick(() => editor.initSectionSortable());
                            await this.sleep(300);
                            return 'success';
                        }
                        return 'failed';
                    }

                    case 'navigate_to_field': {
                        const idx = action.section_index ?? 0;
                        const path = action.field_path || '_root';
                        if (typeof editor.focusField === 'function') {
                            editor.focusField(path, idx);
                            await this.sleep(150);
                            return 'success';
                        }
                        return 'skipped';
                    }

                    case 'save_page': {
                        this.statusMessage = 'Saving & Publishing page...';
                        if (typeof editor.save === 'function') {
                            await editor.save();
                            return 'success';
                        }
                        return 'skipped';
                    }

                    default:
                        console.info('Unhandled AI action:', action);
                        return 'unhandled';
                }
            } catch (e) {
                console.error('Error executing AI action with typewriter:', e, action);
                return 'error';
            }
        },

        async typewriteChatMessage(messageId, fullText) {
            if (!fullText) return;

            // Split into words / whitespace tokens so formatting and words stream smoothly without breaking
            const tokens = fullText.match(/\S+|\s+/g) || [fullText];
            let accumulated = '';

            const findMsg = () => this.messages.find(m => m.id === messageId);
            const tokenBatch = tokens.length > 80 ? 3 : (tokens.length > 30 ? 2 : 1);
            const delay = 18;

            for (let i = 0; i < tokens.length; i += tokenBatch) {
                const chunk = tokens.slice(i, i + tokenBatch).join('');
                accumulated += chunk;

                const msg = findMsg();
                if (msg) {
                    msg.content = accumulated;
                    this.scrollToBottom();
                }
                await this.sleep(delay);
            }

            const finalMsg = findMsg();
            if (finalMsg) {
                finalMsg.content = fullText;
                this.scrollToBottom();
            }
        },

        async typewriteField(targetObj, key, fullText) {
            const editor = this.editor;
            const length = fullText.length;
            // Snappy adaptive step: 10-15 frames max per field so animations feel brisk and responsive
            const step = Math.max(1, Math.ceil(length / 15));
            const delay = 12;

            for (let i = 0; i <= length; i += step) {
                targetObj[key] = fullText.slice(0, i);
                if (editor) {
                    editor.schedulePreview();
                }
                await this.sleep(delay);
            }
            targetObj[key] = fullText;
            if (editor) {
                editor.schedulePreview();
            }
        },

        async typewriteNestedField(obj, path, fullText) {
            const editor = this.editor;
            const length = fullText.length;
            const step = Math.max(1, Math.ceil(length / 15));
            const delay = 12;

            for (let i = 0; i <= length; i += step) {
                this.setNestedValue(obj, path, fullText.slice(0, i));
                if (editor) {
                    editor.schedulePreview();
                }
                await this.sleep(delay);
            }
            this.setNestedValue(obj, path, fullText);
            if (editor) {
                editor.schedulePreview();
            }
        },

        sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },

        undoMessage(messageId) {
            const editor = this.editor;
            const snapshot = this.undoStack[messageId];
            if (!editor || !snapshot) {
                alert('Undo snapshot not found.');
                return;
            }

            editor.sections = JSON.parse(JSON.stringify(snapshot));
            editor.ensureSectionKeys();
            editor.dirty = true;
            editor.active = null;
            editor.crumbs = [];
            editor.$nextTick(() => {
                editor.initSectionSortable();
                editor.refreshPreview();
            });

            const msg = this.messages.find(m => m.id === messageId);
            if (msg) {
                msg.undone = true;
            }
            this.setFloatingToast('↺ Changes reverted to previous state', 3000);
        },

        insertAssetIntoActive(asset) {
            const editor = this.editor;
            if (!editor) return;

            if (editor.active !== null && editor.sections[editor.active]) {
                const sec = editor.sections[editor.active];
                const schema = editor.schemas[sec.name] || [];
                const imgField = schema.find(f => f.type === 'image');
                if (imgField) {
                    sec.data[imgField.name] = asset.url;
                    editor.sections = [...editor.sections];
                    editor.dirty = true;
                    editor.schedulePreview();
                    this.closeChat();
                    editor.focusField(imgField.name, editor.active);
                    this.setFloatingToast(`🖼️ Image "${asset.name}" applied to active section!`);
                    return;
                }
            }

            this.activeTab = 'chat';
            this.prompt = `Use image "${asset.name}" (${asset.url}) for the current section.`;
            this.sendMessage();
        },

        navigateTo(secIdx, fieldPath) {
            const editor = this.editor;
            if (!editor) return;
            if (typeof editor.focusField === 'function') {
                editor.focusField(fieldPath || '_root', secIdx);
                if (!this.isExpanded) {
                    this.closeChat();
                }
            }
        },

        cleanFieldValue(key, val) {
            if (typeof val === 'string' && typeof key === 'string' && (/price|amount|cost/i).test(key)) {
                // Strip currency symbols (e.g. $, €, £, ¥, etc.) and trim
                return val.replace(/^[\$€£¥₹\s]+/, '').trim();
            }
            return val;
        },

        deepMerge(target, source) {
            const output = { ...target };
            if (this.isObject(target) && this.isObject(source)) {
                Object.keys(source).forEach(key => {
                    if (this.isObject(source[key])) {
                        if (!(key in target)) {
                            output[key] = source[key];
                        } else {
                            output[key] = this.deepMerge(target[key], source[key]);
                        }
                    } else {
                        output[key] = source[key];
                    }
                });
            }
            return output;
        },

        isObject(item) {
            return (item && typeof item === 'object' && !Array.isArray(item));
        },

        setNestedValue(obj, path, value) {
            if (!path || !obj) return;
            const segments = path.split('/');
            let current = obj;

            for (let i = 0; i < segments.length - 1; i++) {
                const seg = segments[i];
                if (seg.includes(':')) {
                    const [listName, idxStr] = seg.split(':');
                    const idx = parseInt(idxStr, 10);
                    if (!Array.isArray(current[listName])) current[listName] = [];
                    while (current[listName].length <= idx) {
                        current[listName].push({});
                    }
                    current = current[listName][idx];
                } else {
                    if (!current[seg] || typeof current[seg] !== 'object') {
                        current[seg] = {};
                    }
                    current = current[seg];
                }
            }

            const leaf = segments[segments.length - 1];
            if (leaf.includes(':')) {
                const [listName, idxStr] = leaf.split(':');
                const idx = parseInt(idxStr, 10);
                if (Array.isArray(current[listName])) {
                    current[listName][idx] = value;
                }
            } else {
                current[leaf] = value;
            }
        },

        formatMarkdown(text) {
            if (!text) return '';
            let html = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            // Headers
            html = html.replace(/^### (.*$)/gim, '<h4 class="font-bold text-sm text-gray-900 mt-2 mb-1">$1</h4>');
            html = html.replace(/^## (.*$)/gim, '<h3 class="font-bold text-base text-gray-900 mt-2.5 mb-1">$1</h3>');
            html = html.replace(/^# (.*$)/gim, '<h2 class="font-bold text-lg text-gray-900 mt-3 mb-1.5">$1</h2>');

            // Bold & Italics
            html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-semibold text-gray-900">$1</strong>');
            html = html.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');

            // Inline Code
            html = html.replace(/`([^`]+)`/g, '<code class="px-1.5 py-0.5 rounded bg-gray-100 text-primary font-mono text-[11px] border border-gray-200">$1</code>');

            // Lists
            html = html.replace(/^\s*-\s+(.*$)/gim, '<li class="flex items-start gap-1.5 text-xs text-gray-700 my-0.5"><span class="text-primary font-bold">•</span><span>$1</span></li>');

            // Line breaks
            html = html.replace(/\n/g, '<br>');

            return html;
        }
    };
}

document.addEventListener('alpine:init', function () {
    if (window.Alpine) {
        window.Alpine.data('aiAgent', aiAgent);
    }
});
