@extends('admin.layout')

@section('title', 'Create Collection')
@section('breadcrumb', 'Create Collection')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0" x-data="collectionForm()">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
                    class="size-6 shrink-0 text-text-muted">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>
                Create Collection
            </h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button type="submit" form="collection-form"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    <span>Save Collection</span>
                </button>
            </div>
        </header>

        <form id="collection-form" method="POST" action="{{ route('admin.collections.store') }}">
            @csrf

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="flex items-center justify-between px-[18px] pt-3 pb-1">
                    <div>
                        <div class="text-sm font-medium text-text-heading">Collection Details</div>
                        <p class="text-sm text-text-muted mt-1 mb-2">Configure the name and icon for this collection.</p>
                    </div>
                </div>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-name" class="text-sm font-medium text-text-heading">Name</label>
                                    <div class="text-sm text-text-muted">A descriptive name for this collection.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input id="field-name" type="text" name="name" placeholder="Products"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading">Icon</label>
                                    <div class="text-sm text-text-muted">Choose an icon for this collection.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 flex items-center gap-2">
                                        <input type="hidden" name="icon" x-model="selectedIcon">
                                        <div class="relative flex-1">
                                            <button type="button"
                                                @click="iconPickerOpen = !iconPickerOpen; if(iconPickerOpen) { iconLoading = true; iconSearch = ''; $nextTick(() => iconLoading = false); }"
                                                class="flex items-center gap-2 w-full rounded-lg border px-3 py-2 text-sm transition-colors bg-white h-9"
                                                :class="selectedIcon ? 'border-primary' :
                                                    'border-gray-300 hover:border-gray-400'">
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
                                                    class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg">
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
                                    <label class="text-sm font-medium text-text-heading">Enable SEO</label>
                                    <div class="text-sm text-text-muted">By enabling SEO, you will enable the SEO feature on your collection.</div>
                                </div>
                                <div class="flex items-center justify-end h-full">
                                    <button type="button" role="switch" :aria-checked="enableSeo" :data-state="enableSeo ? 'checked' : 'unchecked'" @click="enableSeo = !enableSeo" class="relative flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/30 data-[state=checked]:shadow-inner data-[state=checked]:border-emerald-500 data-[state=checked]:bg-emerald-500 data-[state=unchecked]:!border-gray-300 data-[state=unchecked]:bg-gray-200">
                                        <span :data-state="enableSeo ? 'checked' : 'unchecked'" class="my-auto flex items-center justify-center size-5 rounded-full bg-white text-xs shadow-[0_10px_15px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] transition-transform will-change-transform data-[state=checked]:translate-x-[20px] data-[state=unchecked]:translate-x-0"></span>
                                    </button>
                                    <input type="hidden" name="enable_seo" :value="enableSeo ? '1' : '0'">
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
    <script>
        function collectionForm() {
            return {
                selectedIcon: '',
                iconPickerOpen: false,
                iconSearch: '',
                iconLoading: false,
                faIcons: window.FA_ICONS || [],
                enableSeo: true,

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
