<div
    x-data="sectionPicker()"
    x-on:open-section-picker.window="open()"
    x-show="isOpen || closing"
    class="fixed inset-0 z-[200] flex justify-end font-sans"
    style="display: none;"
>
    <div
        x-show="isOpen || closing"
        x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"
        @click="close()"
    ></div>

    <div
        class="relative w-full max-w-[400px] bg-white shadow-2xl flex flex-col border-l border-gray-200 my-2 mr-2 rounded-xl h-[calc(100%-1rem)]"
        x-show="isOpen || closing"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3 shrink-0 border-b border-gray-200">
            <div>
                <h2 class="text-lg font-bold text-text-heading">Components</h2>
                <p class="text-xs text-text-muted">Select a component to add</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="close()" class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-text-muted hover:text-text-primary transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[18px]">
                        <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Search Input Bar --}}
        <div class="px-4 py-2.5 border-b border-gray-100 bg-gray-50/60 shrink-0">
            <div class="relative">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <path d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <input
                    type="text"
                    x-model="searchQuery"
                    placeholder="Search components..."
                    class="w-full pl-9 pr-8 py-2 text-xs font-medium bg-white rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all placeholder:text-gray-400"
                >
                <button
                    x-show="searchQuery.length > 0"
                    @click="searchQuery = ''"
                    type="button"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm font-bold"
                >&times;</button>
            </div>
        </div>

        {{-- Component List --}}
        <div class="flex-1 overflow-y-auto p-3">
            <template x-if="!filteredAndSortedBlocks() || filteredAndSortedBlocks().length === 0">
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-12 text-gray-300 mb-2">
                        <path d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <h4 class="text-sm font-bold text-gray-500">No components found</h4>
                    <p class="text-xs text-gray-400 mt-0.5" x-text="searchQuery ? 'Try another search term' : 'No components available'"></p>
                </div>
            </template>

            <template x-if="filteredAndSortedBlocks() && filteredAndSortedBlocks().length > 0">
                <div class="space-y-2.5">
                    <template x-for="item in filteredAndSortedBlocks()" :key="item.name">
                        <div class="group rounded-lg bg-content-bg border border-content-border/60 shadow-xs hover:shadow-md transition-all overflow-hidden relative">
                            {{-- Component Thumbnail Preview --}}
                            <div class="w-full bg-panel-bg overflow-hidden max-h-[150px] cursor-pointer" @click="select(item.name)" x-init="const ro=new ResizeObserver(()=>{const e=$el.querySelector(':scope>.thumb-inner');if(e)e.style.zoom=$el.clientWidth/1200});ro.observe($el)">
                                <div class="thumb-inner pointer-events-none" style="width:1200px" x-html="item.previewHtml"></div>
                            </div>

                            {{-- Card Footer --}}
                            <div class="flex items-center justify-between px-3 py-2.5 bg-white/50 group-hover:bg-white transition-colors">
                                {{-- Component Label & Badge --}}
                                <div class="flex items-center gap-2 min-w-0 pr-2 cursor-pointer" @click="select(item.name)">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-4 shrink-0 text-text-muted">
                                        <rect x="3" y="3" width="7" height="7" rx="1" />
                                        <rect x="14" y="3" width="7" height="7" rx="1" />
                                        <rect x="3" y="14" width="7" height="7" rx="1" />
                                        <rect x="14" y="14" width="7" height="7" rx="1" />
                                    </svg>
                                    <span class="text-sm font-semibold text-text-heading group-hover:text-primary transition-colors truncate" x-text="item.label"></span>
                                </div>

                                {{-- Action Buttons: Star + Plus --}}
                                <div class="flex items-center gap-1.5 shrink-0" @click.stop>
                                    {{-- Bookmark Star Button --}}
                                    <button
                                        type="button"
                                        @click.stop.prevent="toggleBookmark(item.name)"
                                        class="inline-flex size-7 items-center justify-center rounded-full transition-colors cursor-pointer"
                                        :class="isBookmarked(item.name) ? 'bg-amber-100 text-amber-500 hover:bg-amber-200' : 'bg-gray-100 text-gray-400 hover:bg-amber-50 hover:text-amber-500'"
                                        :title="isBookmarked(item.name) ? 'Unbookmark component' : 'Bookmark component'"
                                    >
                                        <svg viewBox="0 0 24 24" class="size-4"
                                            :class="isBookmarked(item.name) ? 'fill-amber-400 text-amber-400' : 'fill-none stroke-current'"
                                            stroke-width="1.75"
                                        >
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>

                                    {{-- Add Plus Button --}}
                                    <button
                                        type="button"
                                        @click.stop="select(item.name)"
                                        class="inline-flex size-7 items-center justify-center rounded-full bg-content-border/40 text-text-muted transition-colors group-hover:bg-primary group-hover:text-white cursor-pointer"
                                        title="Add component"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[14px]">
                                            <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function sectionPicker() {
        return {
            isOpen: false,
            closing: false,
            blockList: [],
            searchQuery: '',
            bookmarked: [],

            init() {
                this.blockList = window.editorBlockList || [];
                try {
                    this.bookmarked = JSON.parse(localStorage.getItem('lara_cms_bookmarked_components') || '[]');
                } catch (e) {
                    this.bookmarked = [];
                }
            },

            open() {
                this.isOpen = true;
                this.searchQuery = '';
                if (!this.blockList.length && window.editorBlockList) {
                    this.blockList = window.editorBlockList;
                }
            },

            close() {
                this.closing = true;
                setTimeout(() => {
                    this.closing = false;
                    this.isOpen = false;
                }, 200);
            },

            toggleBookmark(name) {
                if (this.isBookmarked(name)) {
                    this.bookmarked = this.bookmarked.filter(n => n !== name);
                } else {
                    this.bookmarked.push(name);
                }
                try {
                    localStorage.setItem('lara_cms_bookmarked_components', JSON.stringify(this.bookmarked));
                } catch (e) {}
            },

            isBookmarked(name) {
                return this.bookmarked.includes(name);
            },

            filteredAndSortedBlocks() {
                let list = [...(this.blockList || [])];

                // Search filter
                if (this.searchQuery.trim() !== '') {
                    const q = this.searchQuery.toLowerCase().trim();
                    list = list.filter(item =>
                        (item.label || '').toLowerCase().includes(q) ||
                        (item.name || '').toLowerCase().includes(q)
                    );
                }

                // Sort: Bookmarked components FIRST (sorted A-Z), then Unbookmarked components (sorted A-Z)
                return list.sort((a, b) => {
                    const isBookmarkedA = this.isBookmarked(a.name);
                    const isBookmarkedB = this.isBookmarked(b.name);

                    if (isBookmarkedA && !isBookmarkedB) return -1;
                    if (!isBookmarkedA && isBookmarkedB) return 1;

                    const labelA = (a.label || a.name || '').toLowerCase();
                    const labelB = (b.label || b.name || '').toLowerCase();
                    return labelA.localeCompare(labelB);
                });
            },

            select(name) {
                this.close();
                window.dispatchEvent(new CustomEvent('section-selected', { detail: { name: name } }));
            },
        };
    }
</script>
@endpush
