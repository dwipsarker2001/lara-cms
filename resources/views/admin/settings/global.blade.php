@extends('admin.layout')

@section('title', 'My Preferences')
@section('breadcrumb', 'Settings')

@section('content')
    <div
        class="max-w-5xl mx-auto px-2 sm:px-0"
        style="max-width: 64rem;"
        x-data="{
            activeTab: 'general',
            language: 'en',

            updateState: 'idle',
            currentVersion: '{{ $currentVersion }}',
            latestVersion: null,
            updateLogs: [],
            updateError: null,

            checkForUpdates() {
                this.updateState = 'checking';
                this.updateLogs = [];
                this.updateError = null;

                fetch('{{ route('admin.updates.check') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    this.currentVersion = data.current_version;
                    this.latestVersion = data.latest_version;
                    this.updateState = data.update_available ? 'found' : 'up_to_date';
                })
                .catch(() => {
                    this.updateState = 'error';
                    this.updateError = 'Failed to reach the update server. Check your internet connection.';
                });
            },

            runUpdate() {
                this.updateState = 'updating';
                this.updateLogs = ['Preparing update process...'];

                fetch('{{ route('admin.updates.run') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                })
                .then(r => r.json().then(data => ({ ok: r.ok, data })))
                .then(({ ok, data }) => {
                    if (!ok || !data.success) throw new Error(data.error || data.message || 'Update failed.');
                    let delay = 300;
                    data.logs.forEach(log => { setTimeout(() => this.updateLogs.push(log), delay += 600); });
                    setTimeout(() => { this.currentVersion = data.version; this.updateState = 'done'; }, delay + 700);
                })
                .catch(err => {
                    this.updateLogs.push('[ERROR] ' + err.message);
                    this.updateError = err.message;
                    this.updateState = 'error';
                });
            }
        }"
    >
        {{-- ==================== Preferences Form ==================== --}}
        <form method="POST" action="{{ route('admin.settings') }}">
            @csrf
            @method('PUT')

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 17 17" class="size-5 text-text-muted shrink-0" fill="none" stroke="currentColor" stroke-width="1">
                        <g transform="translate(1 1)" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5.2598416,5.94378286 C7.1618416,5.94378286 8.23172732,4.87389714 8.23172732,2.97188571 C8.23172732,1.06987429 7.1618416,0 5.2598416,0 C3.35783017,0 2.28794446,1.06987429 2.28794446,2.97188571 C2.28794446,4.87389714 3.35783017,5.94378286 5.2598416,5.94378286 Z" />
                            <path d="M6.14417303,8.21984 C5.85300732,8.18864 5.55730446,8.17264 5.25785303,8.17264 C3.57371589,8.17264 2.00779589,8.67876571 0.703875888,9.5472 C-0.749792683,10.5153943 0.23180046,12.3827429 1.97837875,12.3827429 L5.69643589,12.3827429" />
                        </g>
                    </svg>
                    My Preferences
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        Save
                    </button>
                </div>
            </header>

            {{-- Tabs Header --}}
            <div class="mb-6 flex gap-4 border-b border-content-border pb-px">
                <button type="button" @click="activeTab = 'general'" :class="{'border-primary text-primary font-medium active': activeTab === 'general', 'border-transparent text-text-muted hover:text-text-heading': activeTab !== 'general'}" class="pb-3 px-1 border-b-2 text-sm transition-colors cursor-pointer" data-tab-btn="general">
                    General Settings
                </button>
                <button type="button" @click="activeTab = 'sendgrid'" :class="{'border-primary text-primary font-medium active': activeTab === 'sendgrid', 'border-transparent text-text-muted hover:text-text-heading': activeTab !== 'sendgrid'}" class="pb-3 px-1 border-b-2 text-sm transition-colors cursor-pointer" data-tab-btn="sendgrid">
                    SendGrid Settings
                </button>
            </div>

            {{-- General Settings Tab Content --}}
            <div x-show="activeTab === 'general'" class="space-y-6">
                <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                    <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Settings</div>
                    <p class="px-[18px] pb-3 text-sm text-text-muted">Configure your site name and control panel preferences.</p>
                    <div class="px-1.5 pb-2">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                            <div class="divide-y divide-content-border">

                                {{-- Language --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading">Language</label>
                                        <div class="text-sm text-text-muted">The preferred language for the control panel.</div>
                                    </div>
                                    <div>
                                        <div x-data="{ open: false, selected: 'en', options: [{value:'en',label:'English'},{value:'fr',label:'French'},{value:'de',label:'German'},{value:'es',label:'Spanish'},{value:'nl',label:'Dutch'}], get selectedLabel() { return this.options.find(o => o.value === this.selected)?.label ?? 'English' }, select(val) { this.selected = val; this.open = false } }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-10 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                <span class="truncate" x-text="selectedLabel"></span>
                                                <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5" style="display: none;">
                                                <template x-for="opt in options" :key="opt.value">
                                                    <button type="button" @click="select(opt.value)" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="opt.value === selected ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                        <span x-text="opt.label"></span>
                                                    </button>
                                                </template>
                                            </div>
                                            <input type="hidden" name="language" :value="selected">
                                        </div>
                                    </div>
                                </div>

                                {{-- Site Title --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-site-title">Site Title</label>
                                        <div class="text-sm text-text-muted">The name of your site, shown in the browser tab and control panel.</div>
                                    </div>
                                    <div>
                                        <input id="field-site-title" type="text" name="site_title" value="{{ old('site_title', $settings->site_title) }}"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" />
                                        @error('site_title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Currency --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-currency">Currency</label>
                                        <div class="text-sm text-text-muted">The default currency for your site.</div>
                                    </div>
                                    <div>
                                        <select id="field-currency" name="currency"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                            @foreach (['USD' => 'USD ($)', 'EUR' => 'EUR (€)', 'GBP' => 'GBP (£)', 'BDT' => 'BDT (৳)', 'INR' => 'INR (₹)', 'CAD' => 'CAD (C$)', 'AUD' => 'AUD (A$)', 'JPY' => 'JPY (¥)', 'CNY' => 'CNY (¥)', 'SAR' => 'SAR (﷼)', 'AED' => 'AED (د.إ)'] as $code => $label)
                                                <option value="{{ $code }}" {{ (old('currency', $settings->currency ?? 'USD')) == $code ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('currency') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SendGrid Settings Tab Content --}}
            <div x-show="activeTab === 'sendgrid'" x-transition class="space-y-6">
                <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                    <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">SendGrid Integration</div>
                    <p class="px-[18px] pb-3 text-sm text-text-muted">Configure your SendGrid API key and default sender email address.</p>
                    <div class="px-1.5 pb-2">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                            <div class="divide-y divide-content-border">

                                {{-- SendGrid API Key --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-sendgrid-api-key">SendGrid API Key</label>
                                        <div class="text-sm text-text-muted">API key used for sending marketing emails and domain white-labeling.</div>
                                    </div>
                                    <div>
                                        <input id="field-sendgrid-api-key" type="password" name="sendgrid_api_key" value="{{ old('sendgrid_api_key', $settings->sendgrid_api_key) }}"
                                            placeholder="SG.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-mono text-xs" />
                                        @error('sendgrid_api_key') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- SendGrid From Email --}}
                                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-sm font-medium text-text-heading" for="field-sendgrid-from-email">Default From Email</label>
                                        <div class="text-sm text-text-muted">The authenticated email address used to dispatch campaign emails.</div>
                                    </div>
                                    <div>
                                        <input id="field-sendgrid-from-email" type="email" name="sendgrid_from_email" value="{{ old('sendgrid_from_email', $settings->sendgrid_from_email) }}"
                                            placeholder="info@yourdomain.com"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" />
                                        @error('sendgrid_from_email') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection
