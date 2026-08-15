@extends('admin.layout')

@section('title', 'Create ' . $collection->name)
@section('breadcrumb', 'Create ' . $collection->name)

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0"
    x-data="{
        activeTab: 'basic',
        title: '{{ old('data.title', '') }}',
        slugEdited: false,
        slug: '{{ old('slug', '') }}',
        published: {{ old('published', true) ? 'true' : 'false' }},
        meta: {
            author: '{{ old('meta.author', $admins->first()?->name ?? 'Admin') }}',
            metaTitle: '{{ old('meta.metaTitle', '') }}',
            metaDescription: '{{ old('meta.metaDescription', '') }}',
            canonicalUrl: '{{ old('meta.canonicalUrl', '') }}',
            schema: '{{ old('meta.schema', '') }}',
            robots: '{{ old('meta.robots', 'Inherit') }}',
            indexing: '{{ old('meta.indexing', 'Inherit') }}',
            linkFollowing: '{{ old('meta.linkFollowing', 'Inherit') }}',
            noArchive: '{{ old('meta.noArchive', 'Inherit') }}',
            noImageIndex: '{{ old('meta.noImageIndex', 'Inherit') }}',
            noSnippet: '{{ old('meta.noSnippet', 'Inherit') }}',
            noTranslate: '{{ old('meta.noTranslate', 'Inherit') }}',
            noSiteLinksSearchBox: '{{ old('meta.noSiteLinksSearchBox', 'Inherit') }}',
            maxSnippet: '{{ old('meta.maxSnippet', '') }}',
            maxVideoPreview: '{{ old('meta.maxVideoPreview', '') }}',
            maxImagePreview: '{{ old('meta.maxImagePreview', 'Inherit') }}',
            ogType: '{{ old('meta.ogType', 'Inherit') }}',
            ogTitle: '{{ old('meta.ogTitle', '') }}',
            socialImage: '{{ old('meta.socialImage', '') }}',
            xHandle: '{{ old('meta.xHandle', '') }}',
            xCardTitle: '{{ old('meta.xCardTitle', '') }}',
            xCardDescription: '{{ old('meta.xCardDescription', '') }}',
            sitemap: '{{ old('meta.sitemap', 'Inherit') }}',
            sitemapPriority: '{{ old('meta.sitemapPriority', '') }}',
            sitemapFrequency: '{{ old('meta.sitemapFrequency', 'Inherit') }}'
        },
        slugify(v) {
            return v.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        },
        updateMeta(key, val) {
            this.meta[key] = val;
        },
        get effectiveSlug() {
            return this.slugEdited ? this.slug : this.slugify(this.title);
        }
    }"
