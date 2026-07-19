@extends('admin.layout')

@section('title', 'Edit Block — '.$block->label)
@section('breadcrumb', 'Edit Block')

@section('content-full')
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
                <span class="flex items-center justify-center size-5 rounded bg-amber-50 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3 text-amber-500">
                        <rect x="3.5" y="3.5" width="17" height="17" rx="2"/>
                        <path d="M8 8h2v2H8zM8 13h8M8 16h5"/>
                    </svg>
                </span>
                fields.json
            </button>
            <button type="button" @click="tab = 'template'"
                :class="tab === 'template' ? 'bg-gray-100 text-text-heading border-content-border border-t-0' : 'bg-transparent text-text-muted border-transparent hover:bg-gray-50 hover:text-text-heading'"
                class="flex items-center gap-2 px-6 h-10 text-xs font-medium border -mb-px -ml-px transition-colors hover:border-b-gray-300"
            >
                <span class="flex items-center justify-center size-5 rounded bg-sky-50 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3 text-sky-500">
                        <path d="M8 4l-5 8 5 8M16 4l5 8-5 8"/>
                    </svg>
                </span>
                template.html
            </button>
            <button type="button" @click="tab = 'preview'; $nextTick(() => window.renderPreview())"
                :class="tab === 'preview' ? 'bg-gray-100 text-text-heading border-content-border border-t-0' : 'bg-transparent text-text-muted border-transparent hover:bg-gray-50 hover:text-text-heading'"
                class="flex items-center gap-2 px-6 h-10 text-xs font-medium border -mb-px -ml-px transition-colors hover:border-b-gray-300"
            >
                <span class="flex items-center justify-center size-5 rounded bg-purple-50 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3 text-purple-500">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </span>
                Preview
            </button>
            <button type="button" @click="tab = 'readme'"
                :class="tab === 'readme' ? 'bg-gray-100 text-text-heading border-content-border border-t-0' : 'bg-transparent text-text-muted border-transparent hover:bg-gray-50 hover:text-text-heading'"
                class="flex items-center gap-2 px-6 h-10 text-xs font-medium border -mb-px -ml-px transition-colors hover:border-b-gray-300"
            >
                <span class="flex items-center justify-center size-5 rounded bg-gray-100 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3 text-gray-500">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </span>
                readme.md
            </button>
        </div>

        {{-- Actions --}}
        <div class="flex items-end -mb-px">
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
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                            <span>Connect Provider</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Send / Generate Arrow Button --}}
                            <button
                                type="button"
                                @click="runAiGeneration()"
                                :disabled="!prompt.trim() || generating"
                                class="size-8 rounded-xl bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 disabled:opacity-30 disabled:pointer-events-none transition-colors cursor-pointer"
                                title="Generate Component"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="size-4">
                                    <line x1="12" y1="19" x2="12" y2="5"/>
                                    <polyline points="5 12 12 5 19 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>
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
                                    <th class="text-left font-medium text-text-heading py-2 pr-4">Type</th>
                                    <th class="text-left font-medium text-text-heading py-2 pr-4">Description</th>
                                    <th class="text-left font-medium text-text-heading py-2">Template</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-content-border">
                                <tr><td class="py-2 pr-4 font-mono text-amber-700">string</td><td class="py-2 pr-4 text-text-muted">Single line text</td><td class="py-2 font-mono text-primary">@{{ fieldName }}</td></tr>
                                <tr><td class="py-2 pr-4 font-mono text-amber-700">text</td><td class="py-2 pr-4 text-text-muted">Multi-line text area</td><td class="py-2 font-mono text-primary">@{{ fieldName }}</td></tr>
                                <tr><td class="py-2 pr-4 font-mono text-amber-700">image</td><td class="py-2 pr-4 text-text-muted">Image picker</td><td class="py-2 font-mono text-primary">@{{ fieldName }}</td></tr>
                                <tr><td class="py-2 pr-4 font-mono text-amber-700">link</td><td class="py-2 pr-4 text-text-muted">URL + label</td><td class="py-2 font-mono text-primary">@{{ fieldName.url }} / @{{ fieldName.label }}</td></tr>
                                <tr><td class="py-2 pr-4 font-mono text-amber-700">boolean</td><td class="py-2 pr-4 text-text-muted">On/off toggle</td><td class="py-2 font-mono text-primary">@{{ fieldName }}</td></tr>
                                <tr><td class="py-2 pr-4 font-mono text-amber-700">select</td><td class="py-2 pr-4 text-text-muted">Dropdown from options</td><td class="py-2 font-mono text-primary">@{{ fieldName }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
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
            
            <div class="p-4 space-y-2">
                <div class="text-xs text-text-muted font-medium uppercase tracking-wider px-1 pb-1">Popular Providers</div>
                
                {{-- Gemini Item --}}
                <div class="flex items-center justify-between p-2 rounded-xl hover:bg-gray-50 transition-colors border border-gray-100 bg-white">
                    <button type="button" @click="startConnectPredefined('gemini')" class="flex-1 flex items-center gap-2.5 text-left font-medium text-text-primary text-xs">
                        <span class="size-2.5 rounded-full bg-blue-400"></span>
                        <span>Google Gemini</span>
                        <template x-if="geminiKey">
                            <span class="text-[9px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">Connected</span>
                        </template>
                    </button>
                    <template x-if="geminiKey">
                        <button type="button" @click="disconnectProvider('gemini')" class="text-[10px] text-danger font-semibold hover:underline px-2 py-1 cursor-pointer">Disconnect</button>
                    </template>
                </div>

                {{-- OpenRouter Item --}}
                <div class="flex items-center justify-between p-2 rounded-xl hover:bg-gray-50 transition-colors border border-gray-100 bg-white">
                    <button type="button" @click="startConnectPredefined('openrouter')" class="flex-1 flex items-center gap-2.5 text-left font-medium text-text-primary text-xs">
                        <span class="size-2.5 rounded-full bg-orange-400"></span>
                        <span>OpenRouter</span>
                        <template x-if="openrouterKey">
                            <span class="text-[9px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">Connected</span>
                        </template>
                    </button>
                    <template x-if="openrouterKey">
                        <button type="button" @click="disconnectProvider('openrouter')" class="text-[10px] text-danger font-semibold hover:underline px-2 py-1 cursor-pointer">Disconnect</button>
                    </template>
                </div>

                {{-- z.ai Item --}}
                <div class="flex items-center justify-between p-2 rounded-xl hover:bg-gray-50 transition-colors border border-gray-100 bg-white">
                    <button type="button" @click="startConnectPredefined('zai')" class="flex-1 flex items-center gap-2.5 text-left font-medium text-text-primary text-xs">
                        <span class="size-2.5 rounded-full bg-indigo-400"></span>
                        <span>z.ai</span>
                        <template x-if="zaiKey">
                            <span class="text-[9px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">Connected</span>
                        </template>
                    </button>
                    <template x-if="zaiKey">
                        <button type="button" @click="disconnectProvider('zai')" class="text-[10px] text-danger font-semibold hover:underline px-2 py-1 cursor-pointer">Disconnect</button>
                    </template>
                </div>

                <div class="text-xs text-text-muted font-medium uppercase tracking-wider px-1 pt-3 pb-1">Advanced Settings</div>

                {{-- Custom Provider trigger --}}
                <button type="button" @click="showConnectModal = false; showCustomFormModal = true;" class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-gray-50 transition-colors border border-gray-100 bg-white text-left font-medium text-text-primary text-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="size-2.5 rounded-full bg-purple-400"></span>
                        <span>Custom OpenAI Endpoint</span>
                    </div>
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 text-text-muted">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            <div class="p-4 border-t border-content-border bg-gray-50 flex justify-end">
                <button type="button" @click="showConnectModal = false" class="px-4 py-1.5 border border-content-border rounded-lg text-xs font-semibold text-text-primary bg-white hover:bg-gray-50 cursor-pointer">Close</button>
            </div>
        </div>
    </div>

    {{-- API Key Input Modal (Ask for API popup) --}}
    <div x-show="showApiKeyModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @keydown.escape.window="showApiKeyModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden flex flex-col" @click.outside="showApiKeyModal = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-content-border">
                <h3 class="text-base font-semibold text-text-heading">
                    Connect <span x-text="connectingProvider === 'gemini' ? 'Google Gemini' : (connectingProvider === 'openrouter' ? 'OpenRouter' : (connectingProvider === 'zai' ? 'z.ai' : 'AI Provider'))"></span>
                </h3>
                <button @click="showApiKeyModal = false" class="p-1 text-text-muted hover:text-text-primary transition-colors">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 1 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 1-1.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
            
            <div class="p-5 space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-text-heading">
                        <span x-text="connectingProvider === 'gemini' ? 'Google Gemini' : (connectingProvider === 'openrouter' ? 'OpenRouter' : (connectingProvider === 'zai' ? 'z.ai' : 'AI Provider'))"></span> API Key
                    </label>
                    <input type="password" x-model="tempApiKey" @keydown.enter="saveApiKey()" placeholder="Enter key..." class="w-full block bg-white border border-content-border text-text-primary text-xs rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
                <p class="text-[11px] text-text-muted">Required to complete the integration. Key is saved locally in your browser.</p>
            </div>
            
            <div class="p-4 border-t border-content-border bg-gray-50 flex justify-end gap-2">
                <button type="button" @click="showApiKeyModal = false; showConnectModal = true;" class="px-4 py-1.5 border border-content-border rounded-lg text-xs font-semibold text-text-primary bg-white hover:bg-gray-50 cursor-pointer">Back</button>
                <button type="button" @click="saveApiKey()" class="px-4 py-1.5 rounded-lg text-xs font-semibold text-white bg-primary hover:opacity-90 cursor-pointer">Connect</button>
            </div>
        </div>
    </div>

    {{-- Custom Providers List Modal --}}
    <div x-show="showCustomFormModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @keydown.escape.window="showCustomFormModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col max-h-[85vh]" @click.outside="showCustomFormModal = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-content-border shrink-0">
                <h3 class="text-base font-semibold text-text-heading">Openai compatablel provider</h3>
                <button @click="showCustomFormModal = false" class="p-1 text-text-muted hover:text-text-primary transition-colors">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 1 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 1-1.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
            
            <div class="p-5 space-y-4 overflow-y-auto flex-1 text-xs">
                {{-- Add Custom Provider Button --}}
                <div class="flex justify-end">
                    <button type="button" @click="showCustomFormModal = false; showAddCustomModal = true;" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white font-semibold rounded-lg hover:opacity-90 transition-opacity cursor-pointer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>Add Custom Provider</span>
                    </button>
                </div>

                {{-- Dynamic list of already configured custom endpoints --}}
                <template x-if="customProviders.length > 0">
                    <div class="space-y-2">
                        <label class="block font-bold text-text-heading text-xs uppercase tracking-wider">Configured Providers</label>
                        <div class="space-y-2 bg-gray-50 rounded-xl p-2.5 border border-content-border">
                            <template x-for="(cp, idx) in customProviders" :key="cp.id">
                                <div class="p-2.5 bg-white rounded-lg border border-content-border flex items-center justify-between shadow-sm">
                                    <div class="min-w-0 flex-1">
                                        <div class="font-semibold text-text-heading text-xs truncate" x-text="cp.name"></div>
                                        <div class="text-[10px] text-text-muted font-mono truncate mt-0.5" x-text="cp.baseUrl"></div>
                                    </div>
                                    <button type="button" @click="removeCustomProvider(idx)" class="text-[10px] font-semibold text-danger hover:underline ml-4 cursor-pointer">Remove</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="customProviders.length === 0">
                    <div class="text-center py-6 text-text-muted font-medium border border-dashed border-content-border rounded-xl bg-gray-50/50">
                        No custom providers configured yet. Click above to connect one.
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-end gap-2 p-5 border-t border-content-border shrink-0 bg-gray-50">
                <button type="button" @click="showCustomFormModal = false; showConnectModal = true;" class="px-4 py-1.5 border border-content-border rounded-lg text-xs font-semibold text-text-primary bg-white hover:bg-gray-50 cursor-pointer">Back</button>
                <button type="button" @click="showCustomFormModal = false" class="px-4 py-1.5 border border-transparent rounded-lg text-xs font-semibold text-text-primary bg-transparent hover:bg-gray-100 cursor-pointer">Close</button>
            </div>
        </div>
    </div>

    {{-- Add Custom Provider Form Modal --}}
    <div x-show="showAddCustomModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @keydown.escape.window="showAddCustomModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col max-h-[85vh]" @click.outside="showAddCustomModal = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-content-border shrink-0">
                <h3 class="text-base font-semibold text-text-heading">Add Custom Provider</h3>
                <button @click="showAddCustomModal = false" class="p-1 text-text-muted hover:text-text-primary transition-colors">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 1 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 1-1.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
            
            <div class="p-5 space-y-4 overflow-y-auto flex-1 text-xs">
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="space-y-1">
                        <label class="block text-[11px] font-medium text-text-muted">Provider ID</label>
                        <input type="text" x-model="newProvider.id" placeholder="myprovider" class="w-full block bg-white border border-content-border text-text-primary text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <span class="text-[9px] text-text-muted block">Lowercase, hyphens, underscores</span>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[11px] font-medium text-text-muted">Display Name</label>
                        <input type="text" x-model="newProvider.name" placeholder="My AI Provider" class="w-full block bg-white border border-content-border text-text-primary text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div class="space-y-1.5 mb-3">
                    <label class="block text-[11px] font-medium text-text-muted">Base URL</label>
                    <input type="text" x-model="newProvider.baseUrl" placeholder="https://api.myprovider.com/v1" class="w-full block bg-white border border-content-border text-text-primary text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="space-y-1.5 mb-3">
                    <label class="block text-[11px] font-medium text-text-muted">API key</label>
                    <input type="password" x-model="newProvider.apiKey" placeholder="API key" class="w-full block bg-white border border-content-border text-text-primary text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <span class="text-[9px] text-text-muted block">Optional. Leave empty for header auth.</span>
                </div>

                {{-- Models list and adding --}}
                <div class="border-t border-content-border pt-3 space-y-2">
                    <label class="block text-[11px] font-bold text-text-heading">Models</label>
                    
                    <template x-if="newProvider.models.length > 0">
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            <template x-for="(m, mIdx) in newProvider.models" :key="m.id">
                                <div class="inline-flex items-center gap-1 bg-white border border-content-border px-2 py-0.5 rounded text-[10px]">
                                    <span x-text="m.name"></span>
                                    <button type="button" @click="removeModelFromNewProvider(mIdx)" class="text-text-muted hover:text-danger font-bold">×</button>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="grid grid-cols-2 gap-3 items-end">
                        <div class="space-y-1">
                            <label class="block text-[10px] text-text-muted">Model ID</label>
                            <input type="text" x-model="newModel.id" placeholder="model-id" class="w-full block bg-white border border-content-border text-text-primary text-xs rounded-lg px-2.5 py-1 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <div class="space-y-1 flex items-center gap-2">
                            <div class="flex-1">
                                <label class="block text-[10px] text-text-muted">Model Name</label>
                                <input type="text" x-model="newModel.name" placeholder="Model Name" class="w-full block bg-white border border-content-border text-text-primary text-xs rounded-lg px-2.5 py-1 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                            <button type="button" @click="addModelToNewProvider()" class="flex h-7 items-center justify-center px-3 border border-content-border rounded-lg text-[10px] font-semibold bg-white hover:bg-gray-100 shrink-0 cursor-pointer">
                                Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>

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
                document.querySelector('#editor-form button[type="submit"]').click();
            }
        });

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
                                    { name: 'title', label: 'Title', type: 'string', defaultValue: 'Feature Title' },
                                    { name: 'desc', label: 'Description', type: 'string', defaultValue: 'Feature description goes here.' }
                                ];
                                template = `<div class="p-6 bg-content-bg rounded-xl border border-content-border shadow-sm max-w-md mx-auto">
  <h2 class="text-lg font-semibold text-text-heading">@{{ title }}</h2>
  <p class="text-text-muted mt-2">@{{ desc }}</p>
</div>`;
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
                            } else if (this.provider === 'openrouter') {
                                apiKey = this.openrouterKey;
                                if (!apiKey) {
                                    this.showConnectModal = true;
                                    throw new Error('Please connect OpenRouter first.');
                                }
                                url = 'https://openrouter.ai/api/v1/chat/completions';
                                modelId = this.selectedCustomModelId || 'anthropic/claude-3.5-sonnet';
                            } else if (this.provider === 'zai') {
                                apiKey = this.zaiKey;
                                if (!apiKey) {
                                    this.showConnectModal = true;
                                    throw new Error('Please connect z.ai first.');
                                }
                                url = 'https://api.z-ai.com/v1/chat/completions';
                                modelId = 'z-ai/glm-5.2-free';
                            } else {
                                // Custom Provider selection
                                const p = this.customProviders.find(cp => cp.id === this.selectedCustomProviderId);
                                if (!p) {
                                    this.showConnectModal = true;
                                    throw new Error('Please select or configure a Custom AI Provider.');
                                }
                                
                                url = p.baseUrl;
                                if (!url.endsWith('/chat/completions')) {
                                    url = url.replace(/\/$/, '') + '/chat/completions';
                                }
                                apiKey = p.apiKey;
                                modelId = this.selectedCustomModelId || 'default-model';
                            }

                            this.logs = 'Sending request to ' + this.providerLabel() + '...';

                            const systemPrompt = `You are a professional web developer component creator.
Your task is to output a new custom block according to this prompt: "${this.prompt}".

You MUST output ONLY a valid JSON object matching the following structure. No markdown headers, no text explanations.
{
  "fields": [
    {
      "name": "field_name",
      "label": "Human Label",
      "type": "string", // can be: string, text, image, link, boolean, select
      "defaultValue": "some value"
    }
  ],
  "template": "<div class=\"my-component\\">\\n  <h2>@{{ field_name }}</h2>\\n</div>"
}

Ensure the template uses standard Tailwind CSS classes. Always escape inner double quotes inside the template property string. Do not output anything else than the JSON object.`;

                            let response;
                            if (this.provider === 'gemini') {
                                const parts = [{ text: systemPrompt }];
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
    </script>
@endpush