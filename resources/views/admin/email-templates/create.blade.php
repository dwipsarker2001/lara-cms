@extends('admin.layout')

@section('title', 'New Email Template')
@section('breadcrumb', 'New Email Template')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0"
    x-data="{
        published: {{ old('published', true) ? 'true' : 'false' }},
        activeTab: 'blank'
    }"
>
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <path d="M21 12H3" />
            </svg>
            New Email Template
        </h1>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.email-templates.index') }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200"
            >Cancel</a>
            <button type="submit" form="template-form"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                x-text="activeTab === 'import' ? 'Import & Create' : 'Create Template'"
            >Create Template</button>
        </div>
    </header>

    <div class="mb-6 flex gap-4 border-b border-content-border pb-px">
        <button type="button" @click="activeTab = 'blank'" :class="{'border-primary text-primary font-medium active': activeTab === 'blank', 'border-transparent text-text-muted hover:text-text-heading': activeTab !== 'blank'}" class="pb-3 px-1 border-b-2 text-sm transition-colors cursor-pointer" data-tab-btn="blank">
            Blank Template
        </button>
        <button type="button" @click="activeTab = 'import'" :class="{'border-primary text-primary font-medium active': activeTab === 'import', 'border-transparent text-text-muted hover:text-text-heading': activeTab !== 'import'}" class="pb-3 px-1 border-b-2 text-sm transition-colors cursor-pointer" data-tab-btn="import">
            Import Template
        </button>
    </div>

    <form id="template-form" method="POST" action="{{ route('admin.email-templates.store') }}">
        @csrf
        <input type="hidden" id="content-input" name="content" value="">

        <div class="space-y-6">
            <div class="bg-panel-bg rounded-2xl p-[7px]">
                <div class="px-[18px] pt-3 pb-1">
                    <div class="text-sm font-medium text-text-heading">Details</div>
                </div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Configure your email template name and visibility.</p>
                <div class="px-1.5 pb-2 space-y-4">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-name" class="text-sm font-medium text-text-heading">Title</label>
                                    <div class="text-sm text-text-muted">The name of your email template.</div>
                                </div>
                                <div>
                                    <input id="field-name" type="text" name="name" value="{{ old('name') }}" placeholder="My Template" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading">Published</label>
                                    <div class="text-sm text-text-muted">Make this template available in the email dashboard.</div>
                                </div>
                                <div class="flex items-center justify-end h-full">
                                    <button type="button" role="switch" :aria-checked="published" :data-state="published ? 'checked' : 'unchecked'" @click="published = !published" class="relative flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/30 data-[state=checked]:shadow-inner data-[state=checked]:border-emerald-500 data-[state=checked]:bg-emerald-500 data-[state=unchecked]:!border-gray-300 data-[state=unchecked]:bg-gray-200">
                                        <span :data-state="published ? 'checked' : 'unchecked'" class="my-auto flex items-center justify-center size-5 rounded-full bg-white text-xs shadow-[0_10px_15px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] transition-transform will-change-transform data-[state=checked]:translate-x-full data-[state=unchecked]:translate-x-0"></span>
                                    </button>
                                    <input type="hidden" name="published" :value="published ? '1' : '0'">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Panel -->
            <div x-show="activeTab === 'import'" x-transition class="bg-panel-bg rounded-2xl p-[7px]">
                <div class="px-[18px] pt-3 pb-1">
                    <div class="text-sm font-medium text-text-heading">Import Configuration</div>
                </div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Import template JSON from Unlayer/BeeFree or raw HTML.</p>
                <div class="px-1.5 pb-2 space-y-4">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <!-- Import Source -->
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="import-source" class="text-sm font-medium text-text-heading">Source Platform</label>
                                    <div class="text-sm text-text-muted">Select the origin format of the file you are importing.</div>
                                </div>
                                <div>
                                    <select id="import-source" class="w-full block bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                        <option value="unlayer">Unlayer (JSON)</option>
                                        <option value="beefree">BeeFree (JSON)</option>
                                        <option value="html">Raw HTML (.html, .htm, .txt)</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Import File -->
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="import-file" class="text-sm font-medium text-text-heading">Template File</label>
                                    <div class="text-sm text-text-muted">Upload the JSON design file or raw HTML file.</div>
                                </div>
                                <div>
                                    <input id="import-file" type="file" accept=".json,.html,.htm,.txt" class="w-full block text-sm text-text-muted file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                                    <div id="import-error" class="hidden text-red-500 text-xs mt-2 font-medium"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    @vite('resources/js/template-importer.js')
@endpush
