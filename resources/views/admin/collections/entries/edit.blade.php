@extends('admin.layout')

@section('title', 'Edit Entry — ' . $collection->name)
@section('breadcrumb', 'Edit Entry')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0"
    x-data="{
        activeTab: 'basic',
        title: '{{ old('data.title', $entry->data['title'] ?? '') }}',
        effectiveSlug: '{{ $entry->slug ?? '' }}',
        published: {{ old('published', $entry->published ?? true) ? 'true' : 'false' }},
        meta: {
            author: '{{ old('meta.author', $entry->meta['author'] ?? $admins->first()?->name ?? 'Admin') }}',
            metaTitle: '{{ old('meta.metaTitle', $entry->meta['metaTitle'] ?? '') }}',
            metaDescription: '{{ old('meta.metaDescription', $entry->meta['metaDescription'] ?? '') }}',
            canonicalUrl: '{{ old('meta.canonicalUrl', $entry->meta['canonicalUrl'] ?? '') }}',
            schema: '{{ old('meta.schema', $entry->meta['schema'] ?? '') }}',
            robots: '{{ old('meta.robots', $entry->meta['robots'] ?? 'Inherit') }}',
            indexing: '{{ old('meta.indexing', $entry->meta['indexing'] ?? 'Inherit') }}',
            linkFollowing: '{{ old('meta.linkFollowing', $entry->meta['linkFollowing'] ?? 'Inherit') }}',
            noArchive: '{{ old('meta.noArchive', $entry->meta['noArchive'] ?? 'Inherit') }}',
            noImageIndex: '{{ old('meta.noImageIndex', $entry->meta['noImageIndex'] ?? 'Inherit') }}',
            noSnippet: '{{ old('meta.noSnippet', $entry->meta['noSnippet'] ?? 'Inherit') }}',
            noTranslate: '{{ old('meta.noTranslate', $entry->meta['noTranslate'] ?? 'Inherit') }}',
            noSiteLinksSearchBox: '{{ old('meta.noSiteLinksSearchBox', $entry->meta['noSiteLinksSearchBox'] ?? 'Inherit') }}',
            maxSnippet: '{{ old('meta.maxSnippet', $entry->meta['maxSnippet'] ?? '') }}',
            maxVideoPreview: '{{ old('meta.maxVideoPreview', $entry->meta['maxVideoPreview'] ?? '') }}',
            maxImagePreview: '{{ old('meta.maxImagePreview', $entry->meta['maxImagePreview'] ?? 'Inherit') }}',
            ogType: '{{ old('meta.ogType', $entry->meta['ogType'] ?? 'Inherit') }}',
            ogTitle: '{{ old('meta.ogTitle', $entry->meta['ogTitle'] ?? '') }}',
            socialImage: '{{ old('meta.socialImage', $entry->meta['socialImage'] ?? '') }}',
            xHandle: '{{ old('meta.xHandle', $entry->meta['xHandle'] ?? '') }}',
            xCardTitle: '{{ old('meta.xCardTitle', $entry->meta['xCardTitle'] ?? '') }}',
            xCardDescription: '{{ old('meta.xCardDescription', $entry->meta['xCardDescription'] ?? '') }}',
            sitemap: '{{ old('meta.sitemap', $entry->meta['sitemap'] ?? 'Inherit') }}',
            sitemapPriority: '{{ old('meta.sitemapPriority', $entry->meta['sitemapPriority'] ?? '') }}',
            sitemapFrequency: '{{ old('meta.sitemapFrequency', $entry->meta['sitemapFrequency'] ?? 'Inherit') }}'
        },
        updateMeta(key, val) {
            this.meta[key] = val;
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
            Edit Entry
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
                Update Entry
            </button>
        </div>
    </header>

    <form id="entry-form" method="POST" action="{{ route('admin.collections.entries.update', [$collection, $entry]) }}">
        @csrf
        @method('PUT')

        {{-- Single Card Panel containing Header and all tabs inside --}}
        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            {{-- Unified Card Header --}}
            <div class="px-[18px] pt-3 pb-2 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <div class="text-sm font-medium text-text-heading">Entry Details</div>
                    <p class="text-sm text-text-muted mt-1">Configure and manage fields, layout configurations, and SEO metadata.</p>
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
                                    <div class="text-sm text-text-muted">A descriptive title for this entry.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input id="field-title" type="text" name="data[title]" :value="title" @input="title = $event.target.value" placeholder="Enter title" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                </div>
                            </div>

                            {{-- Slug --}}
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-slug" class="text-sm font-medium text-text-heading">Slug</label>
                                    <div class="text-sm text-text-muted">URL-friendly identifier for this entry.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input id="field-slug" type="text" name="slug" :value="effectiveSlug" @input="effectiveSlug = $event.target.value" placeholder="my-entry-slug" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                </div>
                            </div>

                            {{-- Dynamic Fields --}}
                            @foreach($collection->fields ?? [] as $field)
                                @php
                                    $rawKey = $field['template'] ?? $loop->index;
                                    $key = str_replace(['@{{', '@}}', '@{', '}@'], '', $rawKey);
                                    $value = old('data.' . $key, $entry->data[$key] ?? $entry->data[$rawKey] ?? '');
                                @endphp
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label for="field-{{ $loop->index }}" class="text-sm font-medium text-text-heading">{{ $field['title'] ?? 'Field' }}</label>
                                        @if(($field['description'] ?? '') !== '')
                                            <div class="text-sm text-text-muted">{{ $field['description'] }}</div>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1">
                                            @switch($field['type'] ?? 'text')
                                                @case('textarea')
                                                    <textarea id="field-{{ $loop->index }}" name="data[{{ $key }}]" rows="3" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ $value }}</textarea>
                                                    @break
                                                @case('number')
                                                    <input type="number" id="field-{{ $loop->index }}" name="data[{{ $key }}]" value="{{ $value }}" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                    @break
                                                @case('collection')
                                                    @php
                                                        $targetCollection = \App\Models\Collection::find($field['collection_id'] ?? null);
                                                        $targetEntries = $targetCollection ? $targetCollection->entries()->get() : collect();
                                                        $selectedEntry = $targetEntries->firstWhere('id', $value);
                                                        $selectedLabel = $selectedEntry?->data['title'] ?? 'Choose entry...';
                                                    @endphp
                                                    <div x-data="{ open: false, selectedValue: '{{ $value }}', label: '{{ addslashes($selectedLabel) }}' }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                            <span class="truncate" x-text="label"></span>
                                                            <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5" style="display: none;">
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

                                                        $selectedValue = is_array($value) ? ($value[0] ?? '') : (string)($value ?? '');
                                                        $selectedCat = $termModels->first(function ($t) use ($selectedValue) {
                                                            $tId = (string) $t->id;
                                                            $tSlug = $t->slug ?? '';
                                                            $tTitle = $t->title ?? '';
                                                            return $selectedValue == $tId || ($selectedValue !== '' && (strtolower($selectedValue) == strtolower($tTitle) || strtolower($selectedValue) == strtolower($tSlug)));
                                                        });
                                                        $selectedLabel = $selectedCat?->title ?? 'Select category...';
                                                        $initialVal = $selectedCat ? (string)$selectedCat->id : $selectedValue;
                                                    @endphp
                                                    <div x-data="{ open: false, selectedValue: '{{ addslashes($initialVal) }}', label: '{{ addslashes($selectedLabel) }}' }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                            <span class="truncate font-medium text-text-heading" x-text="label"></span>
                                                            <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5" style="display: none;">
                                                            <button type="button" @click="selectedValue = ''; label = 'Select category...'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors text-text-muted hover:bg-content-border/30">
                                                                <span>None</span>
                                                            </button>
                                                            @foreach ($termModels as $t)
                                                                @php
                                                                    $tId = (string) $t->id;
                                                                    $tTitle = $t->title ?? 'Untitled Category';
                                                                @endphp
                                                                <button type="button" @click="selectedValue = '{{ $tId }}'; label = '{{ addslashes($tTitle) }}'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selectedValue == '{{ $tId }}' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                                    <span>{{ $tTitle }}</span>
                                                                </button>
                                                            @endforeach
                                                            @if($termModels->isEmpty())
                                                                <div class="px-3 py-2 text-sm text-text-muted text-center">
                                                                    No categories found.
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <input type="hidden" name="data[{{ $key }}]" :value="selectedValue">
                                                    </div>
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
                                        <div class="px-[18px] py-3 flex items-center justify-between">
                                            <h3 class="text-sm font-semibold text-text-heading">JSON-LD Schema</h3>
                                        </div>
                                        <div class="divide-y divide-content-border">
                                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                                <div class="flex flex-col gap-1.5">
                                                    <label for="field-schema" class="text-sm font-medium text-text-heading">Schema Markup</label>
                                                    <div class="text-sm text-text-muted">Add custom JSON-LD schema markup to this page. Wrap inside &lt;script type="application/ld+json"&gt; tags.</div>
                                                </div>
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
                                                                <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5" style="display: none;">
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
                                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5" style="display: none;">
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
                                                                     class="absolute top-1 right-1 text-[11px] font-medium text-white rounded px-2 py-0.5 transition-colors" style="background: rgba(220,38,38,0.8);" onmouseover="this.style.background='rgb(220,38,38)'" onmouseout="this.style.background='rgba(220,38,38,0.8)'"
                                                                >Remove</button>
                                                            </template>
                                                        </div>
                                                        <input type="hidden" name="meta[socialImage]" :value="meta.socialImage">
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
                                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5" style="display: none;">
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
                                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5" style="display: none;">
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
