@extends('admin.layout')

@section('title', 'Settings')
@section('breadcrumb', 'Settings')

@section('content')
    <script>
        window.settingsCustomFields = @json($settings->custom_fields ?? []);
        window.settingsCustomValues = @json($settings->custom_values ?? (object)[]);
    </script>
    <div
        class="max-w-5xl mx-auto px-2 sm:px-0"
        style="max-width: 64rem;"
        x-data="globalSettingsForm()"
    >
        {{-- ==================== Preferences Form ==================== --}}
        <form method="POST" action="{{ route('admin.settings') }}" id="settings-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="custom_fields" :value="JSON.stringify(fields)">

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-6 shrink-0 text-text-muted">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.51a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    Settings
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <button type="button" @click="openFieldModal()" x-show="activeTab === 'general'"
                        class="inline-flex items-center justify-center gap-1.5 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 px-3 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200 text-sm">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                            <path d="M8 3v10M3 8h10" />
                        </svg>
                        Add Input
                    </button>
                    <button type="button" @click="$dispatch('open-ai-model-modal')" x-show="activeTab === 'ai'" x-cloak
                        class="inline-flex items-center justify-center gap-1.5 shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 px-3 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200 text-sm">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                            <path d="M8 3v10M3 8h10" />
                        </svg>
                        Add Model
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        Save Settings
                    </button>
                </div>
            </header>

            {{-- Settings Navigation Tabs --}}
            <div class="flex items-center gap-1 border-b border-content-border mb-6 px-2 sm:px-0">
                <button type="button" @click="activeTab = 'general'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 cursor-pointer -mb-px"
                    :class="activeTab === 'general' ? 'border-primary text-primary font-semibold' : 'border-transparent text-text-muted hover:text-text-primary'">
                    General
                </button>
                <button type="button" @click="activeTab = 'recaptcha'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 cursor-pointer -mb-px"
                    :class="activeTab === 'recaptcha' ? 'border-primary text-primary font-semibold' : 'border-transparent text-text-muted hover:text-text-primary'">
                    reCAPTCHA
                </button>
                <button type="button" @click="activeTab = 'ai'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 cursor-pointer -mb-px"
                    :class="activeTab === 'ai' ? 'border-primary text-primary font-semibold' : 'border-transparent text-text-muted hover:text-text-primary'">
                    AI Assistant
                </button>
                <button type="button" @click="activeTab = 'stock_images'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 cursor-pointer -mb-px"
                    :class="activeTab === 'stock_images' ? 'border-primary text-primary font-semibold' : 'border-transparent text-text-muted hover:text-text-primary'">
                    Stock Images & Keys
                </button>
                <button type="button" @click="activeTab = 'cloudflare_r2'"
                    class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 cursor-pointer -mb-px"
                    :class="activeTab === 'cloudflare_r2' ? 'border-primary text-primary font-semibold' : 'border-transparent text-text-muted hover:text-text-primary'">
                    Cloudflare Storage
                </button>
            </div>

            {{-- Settings Content --}}
            <div class="space-y-6">
                {{-- Tabs --}}
                @include('admin.settings.partials.tabs._general')
                @include('admin.settings.partials.tabs._recaptcha')
                @include('admin.settings.partials.tabs._ai')
                @include('admin.settings.partials.tabs._stock_images')
                @include('admin.settings.partials.tabs._cloudflare_r2')

                {{-- System Updates Card --}}
                @include('admin.settings.partials._system_updates')
            </div>

        </form>

        {{-- Custom Field Modal --}}
        @include('admin.settings.partials.custom-fields._field_modal')
    </div>
@endsection

@push('scripts')
    @include('admin.settings.partials._scripts')
@endpush
