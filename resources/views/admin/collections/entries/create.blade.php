@extends('admin.layout')

@section('title', 'Create Entry — ' . $collection->name)
@section('breadcrumb', 'Create Entry')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0"
    x-data="{
        title: '{{ old('data.title', '') }}',
        slugEdited: false,
        slug: '{{ old('slug', '') }}',
        published: {{ old('published', true) ? 'true' : 'false' }},
        meta: {
            author: '{{ old('meta.author', $admins->first()?->name ?? 'Admin') }}'
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
            Create Entry
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

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] pt-3 pb-1">
                <div class="text-sm font-medium text-text-heading">New Entry</div>
                 <p class="text-sm text-text-muted mt-1 mb-2">Configure the fields to create a new page in this collection.</p>
            </div>
            <div class="px-1.5 pb-2">
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
                                        <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5" style="display: none;">
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

                        {{-- Layout --}}
                        @if($layouts->isNotEmpty())
                            @php
                                $layoutOptions = $layouts->map(fn($l) => ['value' => (string)$l->id, 'label' => $l->name])->values()->toArray();
                            @endphp
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading">Layout</label>
                                    <div class="text-sm text-text-muted">Optionally pre-fill sections from a layout.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <div
                                            x-data="{
                                                open: false,
                                                selected: '{{ old('layout_id', '') }}',
                                                options: {{ Js::from($layoutOptions) }},
                                                get selectedLabel() {
                                                    return this.options.find(o => o.value === this.selected)?.label ?? 'None';
                                                },
                                                select(val) {
                                                    this.selected = val;
                                                    this.open = false;
                                                },
                                            }"
                                            @click.outside="open = false"
                                            @keydown.escape.window="open = false"
                                            class="relative"
                                        >
                                            <button
                                                type="button"
                                                @click="open = !open"
                                                class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 cursor-pointer transition-all duration-150 hover:bg-content-border/30 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                            >
                                                <span class="truncate" x-text="selectedLabel"></span>
                                                <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div
                                                x-show="open"
                                                class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5"
                                                style="display: none;"
                                            >
                                                <button
                                                    type="button"
                                                    @click="select('')"
                                                    class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors"
                                                    :class="'' === selected ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'"
                                                >
                                                    <span>None (empty page)</span>
                                                </button>
                                                <template x-for="opt in options" :key="opt.value">
                                                    <button
                                                        type="button"
                                                        @click="select(opt.value)"
                                                        class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors"
                                                        :class="opt.value === selected ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'"
                                                    >
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
    </form>
</div>
@endsection
