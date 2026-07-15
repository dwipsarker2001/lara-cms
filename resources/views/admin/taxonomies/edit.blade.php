@extends('admin.layout')

@section('title', 'Edit Taxonomy')
@section('breadcrumb', 'Edit Taxonomy')

@section('content')
    <div
        class="max-w-5xl mx-auto px-2 sm:px-0"
        x-data="{
            title: '{{ old('title', $taxonomy->title) }}',
            slug: '{{ old('slug', $taxonomy->slug) }}',
            description: '{{ old('description', $taxonomy->description) }}',
            terms: @js($taxonomy->terms->map(fn ($t) => ['id' => $t->id, 'title' => $t->title])->values()->toArray()),
            termInput: '',
            addTerm() {
                const t = this.termInput.trim();
                if (t && !this.terms.some(term => term.title === t)) {
                    this.terms.push({ id: null, title: t });
                }
                this.termInput = '';
            },
            removeTerm(index) {
                this.terms.splice(index, 1);
            },
        }"
    >
        <form method="POST" action="{{ route('admin.taxonomies.update', $taxonomy) }}">
            @csrf @method('PATCH')

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                        <line x1="4" y1="9" x2="20" y2="9" />
                        <line x1="4" y1="15" x2="20" y2="15" />
                        <line x1="10" y1="3" x2="8" y2="21" />
                        <line x1="16" y1="3" x2="14" y2="21" />
                    </svg>
                    Edit Taxonomy
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <a href="{{ route('admin.taxonomies.index') }}"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-gradient-to-b from-content-bg to-gray-50 hover:to-gray-100 text-text-primary border border-content-border shadow-sm"
                    >
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        <span>Update Taxonomy</span>
                    </button>
                </div>
            </header>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Taxonomy Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Update this taxonomy group and manage its terms.</p>
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
                                            value="{{ old('title', $taxonomy->title) }}"
                                            placeholder="e.g. Categories, Topics, Regions"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-slug" class="text-sm font-medium text-text-heading">Slug</label>
                                    <div class="text-sm text-text-muted">The URL-friendly identifier. Used in API and template references.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-slug"
                                            type="text"
                                            name="slug"
                                            x-model="slug"
                                            value="{{ old('slug', $taxonomy->slug) }}"
                                            placeholder="e.g. categories"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('slug') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-description" class="text-sm font-medium text-text-heading">Description</label>
                                    <div class="text-sm text-text-muted">A brief explanation of this taxonomy's purpose.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <textarea
                                            id="field-description"
                                            name="description"
                                            x-model="description"
                                            rows="3"
                                            placeholder="Optional description..."
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-y min-h-[60px]"
                                        >{{ old('description', $taxonomy->description) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Terms</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Add, edit, or remove the terms within this taxonomy. These become selectable tags on posts.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="px-[18px] py-3">
                            <div class="w-full rounded-lg border border-content-border bg-content-bg px-3 py-1.5 text-sm text-text-primary transition-all duration-150 focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary focus:outline-none">
                                <div class="flex flex-wrap gap-1 mb-1">
                                    <template x-for="(term, ti) in terms" :key="ti">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-panel-bg rounded text-xs font-medium text-text-primary">
                                            <span x-text="term.title"></span>
                                            <button @click.prevent="removeTerm(ti)" type="button" class="text-danger hover:text-danger/70 leading-none">&times;</button>
                                        </span>
                                    </template>
                                </div>
                                <input
                                    id="field-term"
                                    type="text"
                                    x-model="termInput"
                                    @keydown.enter.prevent="addTerm"
                                    placeholder="Type a term and press Enter..."
                                    class="w-full border-0 p-0 bg-transparent text-sm text-text-primary placeholder:text-text-muted outline-none ring-0 focus:outline-none focus:ring-0"
                                >
                            </div>

                            <template x-for="(term, ti) in terms" :key="ti">
                                <div>
                                    <input type="hidden" :name="`terms[${ti}][id]`" :value="term.id">
                                    <input type="hidden" :name="`terms[${ti}][title]`" :value="term.title">
                                </div>
                            </template>

                            @if ($taxonomy->terms->isEmpty())
                                <p class="text-sm text-text-muted mt-3">No terms yet. Add your first term above.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection