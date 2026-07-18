@extends('admin.layout')

@section('title', 'Edit Block — '.$block->label)
@section('breadcrumb', 'Edit Block')

@section('content-full')
<div class="bg-content-bg min-h-[calc(100%-8px)] mx-2 overflow-hidden mt-2 rounded-t-2xl border border-content-border border-b-0 relative" style="container-type: inline-size;"  x-data="{ tab: 'fields' }">
    {{-- Header bar --}}
    <div class="shrink-0 flex items-center justify-between pr-4 pl-0 border-b border-content-border bg-white">
        {{-- Tab strip --}}
        <div class="flex items-end gap-0.5">
            <a href="{{ route('admin.dynamic-blocks.index') }}" class="flex items-center justify-center size-9  -mb-px text-text-muted hover:text-text-heading transition-colors border-r border-content-border">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <button type="button" @click="tab = 'generate'"
:class="tab === 'generate'
    ? 'bg-content-bg text-text-heading border-content-border border-b-content-bg border-t-0'
    : 'bg-transparent text-text-muted border-transparent hover:bg-gray-50 hover:text-text-heading'"
                class="flex items-center gap-2 px-6 h-10 text-xs font-medium border border-b-0 -mb-px transition-colors "
            >
                <span class="flex items-center justify-center size-5 rounded bg-emerald-50 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3 text-emerald-500">
                        <polyline points="16 3 21 3 21 8"/>
                        <line x1="4" y1="20" x2="21" y2="3"/>
                        <polyline points="21 16 21 21 16 21"/>
                        <line x1="15" y1="15" x2="21" y2="21"/>
                        <line x1="4" y1="4" x2="9" y2="9"/>
                    </svg>
                </span>
                Generate
            </button>
            <button type="button" @click="tab = 'fields'"
:class="tab === 'fields'
    ? 'bg-content-bg text-text-heading border-content-border border-b-content-bg border-t-0'
    : 'bg-transparent text-text-muted border-transparent hover:bg-gray-50 hover:text-text-heading'"
                class="flex items-center gap-2 px-6 h-10 text-xs font-medium border border-b-0 -mb-px transition-colors "
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
:class="tab === 'template'
    ? 'bg-content-bg text-text-heading border-content-border border-b-content-bg border-t-0'
    : 'bg-transparent text-text-muted border-transparent hover:bg-gray-50 hover:text-text-heading'"
                class="flex items-center gap-2 px-6 h-10 text-xs font-medium border border-b-0 -mb-px transition-colors "
            >
                <span class="flex items-center justify-center size-5 rounded bg-sky-50 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3 text-sky-500">
                        <path d="M8 4l-5 8 5 8M16 4l5 8-5 8"/>
                    </svg>
                </span>
                template.html
            </button>
            <button type="button" @click="tab = 'preview'; $nextTick(() => window.renderPreview())"
:class="tab === 'preview'
    ? 'bg-content-bg text-text-heading border-content-border border-b-content-bg border-t-0'
    : 'bg-transparent text-text-muted border-transparent hover:bg-gray-50 hover:text-text-heading'"
                class="flex items-center gap-2 px-6 h-10 text-xs font-medium border border-b-0 -mb-px transition-colors "
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
:class="tab === 'readme'
    ? 'bg-content-bg text-text-heading border-content-border border-b-content-bg border-t-0'
    : 'bg-transparent text-text-muted border-transparent hover:bg-gray-50 hover:text-text-heading'"
                class="flex items-center gap-2 px-6 h-10 text-xs font-medium border border-b-0 -mb-px transition-colors "
            >
                <span class="flex items-center justify-center size-5 rounded bg-gray-100 shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3 text-gray-500">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </span>
                readme.md
            </button>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dynamic-blocks.index') }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-8 text-sm leading-tight px-3.5 bg-white hover:bg-gray-100 text-text-muted hover:text-text-heading border border-gray-200 hover:border-gray-300"
            >Cancel</a>
            <button type="submit" form="editor-form"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-8 text-sm leading-tight px-4 bg-primary hover:bg-primary/90 text-white shadow-sm"
            >Save</button>
        </div>
    </div>

    {{-- Editor area --}}
    <div class="flex-1 min-h-0 bg-content-bg">
        <form id="editor-form" method="POST" action="{{ route('admin.dynamic-blocks.update-editor', $block) }}" class="h-full">
            @csrf @method('PUT')

            <div x-show="tab === 'generate'" class="h-full overflow-y-auto p-6 text-sm text-text-primary leading-relaxed" x-data="{ image: null, previewUrl: null }">
                <div class="flex flex-col items-center justify-center min-h-full text-center">
                    <div class="size-16 rounded-2xl bg-emerald-50 flex items-center justify-center mb-4">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-8 text-emerald-500">
                            <polyline points="16 3 21 3 21 8"/>
                            <line x1="4" y1="20" x2="21" y2="3"/>
                            <polyline points="21 16 21 21 16 21"/>
                            <line x1="15" y1="15" x2="21" y2="21"/>
                            <line x1="4" y1="4" x2="9" y2="9"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-text-heading mb-2">AI Block Generator</h2>
                    <p class="text-text-muted max-w-md mb-6">Describe the block you want to create, or upload a wireframe/screenshot.</p>

                    {{-- Image upload --}}
                    <div class="w-full max-w-lg mb-4">
                        <template x-if="!previewUrl">
                            <button type="button" @click="document.getElementById('generate-image-input').click()" class="w-full border-2 border-dashed border-gray-300 hover:border-emerald-400 rounded-xl py-8 px-4 transition-colors cursor-pointer bg-white">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-8 mx-auto mb-2 text-gray-400">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <p class="text-sm text-text-muted">Click to upload a wireframe or screenshot</p>
                            </button>
                        </template>
                        <template x-if="previewUrl">
                            <div class="relative rounded-xl overflow-hidden border border-gray-200 bg-white">
                                <img :src="previewUrl" class="w-full max-h-64 object-contain">
                                <button type="button" @click="image = null; previewUrl = null; document.getElementById('generate-image-input').value = ''" class="absolute top-2 right-2 size-7 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-colors">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                        </template>
                        <input id="generate-image-input" type="file" accept="image/*" class="hidden" @change="const f = $event.target.files[0]; if (f) { image = f; previewUrl = URL.createObjectURL(f); }">
                    </div>

                    <textarea id="generate-prompt" rows="4" class="w-full max-w-lg bg-white border border-content-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary mb-4" placeholder="e.g. A hero section with a heading, subheading, background image, and a CTA button..."></textarea>
                    <button type="button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-5 bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                            <polyline points="16 3 21 3 21 8"/>
                            <line x1="4" y1="20" x2="21" y2="3"/>
                        </svg>
                        Generate Block
                    </button>
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
    </script>
@endpush