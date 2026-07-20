@extends('marketing.layouts.app')

@section('customStyle')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    main {
        padding: 0 !important;
        overflow: hidden !important;
    }
    main > div {
        max-width: 100% !important;
        height: 100% !important;
        position: relative;
    }
    #email-editor-container {
        height: calc(100vh - 64px) !important;
        border: none !important;
        border-radius: 0 !important;
        
        /* Override editor chrome and panel backgrounds to white */
        --tpl-user-bg: #ffffff;
        --tpl-user-bg-elevated: #ffffff;
        --tpl-user-bg-hover: #f8fafc;
        --tpl-user-bg-active: #f1f5f9;
        --tpl-user-border: #e2e8f0;
        --tpl-user-border-light: #f1f5f9;

        /* Soften the email template layout / canvas shadow */
        --tpl-user-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.03);
        --tpl-user-shadow-lg: 0 8px 24px 0 rgba(0, 0, 0, 0.04);
        --tpl-user-shadow-md: 0 4px 12px 0 rgba(0, 0, 0, 0.03);
        --tpl-user-shadow-sm: 0 2px 6px 0 rgba(0, 0, 0, 0.015);
        --tpl-user-shadow-xl: 0 12px 32px 0 rgba(0, 0, 0, 0.05);
    }
</style>
@endsection

@section('content')
<script>
    window.templateContent = @json($template->content ? json_decode($template->content) : null);
    window.templateSaveUrl = @json(route('app.template.save-content'));
    window.templateId = @json($template->template_id);
</script>

<div class="flex h-full flex-col">
    <!-- Keep the items in the DOM so we can fetch them from JS, but keep the top bar hidden to avoid layout shift -->
    <div id="editor-top-bar" class="hidden">
        <div class="flex items-center gap-3">
            <a id="btn-back" href="{{ route('app.template.index') }}" class="size-7 shrink-0 flex items-center justify-center rounded-full border border-gray-300 bg-white text-text-primary hover:bg-gray-100 transition-colors">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <span id="template-name" class="text-sm font-semibold text-text-heading">{{ $template->name }}</span>
        </div>
        <button id="btn-save-template"
            class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-9 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
        >Save Template</button>
    </div>

    <!-- The container style is updated to fill the screen correctly -->
    <div id="email-editor-container" class="flex-1" style="height: calc(100vh - 170px); min-height: 600px; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;"></div>
</div>
@endsection

@section('script')
    @vite('resources/js/email-editor.js')
@endsection
