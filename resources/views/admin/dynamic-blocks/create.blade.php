@extends('admin.layout')

@section('title', 'New Block')
@section('breadcrumb', 'New Block')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0">
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <path d="M21 12H3" />
            </svg>
            New Block
        </h1>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.dynamic-blocks.index') }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200"
            >Cancel</a>
            <button type="submit" form="block-form"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
            >Create Block</button>
        </div>
    </header>

    <form id="block-form" method="POST" action="{{ route('admin.dynamic-blocks.store') }}">
        @csrf

        <div class="space-y-6">
            <div class="bg-panel-bg rounded-2xl p-[7px]">
                <div class="px-[18px] pt-3 pb-1">
                    <div class="text-sm font-medium text-text-heading">Details</div>
                </div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Configure the block name, visibility, and behaviour.</p>
                <div class="px-1.5 pb-2 space-y-4">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-label" class="text-sm font-medium text-text-heading">Block Name</label>
                                    <div class="text-sm text-text-muted">This name will appear in the block picker when adding a block to your page.</div>
                                </div>
                                <div>
                                    <input id="field-label" type="text" name="label" value="{{ old('label') }}" placeholder="My Block" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    @error('label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-sm font-medium text-text-heading">Do you want to make this global?</span>
                                    <div class="text-sm text-text-muted">Global blocks are synced on every page — if one page changes it, every page changes.</div>
                                </div>
                                <x-admin::toggle-switch model="global" name="global" :value="old('global') ? 'true' : 'false'" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="name" id="field-name" value="{{ old('name') }}">
    </form>
</div>

<script>
    document.getElementById('field-label').addEventListener('input', function () {
        var name = this.value
            .replace(/[^a-zA-Z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .toLowerCase();
        document.getElementById('field-name').value = name;
    });
</script>
@endsection
