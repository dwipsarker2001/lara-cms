@extends('admin.layout')

@section('title', 'Create Taxonomy')
@section('breadcrumb', 'Create Taxonomy')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0" x-data="taxonomyForm([])">
        <form method="POST" action="{{ route('admin.taxonomies.store') }}">
            @csrf
            <input type="hidden" name="fields" :value="JSON.stringify(fields)">

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                        <line x1="4" y1="9" x2="20" y2="9" />
                        <line x1="4" y1="15" x2="20" y2="15" />
                        <line x1="10" y1="3" x2="8" y2="21" />
                        <line x1="16" y1="3" x2="14" y2="21" />
                    </svg>
                    Create Taxonomy Type
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <button type="button" @click="openFieldModal()"
                        class="inline-flex items-center justify-center gap-1.5 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 px-3 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200 text-sm">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                            <path d="M8 3v10M3 8h10" />
                        </svg>
                        Add Input
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                        <span>Create Taxonomy</span>
                    </button>
                </div>
            </header>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Taxonomy Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Define a new taxonomy group (e.g. Categories, Destinations, Brands) and configure custom term input fields.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-title" class="text-sm font-medium text-text-heading">Title</label>
                                    <div class="text-sm text-text-muted">A descriptive name for this taxonomy group.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-title"
                                            type="text"
                                            name="title"
                                            x-model="title"
                                            @input="onTitleInput()"
                                            placeholder="e.g. Categories, Destinations, Tags"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-slug" class="text-sm font-medium text-text-heading">Slug</label>
                                    <div class="text-sm text-text-muted">The URL-friendly identifier. Auto-generated as you type the title.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-slug"
                                            type="text"
                                            name="slug"
                                            x-model="slug"
                                            @input="onSlugInput($event.target.value)"
                                            placeholder="Auto-generated slug"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-mono"
                                        >
                                        @error('slug') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div x-show="fields.length > 0" class="px-[18px] py-4">
                                <div class="text-sm font-medium text-text-heading mb-2">Custom Term Fields</div>
                                <div id="sortable-fields" class="space-y-1">
                                    <template x-for="(field, index) in fields" :key="field._key || index">
                                        <div class="flex rounded-lg shadow-sm bg-gray-50 border border-gray-200 group overflow-hidden px-2 hover:bg-gray-100/60 transition-colors">
                                            <div class="w-6 shrink-0 flex items-center justify-center cursor-grab active:cursor-grabbing opacity-70 hover:opacity-100 touch-none transition-opacity text-text-muted/70" data-drag-handle>
                                                <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                                                    <circle cx="8" cy="6" r="2.5" /><circle cx="16" cy="6" r="2.5" />
                                                    <circle cx="8" cy="12" r="2.5" /><circle cx="16" cy="12" r="2.5" />
                                                    <circle cx="8" cy="18" r="2.5" /><circle cx="16" cy="18" r="2.5" />
                                                </svg>
                                            </div>
                                            <div class="flex flex-1 min-w-0 items-center justify-between px-1.5 py-2.5 text-xs leading-normal gap-2">
                                                <div class="flex min-w-0 flex-1 items-center gap-2">
                                                    <span class="text-sm font-semibold text-text-heading group-hover:text-primary truncate transition-colors" x-text="field.title"></span>
                                                    <span class="text-[11px] font-mono text-primary/80 bg-primary/10 px-1.5 py-0.5 rounded" x-text="field.type"></span>
                                                </div>
                                                <div class="flex items-center gap-2.5 shrink-0 ml-1">
                                                    <span class="text-[11px] font-mono text-text-muted bg-white px-1.5 py-0.5 rounded border border-gray-200" x-text="field.template"></span>
                                                    <div class="flex items-center gap-0.5">
                                                        <button type="button" @click="editField(index)" class="p-1 text-text-muted/60 hover:text-primary transition-colors rounded hover:bg-text-primary/10" title="Edit">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                            </svg>
                                                        </button>
                                                        <button type="button" @click="fields.splice(index, 1)" class="p-1 text-text-muted/60 hover:text-danger transition-colors rounded hover:bg-text-primary/10" title="Remove">
                                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- ADD / EDIT FIELD MODAL --}}
        <div x-show="showFieldModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40"
            @click.self="showFieldModal = false" style="display: none;">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 relative">
                <div class="px-6 pt-5 pb-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-text-heading"
                            x-text="editingFieldIndex !== null ? 'Edit Custom Field' : 'Add Custom Field'"></h3>
                        <button type="button" @click="showFieldModal = false"
                            class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-text-muted transition-colors">
                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                <path d="M4 4l8 8M12 4l-8 8" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-text-muted mt-1">Configure custom input fields for items under this taxonomy.</p>
                </div>
                <div class="px-6 pb-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Title</label>
                        <input type="text" x-model="fieldForm.title" @input="generateTemplate"
                            placeholder="e.g. Featured Image, Icon, Color Code, Subtitle"
                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Description</label>
                        <input type="text" x-model="fieldForm.description"
                            placeholder="e.g. Upload or paste featured image URL for this item"
                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div class="relative z-30" x-data="{ open: false }" @click.outside="open = false">
                        <label class="block text-sm font-medium text-text-heading mb-1.5">Input Type</label>
                        <button type="button" @click="open = !open"
                            class="flex items-center justify-between gap-2 w-full rounded-lg border border-gray-300 hover:border-gray-400 bg-white px-3 py-2 text-sm text-text-primary h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer shadow-sm">
                            <div class="flex items-center gap-2.5 min-w-0 truncate">
                                <template x-if="fieldForm.type === 'text'">
                                    <div class="flex items-center gap-2">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>
                                        <span class="font-medium">Text</span>
                                    </div>
                                </template>
                                <template x-if="fieldForm.type === 'textarea'">
                                    <div class="flex items-center gap-2">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/><line x1="9" y1="9" x2="10" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                                        <span class="font-medium">Textarea</span>
                                    </div>
                                </template>
                                <template x-if="fieldForm.type === 'number'">
                                    <div class="flex items-center gap-2">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>
                                        <span class="font-medium">Number</span>
                                    </div>
                                </template>
                                <template x-if="fieldForm.type === 'image'">
                                    <div class="flex items-center gap-2">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        <span class="font-medium">Image</span>
                                    </div>
                                </template>
                                <template x-if="fieldForm.type === 'color'">
                                    <div class="flex items-center gap-2">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                                        <span class="font-medium">Color</span>
                                    </div>
                                </template>
                                <template x-if="fieldForm.type === 'select'">
                                    <div class="flex items-center gap-2">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-primary shrink-0"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                                        <span class="font-medium">Select</span>
                                    </div>
                                </template>
                            </div>
                            <svg :class="open ? 'rotate-180 text-primary' : 'text-gray-400'" class="size-4 transition-transform duration-150 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 top-full mt-1 left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-xl p-1.5 space-y-0.5 max-h-60 overflow-y-auto"
                            style="display: none;">
                            <button type="button" @click="fieldForm.type = 'text'; open = false"
                                class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100/80 transition-colors"
                                :class="fieldForm.type === 'text' ? 'bg-primary/10 text-primary font-medium' : ''">
                                <div class="flex items-center gap-2.5">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'text' ? 'text-primary' : 'text-text-muted'"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>
                                    <span>Text</span>
                                </div>
                                <svg x-show="fieldForm.type === 'text'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button type="button" @click="fieldForm.type = 'textarea'; open = false"
                                class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100/80 transition-colors"
                                :class="fieldForm.type === 'textarea' ? 'bg-primary/10 text-primary font-medium' : ''">
                                <div class="flex items-center gap-2.5">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'textarea' ? 'text-primary' : 'text-text-muted'"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/><line x1="9" y1="9" x2="10" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                                    <span>Textarea</span>
                                </div>
                                <svg x-show="fieldForm.type === 'textarea'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button type="button" @click="fieldForm.type = 'number'; open = false"
                                class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100/80 transition-colors"
                                :class="fieldForm.type === 'number' ? 'bg-primary/10 text-primary font-medium' : ''">
                                <div class="flex items-center gap-2.5">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'number' ? 'text-primary' : 'text-text-muted'"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>
                                    <span>Number</span>
                                </div>
                                <svg x-show="fieldForm.type === 'number'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button type="button" @click="fieldForm.type = 'image'; open = false"
                                class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100/80 transition-colors"
                                :class="fieldForm.type === 'image' ? 'bg-primary/10 text-primary font-medium' : ''">
                                <div class="flex items-center gap-2.5">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'image' ? 'text-primary' : 'text-text-muted'"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    <span>Image</span>
                                </div>
                                <svg x-show="fieldForm.type === 'image'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button type="button" @click="fieldForm.type = 'color'; open = false"
                                class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100/80 transition-colors"
                                :class="fieldForm.type === 'color' ? 'bg-primary/10 text-primary font-medium' : ''">
                                <div class="flex items-center gap-2.5">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'color' ? 'text-primary' : 'text-text-muted'"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                                    <span>Color</span>
                                </div>
                                <svg x-show="fieldForm.type === 'color'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button type="button" @click="fieldForm.type = 'select'; open = false"
                                class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg text-text-primary hover:bg-gray-100/80 transition-colors"
                                :class="fieldForm.type === 'select' ? 'bg-primary/10 text-primary font-medium' : ''">
                                <div class="flex items-center gap-2.5">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" :class="fieldForm.type === 'select' ? 'text-primary' : 'text-text-muted'"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                                    <span>Select</span>
                                </div>
                                <svg x-show="fieldForm.type === 'select'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div x-show="fieldForm.type === 'select'" transition:enter="transition ease-out duration-150" class="space-y-1">
                        <label class="block text-sm font-medium text-text-heading">Options</label>
                        <input type="text" x-model="fieldForm.options"
                            placeholder="e.g. Option 1, Option 2, Option 3 (comma-separated)"
                            class="w-full block bg-white border border-gray-300 text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-heading mb-1">Field Key</label>
                        <input type="text" x-model="fieldForm.template" @input="onKeyInput"
                            placeholder="e.g. image, icon, color"
                            class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-mono">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl">
                    <button type="button" @click="showFieldModal = false"
                        class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-primary shadow-sm border border-gray-200">
                        Cancel
                    </button>
                    <button type="button" @click="saveField"
                        class="inline-flex items-center justify-center gap-2 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                        <span x-text="editingFieldIndex !== null ? 'Update Field' : 'Add Field'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function taxonomyForm(existingFields) {
        return {
            title: @json(old('title', '')),
            slug: @json(old('slug', '')),
            customSlug: @json(old('slug') ? true : false),
            fields: (existingFields || []).map(f => ({
                ...f,
                template: (f.template || '').replace(/[^a-zA-Z0-9_]+/g, ''),
                _key: f._key || crypto.randomUUID()
            })),
            showFieldModal: false,
            editingFieldIndex: null,
            fieldForm: {
                title: '',
                description: '',
                type: 'text',
                options: '',
                template: ''
            },
            isKeyManuallyEdited: false,

            slugify(text) {
                return text.toString().toLowerCase().trim()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
            },
            onTitleInput() {
                if (!this.customSlug) {
                    this.slug = this.slugify(this.title);
                }
            },
            onSlugInput(val) {
                this.customSlug = true;
                this.slug = this.slugify(val);
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
                    options: '',
                    template: ''
                };
                this.showFieldModal = true;
            },
            editField(index) {
                this.editingFieldIndex = index;
                this.isKeyManuallyEdited = true;
                const field = { ...this.fields[index] };
                if (field.template) {
                    field.template = field.template.replace(/[^a-zA-Z0-9_]+/g, '');
                }
                if (!field.options) field.options = '';
                this.fieldForm = field;
                this.showFieldModal = true;
            },
            saveField() {
                if (!this.fieldForm.title.trim()) return;
                if (!this.fieldForm.template || !this.fieldForm.template.trim()) {
                    this.fieldForm.template = this.fieldForm.title.replace(/[^a-zA-Z0-9]+/g, '_').replace(/^_|_$/g, '').toLowerCase();
                } else {
                    this.fieldForm.template = this.fieldForm.template.replace(/[^a-zA-Z0-9_]+/g, '').toLowerCase();
                }
                if (this.editingFieldIndex !== null) {
                    this.fields[this.editingFieldIndex] = { ...this.fieldForm, _key: this.fields[this.editingFieldIndex]._key };
                    this.fields = [...this.fields];
                } else {
                    this.fields.push({ ...this.fieldForm, _key: crypto.randomUUID() });
                }
                this.showFieldModal = false;
            }
        };
    }
</script>
@endpush