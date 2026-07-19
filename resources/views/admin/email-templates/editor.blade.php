@extends('admin.layout')

@section('title', 'Template Editor — '.$template->name)

@section('content-full')
<script>
    window.templateContent = @json($template->content ? json_decode($template->content) : null);
    window.templateSaveUrl = @json(route('admin.email-templates.save-content', $template));
</script>

<div class="flex h-full flex-col">
    <div class="flex items-center justify-between px-4 py-2 border-b border-content-border bg-white shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.email-templates.index') }}" class="size-7 shrink-0 flex items-center justify-center rounded-full border border-gray-300 bg-white text-text-primary hover:bg-gray-100 transition-colors">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <span class="text-sm font-semibold text-text-heading">{{ $template->name }}</span>
        </div>
        <button id="btn-save-template"
            class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
        >Save Template</button>
    </div>

    <div id="email-editor-container" class="flex-1"></div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/email-editor.js')
@endpush
