{{-- Delete AI Model Confirmation Modal --}}
<div
    x-show="showDeleteModal"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    @keydown.escape.window="showDeleteModal = false"
>
    <div class="fixed inset-0 bg-black/40" @click="showDeleteModal = false"></div>
    <div class="relative w-full max-w-[400px] bg-content-bg rounded-2xl border border-content-border shadow-2xl p-6 z-10">
        <h3 class="text-base font-bold text-text-heading leading-tight">Delete AI Model</h3>
        <p class="text-xs text-text-muted mt-2">Are you sure you want to delete <span class="font-semibold text-text-heading" x-text="deletingModel?.name"></span>? This model will be removed from your AI Copilot.</p>
        <div class="mt-6 flex items-center gap-3">
            <button type="button" @click="showDeleteModal = false" class="flex-1 py-2.5 rounded-xl border border-content-border bg-content-bg text-xs font-semibold text-text-heading hover:bg-body-bg transition-colors cursor-pointer text-center">Cancel</button>
            <button type="button" @click="executeDelete()" class="flex-1 py-2.5 rounded-xl bg-danger text-xs font-semibold text-white hover:opacity-90 active:scale-[0.98] shadow-sm shadow-danger/20 transition-all cursor-pointer text-center">Delete</button>
        </div>
    </div>
</div>
