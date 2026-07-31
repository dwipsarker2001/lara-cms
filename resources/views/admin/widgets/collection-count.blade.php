<div x-data="collectionCountWidget()" class="w-full min-h-[50px]">
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
</div>

<script>
    (function() {
        const register = () => {
            if (window.Alpine.components && window.Alpine.components.collectionCountWidget) {
                return;
            }
            window.Alpine.data('collectionCountWidget', () => ({
                collections: @json($collections),
                selectedSlug: localStorage.getItem('widget_col_slug') || (@json($collections)[0]?.slug || ''),

                init() {
                    this.$nextTick(() => {
                        this.updateHeaderLabel();
                    });
                },

                get current() {
                    if (!this.selectedSlug) return null;
                    return this.collections.find(c => c.slug === this.selectedSlug) || null;
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
