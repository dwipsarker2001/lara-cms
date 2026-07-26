@extends('admin.layout')

@section('title', 'Edit Form')
@section('breadcrumb', 'Edit Form')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0" x-data="formBuilderForm()">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="12" y1="18" x2="12" y2="12" />
                    <line x1="9" y1="15" x2="15" y2="15" />
                </svg>
                Edit Form
            </h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button type="submit" form="form-builder"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                >
                    <span>Update Form</span>
                </button>
            </div>
        </header>

        <form id="form-builder" method="POST" action="{{ route('admin.forms.update', $form) }}">
            @csrf
            @method('PUT')

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Form Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Configure the title, icon, description, and submission settings for this form.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-title" class="text-sm font-medium text-text-heading">Form Title</label>
                                    <div class="text-sm text-text-muted">A descriptive name for this form.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input id="field-title" type="text" name="title" value="{{ old('title', $form->title) }}" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                </div>
                            </div>

                            {{-- Icon Picker Field --}}
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading">Icon</label>
                                    <div class="text-sm text-text-muted">Choose an icon for this form.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 flex items-center gap-2">
                                        <input type="hidden" name="icon" x-model="selectedIcon">
                                        <div class="relative flex-1">
                                            <button type="button"
                                                @click="iconPickerOpen = !iconPickerOpen; if(iconPickerOpen) { iconLoading = true; iconSearch = ''; $nextTick(() => iconLoading = false); }"
                                                class="flex items-center gap-2 w-full rounded-lg border px-3 py-2 text-sm transition-colors bg-white h-9"
                                                :class="selectedIcon ? 'border-primary' : 'border-gray-300 hover:border-gray-400'">
                                                <template x-if="selectedIcon">
                                                    <i :class="selectedIcon" class="text-base w-5 text-center"></i>
                                                </template>
                                                <template x-if="!selectedIcon">
                                                    <span class="text-gray-400 w-5 text-center">?</span>
                                                </template>
                                                <span class="text-text-primary"
                                                    x-text="selectedIcon ? iconLabel(selectedIcon) : 'Choose icon'"></span>
                                                <svg class="ml-auto size-3 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                            <template x-if="iconPickerOpen">
                                                <div
                                                    class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg" @click.outside="iconPickerOpen = false">
                                                    <div class="p-2 border-b border-gray-100">
                                                        <input type="text" x-model="iconSearch"
                                                            placeholder="Search icons..."
                                                            class="w-full rounded-md border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                                    </div>
                                                    <div x-show="iconLoading"
                                                        class="flex items-center justify-center py-4 text-sm text-gray-400">
                                                        <svg class="animate-spin size-4 mr-2" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4" />
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                                        </svg>
                                                        Loading icons...
                                                    </div>
                                                    <div x-show="!iconLoading"
                                                        class="p-2 max-h-72 overflow-y-auto grid grid-cols-8 gap-1">
                                                        <template x-for="icon in filteredIcons" :key="icon.c">
                                                            <button type="button"
                                                                @click="selectedIcon = icon.c; iconPickerOpen = false; iconSearch = ''"
                                                                class="flex items-center justify-center size-8 rounded-md border transition-colors text-sm"
                                                                :class="selectedIcon === icon.c ?
                                                                    'border-primary bg-primary/10 ring-1 ring-primary' :
                                                                    'border-gray-200 hover:border-gray-300 bg-white'"
                                                                :title="icon.l">
                                                                <i :class="icon.c"></i>
                                                            </button>
                                                        </template>
                                                        <template x-if="filteredIcons.length === 0">
                                                            <div class="col-span-8 py-4 text-center text-sm text-gray-400">
                                                                No icons found</div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <button type="button" x-show="selectedIcon" @click="selectedIcon = ''"
                                            class="size-9 shrink-0 flex items-center justify-center rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-text-muted transition-colors">
                                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="size-4">
                                                <path d="M4 4l8 8M12 4l-8 8" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-description" class="text-sm font-medium text-text-heading">Description</label>
                                    <div class="text-sm text-text-muted">Instructions or help text shown above the form.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <textarea id="field-description" name="description" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 min-h-[60px] resize-y transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('description', $form->description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading">Submit Button Text</label>
                                    <div class="text-sm text-text-muted">The label for the form submit button.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input type="text" name="submit_text" value="{{ old('submit_text', $form->submit_text) }}" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading">Success Message</label>
                                    <div class="text-sm text-text-muted">Message shown after successful submission.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input type="text" name="success_message" value="{{ old('success_message', $form->success_message) }}" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-1.5 pb-2 pt-2">
                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                    <div class="divide-y divide-content-border">
                        <div class="grid md:grid-cols-2 items-center px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                            <div class="flex flex-col gap-1.5">
                                <div class="text-sm font-medium text-text-heading">Delete Form</div>
                                <div class="text-sm text-text-muted">Permanently delete this form and all its entries.</div>
                            </div>
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.forms.destroy', $form) }}" onsubmit="return confirm('Are you sure you want to delete this form? All entries will be lost.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-3 bg-red-500 hover:bg-red-600 text-white shadow-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        <span>Delete form</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function formBuilderForm() {
            return {
                selectedIcon: @js(old('icon', $form->icon ?? '')),
                iconPickerOpen: false,
                iconSearch: '',
                iconLoading: false,
                faIcons: window.FA_ICONS || [],

                get filteredIcons() {
                    let icons = this.faIcons;
                    if (this.iconSearch.trim()) {
                        const q = this.iconSearch.toLowerCase();
                        icons = icons.filter(i => i.l.toLowerCase().includes(q) || i.c.toLowerCase().includes(q));
                    }
                    return icons.slice(0, 2000);
                },

                iconLabel(cls) {
                    const found = this.faIcons.find(i => i.c === cls);
                    return found ? found.l : cls;
                },
            };
        }
    </script>
@endpush
