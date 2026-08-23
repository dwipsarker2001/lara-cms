{{-- =========================================================================
     Lara-CMS Autonomous Interactive AI Agent (Super AI Style)
     Clean Minimalist UI: Seamless Borderless Input, Theme Send Button, Lucide Icons
     ========================================================================= --}}
<div
    x-data="aiAgent()"
    x-init="init()"
    class="ai-agent-system font-sans select-none"
>
    <style>
        .ai-chat-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(209, 213, 219, 0.7) transparent;
        }
        .ai-chat-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .ai-chat-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .ai-chat-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(209, 213, 219, 0.7);
            border-radius: 9999px;
        }
        .ai-chat-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.9);
        }
    </style>
    {{-- ===================================================
         1. Frosted Backdrop Overlay (Smooth Blur)
    =================================================== --}}
    <div
        x-show="isOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 backdrop-blur-none"
        x-transition:enter-end="opacity-100 backdrop-blur-sm"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 backdrop-blur-sm"
        x-transition:leave-end="opacity-0 backdrop-blur-none"
        class="fixed inset-0 z-[110] bg-black/20 backdrop-blur-sm"
        @click="closeChat()"
    ></div>

    {{-- ===================================================
         2. Floating Draggable Creature Avatar (Always Visible)
    =================================================== --}}
    <div
        class="fixed z-[125] group"
        :style="`left: ${posX}px; top: ${posY}px; touch-action: none;`"
        x-show="!isOpen || isDragging"
        x-cloak
    >
        {{-- Floating Speech Bubble / Live Status Typewriter Pill --}}
        <div
            x-show="isLoading || isProcessingActions || statusMessage"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
            class="absolute -top-11 left-1/2 -translate-x-1/2 whitespace-nowrap px-3 py-1 rounded-full bg-white text-gray-900 text-xs font-medium shadow-md border border-gray-150 flex items-center gap-1.5 pointer-events-none z-10"
        >
            <span class="inline-flex items-center gap-0.5">
                <span class="size-1.5 rounded-full bg-primary animate-bounce [animation-delay:-0.3s]"></span>
                <span class="size-1.5 rounded-full bg-primary animate-bounce [animation-delay:-0.15s]"></span>
                <span class="size-1.5 rounded-full bg-primary animate-bounce"></span>
            </span>
            <span class="text-gray-700 font-medium" x-text="statusMessage || 'Thinking...'"></span>
            <span class="inline-block w-1 h-3 bg-primary animate-pulse ml-0.5"></span>
        </div>

        {{-- Toast Bubble on completion --}}
        <div
            x-show="showToast && !isLoading && !isProcessingActions"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="absolute -top-12 left-1/2 -translate-x-1/2 whitespace-nowrap px-3 py-1.5 rounded-xl bg-white text-gray-900 text-xs font-semibold shadow-lg border border-emerald-500/30 flex items-center gap-1.5 pointer-events-auto cursor-pointer"
            @click="openChat()"
        >
            <span class="size-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span class="text-gray-800 font-medium" x-text="toastMessage"></span>
        </div>

        {{-- Floating circular avatar button (Snug Minimal Base, Zero Hover Popups) --}}
        <div
            @pointerdown="startDrag($event)"
            @pointermove="onDragMove($event)"
            @pointerup="endDrag($event)"
            @pointercancel="endDrag($event)"
            class="relative size-13 sm:size-14 rounded-full bg-white p-0.5 shadow-[0_4px_16px_rgba(0,0,0,0.1),0_0_0_1px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_24px_rgba(0,0,0,0.14)] active:scale-95 transition-all duration-200 cursor-grab active:cursor-grabbing flex items-center justify-center overflow-visible select-none"
            style="touch-action: none; -webkit-user-drag: none; user-select: none;"
            :class="(isLoading || isProcessingActions) ? 'ring-2 ring-primary/20 animate-pulse' : ''"
        >
            {{-- SVG Animated Creature --}}
            <img
                :src="avatarSrc"
                alt="AI Agent Avatar"
                class="size-full object-contain pointer-events-none select-none transition-transform duration-300 overflow-visible"
                :class="(isLoading || isProcessingActions) ? 'scale-105' : ''"
                draggable="false"
            />
        </div>
    </div>

    {{-- ===================================================
         3. Super AI Modal Window
    =================================================== --}}
    <div
        x-show="isOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="fixed z-[120] inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 flex flex-col bg-white rounded-2xl shadow-[0_16px_48px_rgba(0,0,0,0.12),0_0_0_1px_rgba(0,0,0,0.06)] border border-gray-100 overflow-hidden transition-all duration-200"
        :class="isExpanded ? 'sm:w-[90vw] sm:max-w-4xl sm:h-[85vh]' : 'sm:w-[460px] sm:h-[590px]'"
    >
        {{-- Clean Super AI Header (Balanced Spacing) --}}
        <div class="px-5 py-4 bg-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Ask Super AI</h3>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-200/60 font-mono">
                    {{ \App\Services\AiAgentService::getActiveModelName() }}
                </span>
            </div>

            <div class="flex items-center gap-1">
                {{-- Erase / Clear Chat --}}
                <button
                    type="button"
                    @click="clearChat()"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                    title="Erase Chat"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                        <path d="m7 21-4.3-4.3c-1-1-1-2.5 0-3.4l9.6-9.6c1-1 2.5-1 3.4 0l5.6 5.6c1 1 1 2.5 0 3.4L13 21"/>
                        <path d="M22 21H7"/>
                        <path d="m5 11 9 9"/>
                    </svg>
                </button>

                {{-- Close Button (Lucide X) --}}
                <button
                    type="button"
                    @click="closeChat()"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-800 hover:bg-gray-100 transition-colors"
                    title="Close"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ===================================================
             Chat Conversation Stream
        =================================================== --}}
        <div class="flex-1 flex flex-col min-h-0 bg-white">
            {{-- Message List Container (Custom Minimalist Scrollbar) --}}
            <div
                x-ref="chatContainer"
                class="ai-chat-scroll flex-1 overflow-y-auto px-5 py-2 space-y-3.5"
                style="scrollbar-width: thin; scrollbar-color: rgba(226, 228, 233, 0.9) transparent;"
            >
                <template x-for="msg in messages" :key="msg.id">
                    <div class="flex flex-col" :class="msg.role === 'user' ? 'items-end' : 'items-start'">
                        {{-- Message Bubble Wrapper --}}
                        <div class="flex items-start gap-2.5 max-w-[90%]" :class="msg.role === 'user' ? 'flex-row-reverse' : 'flex-row'">
                            {{-- Avatar for AI Assistant (Standard Avatar) --}}
                            <template x-if="msg.role === 'assistant'">
                                <div class="size-8 shrink-0 flex items-center justify-center overflow-visible bg-transparent mt-0.5">
                                    <img src="/images/ai-agent-avatar.svg" alt="AI" class="size-7.5 object-contain pointer-events-none overflow-visible" />
                                </div>
                            </template>

                            {{-- Message Content Box --}}
                            <div class="flex flex-col gap-1 min-w-0">
                                <div
                                    class="text-xs sm:text-sm leading-relaxed break-words"
                                    :class="msg.role === 'user'
                                        ? 'bg-primary text-white font-medium rounded-xl rounded-tr-xs px-4 py-2.5 shadow-xs'
                                        : (msg.error
                                            ? 'bg-red-50 text-red-800 border border-red-200/80 rounded-xl rounded-tl-xs px-3.5 py-2.5'
                                            : 'bg-[#f4f5f7] text-gray-800 rounded-xl rounded-tl-xs px-3.5 py-2.5')"
                                >
                                    {{-- Markdown rendered content --}}
                                    <div
                                        class="prose prose-xs sm:prose-sm max-w-none prose-p:my-0.5 prose-headings:my-1"
                                        :class="msg.role === 'user' ? 'text-white prose-p:text-white prose-strong:text-white' : 'prose-strong:text-gray-900'"
                                        x-html="formatMarkdown(msg.content)"
                                    ></div>
                                </div>

                                {{-- Action execution summary Accordion (Seamless Borderless Design) --}}
                                <template x-if="msg.actions && msg.actions.length > 0">
                                    <div
                                        x-data="{ isExpanded: false }"
                                        class="mt-1.5 bg-[#f4f5f7] rounded-xl overflow-hidden shadow-2xs transition-all duration-200"
                                    >
                                        {{-- Accordion Toggle Header --}}
                                        <div
                                            @click="isExpanded = !isExpanded"
                                            class="px-3 py-2 flex items-center justify-between cursor-pointer hover:bg-[#e9ecef] select-none transition-colors"
                                        >
                                            <div class="flex items-center gap-2">
                                                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                                <span class="text-xs font-semibold text-gray-800">Changes Applied</span>
                                                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-medium bg-white text-gray-700 font-mono shadow-2xs" x-text="msg.actions.length"></span>
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2.25"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="size-3 text-gray-400 transition-transform duration-200"
                                                    :class="isExpanded ? 'rotate-180 text-gray-700' : ''"
                                                >
                                                    <path d="m6 9 6 6 6-6"/>
                                                </svg>
                                            </div>

                                            <div class="flex items-center gap-1.5" @click.stop>
                                                <template x-if="msg.canUndo && !msg.undone">
                                                    <button
                                                        type="button"
                                                        @click="undoMessage(msg.id)"
                                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-medium text-gray-700 bg-white hover:text-gray-900 hover:bg-white/90 transition-all shadow-2xs"
                                                        title="Undo these changes"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-gray-500">
                                                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                                            <path d="M3 3v5h5"/>
                                                        </svg>
                                                        <span>Undo</span>
                                                    </button>
                                                </template>
                                                <template x-if="msg.undone">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded shadow-2xs">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3">
                                                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                                            <path d="M3 3v5h5"/>
                                                        </svg>
                                                        Reverted
                                                    </span>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Collapsible List of Change Items (Borderless) --}}
                                        <div
                                            x-show="isExpanded"
                                            x-cloak
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 -translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 -translate-y-1"
                                            class="px-3 pb-2.5 pt-1.5 bg-[#e9ecef]/60 space-y-1.5"
                                        >
                                            <div class="flex flex-wrap gap-1.5 pt-0.5">
                                                <template x-for="(act, aIdx) in msg.actions" :key="aIdx">
                                                    <button
                                                        type="button"
                                                        @click="navigateTo(act.section_index, act.field_path)"
                                                        class="group inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[11px] font-medium transition-all text-left shadow-2xs"
                                                        :class="act.action === 'add_section'
                                                            ? 'bg-emerald-100/80 hover:bg-emerald-200/80 text-emerald-950'
                                                            : (act.action === 'set_image'
                                                                ? 'bg-indigo-100/80 hover:bg-indigo-200/80 text-indigo-950'
                                                                : (act.action === 'remove_section'
                                                                    ? 'bg-rose-100/80 hover:bg-rose-200/80 text-rose-950'
                                                                    : 'bg-white hover:bg-white/95 text-gray-800'))"
                                                        :title="'Click to focus section #' + ((act.section_index || 0) + 1)"
                                                    >
                                                        {{-- Add Section Icon --}}
                                                        <template x-if="act.action === 'add_section'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-emerald-700 shrink-0">
                                                                    <circle cx="12" cy="12" r="10"/>
                                                                    <path d="M12 8v8M8 12h8"/>
                                                                </svg>
                                                                <span>Added <strong class="font-semibold" x-text="act.name"></strong></span>
                                                            </span>
                                                        </template>

                                                        {{-- Set Image Icon --}}
                                                        <template x-if="act.action === 'set_image'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-indigo-700 shrink-0">
                                                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                                                    <circle cx="9" cy="9" r="2"/>
                                                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                                                </svg>
                                                                <span>Updated Image</span>
                                                            </span>
                                                        </template>

                                                        {{-- Update Field Icon --}}
                                                        <template x-if="act.action === 'update_field'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-gray-600 shrink-0">
                                                                    <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                                                </svg>
                                                                <span class="capitalize" x-text="(act.field_path || 'field').replace(/_/g, ' ')"></span>
                                                            </span>
                                                        </template>

                                                        {{-- Update Section Icon --}}
                                                        <template x-if="act.action === 'update_section'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-gray-600 shrink-0">
                                                                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                                                    <path d="m3.3 7 8.7 5 8.7-5M12 22V12"/>
                                                                </svg>
                                                                <span>Section #<span x-text="(act.section_index || 0) + 1"></span></span>
                                                            </span>
                                                        </template>

                                                        {{-- Remove Section Icon --}}
                                                        <template x-if="act.action === 'remove_section'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-rose-700 shrink-0">
                                                                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                                </svg>
                                                                <span>Removed Section #<span x-text="(act.section_index || 0) + 1"></span></span>
                                                            </span>
                                                        </template>

                                                        {{-- Save Page Icon --}}
                                                        <template x-if="act.action === 'save_page'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-emerald-700 shrink-0">
                                                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                                                    <polyline points="17 21 17 13 7 13 7 21"/>
                                                                    <polyline points="7 3 7 8 15 8"/>
                                                                </svg>
                                                                <span>Saved &amp; Published</span>
                                                            </span>
                                                        </template>

                                                        {{-- Subtle locator arrow indicator --}}
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-2.5 opacity-40 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all ml-0.5">
                                                            <path d="M5 12h14M12 5l7 7-7 7"/>
                                                        </svg>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Loading / Thinking indicator inside chat modal (Dynamic Bloub Animation) --}}
                <div x-show="isLoading || isProcessingActions" x-cloak class="flex items-start gap-2.5 max-w-[85%]">
                    <div class="size-8 shrink-0 flex items-center justify-center overflow-visible bg-transparent mt-0.5">
                        <img :src="avatarSrc" alt="AI" class="size-7.5 object-contain animate-bounce overflow-visible" />
                    </div>
                    <div class="bg-[#f4f5f7] rounded-xl rounded-tl-xs px-3.5 py-2 text-xs flex items-center border border-gray-200/50">
                        <span class="font-medium bg-gradient-to-r from-gray-400 via-gray-900 to-gray-400 bg-clip-text text-transparent animate-pulse" x-text="statusMessage || 'Thinking & Crafting...'"></span>
                    </div>
                </div>
            </div>

            {{-- Super AI Input Capsule (Tight Minimalist Spacing) --}}
            <div class="px-4 pt-1 bg-white shrink-0">
                <form @submit.prevent="sendMessage()" class="relative">
                    <div class="flex items-center gap-2 bg-[#f0f2f5] rounded-full pl-4 pr-1.5 py-1.5 transition-all">
                        <input
                            type="text"
                            x-ref="promptInput"
                            x-model="prompt"
                            @keydown.enter.exact.prevent="sendMessage()"
                            placeholder="How else can I help"
                            class="bg-transparent border-0 focus:ring-0 focus:outline-none text-xs sm:text-sm text-gray-900 placeholder-gray-400 w-full py-1.5 px-0"
                            :disabled="isLoading || isProcessingActions"
                        />

                        {{-- Fully Rounded Theme Color Send Button (Lucide Arrow Up) --}}
                        <button
                            type="submit"
                            :disabled="!prompt.trim() || isLoading || isProcessingActions"
                            class="size-8 rounded-full bg-primary hover:bg-primary/90 text-white flex items-center justify-center transition-all shadow-xs active:scale-95 disabled:opacity-40 disabled:hover:bg-primary disabled:active:scale-100 shrink-0"
                            title="Send"
                        >
                            <template x-if="!isLoading && !isProcessingActions">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                    <path d="m5 12 7-7 7 7"/>
                                    <path d="M12 19V5"/>
                                </svg>
                            </template>
                            <template x-if="isLoading || isProcessingActions">
                                <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
