@extends('admin.layout')

@section('title', 'Global Settings')
@section('breadcrumb', 'Globals')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <circle cx="12" cy="12" r="3" />
                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                </svg>
                Global Settings
            </h1>
        </header>

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] py-3 text-sm font-medium text-text-heading">Site Settings</div>
            <div class="px-1.5 pb-2 max-w-2xl">
                <form method="POST" action="{{ route('admin.settings') }}" class="bg-content-bg rounded-xl shadow-sm p-4 space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-semibold text-text-primary mb-1">Site Title</label>
                        <input name="site_title" value="{{ old('site_title', $settings->site_title) }}"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-text-primary mb-1">Theme Color</label>
                        <input type="color" name="theme_color" value="{{ old('theme_color', $settings->theme_color) }}"
                            class="w-12 h-10 p-0.5 border border-gray-300 rounded cursor-pointer shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-text-primary mb-1">Contact Number</label>
                        <input name="contact_number" value="{{ old('contact_number', $settings->contact_number) }}"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    </div>
                    <div class="flex items-center gap-2 pt-2">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
