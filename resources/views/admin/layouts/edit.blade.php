@extends('admin.layout')

@section('title', 'Edit Layout')
@section('breadcrumb', 'Edit Layout')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0" x-data="{ showDeleteLayoutModal: false }">
        <form method="POST" action="{{ route('admin.layouts.update', $layout) }}">
            @csrf @method('PATCH')

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <line x1="3" y1="9" x2="21" y2="9" />
                        <line x1="9" y1="21" x2="9" y2="9" />
                    </svg>
                    Edit Layout
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <button type="button"
                        @click="showDeleteLayoutModal = true"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-red-600 hover:bg-red-700 text-white shadow-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2 size-4 shrink-0 text-white">
                            <path d="M3 6h18"/>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                        <span>Delete</span>
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        <span>Update Layout</span>
                    </button>
                </div>
            </header>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Layout Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Update the name and content type for this layout.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-name" class="text-sm font-medium text-text-heading">Name</label>
                                    <div class="text-sm text-text-muted">A descriptive name for this layout.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-name"
                                            type="text"
                                            name="name"
                                            value="{{ old('name', $layout->name) }}"
                                            placeholder="e.g. Default Page"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-collection" class="text-sm font-medium text-text-heading">Collection</label>
                                    <div class="text-sm text-text-muted">Which content type this layout applies to.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <div
                                            x-data="{
                                                open: false,
                                                selected: '{{ old('collection', $layout->collection) }}',
                                                options: [
                                                    { value: 'page', label: 'Page' },
                                                    { value: 'blog', label: 'Blog' },
                                                    { value: 'package', label: 'Package' },
                                                ],
                                                get selectedLabel() {
                                                    return this.options.find(o => o.value === this.selected)?.label ?? 'Select...';
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
                                            <input type="hidden" name="collection" :value="selected">
                                            @error('collection') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Delete Layout Confirmation Modal --}}
        <form id="delete-layout-form" method="POST" action="{{ route('admin.layouts.destroy', $layout) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
        <x-admin::delete-modal
            show="showDeleteLayoutModal"
            title="Delete Layout"
            confirm-action="document.getElementById('delete-layout-form')?.submit()"
        >
            Are you sure you want to delete <span class="font-medium text-text-heading">“{{ $layout->name }}”</span>? This action cannot be undone.
        </x-admin::delete-modal>
    </div>
@endsection
