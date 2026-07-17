<div
    x-data="fieldPicker()"
    x-on:open-field-picker.window="open()"
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
        <div class="flex items-center justify-between px-5 py-3 shrink-0 border-b border-gray-200">
            <h2 class="text-lg font-bold text-text-heading">Components</h2>
            <div class="flex items-center gap-2">
                <button @click="close()" class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-text-muted hover:text-text-primary transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[18px]">
                        <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-3">
            <template x-if="!fieldList || fieldList.length === 0">
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <h4 class="text-xl font-bold text-slate-500">No components available</h4>
                </div>
            </template>

            <template x-if="fieldList && fieldList.length > 0">
                <div class="space-y-2">
                    <template x-for="item in fieldList" :key="item.name">
                        <div class="rounded-[5px] bg-content-bg border border-content-border/60 shadow-sm overflow-hidden">
                            <div class="w-full bg-panel-bg overflow-hidden max-h-[160px]" x-init="const ro=new ResizeObserver(()=>{const e=$el.querySelector(':scope>.thumb-inner');if(e)e.style.zoom=$el.clientWidth/1200});ro.observe($el)">
                                <div class="thumb-inner" style="width:1200px" x-html="item.previewHtml"></div>
                            </div>
                            <button
                                @click="select(item.name)"
                                class="flex w-full items-center justify-between px-3 py-2.5 text-left group hover:bg-white/50 transition-colors"
                            >
                                <div class="flex items-center gap-2 min-w-0">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-4 shrink-0 text-text-muted">
                                        <rect x="3" y="3" width="7" height="7" rx="1" />
                                        <rect x="14" y="3" width="7" height="7" rx="1" />
                                        <rect x="3" y="14" width="7" height="7" rx="1" />
                                        <rect x="14" y="14" width="7" height="7" rx="1" />
                                    </svg>
                                    <span class="text-sm font-semibold text-text-heading group-hover:text-primary transition-colors truncate" x-text="item.label"></span>
                                </div>
                                <span class="inline-flex size-7 items-center justify-center rounded-full bg-content-border/40 text-text-muted transition-colors group-hover:bg-primary group-hover:text-white">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[14px]">
                                        <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fieldPicker() {
        return {
            isOpen: false,
            closing: false,
            fieldList: [],

            init() {
                this.fieldList = window.formFieldList || [];
            },

            open() {
                this.isOpen = true;
            },

            close() {
                this.closing = true;
                setTimeout(() => {
                    this.closing = false;
                    this.isOpen = false;
                }, 200);
            },

            select(name) {
                this.close();
                window.dispatchEvent(new CustomEvent('field-selected', { detail: { name: name } }));
            },
        };
    }
</script>
@endpush