>
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            @if($collection->icon)
                <i class="{{ $collection->icon }} text-lg w-6 text-center text-text-muted"></i>
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>
            @endif
            Create {{ $collection->name }}
        </h1>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.collections.entries.index', $collection) }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200"
            >
                Back
            </a>
            <button type="submit" form="entry-form"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
            >
                Create & Edit
            </button>
        </div>
    </header>

    <form id="entry-form" method="POST" action="{{ route('admin.collections.entries.store', $collection) }}">
        @csrf
        <input type="hidden" name="slug" :value="effectiveSlug">

        {{-- Single Card Panel containing Header and all tabs inside --}}
        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            {{-- Unified Card Header --}}
            <div class="px-[18px] pt-3 pb-2 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <div class="text-sm font-medium text-text-heading">New Entry</div>
                    <p class="text-sm text-text-muted mt-1">Configure the fields to create a new page in this collection.</p>
                </div>
                {{-- Switch pill buttons group --}}
                @if($collection->enable_seo)
                    <div class="bg-gray-100 p-1 rounded-xl inline-flex items-center gap-1 border border-gray-200 shadow-inner">
                        <button type="button" @click="activeTab = 'basic'" :class="activeTab === 'basic' ? 'bg-white shadow-sm text-text-heading font-semibold ring-1 ring-black/5' : 'text-text-muted hover:text-text-primary'" class="px-3.5 py-1.5 rounded-lg text-xs transition-all duration-150 cursor-pointer">Basic</button>
                        <button type="button" @click="activeTab = 'seo'" :class="activeTab === 'seo' ? 'bg-white shadow-sm text-text-heading font-semibold ring-1 ring-black/5' : 'text-text-muted hover:text-text-primary'" class="px-3.5 py-1.5 rounded-lg text-xs transition-all duration-150 cursor-pointer">Seo</button>
                        <button type="button" @click="activeTab = 'seo_pro'" :class="activeTab === 'seo_pro' ? 'bg-white shadow-sm text-text-heading font-semibold ring-1 ring-black/5' : 'text-text-muted hover:text-text-primary'" class="px-3.5 py-1.5 rounded-lg text-xs transition-all duration-150 cursor-pointer">SEO pro</button>
                    </div>
                @endif
            </div>

            {{-- Card Body --}}
            <div class="px-1.5 pb-2">
                {{-- Basic Tab --}}
                <div x-show="activeTab === 'basic'">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            {{-- Title --}}
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-title" class="text-sm font-medium text-text-heading">Title</label>
                                    <div class="text-sm text-text-muted">A descriptive title for this page.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input id="field-title" type="text" name="data[title]" x-model="title" placeholder="Enter title" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                        @error('data.title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- URL Slug --}}
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-slug" class="text-sm font-medium text-text-heading">URL Slug</label>
                                    <div class="text-sm text-text-muted">The URL-friendly identifier for this page. Auto-generated from the title, but you can customize it.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input id="field-slug" type="text" :value="effectiveSlug" @input="slugEdited = true; slug = slugify($event.target.value)" placeholder="enter-slug" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                        @error('slug') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Author --}}
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading">Author</label>
                                    <div class="text-sm text-text-muted">The person credited as the author of this page content.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                <span class="truncate" x-text="meta.author"></span>
                                                <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-80 overflow-y-auto [scrollbar-width:thin] space-y-0.5" style="display: none;">
                                                @foreach($admins as $admin)
                                                    <button type="button" @click="updateMeta('author', '{{ $admin->name }}'); open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="'{{ $admin->name }}' === meta.author ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                        <span>{{ $admin->name }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="meta[author]" :value="meta.author">
                                        </div>
                                    </div>
                                </div>
                            </div>



                            {{-- Custom fields --}}
                            @foreach($collection->fields ?? [] as $field)
                                @php
                                    $rawKey = $field['template'] ?? $loop->index;
                                    $key = str_replace(['@{{', '@}}', '@{', '}@'], '', $rawKey);
                                    $value = old('data.' . $key, $field['default_entry_id'] ?? $field['default_value'] ?? $field['default'] ?? '');
                                @endphp
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label for="field-{{ $loop->index }}" class="text-sm font-medium text-text-heading">{{ $field['title'] ?? 'Field' }}</label>
                                        @if(($field['description'] ?? '') !== '')
                                            <div class="text-sm text-text-muted">{{ $field['description'] }}</div>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 min-w-0 w-full">
                                        <div class="flex-1 min-w-0 w-full">
                                            @switch($field['type'] ?? 'text')
                                                @case('textarea')
                                                    <textarea id="field-{{ $loop->index }}" name="data[{{ $key }}]" rows="3" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ $value }}</textarea>
                                                    @break
                                                @case('number')
                                                    <input type="number" id="field-{{ $loop->index }}" name="data[{{ $key }}]" value="{{ $value }}" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                    @break
                                                 @case('date')
                                                 @case('datetime')
                                                 @case('datetime-local')
                                                     @php
                                                         $isDateTime = ! empty($field['enable_time']) || ($field['type'] ?? '') === 'datetime' || ($field['type'] ?? '') === 'datetime-local';
                                                         $format = $isDateTime ? 'Y-m-d H:i' : 'Y-m-d';
                                                         $displayFormat = $isDateTime ? 'M j, Y h:i K' : 'M j, Y';
                                                     @endphp
                                                     <div class="relative flex items-center w-full"
                                                         x-data="{
                                                             fp: null,
                                                             initFlatpickr() {
                                                                 const self = this;
                                                                 const run = function() {
                                                                     if (typeof flatpickr !== 'undefined' && self.$refs.pickerInput) {
                                                                         self.fp = flatpickr(self.$refs.pickerInput, {
                                                                             enableTime: {{ $isDateTime ? 'true' : 'false' }},
                                                                             dateFormat: '{{ $format }}',
                                                                             altInput: true,
                                                                             altFormat: '{{ $displayFormat }}',
                                                                             altInputClass: 'w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer',
                                                                             allowInput: true
                                                                         });
                                                                     } else {
                                                                         setTimeout(run, 50);
                                                                     }
                                                                 };
                                                                 run();
                                                             }
                                                         }"
                                                         x-init="initFlatpickr()"
                                                     >
                                                         <input type="text"
                                                             id="field-{{ $loop->index }}"
                                                             name="data[{{ $key }}]"
                                                             value="{{ $value }}"
                                                             x-ref="pickerInput"
                                                             placeholder="{{ $isDateTime ? 'Select date & time...' : 'Select date...' }}"
                                                             class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer">
                                                         <div class="absolute right-2.5 pointer-events-none text-text-muted">
                                                             <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                                         </div>
                                                     </div>
                                                     @break
                                                @case('image')
                                                    @php
                                                        $imgVal = is_string($value) ? $value : '';
                                                    @endphp
                                                    <div
                                                        class="w-full rounded-lg border border-content-border bg-content-bg overflow-hidden shadow-sm min-w-0"
                                                        x-data="{
                                                            imageUrl: '{{ addslashes($imgVal) }}',
                                                            altOpen: false,
                                                            alt: '',
                                                            size: null,
                                                            get imageName() {
                                                                if (!this.imageUrl) return '';
                                                                try {
                                                                    const path = this.imageUrl.split('?')[0];
                                                                    return decodeURIComponent(path.split('/').pop() || path);
                                                                } catch (e) {
                                                                    return this.imageUrl;
                                                                }
                                                            },
                                                            formatSize(bytes) {
                                                                if (bytes == null) return '';
                                                                if (bytes < 1024) return bytes + ' B';
                                                                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
                                                                return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
                                                            },
                                                            openAssetPicker() {
                                                                window.dispatchEvent(new CustomEvent('open-asset-picker', {
                                                                    detail: {
                                                                        callback: (url) => {
                                                                            this.imageUrl = url;
                                                                            this.fetchSize(url);
                                                                        }
                                                                    }
                                                                }));
                                                            },
                                                            clearImage() {
                                                                this.imageUrl = '';
                                                                this.alt = '';
                                                                this.altOpen = false;
                                                                this.size = null;
                                                            },
                                                            fetchSize(url) {
                                                                if (!url) {
                                                                    this.size = null;
                                                                    return;
                                                                }
                                                                fetch(url, { method: 'HEAD' })
                                                                    .then((r) => {
                                                                        const len = r.headers.get('content-length');
                                                                        this.size = len ? parseInt(len, 10) : null;
                                                                    })
                                                                    .catch(() => { this.size = null; });
                                                            },
                                                            init() {
                                                                if (this.imageUrl) {
                                                                    this.fetchSize(this.imageUrl);
                                                                }
                                                            }
                                                        }"
                                                        @dragover.prevent="$event.currentTarget.classList.add('border-primary', 'bg-primary/5')"
                                                        @dragleave.prevent="$event.currentTarget.classList.remove('border-primary', 'bg-primary/5')"
                                                        @drop.prevent="
                                                            $event.currentTarget.classList.remove('border-primary', 'bg-primary/5');
                                                            const file = $event.dataTransfer.files[0];
                                                            if (file && file.type.startsWith('image/')) {
                                                                const reader = new FileReader();
                                                                reader.onload = (e) => { imageUrl = e.target.result; fetchSize(e.target.result); };
                                                                reader.readAsDataURL(file);
                                                            }
                                                        "
                                                    >
                                                        <input type="hidden" id="field-{{ $loop->index }}" name="data[{{ $key }}]" :value="imageUrl">
                                                        {{-- Toolbar --}}
                                                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 px-2.5 py-2.5 min-w-0">
                                                            <button
                                                                type="button"
                                                                @click="openAssetPicker()"
                                                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-8 text-xs leading-tight px-3 bg-gradient-to-b from-content-bg to-gray-50 hover:to-gray-100 text-text-primary border border-content-border shadow-sm"
                                                            >
                                                                <svg viewBox="0 0 24 24" fill="none" class="size-4 shrink-0">
                                                                    <path d="M3 7a2 2 0 0 1 2-2h3.5l2 2H19a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                                                </svg>
                                                                Browse Assets
                                                            </button>
                                                            <div class="flex items-center gap-1.5 text-xs sm:text-sm text-text-muted min-w-0 flex-1">
                                                                <svg viewBox="0 0 24 24" fill="none" class="size-4 shrink-0">
                                                                    <path d="M7 18a4 4 0 0 1-.5-7.97A5 5 0 0 1 16 8.5a3.5 3.5 0 0 1 1.5 6.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                                    <path d="M12 11.5v6m0-6 2 2m-2-2-2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                                <span class="truncate">
                                                                    Drag &amp; drop or
                                                                    <button type="button" @click="openAssetPicker()" class="underline hover:text-text-primary">choose file</button>.
                                                                    <span x-show="imageUrl" x-cloak class="font-medium text-text-primary"> (selected)</span>
                                                                </span>
                                                            </div>
                                                        </div>

                                                        {{-- Selected file row --}}
                                                        <div class="border-t border-content-border min-w-0" x-show="imageUrl" x-cloak>
                                                            <div class="flex items-center gap-2 sm:gap-3 px-2.5 py-2 min-w-0">
                                                                <div class="size-8 rounded-md overflow-hidden bg-panel-bg flex items-center justify-center shrink-0">
                                                                    <img :src="imageUrl" :alt="alt || imageName" class="size-full object-cover">
                                                                </div>
                                                                <span class="flex-1 min-w-0 truncate text-sm text-text-primary" x-text="imageName"></span>
                                                                <button
                                                                    type="button"
                                                                    @click="altOpen = !altOpen"
                                                                    class="shrink-0 rounded-md border px-2 py-0.5 text-xs font-medium transition-colors"
                                                                    :class="alt
                                                                        ? 'border-primary bg-primary/10 text-primary'
                                                                        : 'border-content-border text-primary hover:bg-primary/10'"
                                                                >
                                                                    Set Alt
                                                                </button>
                                                                <span class="shrink-0 text-xs text-text-muted tabular-nums" x-show="size != null" x-text="formatSize(size)"></span>
                                                                <button
                                                                    type="button"
                                                                    aria-label="Remove image"
                                                                    @click="clearImage()"
                                                                    class="shrink-0 flex size-6 items-center justify-center rounded-md text-text-muted hover:bg-text-primary/10 hover:text-text-primary transition-colors"
                                                                >
                                                                    <svg viewBox="0 0 24 24" fill="none" class="size-4">
                                                                        <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            <div class="px-2.5 pb-2.5" x-show="altOpen" x-cloak>
                                                                <input
                                                                    type="text"
                                                                    x-model="alt"
                                                                    placeholder="Alt text"
                                                                    class="w-full bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-1.5 h-9 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @break
                                                @case('collection')
                                                    @php
                                                        $targetCollection = \App\Models\Collection::find($field['collection_id'] ?? null);
                                                        $targetEntries = $targetCollection ? $targetCollection->entries()->get() : collect();
                                                        $selectedEntry = $targetEntries->firstWhere('id', $value);
                                                        $selectedLabel = $selectedEntry?->title ?? $selectedEntry?->data['title'] ?? 'Choose entry...';
                                                    @endphp
                                                    <div x-data="{ open: false, selectedValue: '{{ $value }}', label: '{{ addslashes($selectedLabel) }}' }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                            <span class="truncate" x-text="label"></span>
                                                            <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-80 overflow-y-auto [scrollbar-width:thin] space-y-0.5" style="display: none;">
                                                            <button type="button" @click="selectedValue = ''; label = 'Choose entry...'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors text-text-muted hover:bg-content-border/30">
                                                                <span>None</span>
                                                            </button>
                                                            @foreach ($targetEntries as $te)
                                                                @php $teTitle = $te->data['title'] ?? $te->page?->title ?? 'Untitled Entry'; @endphp
                                                                <button type="button" @click="selectedValue = '{{ $te->id }}'; label = '{{ addslashes($teTitle) }}'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selectedValue == '{{ $te->id }}' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                    <span>{{ $teTitle }}</span>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                        <input type="hidden" name="data[{{ $key }}]" :value="selectedValue">
                                                    </div>
                                                    @break
                                                @case('taxonomies')
                                                    @php
                                                        $taxId = $field['taxonomy_id'] ?? null;
                                                        $isMultiple = !empty($field['multiple']);
                                                        $termModels = collect();
                                                        if ($taxId) {
                                                            $termModels = \App\Models\Term::where('taxonomy_id', $taxId)->orderBy('title')->get();
                                                            if ($termModels->isEmpty()) {
                                                                $termModels = \App\Models\Taxonomy::where('id', $taxId)->orderBy('title')->get();
                                                            }
                                                        } else {
                                                            $termsFromTable = \App\Models\Term::orderBy('title')->get();
                                                            $taxonomiesFromTable = \App\Models\Taxonomy::orderBy('title')->get();
                                                            $termModels = $termsFromTable->concat($taxonomiesFromTable)->unique(fn($item) => $item->title);
                                                        }
                                                    @endphp

                                                    @if($isMultiple)
                                                        @php
                                                            $rawSelected = is_array($value) ? $value : (is_string($value) && $value !== '' ? json_decode($value, true) ?: [$value] : []);
                                                            $selectedArray = array_map('strval', array_filter((array) $rawSelected));
                                                        @endphp
                                                        <div x-data="{
                                                            open: false,
                                                            selected: @json($selectedArray),
                                                            toggle(val) {
                                                                val = String(val);
                                                                if (this.selected.includes(val)) {
                                                                    this.selected = this.selected.filter(i => i !== val);
                                                                } else {
                                                                    this.selected.push(val);
                                                                }
                                                            },
                                                            get label() {
                                                                if (this.selected.length === 0) return 'Select items...';
                                                                return this.selected.length + ' selected';
                                                            }
                                                        }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                                <span class="truncate font-medium text-text-heading" x-text="label"></span>
                                                                <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-80 overflow-y-auto [scrollbar-width:thin] space-y-0.5" style="display: none;">
                                                                @foreach ($termModels as $t)
                                                                    @php
                                                                        $tId = (string) $t->id;
                                                                        $tTitle = $t->title ?? 'Untitled Item';
                                                                    @endphp
                                                                    <button type="button" @click="toggle('{{ $tId }}')" class="w-full flex items-center justify-between px-3 py-1.5 text-sm rounded-md transition-colors" :class="selected.includes('{{ $tId }}') ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                        <span>{{ $tTitle }}</span>
                                                                        <svg x-show="selected.includes('{{ $tId }}')" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                                        </svg>
                                                                    </button>
                                                                @endforeach
                                                                @if($termModels->isEmpty())
                                                                    <div class="px-3 py-2 text-sm text-text-muted text-center">
                                                                        No items found.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <template x-for="itemVal in selected" :key="itemVal">
                                                                <input type="hidden" name="data[{{ $key }}][]" :value="itemVal">
                                                            </template>
                                                        </div>
                                                    @else
                                                        @php
                                                            $selectedValue = is_array($value) ? ($value[0] ?? '') : (string)($value ?? '');
                                                            $selectedCat = $termModels->first(function ($t) use ($selectedValue) {
                                                                $tId = (string) $t->id;
                                                                $tSlug = $t->slug ?? '';
                                                                $tTitle = $t->title ?? '';
                                                                return $selectedValue == $tId || ($selectedValue !== '' && (strtolower($selectedValue) == strtolower($tTitle) || strtolower($selectedValue) == strtolower($tSlug)));
                                                            });
                                                            $selectedLabel = $selectedCat?->title ?? 'Select item...';
                                                            $initialVal = $selectedCat ? (string)$selectedCat->id : $selectedValue;
                                                        @endphp
                                                        <div x-data="{ open: false, selectedValue: '{{ addslashes($initialVal) }}', label: '{{ addslashes($selectedLabel) }}' }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                                <span class="truncate font-medium text-text-heading" x-text="label"></span>
                                                                <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-80 overflow-y-auto [scrollbar-width:thin] space-y-0.5" style="display: none;">
                                                                <button type="button" @click="selectedValue = ''; label = 'Select item...'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors text-text-muted hover:bg-content-border/30">
                                                                    <span>None</span>
                                                                </button>
                                                                @foreach ($termModels as $t)
                                                                    @php
                                                                        $tId = (string) $t->id;
                                                                        $tTitle = $t->title ?? 'Untitled Item';
                                                                    @endphp
                                                                    <button type="button" @click="selectedValue = '{{ $tId }}'; label = '{{ addslashes($tTitle) }}'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selectedValue == '{{ $tId }}' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                        <span>{{ $tTitle }}</span>
                                                                    </button>
                                                                @endforeach
                                                                @if($termModels->isEmpty())
                                                                    <div class="px-3 py-2 text-sm text-text-muted text-center">
                                                                        No items found.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <input type="hidden" name="data[{{ $key }}]" :value="selectedValue">
                                                        </div>
                                                    @endif
                                                    @break
                                                @case('tags')
                                                    @php
                                                        $selectedTags = is_array($value) ? $value : (is_string($value) ? json_decode($value, true) ?: [] : []);
                                                        $selectedTags = array_filter($selectedTags);
                                                    @endphp
                                                    <div x-data="{
                                                        tags: @json($selectedTags),
                                                        newTag: '',
                                                        addTag() {
                                                            const val = this.newTag.trim();
                                                            if (val && !this.tags.includes(val)) {
                                                                this.tags.push(val);
                                                            }
                                                            this.newTag = '';
                                                        },
                                                        removeTag(index) {
                                                            this.tags.splice(index, 1);
                                                        }
                                                    }" class="w-full">
                                                        <div class="flex flex-wrap items-center gap-1.5 p-1.5 w-full bg-content-bg border border-content-border rounded-lg min-h-[36px] transition-all focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary">
                                                            <template x-for="(tag, index) in tags" :key="index">
                                                                <span class="inline-flex items-center gap-1 bg-primary/10 text-primary text-xs font-medium px-2 py-0.5 rounded-md">
                                                                    <span x-text="tag"></span>
                                                                    <button type="button" @click="removeTag(index)" class="hover:text-danger focus:outline-none">
                                                                        <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                                        </svg>
                                                                    </button>
                                                                </span>
                                                            </template>
                                                            <input type="text"
                                                                x-model="newTag"
                                                                @keydown.enter.prevent="addTag()"
                                                                @keydown.comma.prevent="addTag()"
                                                                @blur="addTag()"
                                                                placeholder="Add tag..."
                                                                class="flex-1 bg-transparent border-0 p-0 text-sm focus:ring-0 focus:outline-none min-w-[120px] text-text-primary placeholder:text-text-muted"
                                                            >
                                                        </div>
                                                        <template x-for="tag in tags" :key="tag">
                                                            <input type="hidden" name="data[{{ $key }}][]" :value="tag">
                                                        </template>
                                                        <input type="hidden" name="data[{{ $key }}][]" value="" x-show="tags.length === 0">
                                                    </div>
                                                    @break
                                                @case('location')
                                                    @php
                                                        $showCountry = $field['enable_country'] ?? true;
                                                        $showState = $field['enable_state'] ?? true;
                                                        $showCity = $field['enable_city'] ?? true;
                                                        $activeCount = ($showCountry ? 1 : 0) + ($showState ? 1 : 0) + ($showCity ? 1 : 0);
                                                        $gridClass = match($activeCount) {
                                                            1 => 'grid-cols-1',
                                                            2 => 'grid-cols-1 sm:grid-cols-2',
                                                            default => 'grid-cols-1 sm:grid-cols-3',
                                                        };
                                                    @endphp
                                                    <div class="w-full rounded-lg border border-content-border bg-content-bg shadow-sm min-w-0" x-data="locationField({{ json_encode($value, JSON_UNESCAPED_UNICODE) }}, { enable_country: {{ $showCountry ? 'true' : 'false' }}, enable_state: {{ $showState ? 'true' : 'false' }}, enable_city: {{ $showCity ? 'true' : 'false' }} })">
                                                        {{-- Selectors toolbar / grid --}}
                                                        <div class="p-2.5">
                                                            <div class="grid {{ $gridClass }} gap-2.5 w-full">
                                                                @if($showCountry)
                                                                    {{-- Country Select --}}
                                                                    <div class="relative">
                                                                        <button type="button" @click="countryOpen = !countryOpen; stateOpen = false; cityOpen = false" class="w-full flex items-center justify-between bg-white border border-content-border text-text-primary text-xs rounded-lg px-2.5 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs hover:border-gray-400">
                                                                            <div class="flex items-center gap-1.5 truncate">
                                                                                <span x-show="countryFlag" x-text="countryFlag" class="text-sm leading-none"></span>
                                                                                <span class="truncate font-medium text-text-heading" x-text="country || 'Select Country...'"></span>
                                                                            </div>
                                                                            <svg class="size-3.5 text-text-muted shrink-0 transition-transform duration-150" :class="countryOpen ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                            </svg>
                                                                        </button>
                                                                        <div x-show="countryOpen" @click.outside="countryOpen = false" @keydown.escape.window="countryOpen = false" class="absolute z-50 top-full mt-1.5 left-0 right-0 min-w-[220px] bg-content-bg border border-content-border rounded-lg shadow-xl overflow-hidden" style="display: none;">
                                                                            <div class="p-1.5 border-b border-content-border bg-content-bg">
                                                                                <input type="text" x-model="countrySearch" placeholder="Search countries..." class="w-full bg-white border border-content-border text-text-primary text-xs rounded-md px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                                                            </div>
                                                                            <div class="p-1.5 space-y-0.5 max-h-56 overflow-y-auto [scrollbar-width:thin]">
                                                                                <button type="button" @click="selectCountry(null)" class="w-full text-left px-2.5 py-1.5 text-xs rounded-md transition-colors text-text-muted hover:bg-content-border/30">
                                                                                    None (Clear)
                                                                                </button>
                                                                                <template x-for="c in countries" :key="c.isoCode">
                                                                                    <button type="button" @click="selectCountry(c)" class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-md transition-colors" :class="countryCode === c.isoCode ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                                        <div class="flex items-center gap-1.5 truncate">
                                                                                            <span x-text="c.flag" class="text-sm"></span>
                                                                                            <span class="truncate" x-text="c.name"></span>
                                                                                        </div>
                                                                                        <span class="text-[10px] font-mono text-text-muted shrink-0 ml-1" x-text="c.isoCode"></span>
                                                                                    </button>
                                                                                </template>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if($showState)
                                                                    {{-- State Select --}}
                                                                    <div class="relative">
                                                                        <button type="button" :disabled="enableCountry && !countryCode" @click="if (!enableCountry || countryCode) { stateOpen = !stateOpen; countryOpen = false; cityOpen = false; }" class="w-full flex items-center justify-between bg-white border border-content-border text-text-primary text-xs rounded-lg px-2.5 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs hover:border-gray-400" :class="enableCountry && !countryCode ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'cursor-pointer'">
                                                                            <span class="truncate font-medium text-text-heading" x-text="state || (enableCountry && !countryCode ? 'State' : 'Select State...')"></span>
                                                                            <svg class="size-3.5 text-text-muted shrink-0 transition-transform duration-150" :class="stateOpen ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                            </svg>
                                                                        </button>
                                                                        <div x-show="stateOpen" @click.outside="stateOpen = false" @keydown.escape.window="stateOpen = false" class="absolute z-50 top-full mt-1.5 left-0 right-0 min-w-[220px] bg-content-bg border border-content-border rounded-lg shadow-xl overflow-hidden" style="display: none;">
                                                                            <div class="p-1.5 border-b border-content-border bg-content-bg">
                                                                                <input type="text" x-model="stateSearch" placeholder="Search states..." class="w-full bg-white border border-content-border text-text-primary text-xs rounded-md px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                                                                            </div>
                                                                            <div class="p-1.5 space-y-0.5 max-h-56 overflow-y-auto [scrollbar-width:thin]">
                                                                                <button type="button" @click="selectState(null)" class="w-full text-left px-2.5 py-1.5 text-xs rounded-md transition-colors text-text-muted hover:bg-content-border/30">
                                                                                    None (Clear State)
                                                                                </button>
                                                                                <template x-for="s in states" :key="s.isoCode || s.name">
                                                                                    <button type="button" @click="selectState(s)" class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-md transition-colors" :class="stateCode === s.isoCode || state === s.name ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                                        <span class="truncate" x-text="s.name"></span>
                                                                                        <span class="text-[10px] font-mono text-text-muted shrink-0 ml-1" x-text="s.isoCode"></span>
                                                                                    </button>
                                                                                </template>
                                                                                <template x-if="states.length === 0">
                                                                                    <div class="px-2.5 py-2 text-xs text-text-muted text-center">No states found</div>
                                                                                </template>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if($showCity)
                                                                    {{-- City Select / Input --}}
                                                                    <div class="relative">
                                                                        <button type="button" :disabled="enableCountry && !countryCode" @click="if (!enableCountry || countryCode) { cityOpen = !cityOpen; countryOpen = false; stateOpen = false; }" class="w-full flex items-center justify-between bg-white border border-content-border text-text-primary text-xs rounded-lg px-2.5 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs hover:border-gray-400" :class="enableCountry && !countryCode ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'cursor-pointer'">
                                                                            <span class="truncate font-medium text-text-heading" x-text="city || (enableCountry && !countryCode ? 'City' : 'Select City...')"></span>
                                                                            <svg class="size-3.5 text-text-muted shrink-0 transition-transform duration-150" :class="cityOpen ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                            </svg>
                                                                        </button>
                                                                        <div x-show="cityOpen" @click.outside="cityOpen = false" @keydown.escape.window="cityOpen = false" class="absolute z-50 top-full mt-1.5 left-0 right-0 min-w-[220px] bg-content-bg border border-content-border rounded-lg shadow-xl overflow-hidden" style="display: none;">
                                                                            <div class="p-1.5 border-b border-content-border bg-content-bg">
                                                                                <input type="text" x-model="citySearch" placeholder="Search or type city..." class="w-full bg-white border border-content-border text-text-primary text-xs rounded-md px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary" @keydown.enter.prevent="if (citySearch.trim()) { selectCity(citySearch.trim()); }">
                                                                            </div>
                                                                            <div class="p-1.5 space-y-0.5 max-h-56 overflow-y-auto [scrollbar-width:thin]">
                                                                                <button type="button" @click="selectCity('')" class="w-full text-left px-2.5 py-1.5 text-xs rounded-md transition-colors text-text-muted hover:bg-content-border/30">
                                                                                    None (Clear City)
                                                                                </button>
                                                                                <template x-if="citySearch.trim() && !cities.some(c => c.name.toLowerCase() === citySearch.trim().toLowerCase())">
                                                                                    <button type="button" @click="selectCity(citySearch.trim())" class="w-full flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-md text-primary hover:bg-primary/10 transition-colors font-medium border border-primary/20">
                                                                                        <span>Use:</span>
                                                                                        <strong x-text="citySearch.trim()" class="truncate"></strong>
                                                                                    </button>
                                                                                </template>
                                                                                <template x-for="ct in cities" :key="ct.name + (ct.stateCode || '')">
                                                                                    <button type="button" @click="selectCity(ct.name)" class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-md transition-colors" :class="city === ct.name ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                                        <span class="truncate" x-text="ct.name"></span>
                                                                                    </button>
                                                                                </template>
                                                                                <template x-if="cities.length === 0 && !citySearch.trim()">
                                                                                    <div class="px-2.5 py-2 text-xs text-text-muted text-center">No cities found. Type to add custom city.</div>
                                                                                </template>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        {{-- Selected Location row (matching image selector's selected row) --}}
                                                        <div class="border-t border-content-border min-w-0" x-show="formatted" style="display: none;">
                                                            <div class="flex items-center gap-2.5 sm:gap-3 pl-3.5 pr-2.5 py-2.5 min-w-0">
                                                                <template x-if="countryFlag">
                                                                    <span x-text="countryFlag" class="text-lg leading-none shrink-0"></span>
                                                                </template>
                                                                <template x-if="!countryFlag">
                                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-4 text-text-muted shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                                                </template>
                                                                <span class="flex-1 min-w-0 truncate text-sm font-medium text-text-primary" x-text="formatted"></span>
                                                                <template x-if="countryCode">
                                                                    <span class="shrink-0 text-xs text-text-muted tabular-nums font-mono px-1.5 py-0.5 rounded bg-panel-bg border border-content-border" x-text="countryCode"></span>
                                                                </template>
                                                                <button
                                                                    type="button"
                                                                    aria-label="Remove location"
                                                                    @click="clearAll()"
                                                                    class="shrink-0 flex size-6 items-center justify-center rounded-md text-text-muted hover:bg-text-primary/10 hover:text-text-primary transition-colors cursor-pointer"
                                                                >
                                                                    <svg viewBox="0 0 24 24" fill="none" class="size-4">
                                                                        <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        {{-- Hidden Inputs --}}
                                                        @if($showCountry)
                                                            <input type="hidden" name="data[{{ $key }}][country]" :value="country">
                                                            <input type="hidden" name="data[{{ $key }}][country_code]" :value="countryCode">
                                                        @endif
                                                        @if($showState)
                                                            <input type="hidden" name="data[{{ $key }}][state]" :value="state">
                                                            <input type="hidden" name="data[{{ $key }}][state_code]" :value="stateCode">
                                                        @endif
                                                        @if($showCity)
                                                            <input type="hidden" name="data[{{ $key }}][city]" :value="city">
                                                        @endif
                                                        <input type="hidden" name="data[{{ $key }}][formatted]" :value="formatted">
                                                    </div>
                                                    @break
                                                @default
                                                    <input type="text" id="field-{{ $loop->index }}" name="data[{{ $key }}]" value="{{ $value }}" placeholder="{{ $field['title'] ?? '' }}" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Published --}}
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading">Published</label>
                                    <div class="text-sm text-text-muted">Make this page visible to visitors. Unpublished pages are only accessible from the admin area.</div>
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

                {{-- SEO & SEO Pro Tabs Content --}}
                @if($collection->enable_seo)
                    <div x-show="activeTab === 'seo' || activeTab === 'seo_pro'">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
                        {{-- Left Fields Column (2/3 width) --}}
                        <div class="lg:col-span-2">
                            {{-- SEO Tab Fields --}}
                            <div x-show="activeTab === 'seo'" class="space-y-4">
                                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                                    <div class="divide-y divide-content-border">
                                        {{-- Section: Meta Data --}}
                                        <div class="px-[18px] py-3">
                                            <h3 class="text-sm font-semibold text-text-heading">Meta Data</h3>
                                        </div>
                                        <div class="divide-y divide-content-border">
                                            {{-- Meta Title --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label for="field-metaTitle" class="text-sm font-medium text-text-heading">Meta Title</label>
                                                    <div class="text-sm text-text-muted">A unique Meta Title for this page, ideally less than 60 characters. Leave blank to use the page title.</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <input id="field-metaTitle" type="text" name="meta[metaTitle]" :value="meta.metaTitle" @input="updateMeta('metaTitle', $event.target.value)" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Meta Description --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label for="field-metaDescription" class="text-sm font-medium text-text-heading">Meta Description</label>
                                                    <div class="text-sm text-text-muted">A unique Meta Description for this page, ideally less than 160 characters. Leave blank to use the site default.</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <textarea id="field-metaDescription" name="meta[metaDescription]" :value="meta.metaDescription" @input="updateMeta('metaDescription', $event.target.value)" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 min-h-[60px] resize-y transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Canonical URL --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label for="field-canonicalUrl" class="text-sm font-medium text-text-heading">Canonical URL</label>
                                                    <div class="text-sm text-text-muted">The canonical URL for this page. Leave blank to use the page's own URL.</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <input id="field-canonicalUrl" type="text" name="meta[canonicalUrl]" :value="meta.canonicalUrl" @input="updateMeta('canonicalUrl', $event.target.value)" placeholder="https://" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Schema / JSON-LD Section --}}
                                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                                    <div class="divide-y divide-content-border">
                                        <div class="px-[18px] py-3">
                                            <h3 class="text-sm font-semibold text-text-heading">JSON-LD</h3>
                                        </div>
                                        <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                            <div class="flex flex-col gap-1.5">
                                                <label for="field-schema" class="text-sm font-medium text-text-heading">Schema</label>
                                                <div class="text-sm text-text-muted">Paste your custom schema objects here (Recipe, Event, etc). Will be wrapped in the appropriate script tag.</div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1">
                                                    <textarea id="field-schema" name="meta[schema]" :value="meta.schema" @input="updateMeta('schema', $event.target.value)" placeholder='{ "@type": "..." }' class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 min-h-[60px] resize-y transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SEO Pro Tab Fields --}}
                            <div x-show="activeTab === 'seo_pro'" class="space-y-4">
                                {{-- Section: Robots --}}
                                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                                    <div class="divide-y divide-content-border">
                                        <div class="px-[18px] py-3">
                                            <h3 class="text-sm font-semibold text-text-heading">Robots</h3>
                                        </div>
                                        @php
                                            $robotFields = [
                                                ['key' => 'robots', 'label' => 'Robots', 'desc' => 'Control how search engines crawl and index this page.', 'options' => ['Inherit', 'all', 'noindex', 'nofollow', 'none', 'noarchive', 'nosnippet']],
                                                ['key' => 'indexing', 'label' => 'Indexing', 'desc' => 'Control whether search engines can index this page.', 'options' => ['Inherit', 'Yes', 'No']],
                                                ['key' => 'linkFollowing', 'label' => 'Link Following', 'desc' => 'Control whether search engines can follow links on this page.', 'options' => ['Inherit', 'Yes', 'No']],
                                                ['key' => 'noArchive', 'label' => 'No Archive', 'desc' => 'Prevent search engines from showing a cached link to this page.', 'options' => ['Inherit', 'Yes', 'No']],
                                                ['key' => 'noImageIndex', 'label' => 'No Image Index', 'desc' => 'Prevent search engines from indexing images on this page.', 'options' => ['Inherit', 'Yes', 'No']],
                                                ['key' => 'noSnippet', 'label' => 'No Snippet', 'desc' => 'Prevent search engines from showing a text snippet for this page.', 'options' => ['Inherit', 'Yes', 'No']],
                                                ['key' => 'noTranslate', 'label' => 'No Translate', 'desc' => 'Prevent search engines from offering translation of this page.', 'options' => ['Inherit', 'Yes', 'No']],
                                                ['key' => 'noSiteLinksSearchBox', 'label' => 'No Site Links Search Box', 'desc' => 'Prevent Google from showing a sitelinks search box.', 'options' => ['Inherit', 'Yes', 'No']],
                                                ['key' => 'maxSnippet', 'label' => 'Max Snippet', 'desc' => 'Maximum number of characters for a text snippet.', 'options' => null, 'placeholder' => '-1'],
                                                ['key' => 'maxVideoPreview', 'label' => 'Max Video Preview', 'desc' => 'Maximum duration of video preview in seconds.', 'options' => null, 'placeholder' => '-1'],
                                                ['key' => 'maxImagePreview', 'label' => 'Max Image Preview', 'desc' => 'Maximum size of image preview.', 'options' => ['Inherit', 'none', 'standard', 'large']],
                                            ];
                                        @endphp
                                        @foreach ($robotFields as $rf)
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label for="field-{{ $rf['key'] }}" class="text-sm font-medium text-text-heading">{{ $rf['label'] }}</label>
                                                    <div class="text-sm text-text-muted">{{ $rf['desc'] }}</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        @if ($rf['options'])
                                                            <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                                <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                                    <span class="truncate" x-text="meta.{{ $rf['key'] }}"></span>
                                                                    <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                                <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-80 overflow-y-auto [scrollbar-width:thin] space-y-0.5" style="display: none;">
                                                                    @foreach ($rf['options'] as $opt)
                                                                        <button type="button" @click="updateMeta('{{ $rf['key'] }}', '{{ $opt }}'); open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="'{{ $opt }}' === meta.{{ $rf['key'] }} ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                            <span>{{ $opt }}</span>
                                                                        </button>
                                                                    @endforeach
                                                                </div>
                                                                <input type="hidden" name="meta[{{ $rf['key'] }}]" :value="meta.{{ $rf['key'] }}">
                                                            </div>
                                                        @else
                                                            <input id="field-{{ $rf['key'] }}" type="text" name="meta[{{ $rf['key'] }}]" :value="meta.{{ $rf['key'] }}" @input="updateMeta('{{ $rf['key'] }}', $event.target.value)" placeholder="{{ $rf['placeholder'] ?? '' }}" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Section: Open Graph --}}
                                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                                    <div class="divide-y divide-content-border">
                                        <div class="px-[18px] py-3">
                                            <h3 class="text-sm font-semibold text-text-heading">Open Graph</h3>
                                        </div>
                                        <div class="divide-y divide-content-border">
                                            {{-- OG Type --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label class="text-sm font-medium text-text-heading">Open Graph Type</label>
                                                    <div class="text-sm text-text-muted">The type of content (eg. website, article).</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                                <span class="truncate" x-text="meta.ogType"></span>
                                                                <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-80 overflow-y-auto [scrollbar-width:thin] space-y-0.5" style="display: none;">
                                                                @foreach (['Inherit', 'website', 'article', 'profile', 'book', 'music.song', 'video.movie'] as $opt)
                                                                    <button type="button" @click="updateMeta('ogType', '{{ $opt }}'); open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="'{{ $opt }}' === meta.ogType ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                        <span>{{ $opt }}</span>
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                            <input type="hidden" name="meta[ogType]" :value="meta.ogType">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- OG Title --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label for="field-ogTitle" class="text-sm font-medium text-text-heading">Open Graph Title</label>
                                                    <div class="text-sm text-text-muted">Title shown when shared on social platforms. Leave blank to use the meta title.</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <input id="field-ogTitle" type="text" name="meta[ogTitle]" :value="meta.ogTitle" @input="updateMeta('ogTitle', $event.target.value)" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Social Image --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label for="field-socialImage" class="text-sm font-medium text-text-heading">Social Image</label>
                                                    <div class="text-sm text-text-muted">This image is used as a social network preview image.</div>
                                                </div>
                                                <div x-data="{
                                                    size: null,
                                                    get imageName() {
                                                        if (!meta.socialImage) return '';
                                                        try {
                                                            const path = meta.socialImage.split('?')[0];
                                                            return decodeURIComponent(path.split('/').pop() || path);
                                                        } catch (e) {
                                                            return meta.socialImage;
                                                        }
                                                    },
                                                    formatSize(bytes) {
                                                        if (bytes == null) return '';
                                                        if (bytes < 1024) return bytes + ' B';
                                                        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
                                                        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
                                                    },
                                                    openAssetPicker() {
                                                        window.dispatchEvent(new CustomEvent('open-asset-picker', {
                                                            detail: {
                                                                callback: (url) => {
                                                                    updateMeta('socialImage', url);
                                                                    this.fetchSize(url);
                                                                }
                                                            }
                                                        }));
                                                    },
                                                    clearImage() {
                                                        updateMeta('socialImage', '');
                                                        this.size = null;
                                                    },
                                                    fetchSize(url) {
                                                        if (!url) { this.size = null; return; }
                                                        fetch(url, { method: 'HEAD' })
                                                            .then((r) => {
                                                                const len = r.headers.get('content-length');
                                                                this.size = len ? parseInt(len, 10) : null;
                                                            })
                                                            .catch(() => { this.size = null; });
                                                    },
                                                    init() {
                                                        if (meta.socialImage) { this.fetchSize(meta.socialImage); }
                                                    }
                                                }">
                                                    <input type="hidden" name="meta[socialImage]" :value="meta.socialImage">
                                                    <div class="rounded-lg border border-content-border bg-content-bg overflow-hidden shadow-sm">
                                                        {{-- Toolbar --}}
                                                        <div class="flex items-center gap-3 px-2.5 py-2.5">
                                                            <button
                                                                type="button"
                                                                @click="openAssetPicker()"
                                                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-8 text-xs leading-tight px-3 bg-gradient-to-b from-content-bg to-gray-50 hover:to-gray-100 text-text-primary border border-content-border shadow-sm"
                                                            >
                                                                <svg viewBox="0 0 24 24" fill="none" class="size-4 shrink-0">
                                                                    <path d="M3 7a2 2 0 0 1 2-2h3.5l2 2H19a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                                                </svg>
                                                                Browse Assets
                                                            </button>
                                                            <div class="flex items-center gap-1.5 text-sm text-text-muted min-w-0">
                                                                <svg viewBox="0 0 24 24" fill="none" class="size-4 shrink-0">
                                                                    <path d="M7 18a4 4 0 0 1-.5-7.97A5 5 0 0 1 16 8.5a3.5 3.5 0 0 1 1.5 6.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                                    <path d="M12 11.5v6m0-6 2 2m-2-2-2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                                <span class="truncate">
                                                                    Drag &amp; drop here or
                                                                    <button type="button" @click="openAssetPicker()" class="underline hover:text-text-primary">choose a file</button>.
                                                                    <span x-show="meta.socialImage" x-cloak> 1/1 selected</span>
                                                                </span>
                                                            </div>
                                                        </div>

                                                        {{-- Selected file row --}}
                                                        <div class="border-t border-content-border" x-show="meta.socialImage" x-cloak>
                                                            <div class="flex items-center gap-3 px-2.5 py-2">
                                                                <div class="size-8 rounded-md overflow-hidden bg-panel-bg flex items-center justify-center shrink-0">
                                                                    <img :src="meta.socialImage" :alt="imageName" class="size-full object-cover">
                                                                </div>
                                                                <span class="flex-1 min-w-0 truncate text-sm text-text-primary" x-text="imageName"></span>
                                                                <span class="shrink-0 text-xs text-text-muted tabular-nums" x-show="size != null" x-text="formatSize(size)"></span>
                                                                <button
                                                                    type="button"
                                                                    aria-label="Remove image"
                                                                    @click="clearImage()"
                                                                    class="shrink-0 flex size-6 items-center justify-center rounded-md text-text-muted hover:bg-text-primary/10 hover:text-text-primary transition-colors"
                                                                >
                                                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Section: X (Twitter) --}}
                                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                                    <div class="divide-y divide-content-border">
                                        <div class="px-[18px] py-3">
                                            <h3 class="text-sm font-semibold text-text-heading">X (Twitter)</h3>
                                        </div>
                                        <div class="divide-y divide-content-border">
                                            {{-- X Handle --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label for="field-xHandle" class="text-sm font-medium text-text-heading">X Handle</label>
                                                    <div class="text-sm text-text-muted">Optionally override the X handle for this page.</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <input id="field-xHandle" type="text" name="meta[xHandle]" :value="meta.xHandle" @input="updateMeta('xHandle', $event.target.value)" placeholder="@username" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- X Card Title --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label for="field-xCardTitle" class="text-sm font-medium text-text-heading">X Card Title</label>
                                                    <div class="text-sm text-text-muted">Title shown on X cards. Leave blank to use the Open Graph or meta title.</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <input id="field-xCardTitle" type="text" name="meta[xCardTitle]" :value="meta.xCardTitle" @input="updateMeta('xCardTitle', $event.target.value)" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- X Card Description --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label for="field-xCardDescription" class="text-sm font-medium text-text-heading">X Card Description</label>
                                                    <div class="text-sm text-text-muted">Description shown on X cards. Leave blank to use the meta description.</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <textarea id="field-xCardDescription" name="meta[xCardDescription]" :value="meta.xCardDescription" @input="updateMeta('xCardDescription', $event.target.value)" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 min-h-[60px] resize-y transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Section: Site Map --}}
                                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                                    <div class="divide-y divide-content-border">
                                        <div class="px-[18px] py-3">
                                            <h3 class="text-sm font-semibold text-text-heading">Site Map</h3>
                                        </div>
                                        <div class="divide-y divide-content-border">
                                            {{-- Sitemap --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label class="text-sm font-medium text-text-heading">Sitemap</label>
                                                    <div class="text-sm text-text-muted">If disabled, this item will not appear in the sitemap.</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                                <span class="truncate" x-text="meta.sitemap"></span>
                                                                <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-80 overflow-y-auto [scrollbar-width:thin] space-y-0.5" style="display: none;">
                                                                @foreach (['Inherit', 'Enabled', 'Disabled'] as $opt)
                                                                    <button type="button" @click="updateMeta('sitemap', '{{ $opt }}'); open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="'{{ $opt }}' === meta.sitemap ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                        <span>{{ $opt }}</span>
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                            <input type="hidden" name="meta[sitemap]" :value="meta.sitemap">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Priority --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label for="field-sitemapPriority" class="text-sm font-medium text-text-heading">Sitemap: Priority</label>
                                                    <div class="text-sm text-text-muted">The priority of this URL relative to other URLs on your site. Valid values range from 0.0 to 1.0.</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <input id="field-sitemapPriority" type="text" name="meta[sitemapPriority]" :value="meta.sitemapPriority" @input="updateMeta('sitemapPriority', $event.target.value)" placeholder="0.5" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Change Frequency --}}
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label class="text-sm font-medium text-text-heading">Sitemap: Change Frequency</label>
                                                    <div class="text-sm text-text-muted">A hint to search engines on how frequently the page is likely to change.</div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                                <span class="truncate" x-text="meta.sitemapFrequency"></span>
                                                                <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-80 overflow-y-auto [scrollbar-width:thin] space-y-0.5" style="display: none;">
                                                                @foreach (['Inherit', 'Always', 'Hourly', 'Daily', 'Weekly', 'Monthly', 'Yearly', 'Never'] as $opt)
                                                                    <button type="button" @click="updateMeta('sitemapFrequency', '{{ $opt }}'); open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="'{{ $opt }}' === meta.sitemapFrequency ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                        <span>{{ $opt }}</span>
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                            <input type="hidden" name="meta[sitemapFrequency]" :value="meta.sitemapFrequency">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SEO Previews Column (Right Side) --}}
                        <div class="lg:col-span-1 lg:sticky lg:top-4 space-y-4">
                            {{-- Google Preview --}}
                            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-2.5">
                                <h3 class="text-xs font-semibold text-text-heading uppercase tracking-wider pb-3 pt-2 px-2">Google Preview</h3>
                                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                    <div class="p-2">
                                        <div class="text-xs text-green-700 truncate select-none">
                                            example.com › <span class="text-gray-500" x-text="meta.canonicalUrl || 'https://example.com/' + effectiveSlug"></span>
                                        </div>
                                        <div class="text-sm text-blue-700 font-medium truncate mt-0.5 cursor-pointer hover:underline" x-text="meta.metaTitle || title || 'Page Title'"></div>
                                        <div class="text-xs text-gray-600 mt-0.5 line-clamp-2" x-text="meta.metaDescription || 'Meta description would appear here.'"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Facebook Preview --}}
                            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-2.5">
                                <h3 class="text-xs font-semibold text-text-heading uppercase tracking-wider pb-3 pt-2 px-2">Facebook Preview</h3>
                                <div class="bg-white rounded-lg overflow-hidden border border-gray-200">
                                    <template x-if="meta.socialImage">
                                        <div class="h-40 flex items-center justify-center bg-cover bg-center" :style="meta.socialImage ? 'background-image: url(' + meta.socialImage + ')' : ''"></div>
                                    </template>
                                    <template x-if="!meta.socialImage">
                                        <div class="h-40 flex items-center justify-center bg-gray-100">
                                            <svg viewBox="0 0 24 24" class="size-9 text-[#1877f2]" fill="currentColor">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                        </div>
                                    </template>
                                    <div class="p-3 bg-[#f2f3f5] border-t border-gray-200">
                                        <div class="text-[11px] text-gray-500 uppercase tracking-wider truncate" x-text="(meta.canonicalUrl || 'example.com/' + effectiveSlug).replace(/^https?:\/\//, '').split('/')[0]"></div>
                                        <div class="text-sm font-semibold text-[#1c1e21] mt-0.5 truncate leading-snug" x-text="meta.xCardTitle || meta.metaTitle || title || 'Page Title'"></div>
                                        <div class="text-xs text-[#606770] mt-0.5 line-clamp-2" x-text="meta.xCardDescription || meta.metaDescription || 'Card description would appear here.'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection
