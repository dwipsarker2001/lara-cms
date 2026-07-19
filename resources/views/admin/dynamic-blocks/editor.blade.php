@extends('admin.layout')

@section('title', 'Edit Block — '.$block->label)
@section('breadcrumb', 'Edit Block')

@section('content-full')
<div class="bg-content-bg min-h-[calc(100%-8px)] mx-2 overflow-hidden mt-2 rounded-t-2xl border border-content-border border-b-0 relative" style="container-type: inline-size;"
     x-data="blockGenerator"
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
            <div x-show="tab === 'generate'" x-cloak class="h-full relative overflow-y-auto bg-white flex flex-col justify-center items-center px-4">
                
                {{-- OpenCode watermark text in background --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none overflow-hidden pb-32">
                    <span class="text-[90px] md:text-[130px] font-black text-gray-100/60 uppercase tracking-widest font-mono select-none">opencode</span>
                </div>

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
                                        <span x-text="providerLabel()"></span>
                                        <svg class="size-3 text-text-muted" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    <div
                                        x-show="openProvider"
                                        @click.outside="openProvider = false"
                                        style="display: none;"
                                        class="absolute left-0 bottom-full mb-1.5 z-50 min-w-[240px] bg-content-bg border border-content-border rounded-xl shadow-xl p-1"
                                    >
                                        <button type="button" @click="setProvider('opencode'); openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-2.5">
                                            <span class="size-2 rounded-full bg-emerald-400"></span>
                                            <span>OpenCode (Free AI Simulation)</span>
                                        </button>
                                        <button type="button" @click="setProvider('gemini'); openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-2.5">
                                            <span class="size-2 rounded-full bg-blue-400"></span>
                                            <span>Google Gemini API</span>
                                        </button>
                                        <button type="button" @click="setProvider('custom'); openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-text-primary hover:bg-body-bg flex items-center gap-2.5">
                                            <span class="size-2 rounded-full bg-purple-400"></span>
                                            <span>Custom API Endpoint</span>
                                        </button>
                                        <hr class="my-1 border-content-border">
                                        <button type="button" @click="showSettings = true; openProvider = false;" class="w-full text-left px-3 py-2 text-xs rounded-lg text-primary hover:bg-body-bg flex items-center gap-2.5 font-medium">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                            <span>Configure API Keys</span>
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

                    {{-- Green Project Indicator --}}
                    <div class="flex items-center gap-1.5 text-xs text-text-muted mt-3 select-none bg-gray-50 border border-gray-200/60 rounded-full px-3.5 py-1 shadow-sm">
                        <span class="w-3.5 h-3.5 rounded bg-emerald-500 text-white font-bold flex items-center justify-center text-[9px]">L</span>
                        <span class="font-semibold text-text-heading">lara-cms</span>
                        <svg class="size-3 text-text-muted" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                        </svg>
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

    {{-- API Provider Configuration Modal --}}
    <div x-show="showSettings" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @keydown.escape.window="showSettings = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="showSettings = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-content-border">
                <h3 class="text-base font-semibold text-text-heading">AI Provider Settings</h3>
                <button @click="showSettings = false" class="p-1 text-text-muted hover:text-text-primary transition-colors">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 1-1.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
            <div class="p-5 space-y-4 text-sm">
                
                {{-- Gemini Section --}}
                <div class="space-y-2">
                    <label class="block font-medium text-text-heading">Google Gemini API Key</label>
                    <input type="password" x-model="geminiKey" placeholder="AIzaSy..." class="w-full block bg-white border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <p class="text-xs text-text-muted">Enter your Gemini key to use the live <code class="font-mono bg-gray-50 px-1 py-0.5 rounded">gemini-2.0-flash</code> model.</p>
                </div>

                <hr class="border-content-border">

                {{-- Custom API Section --}}
                <div class="space-y-3">
                    <h4 class="font-semibold text-text-heading">Custom Provider Settings</h4>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-text-muted">API Endpoint URL</label>
                        <input type="text" x-model="customUrl" placeholder="https://api.openai.com/v1/chat/completions" class="w-full block bg-white border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-text-muted">API Key / Authorization Token</label>
                        <input type="password" x-model="customKey" placeholder="sk-..." class="w-full block bg-white border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="showSettings = false" class="px-4 py-2 border border-content-border rounded-lg text-xs font-medium text-text-primary bg-white hover:bg-gray-50">Cancel</button>
                    <button type="button" @click="saveSettings()" class="px-4 py-2 rounded-lg text-xs font-medium text-white bg-primary hover:opacity-90">Save Settings</button>
                </div>
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

        document.addEventListener('alpine:init', () => {
            Alpine.data('blockGenerator', () => ({
                tab: 'fields',
                prompt: '',
                generating: false,
                logs: '',
                provider: localStorage.getItem('lara_cms_ai_provider') || 'opencode',
                showSettings: false,
                
                // API keys & Configuration (loaded from localStorage)
                geminiKey: localStorage.getItem('lara_cms_gemini_key') || '',
                customUrl: localStorage.getItem('lara_cms_custom_url') || '',
                customKey: localStorage.getItem('lara_cms_custom_key') || '',

                // Attachment wireframe state
                attachmentName: null,
                attachmentBase64: null,
                attachmentMime: null,

                providerLabel() {
                    if (this.provider === 'opencode') return 'opencode/gemini-2.0-flash';
                    if (this.provider === 'gemini') return 'gemini/gemini-2.0-flash';
                    return 'custom/api-provider';
                },

                setProvider(val) {
                    this.provider = val;
                    localStorage.setItem('lara_cms_ai_provider', val);
                },

                saveSettings() {
                    localStorage.setItem('lara_cms_gemini_key', this.geminiKey);
                    localStorage.setItem('lara_cms_custom_url', this.customUrl);
                    localStorage.setItem('lara_cms_custom_key', this.customKey);
                    this.showSettings = false;
                    window.showToast?.('✓ API configurations saved.', 'success');
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
                            let model = 'gemini-2.0-flash';

                            if (this.provider === 'gemini') {
                                apiKey = this.geminiKey;
                                if (!apiKey) {
                                    this.showSettings = true;
                                    throw new Error('Please configure your Gemini API Key.');
                                }
                                url = 'https://generativelanguage.googleapis.com/v1beta/models/' + model + ':generateContent?key=' + apiKey;
                            } else {
                                url = this.customUrl;
                                apiKey = this.customKey;
                                if (!url) {
                                    this.showSettings = true;
                                    throw new Error('Please configure your Custom API Endpoint URL.');
                                }
                            }

                            this.logs = 'Sending request to ' + (this.provider === 'gemini' ? 'Google Gemini' : 'Custom Provider') + '...';

                            const systemPrompt = `You are a professional web developer component creator.
Your task is to output a new custom block according to this prompt: "${this.prompt}".

You MUST output ONLY a valid JSON object matching the following structure. No markdown headers, no text explanations.
{
  "fields": [
    {
      "name": "field_name",
      "label": "Human Label",
      "type": "string",
      "defaultValue": "some value"
    }
  ],
  "template": "<div class=\\"my-component\\">\\n  <h2>@{{ field_name }}</h2>\\n</div>"
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
                                response = await fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Authorization': apiKey ? 'Bearer ' + apiKey : ''
                                    },
                                    body: JSON.stringify({
                                        model: 'custom-model',
                                        messages: [{ role: 'user', content: systemPrompt }]
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
            }));
        });
    </script>
@endpush