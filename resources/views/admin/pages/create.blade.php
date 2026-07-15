@extends('admin.layout')

@section('title', 'Create Page')
@section('breadcrumb', 'Create Page')

@section('content')
    <div
        class="max-w-5xl mx-auto px-2 sm:px-0"
        x-data="{
            activeTab: 'page',
            title: '{{ old('title') }}',
            slugEdited: false,
            slug: '{{ old('slug') }}',
            published: {{ old('published', true) ? 'true' : 'false' }},
            meta: {
                author: '{{ old('meta.author', 'Inherit') }}',
                siteName: '{{ old('meta.siteName', 'Inherit') }}',
                customSiteName: '{{ old('meta.customSiteName', '') }}',
                siteNamePosition: '{{ old('meta.siteNamePosition', 'Inherit') }}',
                siteNameSeparator: '{{ old('meta.siteNameSeparator', 'Inherit') }}',
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
                sitemapFrequency: '{{ old('meta.sitemapFrequency', 'Inherit') }}',
            },
            slugify(v) {
                return v.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            },
            updateMeta(key, val) {
                this.meta[key] = val;
            },
            get effectiveSlug() {
                return this.slugEdited ? this.slug : this.slugify(this.title);
            },
        }"
    >
        <form method="POST" action="{{ route('admin.pages.store') }}">
            @csrf

            @php $pageLayouts = \App\Models\Layout::where('collection', 'page')->orderBy('position')->orderBy('name')->get(); @endphp
            @if($pageLayouts->isNotEmpty())
                <div class="bg-panel-bg rounded-2xl mb-6 p-[7px]">
                    <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Layout</div>
                    <p class="px-[18px] pb-3 text-sm text-text-muted">Choose a layout to pre-populate sections.</p>
                    <div class="px-1.5 pb-2">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading">Start from layout</label>
                                    <div class="text-sm text-text-muted">Optionally pre-fill this page with sections from a layout.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <select name="layout_id" class="w-full block bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                            <option value="">None (empty page)</option>
                                            @foreach($pageLayouts as $pl)
                                                <option value="{{ $pl->id }}" @selected(old('layout_id') == $pl->id)>{{ $pl->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <input type="hidden" name="slug" :value="effectiveSlug">

            {{-- Header --}}
            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                    Create Page
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        <span>Create &amp; Edit</span>
                    </button>
                </div>
            </header>

            {{-- Tabs --}}
            <div class="flex items-center gap-x-2 -mt-2 mb-6 px-2 sm:px-0">
                <div role="tablist" class="relative flex shrink-0 mx-2 lg:mx-0 space-x-2 lg:space-x-4 border-b border-content-border text-sm text-text-muted flex-1 min-w-0 overflow-x-clip overflow-y-visible">
                    <button
                        type="button"
                        role="tab"
                        aria-selected="true"
                        :aria-selected="activeTab === 'page'"
                        :data-state="activeTab === 'page' ? 'active' : ''"
                        @click="activeTab = 'page'"
                        :class="activeTab === 'page' ? 'border-primary text-text-heading' : 'border-transparent text-text-muted hover:text-text-primary'"
                        class="cursor-pointer px-2 py-1 -mb-px border-b-2 focus-visible:rounded-lg transition-colors"
                    >Page</button>
                    <button
                        type="button"
                        role="tab"
                        aria-selected="false"
                        :aria-selected="activeTab === 'basics'"
                        :data-state="activeTab === 'basics' ? 'active' : ''"
                        @click="activeTab = 'basics'"
                        :class="activeTab === 'basics' ? 'border-primary text-text-heading' : 'border-transparent text-text-muted hover:text-text-primary'"
                        class="cursor-pointer px-2 py-1 -mb-px border-b-2 focus-visible:rounded-lg transition-colors"
                    >Basics</button>
                    <button
                        type="button"
                        role="tab"
                        aria-selected="false"
                        :aria-selected="activeTab === 'seo'"
                        :data-state="activeTab === 'seo' ? 'active' : ''"
                        @click="activeTab = 'seo'"
                        :class="activeTab === 'seo' ? 'border-primary text-text-heading' : 'border-transparent text-text-muted hover:text-text-primary'"
                        class="cursor-pointer px-2 py-1 -mb-px border-b-2 focus-visible:rounded-lg transition-colors"
                    >SEO</button>
                </div>
            </div>

            <div role="tabpanel">
                {{-- Page Tab --}}
                <div x-show="activeTab === 'page'" class="mb-8">
                    <div class="bg-panel-bg rounded-2xl p-[7px]">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                            <div class="divide-y divide-content-border">
                                {{-- Title --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label for="field-title" class="text-sm font-medium text-text-heading">Page Title</label>
                                        <div class="text-sm text-text-muted">A clear, descriptive title for this page. Used in the browser tab, search results, and navigation.</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1">
                                            <input
                                                id="field-title"
                                                type="text"
                                                name="title"
                                                x-model="title"
                                                value="{{ old('title') }}"
                                                placeholder="About Us"
                                                class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                            >
                                            @error('title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Slug (visible) --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label for="field-slug" class="text-sm font-medium text-text-heading">URL Slug</label>
                                        <div class="text-sm text-text-muted">The URL-friendly identifier for this page. Auto-generated from the title, but you can customize it.</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1">
                                            <input
                                                id="field-slug"
                                                type="text"
                                                :value="effectiveSlug"
                                                @input="slugEdited = true; slug = slugify($event.target.value)"
                                                placeholder="about-us"
                                                class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                            >
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
                                                <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5">
                                                    <template x-for="opt in ['Inherit', 'David Hasselhoff', 'Admin']" :key="opt">
                                                        <button type="button" @click="updateMeta('author', opt); open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="opt === meta.author ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                            <span x-text="opt"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                                <input type="hidden" name="meta[author]" :value="meta.author">
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                </div>

                {{-- Basics Tab --}}
                <div x-show="activeTab === 'basics'" class="mb-8">
                    <div class="bg-panel-bg rounded-2xl p-[7px]">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                            <div class="divide-y divide-gray-200">
                                {{-- Enabled --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Enabled</label>
                                        <div class="text-sm text-text-muted">Disabling this item will exclude it from reports and the sitemap, and prevent anything from being rendered through the template tag.</div>
                                    </div>
                                    <div class="flex items-center justify-end h-full">
                                        <button type="button" role="switch" :aria-checked="published" :data-state="published ? 'checked' : 'unchecked'" @click="published = !published" class="relative flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/30 data-[state=checked]:shadow-inner data-[state=checked]:border-emerald-500 data-[state=checked]:bg-emerald-500 data-[state=unchecked]:!border-gray-300 data-[state=unchecked]:bg-gray-200">
                                            <span :data-state="published ? 'checked' : 'unchecked'" class="my-auto flex items-center justify-center size-5 rounded-full bg-white text-xs shadow-[0_10px_15px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] transition-transform will-change-transform data-[state=checked]:translate-x-full data-[state=unchecked]:translate-x-0"></span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Section: Site Name --}}
                                <div>
                                    <div class="px-[18px] py-3">
                                        <h3 class="text-sm font-semibold text-text-heading">Site Name</h3>
                                    </div>
                                    <div class="divide-y divide-gray-200">
                                        {{-- Site Name --}}
                                        <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                            <div class="flex flex-col gap-1.5">
                                                <label class="text-sm font-medium text-text-heading">Site Name</label>
                                                <div class="text-sm text-text-muted">Optionally disable the site name for this page.</div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1">
                                                    <div x-data="{ open: false, showCustom: meta.siteName === 'Custom' }" x-init="$watch('meta.siteName', val => showCustom = val === 'Custom')" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                            <span class="truncate" x-text="meta.siteName"></span>
                                                            <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5">
                                                            <template x-for="opt in ['Inherit', 'Custom', 'Disabled']" :key="opt">
                                                                <button type="button" @click="updateMeta('siteName', opt); showCustom = opt === 'Custom'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="opt === meta.siteName ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                    <span x-text="opt"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                        <input type="hidden" name="meta[siteName]" :value="meta.siteName">
                                                        <div x-show="showCustom" class="mt-2">
                                                            <input type="text" name="meta[customSiteName]" :value="meta.customSiteName" @input="updateMeta('customSiteName', $event.target.value)" placeholder="Custom value" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Site Name Position --}}
                                        <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                            <div class="flex flex-col gap-1.5">
                                                <label class="text-sm font-medium text-text-heading">Site Name Position</label>
                                                <div class="text-sm text-text-muted">Optionally adjust the position for this page.</div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1">
                                                    <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                            <span class="truncate" x-text="meta.siteNamePosition"></span>
                                                            <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5">
                                                            <template x-for="opt in ['Inherit', 'Before', 'After']" :key="opt">
                                                                <button type="button" @click="updateMeta('siteNamePosition', opt); open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="opt === meta.siteNamePosition ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                    <span x-text="opt"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                        <input type="hidden" name="meta[siteNamePosition]" :value="meta.siteNamePosition">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Site Name Separator --}}
                                        <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                            <div class="flex flex-col gap-1.5">
                                                <label class="text-sm font-medium text-text-heading">Site Name Separator</label>
                                                <div class="text-sm text-text-muted">Optionally adjust the separator for this page.</div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1">
                                                    <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                            <span class="truncate" x-text="meta.siteNameSeparator"></span>
                                                            <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5">
                                                            <template x-for="opt in ['Inherit', '|', '—', '•', ':']" :key="opt">
                                                                <button type="button" @click="updateMeta('siteNameSeparator', opt); open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="opt === meta.siteNameSeparator ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                    <span x-text="opt"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                        <input type="hidden" name="meta[siteNameSeparator]" :value="meta.siteNameSeparator">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEO Tab --}}
                <div x-show="activeTab === 'seo'">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                        <div class="lg:col-span-2">
                            <div class="bg-panel-bg rounded-2xl p-[7px]">
                                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                                    <div class="divide-y divide-content-border">
                                        {{-- Section: Meta Data --}}
                                        <div>
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
                                                        <div class="text-sm text-text-muted">The canonical URL for this page. Leave blank to use the page&rsquo;s own URL.</div>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex-1">
                                                            <input id="field-canonicalUrl" type="text" name="meta[canonicalUrl]" :value="meta.canonicalUrl" @input="updateMeta('canonicalUrl', $event.target.value)" placeholder="https://" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Section: JSON-LD --}}
                                        <div>
                                            <div class="px-[18px] py-3">
                                                <h3 class="text-sm font-semibold text-text-heading">JSON-LD</h3>
                                            </div>
                                            <div class="divide-y divide-content-border">
                                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                    <div class="flex flex-col gap-1.5">
                                                        <label for="field-schema" class="text-sm font-medium text-text-heading">Schema</label>
                                                        <div class="text-sm text-text-muted">Paste your custom schema objects here (<code class="text-xs bg-panel-bg px-1 rounded">Recipe</code>, <code class="text-xs bg-panel-bg px-1 rounded">Event</code>, etc). You can use Antlers to output data from the item. Will be wrapped in the appropriate script tag.</div>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex-1">
                                                            <textarea id="field-schema" name="meta[schema]" :value="meta.schema" @input="updateMeta('schema', $event.target.value)" placeholder='{ "@type": "..." }' class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 min-h-[60px] resize-y transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Section: Robots --}}
                                        <div>
                                            <div class="px-[18px] py-3">
                                                <h3 class="text-sm font-semibold text-text-heading">Robots</h3>
                                            </div>
                                            <div class="divide-y divide-content-border">
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
                                                                        <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5">
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
                                        <div>
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
                                                                <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5">
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
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex-1">
                                                            <div
                                                                @click="window.dispatchEvent(new CustomEvent('open-asset-picker', { detail: { callback: (url) => { updateMeta('socialImage', url) } } }))"
                                                                @dragover.prevent="$event.currentTarget.classList.add('border-primary', 'bg-primary/5')"
                                                                @dragleave.prevent="$event.currentTarget.classList.remove('border-primary', 'bg-primary/5')"
                                                                @drop.prevent="
                                                                    $event.currentTarget.classList.remove('border-primary', 'bg-primary/5');
                                                                    const file = $event.dataTransfer.files[0];
                                                                    if (file && file.type.startsWith('image/')) {
                                                                        const reader = new FileReader();
                                                                        reader.onload = (e) => { updateMeta('socialImage', e.target.result); };
                                                                        reader.readAsDataURL(file);
                                                                    }
                                                                "
                                                                 class="relative w-full h-32 rounded-lg border-2 border-dashed cursor-pointer transition-colors bg-white overflow-hidden"
                                                                 :class="meta.socialImage ? 'border-gray-300 hover:border-gray-400' : 'border-gray-300 hover:border-gray-400'"
                                                             >
                                                                 <template x-if="meta.socialImage">
                                                                     <img :src="meta.socialImage" alt="" class="w-full h-full object-cover rounded-lg">
                                                                 </template>
                                                                 <template x-if="!meta.socialImage">
                                                                     <div class="flex flex-col items-center justify-center w-full h-full text-text-muted">
                                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-8 mb-1">
                                                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                                                            <circle cx="8.5" cy="8.5" r="1.5" />
                                                                            <polyline points="21 15 16 10 5 21" />
                                                                        </svg>
                                                                        <span class="text-xs font-medium">Click or drag to upload</span>
                                                                    </div>
                                                                </template>
                                                                <template x-if="meta.socialImage">
                                                                    <button type="button" @click.stop="updateMeta('socialImage', '')"
                                                                        class="absolute top-1 right-1 text-[11px] font-medium text-white bg-danger/80 hover:bg-danger rounded px-2 py-0.5 transition-colors"
                                                                    >Remove</button>
                                                                </template>
                                                            </div>
                                                            <input type="hidden" name="meta[socialImage]" :value="meta.socialImage">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Section: X (Twitter) --}}
                                        <div>
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

                                        {{-- Section: Site Map --}}
                                        <div>
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
                                                                <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5">
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
                                                                <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5">
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
                        </div>

                        {{-- SEO Previews (right sidebar) --}}
                        <div class="lg:col-span-1 lg:sticky lg:top-4 space-y-6">
                            {{-- Google Preview --}}
                            <div class="bg-panel-bg rounded-2xl p-[7px]">
                                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-2.5">
                                    <h3 class="text-xs font-semibold text-text-heading uppercase tracking-wider pb-3 pt-2 px-2">Google Preview</h3>
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                        <div class="p-2">
                                            <div class="text-xs text-green-700 truncate">
                                                example.com › <span class="text-gray-500" x-text="meta.canonicalUrl || 'https://example.com/' + effectiveSlug"></span>
                                            </div>
                                            <div class="text-sm text-blue-700 font-medium truncate mt-0.5 cursor-pointer hover:underline" x-text="meta.metaTitle || title || 'Page Title'"></div>
                                            <div class="text-xs text-gray-600 mt-0.5 line-clamp-2" x-text="meta.metaDescription || 'Meta description would appear here.'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- X (Twitter) Preview --}}
                            <div class="bg-panel-bg rounded-2xl p-[7px]">
                                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-2.5">
                                    <h3 class="text-xs font-semibold text-text-heading uppercase tracking-wider pb-3 pt-2 px-2">X (Twitter) Preview</h3>
                                    <div class="bg-white rounded-lg overflow-hidden">
                                        <template x-if="meta.socialImage">
                                            <div class="h-40 flex items-center justify-center bg-cover bg-center" :style="meta.socialImage ? 'background-image: url(' + meta.socialImage + ')' : ''"></div>
                                        </template>
                                        <template x-if="!meta.socialImage">
                                            <div class="h-40 flex items-center justify-center bg-gray-300">
                                                <svg viewBox="0 0 24 24" class="size-8 text-white" fill="currentColor">
                                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                                </svg>
                                            </div>
                                        </template>
                                        <div class="border border-gray-200 rounded-b-lg p-3">
                                            <div class="text-xs text-gray-500 truncate" x-text="meta.canonicalUrl || 'example.com/' + effectiveSlug"></div>
                                            <div class="text-sm font-medium text-gray-900 mt-0.5 truncate" x-text="meta.xCardTitle || meta.metaTitle || title || 'Page Title'"></div>
                                            <div class="text-xs text-gray-600 mt-0.5 line-clamp-2" x-text="meta.xCardDescription || meta.metaDescription || 'Card description would appear here.'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Facebook Preview --}}
                            <div class="bg-panel-bg rounded-2xl p-[7px]">
                                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-2.5">
                                    <h3 class="text-xs font-semibold text-text-heading uppercase tracking-wider pb-3 pt-2 px-2">Facebook Preview</h3>
                                    <div class="bg-white rounded-lg overflow-hidden">
                                        <template x-if="meta.socialImage">
                                            <div class="h-40 flex items-center justify-center bg-cover bg-center" :style="meta.socialImage ? 'background-image: url(' + meta.socialImage + ')' : ''"></div>
                                        </template>
                                        <template x-if="!meta.socialImage">
                                            <div class="h-40 flex items-center justify-center bg-blue-600">
                                                <svg viewBox="0 0 24 24" class="size-8 text-white" fill="currentColor">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                                </svg>
                                            </div>
                                        </template>
                                        <div class="border border-gray-200 rounded-b-lg p-3">
                                            <div class="text-[11px] text-gray-500 uppercase tracking-wider truncate" x-text="meta.canonicalUrl || 'example.com/' + effectiveSlug"></div>
                                            <div class="text-sm font-semibold text-gray-900 mt-0.5 truncate" x-text="meta.ogTitle || meta.metaTitle || title || 'Page Title'"></div>
                                            <div class="text-xs text-gray-600 mt-0.5 line-clamp-2" x-text="meta.metaDescription || 'Social preview description would appear here.'"></div>
                                        </div>
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
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endpush
