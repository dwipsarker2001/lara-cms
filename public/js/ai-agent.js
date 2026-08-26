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
        abortController: null,
        statusMessage: '',
        activeTab: 'chat', // 'chat' | 'assets'
        availableAssets: [],
        assetsLoading: false,
        undoStack: {},
        showToast: false,
        toastMessage: '',
        toastTimer: null,
        eyeX: 0,
        eyeY: 0,
        targetEyeX: 0,
        targetEyeY: 0,
        eyeScaleY: 1,
        isBlinking: false,
        blinkTimer: null,
        eyeFollowRaf: null,
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

        get floatingStatusText() {
            if (this.statusMessage) {
                return this.statusMessage;
            }
            if (this.isLoading) {
                return 'Thinking...';
            }
            if (this.isProcessingActions) {
                return 'Working...';
            }
            if (this.showToast) {
                return this.toastMessage || 'Done';
            }
            return '';
        },

        get looksLeft() {
            // When avatar is on the right side of the screen, more space is on the left
            const screenCenter = window.innerWidth / 2;
            return (this.posX + 28) >= screenCenter;
        },

        get leftEyePath() {
            if (this.isProcessingActions) {
                return this.looksLeft
                    ? 'M-14 0A8.5 8.5 0 0 1 -5.5 -8.5L5.5 -8.5A8.5 8.5 0 0 1 14 0L14 0A8.5 8.5 0 0 1 5.5 8.5L-5.5 8.5A8.5 8.5 0 0 1 -14 0Z'
                    : 'M-10 -12A10 10 0 0 1 0 -22L0 -22A10 10 0 0 1 10 -12L10 12A10 10 0 0 1 0 22L0 22A10 10 0 0 1 -10 12Z';
            }
            if (this.isLoading) {
                return this.looksLeft
                    ? 'M-10 -9A10 10 0 0 1 0 -19L0 -19A10 10 0 0 1 10 -9L10 9A10 10 0 0 1 0 19L0 19A10 10 0 0 1 -10 9Z'
                    : 'M-12 -11A12 12 0 0 1 0 -23L0 -23A12 12 0 0 1 12 -11L12 11A12 12 0 0 1 0 23L0 23A12 12 0 0 1 -12 11Z';
            }
            if (this.isOpen) {
                return 'M-20 -8A20 20 0 0 1 0 -28L0 -28A20 20 0 0 1 20 -8L20 8A20 20 0 0 1 0 28L0 28A20 20 0 0 1 -20 8Z';
            }
            return 'M-22.5 -1A22.5 22.5 0 0 1 0 -23.5L0 -23.5A22.5 22.5 0 0 1 22.5 -1L22.5 1A22.5 22.5 0 0 1 0 23.5L0 23.5A22.5 22.5 0 0 1 -22.5 1Z';
        },

        get rightEyePath() {
            if (this.isProcessingActions) {
                return this.looksLeft
                    ? 'M-10 -12A10 10 0 0 1 0 -22L0 -22A10 10 0 0 1 10 -12L10 12A10 10 0 0 1 0 22L0 22A10 10 0 0 1 -10 12Z'
                    : 'M-14 0A8.5 8.5 0 0 1 -5.5 -8.5L5.5 -8.5A8.5 8.5 0 0 1 14 0L14 0A8.5 8.5 0 0 1 5.5 8.5L-5.5 8.5A8.5 8.5 0 0 1 -14 0Z';
            }
            if (this.isLoading) {
                return this.looksLeft
                    ? 'M-12 -11A12 12 0 0 1 0 -23L0 -23A12 12 0 0 1 12 -11L12 11A12 12 0 0 1 0 23L0 23A12 12 0 0 1 -12 11Z'
                    : 'M-10 -9A10 10 0 0 1 0 -19L0 -19A10 10 0 0 1 10 -9L10 9A10 10 0 0 1 0 19L0 19A10 10 0 0 1 -10 9Z';
            }
            if (this.isOpen) {
                return 'M-20 -8A20 20 0 0 1 0 -28L0 -28A20 20 0 0 1 20 -8L20 8A20 20 0 0 1 0 28L0 28A20 20 0 0 1 -20 8Z';
            }
            return 'M-22.5 -1A22.5 22.5 0 0 1 0 -23.5L0 -23.5A22.5 22.5 0 0 1 22.5 -1L22.5 1A22.5 22.5 0 0 1 0 23.5L0 23.5A22.5 22.5 0 0 1 -22.5 1Z';
        },

        get eyeBasePos() {
            if (this.isProcessingActions) {
                return this.looksLeft
                    ? { leftX: -8, leftY: -4, rightX: 46, rightY: -9 }
                    : { leftX: -46, leftY: -9, rightX: 8, rightY: -4 };
            }
            if (this.isLoading) {
                return this.looksLeft
                    ? { leftX: -55, leftY: 6, rightX: -3, rightY: 21 }
                    : { leftX: 3, leftY: 21, rightX: 55, rightY: 6 };
            }
            if (this.isOpen) {
                return { leftX: -19, leftY: 22, rightX: 46, rightY: 22 };
            }
            return { leftX: -23, leftY: 2, rightX: 42.5, rightY: 2 };
        },

        init() {
            this.loadSavedPosition();
            this.loadAssets();
            this.initWelcomeMessage();
            this.startEyeTracking();

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

        updateEyeTarget(mouseX, mouseY) {
            const avatarCenterX = this.posX + 28;
            const avatarCenterY = this.posY + 28;
            const dx = mouseX - avatarCenterX;
            const dy = mouseY - avatarCenterY;
            const dist = Math.hypot(dx, dy);

            if (dist < 2) {
                this.targetEyeX = 0;
                this.targetEyeY = 0;
                return;
            }

            const maxRangeX = 14;
            const maxRangeY = 10;
            const angle = Math.atan2(dy, dx);
            const intensity = Math.min(dist / 220, 1);

            this.targetEyeX = Math.cos(angle) * maxRangeX * intensity;
            this.targetEyeY = Math.sin(angle) * maxRangeY * intensity;
        },

        startEyeTracking() {
            // Pointer movement across parent window
            window.addEventListener('pointermove', (e) => {
                this.updateEyeTarget(e.clientX, e.clientY);
            }, { passive: true });

            document.addEventListener('mouseleave', () => {
                this.targetEyeX = 0;
                this.targetEyeY = 0;
            });

            // Support mouse tracking across the live iframe preview
            const attachIframeTracking = () => {
                const iframe = document.getElementById('preview-iframe');
                if (iframe) {
                    try {
                        iframe.contentWindow?.addEventListener('mousemove', (e) => {
                            const rect = iframe.getBoundingClientRect();
                            this.updateEyeTarget(rect.left + e.clientX, rect.top + e.clientY);
                        }, { passive: true });
                    } catch (err) {}
                    iframe.addEventListener('load', () => {
                        try {
                            iframe.contentWindow?.addEventListener('mousemove', (e) => {
                                const rect = iframe.getBoundingClientRect();
                                this.updateEyeTarget(rect.left + e.clientX, rect.top + e.clientY);
                            }, { passive: true });
                        } catch (err) {}
                    });
                }
            };
            attachIframeTracking();

            // Smooth animation loop using requestAnimationFrame + lerp
            const updateEyesLoop = () => {
                let targetX = this.targetEyeX;
                let targetY = this.targetEyeY;

                if (this.isProcessingActions) {
                    const t = Date.now() / 120;
                    targetX = Math.sin(t) * 4;
                    targetY = 4;
                } else if (this.isLoading) {
                    const t = Date.now() / 350;
                    const dir = this.looksLeft ? -1 : 1;
                    targetX = dir * (Math.cos(t) * 6);
                    targetY = -3 + Math.sin(t) * 3;
                }

                const lerp = 0.18;
                this.eyeX += (targetX - this.eyeX) * lerp;
                this.eyeY += (targetY - this.eyeY) * lerp;

                if (Math.abs(targetX - this.eyeX) < 0.01) this.eyeX = targetX;
                if (Math.abs(targetY - this.eyeY) < 0.01) this.eyeY = targetY;

                this.eyeFollowRaf = requestAnimationFrame(updateEyesLoop);
            };
            this.eyeFollowRaf = requestAnimationFrame(updateEyesLoop);

            // Natural organic blinking cycle
            const scheduleBlink = () => {
                const delay = 2600 + Math.random() * 3200;
                this.blinkTimer = setTimeout(() => {
                    if (!this.isLoading && !this.isProcessingActions) {
                        this.isBlinking = true;
                        this.eyeScaleY = 0.08;
                        setTimeout(() => {
                            this.eyeScaleY = 1;
                            this.isBlinking = false;
                            scheduleBlink();
                        }, 130);
                    } else {
                        scheduleBlink();
                    }
                }, delay);
            };
            scheduleBlink();
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

        adjustTextareaHeight(el) {
            const target = el || this.$refs.promptInput;
            if (!target) return;
            target.style.height = 'auto';
            const newHeight = Math.min(target.scrollHeight, 200);
            target.style.height = newHeight + 'px';
            target.style.overflowY = target.scrollHeight > 200 ? 'auto' : 'hidden';
        },

        resetTextareaHeight() {
            if (this.$refs.promptInput) {
                this.$refs.promptInput.style.height = 'auto';
                this.$refs.promptInput.style.overflowY = 'hidden';
            }
        },

        stopGeneration() {
            if (this.abortController) {
                this.abortController.abort();
                this.abortController = null;
            }
            this.isLoading = false;
            this.isProcessingActions = false;
            this.statusMessage = '';
        },

        clearChat() {
            if (confirm('Clear chat history?')) {
                this.messages = [];
                this.prompt = '';
                this.resetTextareaHeight();
                this.initWelcomeMessage();
            }
        },

        useSuggestion(text) {
            this.prompt = text;
            this.adjustTextareaHeight();
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

        buildSectionsDigest(sections) {
            const truncate = (v, len = 60) =>
                typeof v === 'string' && v.length > len ? v.slice(0, len) + '…' : v;

            const compactValue = (v, key) => {
                if (v === null || v === undefined) return null;
                if (Array.isArray(v)) {
                    // Show count + first item's keys with truncated values so AI knows the field structure
                    const sample = v[0] && typeof v[0] === 'object'
                        ? Object.fromEntries(Object.entries(v[0]).filter(([k]) => !k.startsWith('_')).map(([k, sv]) => [k, truncate(sv instanceof Object ? '(object)' : sv)]))
                        : v[0];
                    return { _count: v.length, _fields: sample ?? null };
                }
                if (v && typeof v === 'object') return '{...}';
                return truncate(v);
            };

            return sections.map((sec, i) => {
                const data = sec.data || {};
                const fields = {};
                for (const [k, v] of Object.entries(data)) {
                    if (!k.startsWith('_')) {
                        fields[k] = compactValue(v, k);
                    }
                }
                return { index: i, name: sec.name, enabled: sec.enabled, data: fields };
            });
        },

        schemaHash(schemas) {
            const str = JSON.stringify(Object.keys(schemas).sort());
            let h = 0;
            for (let i = 0; i < str.length; i++) { h = (Math.imul(31, h) + str.charCodeAt(i)) | 0; }
            return h.toString(36);
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
            this.resetTextareaHeight();
            this.isLoading = true;
            this.statusMessage = 'Thinking...';

            this.$nextTick(() => this.scrollToBottom());

            const editor = this.editor;

            // ── Token & Payload Optimisation ─────────────────────────────────────
            // 1. Keep the last 8 turns to maintain context without bloat.
            const allMessages = this.messages.filter(m => m.id !== 'welcome');
            const trimmedMessages = allMessages.slice(-8).map(m => ({ role: m.role, content: m.content }));

            const currentSections = editor ? JSON.parse(JSON.stringify(editor.sections || [])) : [];
            const schemas = editor?.schemas || window.editorSchemas || {};
            const blockList = editor?.blockList || window.editorBlockList || [];
            const entryDataPayload = editor?.entryData || window.editorEntryData || {};

            // 2. Always send schemas and blockList — backend is stateless and cannot
            //    remember them between requests. Sending null causes the AI to lose
            //    knowledge of available blocks and fall back to text-only responses.
            const schemasPayload = schemas;
            const blockListPayload = blockList;

            // 3. Send compact digest of sections for fast, accurate token usage.
            const sectionsDigest = this.buildSectionsDigest(currentSections);

            // 4. Send available collections and entries for link & collection binding
            const rawCols = window.editorAllCollections || (editor?.allCollections) || [];
            const collectionsPayload = (rawCols && rawCols.length > 0) ? rawCols.map(c => ({
                name: c.name,
                slug: c.slug,
                entries: (c.entries || []).map(e => ({ id: e.id, title: e.title, slug: e.slug, route: e.route || `/${c.slug}/${e.slug}` }))
            })) : null;

            // 5. Send active section data so AI can read/edit it accurately.
            const activeSectionIndex = (editor && editor.active !== null && editor.active !== undefined) ? editor.active : null;
            const activeSectionName = (activeSectionIndex !== null && editor.sections[activeSectionIndex]) ? editor.sections[activeSectionIndex].name : null;
            const activeSectionData = (activeSectionIndex !== null && editor.sections[activeSectionIndex]) ? editor.sections[activeSectionIndex].data : null;
            // ─────────────────────────────────────────────────────────────────────

            this.abortController = new AbortController();
            const assistantMsgId = 'asst_' + Date.now();

            try {
                const response = await fetch('/admin/ai/chat', {
                    method: 'POST',
                    signal: this.abortController.signal,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.editorCsrfToken || document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        messages: trimmedMessages,
                        sections: sectionsDigest,
                        full_sections: currentSections,
                        schemas: schemasPayload,
                        blockList: blockListPayload,
                        entryData: entryDataPayload,
                        collections: collectionsPayload,
                        activeSectionIndex,
                        activeSectionName,
                        activeSectionData,
                    })
                });

                if (!response.ok) {
                    const errJson = await response.json().catch(() => ({}));
                    throw new Error(errJson.message || `AI request failed (${response.status})`);
                }

                const data = await response.json();

                if (!data || !data.success) {
                    throw new Error(data?.message || 'Failed to process AI response');
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

                    // 4. Finished actions: clear status, show brief Done tooltip (1.8s)
                    this.statusMessage = '';
                    this.isProcessingActions = false;
                    this.setFloatingToast('Done', 1800);

                    await this.sleep(400);

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
                    // Conversational / advice / general response
                    this.isOpen = true;
                    this.isLoading = false;

                    const existingMsg = this.messages.find(m => m.id === assistantMsgId);
                    if (existingMsg) {
                        existingMsg.content = data.message || existingMsg.content;
                        existingMsg.thought = data.thought || '';
                    } else {
                        const asstMsg = {
                            id: assistantMsgId,
                            role: 'assistant',
                            content: data.message || 'I have analyzed your page. Let me know what you would like to customize!',
                            thought: data.thought || '',
                            actions: [],
                            timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                            canUndo: false,
                            undone: false,
                        };
                        this.messages.push(asstMsg);
                    }

                    this.$nextTick(() => {
                        this.scrollToBottom();
                        const input = this.$refs.promptInput;
                        if (input) input.focus();
                    });
                }

            } catch (err) {
                if (err.name === 'AbortError') {
                    this.isOpen = true;
                    this.messages.push({
                        id: assistantMsgId,
                        role: 'assistant',
                        content: 'Generation stopped.',
                        actions: [],
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                    });
                    this.$nextTick(() => this.scrollToBottom());
                    return;
                }

                console.error('AI chat error:', err);
                this.isOpen = true;
                this.messages.push({
                    id: assistantMsgId,
                    role: 'assistant',
                    content: err.message || 'Unable to connect to the AI service. Please try again.',
                    error: true,
                    actions: [],
                    timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                });
                this.$nextTick(() => this.scrollToBottom());
                this.setFloatingToast('Error', 2200);
            } finally {
                this.abortController = null;
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
                        this.statusMessage = 'Working...';
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
                            editor.focusField('_root', pos, true);
                        }

                        // Animate typing into string fields, and deep copy objects/arrays
                        if (action.data && typeof action.data === 'object') {
                            for (let [key, val] of Object.entries(action.data)) {
                                val = this.cleanFieldValue(key, val);
                                if (typeof val === 'string' && val.length > 0 && !val.startsWith('http') && !val.startsWith('/storage/')) {
                                    this.statusMessage = 'Typing...';
                                    if (typeof editor.focusField === 'function') {
                                        editor.focusField(key, pos, true);
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

                        this.statusMessage = 'Working...';
                        if (typeof editor.focusField === 'function') {
                            editor.focusField('_root', idx, true);
                        }

                        if (action.data && typeof action.data === 'object') {
                            for (let [key, val] of Object.entries(action.data)) {
                                val = this.cleanFieldValue(key, val);
                                if (typeof val === 'string' && val.length > 0 && !val.startsWith('http') && !val.startsWith('/storage/')) {
                                    this.statusMessage = 'Typing...';
                                    if (typeof editor.focusField === 'function') {
                                        editor.focusField(key, idx, true);
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

                        this.statusMessage = 'Typing...';
                        if (typeof editor.focusField === 'function') {
                            editor.focusField(path, idx, true);
                        }

                        // Give smooth scrolling time to settle on both sidebar and iframe
                        await this.sleep(250);

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

                        const imageUrl = action.image_url || action.value || action.url || '';
                        this.statusMessage = 'Working...';
                        this.setNestedValue(editor.sections[idx].data, action.field_path, imageUrl);

                        if (typeof editor.focusField === 'function') {
                            editor.focusField(action.field_path, idx, true);
                        }

                        editor.sections = [...editor.sections];
                        editor.dirty = true;
                        editor.schedulePreview();
                        await this.sleep(200);
                        return 'success';
                    }

                    case 'set_field_source': {
                        const idx = action.section_index;
                        if (idx === undefined || !editor.sections[idx]) return 'not_found';

                        const path = action.field_path;
                        const sourceKey = action.source;

                        if (!editor.sections[idx].data) editor.sections[idx].data = {};
                        if (!editor.sections[idx].data._sources) editor.sections[idx].data._sources = {};
                        editor.sections[idx].data._sources[path] = sourceKey;
                        editor.sections[idx].data = { ...editor.sections[idx].data };
                        editor.sections = [...editor.sections];
                        editor.dirty = true;
                        editor.schedulePreview();
                        await this.sleep(200);
                        return 'success';
                    }

                    case 'remove_section': {
                        const idx = action.section_index;
                        if (idx !== undefined && editor.sections[idx]) {
                            this.statusMessage = 'Working...';
                            editor.removeSection(idx);
                            await this.sleep(200);
                            return 'success';
                        }
                        return 'not_found';
                    }

                    case 'reorder_sections': {
                        if (Array.isArray(action.order)) {
                            this.statusMessage = 'Working...';
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
                            this.statusMessage = 'Working...';
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
                            editor.focusField(path, idx, true);
                            await this.sleep(150);
                            return 'success';
                        }
                        return 'skipped';
                    }

                    case 'save_page': {
                        this.statusMessage = 'Working...';
                        if (typeof editor.save === 'function') {
                            await editor.save();
                            return 'success';
                        }
                        return 'skipped';
                    }

                    case 'add_list_item': {
                        // Appends a new item to a list field, optionally populated with data.
                        // action.section_index: section to target
                        // action.list_path: dot-path to the list (e.g. "itinerary", "highlights", "faqs")
                        // action.data: object with field values for the new item (optional)
                        const idx = action.section_index;
                        if (idx === undefined || !editor.sections[idx]) return 'not_found';

                        const listPath = action.list_path;
                        if (!listPath) return 'failed';

                        this.statusMessage = 'Working...';

                        // Navigate to the list using the path
                        const tokens = this.normalizePathSegments(listPath);
                        let obj = editor.sections[idx].data;
                        for (let i = 0; i < tokens.length - 1; i++) {
                            if (!obj[tokens[i]] || typeof obj[tokens[i]] !== 'object') obj[tokens[i]] = {};
                            obj = obj[tokens[i]];
                        }
                        const listKey = tokens[tokens.length - 1];
                        if (!Array.isArray(obj[listKey])) obj[listKey] = [];

                        // Build new item: use provided data merged with a fresh _key
                        const newItem = {
                            _key: (crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2)),
                            ...(action.data && typeof action.data === 'object' ? JSON.parse(JSON.stringify(action.data)) : {}),
                        };
                        obj[listKey] = [...obj[listKey], newItem];

                        editor.ensureSectionKeys();
                        editor.sections = [...editor.sections];
                        editor.dirty = true;
                        editor.schedulePreview();
                        editor.$nextTick(() => editor.initListSortables?.());

                        // Typewrite any string values in the new item
                        if (action.data && typeof action.data === 'object') {
                            const itemIndex = obj[listKey].length - 1;
                            for (const [key, val] of Object.entries(action.data)) {
                                if (typeof val === 'string' && val.length > 0 && !val.startsWith('http') && !val.startsWith('/storage/')) {
                                    this.statusMessage = 'Typing...';
                                    const nestedPath = `${listPath}.${itemIndex}.${key}`;
                                    await this.typewriteNestedField(editor.sections[idx].data, nestedPath, val);
                                }
                            }
                        }

                        editor.sections = [...editor.sections];
                        editor.schedulePreview();
                        return 'success';
                    }

                    case 'remove_list_item': {
                        // Removes a specific item from a list field by index.
                        // action.section_index: section to target
                        // action.list_path: dot-path to the list (e.g. "itinerary", "faqs")
                        // action.index: 0-based index of the item to remove
                        const idx = action.section_index;
                        if (idx === undefined || !editor.sections[idx]) return 'not_found';

                        const listPath = action.list_path;
                        const removeIndex = action.index;
                        if (!listPath || removeIndex === undefined) return 'failed';

                        this.statusMessage = 'Working...';

                        const tokens = this.normalizePathSegments(listPath);
                        let obj = editor.sections[idx].data;
                        for (let i = 0; i < tokens.length - 1; i++) {
                            if (!obj[tokens[i]]) return 'not_found';
                            obj = obj[tokens[i]];
                        }
                        const listKey = tokens[tokens.length - 1];
                        if (!Array.isArray(obj[listKey]) || obj[listKey][removeIndex] === undefined) return 'not_found';

                        obj[listKey] = obj[listKey].filter((_, i) => i !== removeIndex);
                        editor.ensureSectionKeys();
                        editor.sections = [...editor.sections];
                        editor.dirty = true;
                        editor.schedulePreview();
                        await this.sleep(150);
                        return 'success';
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
            // Smooth pacing: ~25-30 frames for a satisfying, visible typewriter effect (~400-600ms total)
            const step = Math.max(1, Math.ceil(length / 28));
            const delay = 20;

            for (let i = 0; i <= length; i += step) {
                const slice = fullText.slice(0, i);
                targetObj[key] = slice;
                if (editor && typeof editor.syncPreviewField === 'function') {
                    editor.syncPreviewField(key, slice);
                }
                await this.sleep(delay);
            }
            targetObj[key] = fullText;
            if (editor) {
                if (typeof editor.syncPreviewField === 'function') {
                    editor.syncPreviewField(key, fullText);
                }
                editor.schedulePreview();
            }
        },

        async typewriteNestedField(obj, path, fullText) {
            const editor = this.editor;
            const length = fullText.length;
            const step = Math.max(1, Math.ceil(length / 28));
            const delay = 20;

            const tokens = this.normalizePathSegments(path);
            const leaf = tokens[tokens.length - 1] || path;

            for (let i = 0; i <= length; i += step) {
                const slice = fullText.slice(0, i);
                this.setNestedValue(obj, path, slice);
                if (editor && typeof editor.syncPreviewField === 'function') {
                    editor.syncPreviewField(leaf, slice);
                }
                await this.sleep(delay);
            }
            this.setNestedValue(obj, path, fullText);
            if (editor) {
                if (typeof editor.syncPreviewField === 'function') {
                    editor.syncPreviewField(leaf, fullText);
                }
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
            this.setFloatingToast('Reverted', 1800);
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
                    this.setFloatingToast('Applied', 1800);
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

        normalizePathSegments(path) {
            if (!path) return [];
            const cleaned = String(path)
                .replace(/\[(\w+)\]/g, '.$1')
                .replace(/[:\/]/g, '.')
                .replace(/^\.+|\.+$/g, '');
            return cleaned.split('.').filter(Boolean);
        },

        setNestedValue(obj, path, value) {
            if (!path || !obj || typeof obj !== 'object') return;
            const tokens = this.normalizePathSegments(path);
            if (tokens.length === 0) return;

            let current = obj;
            for (let i = 0; i < tokens.length - 1; i++) {
                const token = tokens[i];
                const nextToken = tokens[i + 1];
                const nextIsIndex = /^\d+$/.test(nextToken);

                if (current[token] === undefined || current[token] === null || typeof current[token] !== 'object') {
                    current[token] = nextIsIndex ? [] : {};
                }
                if (nextIsIndex && !Array.isArray(current[token])) {
                    current[token] = [];
                }
                if (nextIsIndex) {
                    const idx = parseInt(nextToken, 10);
                    while (current[token].length <= idx) {
                        current[token].push({});
                    }
                }
                current = current[token];
            }

            const leaf = tokens[tokens.length - 1];
            current[leaf] = value;
        },

        getNestedValue(obj, path) {
            if (!path || !obj || typeof obj !== 'object') return undefined;
            const tokens = this.normalizePathSegments(path);
            let current = obj;
            for (const token of tokens) {
                if (current === undefined || current === null) return undefined;
                current = current[token];
            }
            return current;
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
