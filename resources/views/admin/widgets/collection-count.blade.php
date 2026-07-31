<div x-data="collectionCountWidget()" class="w-full relative min-h-[50px]">
    <!-- Top-Right Settings Gear inside the white area -->
    <div class="absolute top-0 right-0 z-20">
        <button @click="showSettings = !showSettings" class="text-text-muted hover:text-text-primary p-0.5 rounded transition-colors cursor-pointer" title="Select Collection">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
        <!-- Dropdown Menu -->
        <div x-show="showSettings" @click.outside="showSettings = false" class="absolute right-0 top-full mt-1.5 min-w-[10rem] bg-white border border-gray-200 rounded-xl shadow-xl p-1.5 space-y-0.5 z-[100]" style="display: none;">
            <template x-for="c in collections">
                <button @click="selectCollection(c.slug)" class="flex w-full items-center justify-between px-2.5 py-1.5 rounded-lg text-xs hover:bg-gray-50 transition-colors text-left cursor-pointer" :class="selectedSlug === c.slug ? 'bg-primary/5 text-primary font-medium' : 'text-text-primary'">
                    <span x-text="c.name"></span>
                    <span x-show="selectedSlug === c.slug" class="font-bold">✓</span>
                </button>
            </template>
        </div>
    </div>

    <!-- Card Content -->
    <template x-if="current">
        <div class="flex flex-col">
            <div class="flex items-end justify-between gap-2">
                <div>
                    <!-- Big Value -->
                    <div class="text-[26px] font-semibold leading-none text-text-heading" x-text="current.count"></div>
                    <!-- Delta/Subtitle -->
                    <div class="mt-2 flex items-center gap-1 text-[12px]">
                        <span :class="current.up ? 'font-medium text-emerald-600' : 'font-medium text-red-500'" x-text="current.delta"></span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template x-if="!current">
        <div class="text-center py-2 text-xs text-text-muted flex flex-col items-center justify-center gap-1 min-h-[44px] pr-6">
            <span>Click settings gear to select collection</span>
        </div>
    </template>
</div>

<script>
    (function() {
        const register = () => {
            if (window.Alpine.components && window.Alpine.components.collectionCountWidget) {
                return;
            }
            window.Alpine.data('collectionCountWidget', () => ({
                showSettings: false,
                selectedSlug: localStorage.getItem('widget_col_slug') || '',
                collections: @json($collections),

                init() {
                    this.$nextTick(() => {
                        this.updateHeaderLabel();
                    });
                },

                get current() {
                    if (!this.selectedSlug) return null;
                    return this.collections.find(c => c.slug === this.selectedSlug) || null;
                },

                selectCollection(slug) {
                    this.selectedSlug = slug;
                    localStorage.setItem('widget_col_slug', slug);
                    this.showSettings = false;
                    this.updateHeaderLabel();
                },

                updateHeaderLabel() {
                    const c = this.current;
                    const headerSpan = this.$el.closest('.bg-gray-100')?.querySelector('.text-text-muted.truncate');
                    if (headerSpan) {
                        headerSpan.textContent = c ? 'Total ' + c.name : 'Collection Counter';
                    }
                }
            }));
        };

        if (window.Alpine) {
            register();
        } else {
            document.addEventListener('alpine:init', register);
        }
    })();
</script>
