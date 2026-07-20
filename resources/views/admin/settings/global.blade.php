@extends('admin.layout')

@section('title', 'My Preferences')
@section('breadcrumb', 'Settings')

@section('content')
    <div
        class="max-w-5xl mx-auto px-2 sm:px-0"
        style="max-width: 64rem;"
        x-data="{
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

        </form>

        {{-- ==================== CMS Updates Panel ==================== --}}
        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">CMS Updates</div>
            <p class="px-[18px] pb-3 text-sm text-text-muted">Check for new Lara CMS versions and apply updates with one click.</p>

            <div class="px-1.5 pb-2">
                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm">
                    <div class="divide-y divide-content-border">

                        {{-- Version info & check button row --}}
                        <div class="grid md:grid-cols-2 items-center px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-sm font-medium text-text-heading">Installed Version</label>
                                <div class="text-sm text-text-muted">Your currently installed Lara CMS release.</div>
                            </div>
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-mono text-sm font-semibold text-text-heading bg-content-border/40 px-2.5 py-1 rounded-lg" x-text="'v' + currentVersion"></span>

                                {{-- Idle / up-to-date / found: show check button --}}
                                <button
                                    id="btn-check-update"
                                    type="button"
                                    @click="checkForUpdates()"
                                    :disabled="updateState === 'checking' || updateState === 'updating'"
                                    x-show="['idle', 'up_to_date', 'found'].indexOf(updateState) !== -1"
                                    class="inline-flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-lg border border-content-border bg-content-bg hover:bg-panel-bg text-text-primary transition-colors cursor-pointer disabled:opacity-50"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 shrink-0">
                                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                                    </svg>
                                    Check for Updates
                                </button>

                                {{-- Checking spinner --}}
                                <span x-show="updateState === 'checking'" style="display:none;" class="inline-flex items-center gap-1.5 text-sm text-text-muted">
                                    <svg class="animate-spin size-4 text-primary" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Checking...
                                </span>

                                {{-- Up to date badge --}}
                                <span x-show="updateState === 'up_to_date'" style="display:none;" class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="size-3 shrink-0"><path d="M5 13l4 4L19 7"/></svg>
                                    You're up to date
                                </span>

                                {{-- Done badge --}}
                                <span x-show="updateState === 'done'" style="display:none;" class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="size-3 shrink-0"><path d="M5 13l4 4L19 7"/></svg>
                                    Updated!
                                </span>
                            </div>
                        </div>

                        {{-- Update found / progress / done / error row --}}
                        <div
                            x-show="['found','updating','done','error'].indexOf(updateState) !== -1"
                            style="display:none;"
                            class="px-[18px] py-4 space-y-4"
                        >
                            {{-- Update Available Banner --}}
                            <div x-show="updateState === 'found'" style="display:none;" class="flex items-center gap-4 p-4 bg-blue-50/70 border border-blue-200 rounded-xl">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4">
                                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                                    </svg>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-blue-900">
                                        Lara CMS <span x-text="'v' + latestVersion" class="font-mono"></span> is available!
                                    </p>
                                    <p class="text-xs text-blue-700 mt-0.5">Click <strong>Update Now</strong> to download and apply it automatically.</p>
                                </div>
                                <button
                                    id="btn-run-update"
                                    type="button"
                                    @click="runUpdate()"
                                    class="shrink-0 inline-flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors cursor-pointer shadow-sm"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="size-3.5 shrink-0">
                                        <polyline points="8 17 12 21 16 17"/><line x1="12" x2="12" y1="3" y2="21"/>
                                    </svg>
                                    Update Now
                                </button>
                            </div>

                            {{-- Updating progress warning --}}
                            <div x-show="updateState === 'updating'" style="display:none;" class="flex items-center gap-3 p-3 bg-amber-50/80 border border-amber-200 rounded-xl">
                                <svg class="animate-spin size-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-amber-900">Update in progress — do not close this page.</p>
                                    <p class="text-xs text-amber-700">Downloading and installing the update package...</p>
                                </div>
                            </div>

                            {{-- Done banner --}}
                            <div x-show="updateState === 'done'" style="display:none;" class="flex items-center gap-3 p-3 bg-emerald-50/80 border border-emerald-200 rounded-xl">
                                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="size-4"><path d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-emerald-900">
                                        Update complete! Now on <span x-text="'v' + currentVersion" class="font-mono"></span>.
                                    </p>
                                    <p class="text-xs text-emerald-700">Reload the panel to apply all changes.</p>
                                </div>
                                <button type="button" @click="window.location.reload()"
                                    class="shrink-0 inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors cursor-pointer">
                                    Reload Panel
                                </button>
                            </div>

                            {{-- Error banner --}}
                            <div x-show="updateState === 'error' && updateError" style="display:none;" class="flex items-start gap-3 p-3 bg-red-50/80 border border-red-200 rounded-xl">
                                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="size-4"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-red-900">Update failed</p>
                                    <p class="text-xs text-red-700 mt-0.5 break-words" x-text="updateError"></p>
                                </div>
                                <button type="button" @click="updateState = 'idle'; updateError = null;"
                                    class="shrink-0 text-xs text-red-500 hover:text-red-700 cursor-pointer">Dismiss</button>
                            </div>

                            {{-- Live Console Log --}}
                            <div
                                x-show="updateLogs.length > 0"
                                style="display:none;"
                                class="rounded-xl border border-gray-800 bg-gray-950 overflow-hidden"
                            >
                                <div class="flex items-center justify-between px-4 py-2.5 bg-gray-900 border-b border-gray-800">
                                    <div class="flex items-center gap-1.5">
                                        <span class="size-2.5 rounded-full bg-red-500/80"></span>
                                        <span class="size-2.5 rounded-full bg-yellow-500/80"></span>
                                        <span class="size-2.5 rounded-full bg-green-500/80"></span>
                                    </div>
                                    <span class="text-gray-500 font-mono text-[11px]">Update Console</span>
                                    <span
                                        class="text-[11px] font-mono"
                                        :class="{
                                            'text-blue-400 animate-pulse': updateState === 'updating',
                                            'text-emerald-400': updateState === 'done',
                                            'text-red-400': updateState === 'error'
                                        }"
                                        x-text="updateState === 'updating' ? '● Running' : (updateState === 'done' ? '● Done' : (updateState === 'error' ? '● Failed' : ''))"
                                    ></span>
                                </div>
                                <div class="p-4 max-h-56 overflow-y-auto space-y-1.5">
                                    <template x-for="(log, i) in updateLogs" :key="i">
                                        <div
                                            class="font-mono text-xs leading-relaxed"
                                            :class="{
                                                'text-red-400': log.startsWith('[ERROR]'),
                                                'text-emerald-400': log.includes('✓') || log.toLowerCase().includes('successfully'),
                                                'text-amber-300': log.startsWith('[') && !log.startsWith('[ERROR]'),
                                                'text-gray-300': !log.startsWith('[') && !log.includes('✓') && !log.toLowerCase().includes('successfully')
                                            }"
                                            x-text="'$ ' + log"
                                        ></div>
                                    </template>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
