{{-- Field label row with source binding button --}}
<div class="flex items-center justify-between mb-1">
    <label class="block text-sm font-semibold text-text-primary" x-text="field.label"></label>
    @include('admin.collections.entries.partials._link-btn')
</div>
