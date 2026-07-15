<div x-data="{
    show: {{ (session('success') || session('error')) ? 'true' : 'false' }},
    message: '{{ addslashes(session('success') ?? (session('error') ?? '')) }}',
    type: '{{ session('success') ? 'success' : (session('error') ? 'error' : 'success') }}',

    timer: null,

    hideAfter(ms) {
        clearTimeout(this.timer);
        this.timer = setTimeout(() => this.show = false, ms);
    },

    showToast(message, type = 'success') {
        this.message = message;
        this.type = type;
        this.show = true;
        this.hideAfter(type === 'error' ? 6000 : 4000);
    },

    init() {
        if (this.show) {
            this.hideAfter(this.type === 'error' ? 6000 : 4000);
        }

        window.addEventListener('toast', (e) => {
            const detail = e.detail || {};
            this.showToast(detail.message ?? '', detail.type || 'success');
        });
    }
}" x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-2"
class="absolute bottom-5 right-5 z-[9999] border border-gray-200 rounded-lg" style="display:none;">
    <div
        class="flex items-center gap-4 bg-white rounded-lg shadow-md border border-gray-100
           px-4 py-3 w-[320px]">
        <!-- Success -->
        <template x-if="type === 'success'">
            <div
                class="flex items-center justify-center w-5 h-5 p-1 rounded-full bg-emerald-500 shrink-0
                    shadow-[0_0_0_5px_rgba(16,185,129,0.15)]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-check-icon lucide-check text-white w-7 h-7">
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            </div>
        </template>

        <!-- Error -->
        <template x-if="type === 'error'">
            <div
                class="flex items-center justify-center w-5 h-5 p-1 rounded-full bg-red-500 shrink-0
                    shadow-[0_0_0_5px_rgba(239,68,68,0.15)]">
                <svg xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="text-white w-7 h-7">
                    <path d="M18 6L6 18"/>
                    <path d="M6 6L18 18"/>
                </svg>
            </div>
        </template>

        <div class="flex-1 min-w-0">
            <p class="text-[15px] font-semibold text-gray-900 leading-5 break-words" x-text="message"></p>
        </div>
    </div>
</div>
