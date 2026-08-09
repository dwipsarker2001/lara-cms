@extends('admin.layout')

@section('title', 'Create ' . $taxonomy->title)
@section('breadcrumb', 'Create ' . $taxonomy->title)

@section('content')
    <div
        class="max-w-5xl mx-auto px-2 sm:px-0"
        x-data="{
            title: '{{ old('title', '') }}',
            slug: '{{ old('slug', '') }}',
            customSlug: {{ old('slug') ? 'true' : 'false' }},

            slugify(text) {
                return text
                    .toString()
                    .toLowerCase()
                    .trim()
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
            }
        }"
    >
        <form method="POST" action="{{ route('admin.taxonomies.terms.store', $taxonomy) }}">
            @csrf

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                        <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z" />
                        <path d="M7 7h.01" />
                    </svg>
                    Create {{ $taxonomy->title }}
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <a href="{{ route('admin.taxonomies.show', $taxonomy) }}"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-gradient-to-b from-content-bg to-gray-50 hover:to-gray-100 text-text-primary border border-content-border shadow-sm">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                        <span>Save {{ $taxonomy->title }}</span>
                    </button>
                </div>
            </header>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Enter details for this {{ $taxonomy->title }}.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-title" class="text-sm font-medium text-text-heading">Title</label>
                                    <div class="text-sm text-text-muted">Name of the item.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-title"
                                            type="text"
                                            name="title"
                                            x-model="title"
                                            @input="onTitleInput()"
                                            placeholder="e.g. Adventure, Cox's Bazar"
                                            required
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-slug" class="text-sm font-medium text-text-heading">Slug</label>
                                    <div class="text-sm text-text-muted">URL identifier. Auto-generated from title.</div>
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

                            @if(!empty($taxonomy->fields))
                                @foreach($taxonomy->fields as $field)
                                    @php
                                        $fKey = $field['template'] ?? \Illuminate\Support\Str::slug($field['title'], '_');
                                        $fType = $field['type'] ?? 'text';
                                        $fTitle = $field['title'] ?? ucfirst($fKey);
                                        $fDesc = !empty($field['description']) ? $field['description'] : 'Configure ' . strtolower($fTitle) . ' for this item.';
                                        $oldVal = old('data.'.$fKey, '');
                                    @endphp
                                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5" x-data="{ imgPreview: '{{ $oldVal }}' }">
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-sm font-medium text-text-heading">{{ $fTitle }}</label>
                                            <div class="text-sm text-text-muted">{{ $fDesc }}</div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1">
                                                @if($fType === 'image')
                                                    <div class="rounded-lg border border-content-border bg-content-bg overflow-hidden shadow-sm" x-data="{
                                                        imgUrl: '{{ $oldVal }}',
                                                        get imgName() {
                                                            if (!this.imgUrl) return '';
                                                            try {
                                                                const path = this.imgUrl.split('?')[0];
                                                                return decodeURIComponent(path.split('/').pop() || path);
                                                            } catch (e) {
                                                                return this.imgUrl;
                                                            }
                                                        },
                                                        openAssetPicker() {
                                                            window.dispatchEvent(new CustomEvent('open-asset-picker', {
                                                                detail: {
                                                                    callback: (url) => { this.imgUrl = url; }
                                                                }
                                                            }));
                                                        }
                                                    }">
                                                        <input type="hidden" name="data[{{ $fKey }}]" :value="imgUrl">
                                                        <div class="flex items-center gap-3 px-2.5 py-2">
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
                                                            <div class="flex items-center gap-1.5 text-xs text-text-muted min-w-0 flex-1">
                                                                <span class="truncate" x-text="imgUrl ? '1 file selected' : 'Drag & drop here or choose a file'"></span>
                                                            </div>
                                                        </div>
                                                        <div class="border-t border-content-border px-2.5 py-2 flex items-center gap-3" x-show="imgUrl">
                                                            <div class="size-8 rounded-md overflow-hidden bg-panel-bg flex items-center justify-center shrink-0 border border-gray-200">
                                                                <img :src="imgUrl" alt="Selected image" class="size-full object-cover">
                                                            </div>
                                                            <span class="flex-1 min-w-0 truncate text-xs text-text-primary font-medium" x-text="imgName"></span>
                                                            <button
                                                                type="button"
                                                                @click="imgUrl = ''"
                                                                class="shrink-0 flex size-6 items-center justify-center rounded-md text-text-muted hover:bg-text-primary/10 hover:text-text-primary transition-colors"
                                                                title="Remove image"
                                                            >
                                                                <svg viewBox="0 0 24 24" fill="none" class="size-4">
                                                                    <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @elseif($fType === 'textarea')
                                                    <textarea name="data[{{ $fKey }}]" rows="3" class="w-full block bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ $oldVal }}</textarea>
                                                @elseif($fType === 'number')
                                                    <input type="number" name="data[{{ $fKey }}]" value="{{ $oldVal }}" class="w-full block bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                @elseif($fType === 'color')
                                                    <div class="flex items-center gap-2" x-data="{ colorVal: '{{ $oldVal ?: '#000000' }}' }">
                                                        <input type="color" x-model="colorVal" class="h-9 w-12 rounded border border-content-border p-0.5 cursor-pointer">
                                                        <input type="text" name="data[{{ $fKey }}]" x-model="colorVal" class="w-full block bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 font-mono" placeholder="#000000">
                                                    </div>
                                                @elseif($fType === 'select')
                                                     @php
                                                         $rawOpts = $field['options'] ?? '';
                                                         $optsArr = array_filter(array_map('trim', is_array($rawOpts) ? $rawOpts : explode(',', $rawOpts)));
                                                     @endphp
                                                     <div x-data="{ open: false, selectedVal: '{{ $oldVal }}' }" @click.outside="open = false" class="relative">
                                                         <input type="hidden" name="data[{{ $fKey }}]" :value="selectedVal">
                                                         <button type="button" @click="open = !open"
                                                             class="flex items-center justify-between gap-2 w-full rounded-lg border border-content-border hover:border-gray-400 bg-content-bg px-3 py-2 text-sm text-text-primary h-9 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer shadow-sm">
                                                             <span x-text="selectedVal || 'Select option...'"></span>
                                                             <svg :class="open ? 'rotate-180 text-primary' : 'text-text-muted'" class="size-4 transition-transform duration-150 shrink-0" viewBox="0 0 20 20" fill="currentColor">
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
                                                             class="absolute z-50 top-full mt-1 left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-xl p-1 space-y-0.5 max-h-60 overflow-y-auto"
                                                             style="display: none;">
                                                             <button type="button" @click="selectedVal = ''; open = false"
                                                                 class="flex items-center justify-between w-full px-3 py-1.5 text-sm rounded-md text-text-muted hover:bg-gray-100 transition-colors">
                                                                 <span>Select option...</span>
                                                             </button>
                                                             @foreach($optsArr as $opt)
                                                                 <button type="button" @click="selectedVal = '{{ addslashes($opt) }}'; open = false"
                                                                     class="flex items-center justify-between w-full px-3 py-1.5 text-sm rounded-md text-text-primary hover:bg-gray-100 transition-colors"
                                                                     :class="selectedVal === '{{ addslashes($opt) }}' ? 'bg-primary/10 text-primary font-medium' : ''">
                                                                     <span>{{ $opt }}</span>
                                                                     <svg x-show="selectedVal === '{{ addslashes($opt) }}'" class="size-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                                         <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                                     </svg>
                                                                 </button>
                                                             @endforeach
                                                         </div>
                                                     </div>
                                                @else
                                                    <input type="text" name="data[{{ $fKey }}]" value="{{ $oldVal }}" class="w-full block bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
