@extends('admin.layout')

@section('title', 'SEO')
@section('breadcrumb', 'SEO')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0" style="max-width: 64rem;" x-data="{
        activeTab: 'meta',
        tabs: [
            { id: 'meta', label: 'Meta' },
            { id: 'robots', label: 'Robots' },
            { id: 'open-graph', label: 'Open Graph' },
            { id: 'social', label: 'Social' },
            { id: 'sitemap', label: 'Sitemap' },
            { id: 'search-engines', label: 'Search Engines' },
        ],
    }">
        <form method="POST" action="{{ route('admin.seo.update') }}">
            @csrf
            @method('PUT')

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 md:flex-1 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-6 shrink-0 text-text-muted">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                        <path d="m8 11 2 2 4-4" />
                    </svg>
                    Site Defaults
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">Please fix the errors below.</span>
                    @endif
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >Save Settings</button>
                </div>
            </header>

            {{-- Tab Navigation --}}
            <div class="flex items-center gap-x-2 -mt-2 mb-6 px-2 sm:px-0">
                <div role="tablist" class="relative flex shrink-0 mx-2 lg:mx-0 space-x-2 lg:space-x-4 border-b border-content-border text-sm text-text-muted flex-1 min-w-0 overflow-x-clip overflow-y-visible">
                    <template x-for="tab in tabs" :key="tab.id">
                        <button type="button" role="tab"
                            :aria-selected="activeTab === tab.id"
                            :data-state="activeTab === tab.id ? 'active' : ''"
                            @click="activeTab = tab.id"
                            :class="activeTab === tab.id ? 'border-primary text-text-heading' : 'border-transparent text-text-muted hover:text-text-primary'"
                            class="cursor-pointer px-2 py-1 -mb-px border-b-2 focus-visible:rounded-lg transition-colors"
                            x-text="tab.label"
                        ></button>
                    </template>
                </div>
            </div>

            {{-- Meta Tab --}}
            <div x-show="activeTab === 'meta'" role="tabpanel" style="display: none;">
                <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                    <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Meta Data</div>
                    <p class="px-[18px] pb-3 text-sm text-text-muted">Defaults applied to every page. Individual pages can override these on their own SEO tab.</p>
                    <div class="px-1.5 pb-2">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                            <div class="divide-y divide-content-border">

                                {{-- Site Name --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-site-name">Site Name</label>
                                        <div class="text-sm text-text-muted">Your site&rsquo;s name, added to page titles for brand consistency.</div>
                                    </div>
                                    <div>
                                        <input id="field-site-name" type="text" name="siteName" value="{{ old('siteName', $seo['siteName']) }}" placeholder="My Website"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" />
                                        @error('siteName') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Favicon --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Favicon</label>
                                        <div class="text-sm text-text-muted">Small icon shown in browser tabs and bookmark bars (e.g. .ico, .png, .svg).</div>
                                    </div>
                                    <div x-data="{
                                        favicon: '{{ old('favicon', $seo['favicon']) }}',
                                        size: null,
                                        get imageName() {
                                            if (!this.favicon) return '';
                                            try {
                                                const path = this.favicon.split('?')[0];
                                                return decodeURIComponent(path.split('/').pop() || path);
                                            } catch (e) {
                                                return this.favicon;
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
                                                        this.favicon = url;
                                                        this.fetchSize(url);
                                                    }
                                                }
                                            }));
                                        },
                                        clearImage() {
                                            this.favicon = '';
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
                                            if (this.favicon) { this.fetchSize(this.favicon); }
                                        }
                                    }">
                                        <input type="hidden" name="favicon" :value="favicon">
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
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Selected file row --}}
                                            <div class="border-t border-content-border" x-show="favicon" x-cloak>
                                                <div class="flex items-center gap-3 px-2.5 py-2">
                                                    <div class="size-8 rounded-md overflow-hidden bg-panel-bg flex items-center justify-center shrink-0">
                                                        <img :src="favicon" :alt="imageName" class="size-full object-contain">
                                                    </div>
                                                    <span class="flex-1 min-w-0 truncate text-sm text-text-primary" x-text="imageName"></span>
                                                    <span class="shrink-0 text-xs text-text-muted tabular-nums" x-show="size != null" x-text="formatSize(size)"></span>
                                                    <button
                                                        type="button"
                                                        aria-label="Remove favicon"
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
                                        @error('favicon') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Name Position --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Name Position</label>
                                        <div class="text-sm text-text-muted">Show your site name before or after the page title. Or not at all.</div>
                                    </div>
                                    <div>
                                        @php
                                            $namePositionOptions = ['Before', 'After', 'None'];
                                            $namePositionSelected = old('namePosition', $seo['namePosition'] ?? '');
                                            if (! in_array($namePositionSelected, $namePositionOptions, true)) {
                                                $namePositionSelected = 'After';
                                            }
                                        @endphp
                                        <div x-data="{ open: false, selected: '{{ $namePositionSelected }}', options: {{ json_encode($namePositionOptions) }}, get selectedLabel() { return this.selected }, select(val) { this.selected = val; this.open = false } }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-10 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                <span class="truncate" x-text="selectedLabel"></span>
                                                <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-80 overflow-y-auto [scrollbar-width:thin] space-y-0.5" style="display: none;">
                                                <template x-for="opt in options" :key="opt">
                                                    <button type="button" @click="select(opt)" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="opt === selected ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                        <span x-text="opt"></span>
                                                    </button>
                                                </template>
                                            </div>
                                            <input type="hidden" name="namePosition" :value="selected">
                                        </div>
                                        @error('namePosition') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Separator --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-separator">Separator</label>
                                        <div class="text-sm text-text-muted">Choose what appears between the page title and site name.</div>
                                    </div>
                                    <div>
                                        <input id="field-separator" type="text" name="separator" value="{{ old('separator', $seo['separator']) }}"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" />
                                        @error('separator') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Default Meta Description --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-meta-description">Default Meta Description</label>
                                        <div class="text-sm text-text-muted">Used when a page doesn&rsquo;t set its own meta description. Leave blank to use none.</div>
                                    </div>
                                    <div>
                                        <textarea id="field-meta-description" name="metaDescription" rows="3" placeholder="A short description of your site."
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('metaDescription', $seo['metaDescription']) }}</textarea>
                                        @error('metaDescription') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Robots Tab --}}
            <div x-show="activeTab === 'robots'" role="tabpanel" style="display: none;">
                <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                    <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Robots Meta</div>
                    <p class="px-[18px] pb-3 text-sm text-text-muted">Control how search engines crawl and index your site.</p>
                    <div class="px-1.5 pb-2">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                            <div class="divide-y divide-content-border">

                                {{-- Indexing --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Indexing</label>
                                        <div class="text-sm text-text-muted">Control whether search engines can index this site.</div>
                                    </div>
                                    <div x-data="{ indexing: {{ var_export((bool) old('indexing', $seo['indexing']), true) }} }">
                                        <div class="inline-flex items-center">
                                            <button type="button" @click="indexing = true"
                                                :class="indexing ? 'bg-gradient-to-b from-gray-200 to-gray-100 text-gray-900 shadow-[inset_0_2px_4px_0_rgba(0,0,0,0.1)] border-gray-300' : 'bg-gradient-to-b from-white to-gray-50 hover:to-gray-100 text-gray-900 border-gray-200'"
                                                class="inline-flex items-center justify-center whitespace-nowrap font-medium cursor-pointer text-sm h-9 px-4 transition-colors rounded-l-lg border">Index</button>
                                            <button type="button" @click="indexing = false"
                                                :class="!indexing ? 'bg-gradient-to-b from-gray-200 to-gray-100 text-gray-900 shadow-[inset_0_2px_4px_0_rgba(0,0,0,0.1)] border-gray-300' : 'bg-gradient-to-b from-white to-gray-50 hover:to-gray-100 text-gray-900 border-gray-200'"
                                                class="inline-flex items-center justify-center whitespace-nowrap font-medium cursor-pointer text-sm h-9 px-4 transition-colors rounded-r-lg border -ml-px">Noindex</button>
                                        </div>
                                        <input type="hidden" name="indexing" :value="indexing ? 1 : 0">
                                    </div>
                                </div>

                                {{-- Link Following --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Link Following</label>
                                        <div class="text-sm text-text-muted">Control whether search engines can follow links for this site.</div>
                                    </div>
                                    <div x-data="{ linkFollowing: {{ var_export((bool) old('linkFollowing', $seo['linkFollowing']), true) }} }">
                                        <div class="inline-flex items-center">
                                            <button type="button" @click="linkFollowing = true"
                                                :class="linkFollowing ? 'bg-gradient-to-b from-gray-200 to-gray-100 text-gray-900 shadow-[inset_0_2px_4px_0_rgba(0,0,0,0.1)] border-gray-300' : 'bg-gradient-to-b from-white to-gray-50 hover:to-gray-100 text-gray-900 border-gray-200'"
                                                class="inline-flex items-center justify-center whitespace-nowrap font-medium cursor-pointer text-sm h-9 px-4 transition-colors rounded-l-lg border">Follow</button>
                                            <button type="button" @click="linkFollowing = false"
                                                :class="!linkFollowing ? 'bg-gradient-to-b from-gray-200 to-gray-100 text-gray-900 shadow-[inset_0_2px_4px_0_rgba(0,0,0,0.1)] border-gray-300' : 'bg-gradient-to-b from-white to-gray-50 hover:to-gray-100 text-gray-900 border-gray-200'"
                                                class="inline-flex items-center justify-center whitespace-nowrap font-medium cursor-pointer text-sm h-9 px-4 transition-colors rounded-r-lg border -ml-px">Nofollow</button>
                                        </div>
                                        <input type="hidden" name="linkFollowing" :value="linkFollowing ? 1 : 0">
                                    </div>
                                </div>

                                {{-- No Archive --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">No Archive</label>
                                        <div class="text-sm text-text-muted">Prevent search engines from showing cached links for this site.</div>
                                    </div>
                                    <div class="flex items-center">
                                        <button type="button" role="switch" :aria-checked="noArchive" @click="noArchive = !noArchive"
                                            x-data="{ noArchive: {{ var_export((bool) old('noArchive', $seo['noArchive']), true) }} }"
                                            :class="noArchive ? 'bg-primary' : 'bg-content-border'"
                                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20">
                                            <span :class="noArchive ? 'translate-x-4' : 'translate-x-0'" class="pointer-events-none inline-block size-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                            <input type="hidden" name="noArchive" :value="noArchive ? 1 : 0">
                                        </button>
                                    </div>
                                </div>

                                {{-- No Image Index --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">No Image Index</label>
                                        <div class="text-sm text-text-muted">Prevent search engines from indexing images on this site.</div>
                                    </div>
                                    <div class="flex items-center">
                                        <button type="button" role="switch" :aria-checked="noImageIndex" @click="noImageIndex = !noImageIndex"
                                            x-data="{ noImageIndex: {{ var_export((bool) old('noImageIndex', $seo['noImageIndex']), true) }} }"
                                            :class="noImageIndex ? 'bg-primary' : 'bg-content-border'"
                                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20">
                                            <span :class="noImageIndex ? 'translate-x-4' : 'translate-x-0'" class="pointer-events-none inline-block size-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                            <input type="hidden" name="noImageIndex" :value="noImageIndex ? 1 : 0">
                                        </button>
                                    </div>
                                </div>

                                {{-- No Snippet --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">No Snippet</label>
                                        <div class="text-sm text-text-muted">Prevent search engines from showing text snippets for this site.</div>
                                    </div>
                                    <div class="flex items-center">
                                        <button type="button" role="switch" :aria-checked="noSnippet" @click="noSnippet = !noSnippet"
                                            x-data="{ noSnippet: {{ var_export((bool) old('noSnippet', $seo['noSnippet']), true) }} }"
                                            :class="noSnippet ? 'bg-primary' : 'bg-content-border'"
                                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20">
                                            <span :class="noSnippet ? 'translate-x-4' : 'translate-x-0'" class="pointer-events-none inline-block size-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                            <input type="hidden" name="noSnippet" :value="noSnippet ? 1 : 0">
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Open Graph Tab --}}
            <div x-show="activeTab === 'open-graph'" role="tabpanel" style="display: none;">
                <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                    <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Open Graph</div>
                    <p class="px-[18px] pb-3 text-sm text-text-muted">Control how your content appears when shared on social platforms like Facebook and LinkedIn.</p>
                    <div class="px-1.5 pb-2">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                            <div class="divide-y divide-content-border">

                                {{-- Default Social Image --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Default Social Image</label>
                                        <div class="text-sm text-text-muted">Fallback image used for sharing when a page has none of its own.</div>
                                    </div>
                                    <div x-data="{
                                        socialImage: '{{ old('defaultSocialImage', $seo['defaultSocialImage']) }}',
                                        size: null,
                                        get imageName() {
                                            if (!this.socialImage) return '';
                                            try {
                                                const path = this.socialImage.split('?')[0];
                                                return decodeURIComponent(path.split('/').pop() || path);
                                            } catch (e) {
                                                return this.socialImage;
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
                                                        this.socialImage = url;
                                                        this.fetchSize(url);
                                                    }
                                                }
                                            }));
                                        },
                                        clearImage() {
                                            this.socialImage = '';
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
                                            if (this.socialImage) { this.fetchSize(this.socialImage); }
                                        }
                                    }">
                                        <input type="hidden" name="defaultSocialImage" :value="socialImage">
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
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Selected file row --}}
                                            <div class="border-t border-content-border" x-show="socialImage" x-cloak>
                                                <div class="flex items-center gap-3 px-2.5 py-2">
                                                    <div class="size-8 rounded-md overflow-hidden bg-panel-bg flex items-center justify-center shrink-0">
                                                        <img :src="socialImage" :alt="imageName" class="size-full object-cover">
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
                                        @error('defaultSocialImage') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- OG Site Name --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-og-site-name">Site Name</label>
                                        <div class="text-sm text-text-muted">The name of your website for Open Graph.</div>
                                    </div>
                                    <div>
                                        <input id="field-og-site-name" type="text" name="ogSiteName" value="{{ old('ogSiteName', $seo['ogSiteName']) }}" placeholder="My Website"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" />
                                        @error('ogSiteName') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- OG Locale --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-og-locale">Locale</label>
                                        <div class="text-sm text-text-muted">The locale for Open Graph tags (e.g., en_US).</div>
                                    </div>
                                    <div>
                                        <input id="field-og-locale" type="text" name="ogLocale" value="{{ old('ogLocale', $seo['ogLocale']) }}" placeholder="en_US"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" />
                                        @error('ogLocale') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Social Tab --}}
            <div x-show="activeTab === 'social'" role="tabpanel" style="display: none;">
                <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                    <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Social Media</div>
                    <p class="px-[18px] pb-3 text-sm text-text-muted">Configure how your content appears on X (Twitter) and other social platforms.</p>
                    <div class="px-1.5 pb-2">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                            <div class="divide-y divide-content-border">

                                {{-- X Handle --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-x-handle">X Handle</label>
                                        <div class="text-sm text-text-muted">Your X (Twitter) handle (e.g., @yourhandle).</div>
                                    </div>
                                    <div>
                                        <input id="field-x-handle" type="text" name="xHandle" value="{{ old('xHandle', $seo['xHandle']) }}" placeholder="@"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" />
                                        @error('xHandle') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- X Card Type --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Twitter / X Card Type</label>
                                        <div class="text-sm text-text-muted">Choose how preview cards look (e.g., large featured image vs. small thumbnail) when links are shared on X (Twitter).</div>
                                    </div>
                                    <div>
                                        @php
                                            $cardOptionsMap = [
                                                'summary_large_image' => 'Summary with Large Image (Recommended)',
                                                'summary' => 'Summary (Small Thumbnail)',
                                                'app' => 'App Download Card',
                                                'player' => 'Media Player Card',
                                            ];
                                            $cardSelected = old('xCard', $seo['xCard'] ?? '');
                                            if (! array_key_exists($cardSelected, $cardOptionsMap)) {
                                                $cardSelected = 'summary_large_image';
                                            }
                                        @endphp
                                        <div x-data="{ open: false, selected: '{{ $cardSelected }}', optionsMap: {{ json_encode($cardOptionsMap) }}, get selectedLabel() { return this.optionsMap[this.selected] || this.selected }, select(val) { this.selected = val; this.open = false } }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-10 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                <span class="truncate" x-text="selectedLabel"></span>
                                                <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-80 overflow-y-auto [scrollbar-width:thin] space-y-0.5" style="display: none;">
                                                <template x-for="(label, key) in optionsMap" :key="key">
                                                    <button type="button" @click="select(key)" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="key === selected ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                        <span x-text="label"></span>
                                                    </button>
                                                </template>
                                            </div>
                                            <input type="hidden" name="xCard" :value="selected">
                                        </div>
                                        @error('xCard') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sitemap Tab --}}
            <div x-show="activeTab === 'sitemap'" role="tabpanel" style="display: none;">
                <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                    <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Sitemap</div>
                    <p class="px-[18px] pb-3 text-sm text-text-muted">Configure your XML sitemap settings for search engines.</p>
                    <div class="px-1.5 pb-2">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                            <div class="divide-y divide-content-border">

                                {{-- Enable Sitemap --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Enable Sitemap</label>
                                        <div class="text-sm text-text-muted">Generate an XML sitemap for your site.</div>
                                    </div>
                                    <div class="flex items-center">
                                        <button type="button" role="switch" :aria-checked="sitemapEnabled" @click="sitemapEnabled = !sitemapEnabled"
                                            x-data="{ sitemapEnabled: {{ var_export((bool) old('sitemapEnabled', $seo['sitemapEnabled']), true) }} }"
                                            :class="sitemapEnabled ? 'bg-primary' : 'bg-content-border'"
                                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20">
                                            <span :class="sitemapEnabled ? 'translate-x-4' : 'translate-x-0'" class="pointer-events-none inline-block size-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                            <input type="hidden" name="sitemapEnabled" :value="sitemapEnabled ? 1 : 0">
                                        </button>
                                    </div>
                                </div>

                                {{-- Change Frequency --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Change Frequency</label>
                                        <div class="text-sm text-text-muted">How often your content typically changes.</div>
                                    </div>
                                    <div>
                                        @php
                                            $freqOptions = ['Always', 'Hourly', 'Daily', 'Weekly', 'Monthly', 'Yearly', 'Never'];
                                            $freqSelected = old('sitemapChangeFrequency', $seo['sitemapChangeFrequency'] ?? '');
                                            if (! in_array($freqSelected, $freqOptions, true)) {
                                                $freqSelected = 'Monthly';
                                            }
                                        @endphp
                                        <div x-data="{ open: false, selected: '{{ $freqSelected }}', options: {{ json_encode($freqOptions) }}, get selectedLabel() { return this.selected }, select(val) { this.selected = val; this.open = false } }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-10 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                <span class="truncate" x-text="selectedLabel"></span>
                                                <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-80 overflow-y-auto [scrollbar-width:thin] space-y-0.5" style="display: none;">
                                                <template x-for="opt in options" :key="opt">
                                                    <button type="button" @click="select(opt)" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="opt === selected ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                        <span x-text="opt"></span>
                                                    </button>
                                                </template>
                                            </div>
                                            <input type="hidden" name="sitemapChangeFrequency" :value="selected">
                                        </div>
                                        @error('sitemapChangeFrequency') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Priority --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-sitemap-priority">Priority</label>
                                        <div class="text-sm text-text-muted">The default priority for URLs in the sitemap (0.0 to 1.0).</div>
                                    </div>
                                    <div>
                                        <input id="field-sitemap-priority" type="text" name="sitemapPriority" value="{{ old('sitemapPriority', $seo['sitemapPriority']) }}" placeholder="0.5"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" />
                                        @error('sitemapPriority') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- URL Limit --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-sitemap-limit">URL Limit</label>
                                        <div class="text-sm text-text-muted">Maximum number of URLs per sitemap file.</div>
                                    </div>
                                    <div>
                                        <input id="field-sitemap-limit" type="text" name="sitemapLimit" value="{{ old('sitemapLimit', $seo['sitemapLimit']) }}" placeholder="1000"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" />
                                        @error('sitemapLimit') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search Engines Tab --}}
            <div x-show="activeTab === 'search-engines'" role="tabpanel" style="display: none;">
                <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                    <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Search Engines</div>
                    <p class="px-[18px] pb-3 text-sm text-text-muted">Additional search engine configuration and verification.</p>
                    <div class="px-1.5 pb-2">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                            <div class="divide-y divide-content-border">

                                {{-- Enable Search Engines --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Enable Search Engines</label>
                                        <div class="text-sm text-text-muted">Allow search engines to index your site.</div>
                                    </div>
                                    <div class="flex items-center">
                                        <button type="button" role="switch" :aria-checked="searchEnginesEnabled" @click="searchEnginesEnabled = !searchEnginesEnabled"
                                            x-data="{ searchEnginesEnabled: {{ var_export((bool) old('searchEnginesEnabled', $seo['searchEnginesEnabled']), true) }} }"
                                            :class="searchEnginesEnabled ? 'bg-primary' : 'bg-content-border'"
                                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20">
                                            <span :class="searchEnginesEnabled ? 'translate-x-4' : 'translate-x-0'" class="pointer-events-none inline-block size-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                            <input type="hidden" name="searchEnginesEnabled" :value="searchEnginesEnabled ? 1 : 0">
                                        </button>
                                    </div>
                                </div>

                                {{-- Search Engines Indexing --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Indexing</label>
                                        <div class="text-sm text-text-muted">Allow search engines to index your content.</div>
                                    </div>
                                    <div class="flex items-center">
                                        <button type="button" role="switch" :aria-checked="searchEnginesIndexing" @click="searchEnginesIndexing = !searchEnginesIndexing"
                                            x-data="{ searchEnginesIndexing: {{ var_export((bool) old('searchEnginesIndexing', $seo['searchEnginesIndexing']), true) }} }"
                                            :class="searchEnginesIndexing ? 'bg-primary' : 'bg-content-border'"
                                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20">
                                            <span :class="searchEnginesIndexing ? 'translate-x-4' : 'translate-x-0'" class="pointer-events-none inline-block size-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                            <input type="hidden" name="searchEnginesIndexing" :value="searchEnginesIndexing ? 1 : 0">
                                        </button>
                                    </div>
                                </div>

                                {{-- Extra Meta Tags --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-extra-meta-tags">Extra Meta Tags</label>
                                        <div class="text-sm text-text-muted">Additional meta tags for search engine verification (e.g., Google Webmaster Tools).</div>
                                    </div>
                                    <div>
                                        <textarea id="field-extra-meta-tags" name="extraMetaTags" rows="3" placeholder='&lt;meta name="google-site-verification" content="..." /&gt;'
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('extraMetaTags', $seo['extraMetaTags']) }}</textarea>
                                        @error('extraMetaTags') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
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
