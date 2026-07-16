@extends('admin.layout')

@section('title', 'Create Package')
@section('breadcrumb', 'Create Package')

@section('content')
    <div
        class="max-w-5xl mx-auto px-2 sm:px-0"
        x-data="{
            title: '{{ old('title') }}',
            slugEdited: false,
            slug: '{{ old('slug') }}',
            slugify(v) {
                return v.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            },
            get effectiveSlug() {
                return this.slugEdited ? this.slug : this.slugify(this.title);
            },
        }"
    >
        <form method="POST" action="{{ route('admin.packages.store') }}">
            @csrf

            <input type="hidden" name="slug" :value="effectiveSlug">

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                        <path d="M16.5 9.4 7.55 4.24" />
                        <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" />
                        <polyline points="3.29 7 12 12 20.71 7" />
                        <line x1="12" y1="22" x2="12" y2="12" />
                    </svg>
                    Create Package
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        <span>Create Package</span>
                    </button>
                </div>
            </header>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Package Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Configure the title, URL, and details for this package.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-title" class="text-sm font-medium text-text-heading">Title</label>
                                    <div class="text-sm text-text-muted">A clear, descriptive name for this package.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-title"
                                            type="text"
                                            name="title"
                                            x-model="title"
                                            value="{{ old('title') }}"
                                            placeholder="Package title"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-slug" class="text-sm font-medium text-text-heading">URL Slug</label>
                                    <div class="text-sm text-text-muted">The URL-friendly identifier. Auto-generated from the title.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-slug"
                                            type="text"
                                            :value="effectiveSlug"
                                            @input="slugEdited = true; slug = slugify($event.target.value)"
                                            placeholder="package-title"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('slug') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-excerpt" class="text-sm font-medium text-text-heading">Excerpt</label>
                                    <div class="text-sm text-text-muted">A short summary of this package.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <textarea
                                            id="field-excerpt"
                                            name="excerpt"
                                            rows="3"
                                            placeholder="Brief description..."
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >{{ old('excerpt') }}</textarea>
                                        @error('excerpt') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-price" class="text-sm font-medium text-text-heading">Price</label>
                                    <div class="text-sm text-text-muted">The package price.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-price"
                                            type="number"
                                            step="0.01"
                                            name="price"
                                            value="{{ old('price') }}"
                                            placeholder="0.00"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('price') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-duration" class="text-sm font-medium text-text-heading">Duration</label>
                                    <div class="text-sm text-text-muted">How long this package lasts (e.g. 3 days, 7 days).</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-duration"
                                            type="text"
                                            name="duration"
                                            value="{{ old('duration') }}"
                                            placeholder="e.g. 5 days"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('duration') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-destination" class="text-sm font-medium text-text-heading">Destination</label>
                                    <div class="text-sm text-text-muted">The destination or location for this package.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-destination"
                                            type="text"
                                            name="destination"
                                            value="{{ old('destination') }}"
                                            placeholder="e.g. Paris, France"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('destination') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            @php
                                $pkgLayouts = \App\Models\Layout::where('collection', 'package')->orderBy('position')->orderBy('name')->get();
                                $pkgLayoutOptions = $pkgLayouts->map(fn($l) => ['value' => (string)$l->id, 'label' => $l->name])->values()->toArray();
                            @endphp
                            @if($pkgLayouts->isNotEmpty())
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Layout</label>
                                        <div class="text-sm text-text-muted">Pre-fill sections from a layout.</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1">
                                            <div x-data="{
                                                open: false,
                                                selected: '{{ old('layout_id', '') }}',
                                                options: {{ Js::from($pkgLayoutOptions) }},
                                                get selectedLabel() { return this.options.find(o => o.value === this.selected)?.label ?? 'None'; },
                                                select(val) { this.selected = val; this.open = false; },
                                            }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                <button type="button" @click="open = !open"
                                                    class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 hover:bg-content-border/30 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                    <span class="truncate" x-text="selectedLabel"></span>
                                                    <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5" style="display: none;">
                                                    <button type="button" @click="select('')" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="'' === selected ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                        <span>None (empty package)</span>
                                                    </button>
                                                    <template x-for="opt in options" :key="opt.value">
                                                        <button type="button" @click="select(opt.value)" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="opt.value === selected ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                            <span x-text="opt.label"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                                <input type="hidden" name="layout_id" :value="selected">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-published" class="text-sm font-medium text-text-heading">Published</label>
                                    <div class="text-sm text-text-muted">Make this package visible to visitors.</div>
                                </div>
                                <div class="flex items-center justify-end h-full">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="published" value="1" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </form>
    </div>
@endsection
