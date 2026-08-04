@extends('admin.layout')

@section('title', 'Settings')
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

                fetch('{{ route('admin.updates.check') }}?force=1', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    this.currentVersion = data.current_version;
                    this.latestVersion = data.latest_version;

                    if (data.status === 'check_failed') {
                        this.updateState = 'check_failed';
                        this.updateError = data.message || 'Unable to reach the update server.';
                    } else {
                        this.updateState = data.update_available ? 'found' : 'up_to_date';
                    }
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
                    let delay = 200;
                    data.logs.forEach(log => {
                        setTimeout(() => {
                            this.updateLogs.push(log);
                            this.$nextTick(() => {
                                if (this.$refs.logConsole) {
                                    this.$refs.logConsole.scrollTop = this.$refs.logConsole.scrollHeight;
                                }
                            });
                        }, delay += 400);
                    });
                    setTimeout(() => {
                        this.currentVersion = data.version;
                        this.updateState = 'done';
                        this.$nextTick(() => {
                            if (this.$refs.logConsole) {
                                this.$refs.logConsole.scrollTop = this.$refs.logConsole.scrollHeight;
                            }
                        });
                    }, delay + 500);
                })
                .catch(err => {
                    this.updateLogs.push('[ERROR] ' + err.message);
                    this.updateError = err.message;
                    this.updateState = 'error';
                    this.$nextTick(() => {
                        if (this.$refs.logConsole) {
                            this.$refs.logConsole.scrollTop = this.$refs.logConsole.scrollHeight;
                        }
                    });
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

                {{-- System Updates Card --}}
                <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                    <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">System Updates</div>
                    <p class="px-[18px] pb-3 text-sm text-text-muted">Check for the latest Lara CMS updates and upgrade your system in one click (no Git required).</p>
                    <div class="px-1.5 pb-2">
                        <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-4 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <div class="text-sm font-medium text-text-heading">
                                        Current Version: <span class="font-bold text-primary" x-text="`v${currentVersion}`"></span>
                                    </div>
                                    <div class="text-xs text-text-muted mt-1">
                                        <template x-if="updateState === 'idle'">
                                            <span>Click below to check for available system updates.</span>
                                        </template>
                                        <template x-if="updateState === 'checking'">
                                            <span class="text-primary font-medium flex items-center gap-1.5">
                                                <svg class="animate-spin size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                Checking update server...
                                            </span>
                                        </template>
                                        <template x-if="updateState === 'up_to_date'">
                                            <span class="text-emerald-600 font-medium">✓ Your CMS is up to date!</span>
                                        </template>
                                        <template x-if="updateState === 'found'">
                                            <span class="text-amber-600 font-semibold" x-text="`New version v${latestVersion} is available for update!`"></span>
                                        </template>
                                        <template x-if="updateState === 'updating'">
                                            <span class="text-primary font-medium flex items-center gap-1.5">
                                                <svg class="animate-spin size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                Downloading & installing update package...
                                            </span>
                                        </template>
                                        <template x-if="updateState === 'done'">
                                            <span class="text-emerald-600 font-semibold">✓ Update complete! System updated to v<span x-text="currentVersion"></span></span>
                                        </template>
                                        <template x-if="updateState === 'error'">
                                            <span class="text-red-600 font-medium" x-text="updateError || 'Update encountered an error.'"></span>
                                        </template>
                                        <template x-if="updateState === 'check_failed'">
                                            <span class="text-amber-600 font-medium">⚠ Could not verify the latest version. Please try again or check your connection.</span>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button"
                                        @click="checkForUpdates()"
                                        :disabled="updateState === 'checking' || updateState === 'updating'"
                                        class="inline-flex items-center justify-center gap-1.5 h-9 px-3 rounded-lg border border-content-border bg-white text-xs font-medium text-text-heading hover:bg-body-bg shadow-xs transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-text-muted"><path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H4.75a.75.75 0 00-.75.75v3.473a.75.75 0 001.5 0v-2.004l.527.527A7 7 0 0017 11.424a.75.75 0 00-1.688 0zM4.688 8.576a5.5 5.5 0 019.201-2.466l.312.311h-2.433a.75.75 0 000 1.5h3.475a.75.75 0 00.75-.75V3.698a.75.75 0 00-1.5 0v2.004l-.527-.527A7 7 0 003 8.576a.75.75 0 001.688 0z" clip-rule="evenodd" /></svg>
                                        <span>Check for Updates</span>
                                    </button>
                                    <button type="button"
                                        x-show="updateState === 'found'"
                                        @click="runUpdate()"
                                        :disabled="updateState === 'updating'"
                                        class="inline-flex items-center justify-center gap-1.5 h-9 px-3.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold text-white shadow-sm transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v8.69l3.22-3.22a.75.75 0 111.06 1.06l-4.5 4.5a.75.75 0 01-1.06 0l-4.5-4.5a.75.75 0 111.06-1.06l3.22 3.22V3.75A.75.75 0 0110 3z" clip-rule="evenodd" /></svg>
                                        <span>Update Now</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Terminal-style Update Console Logs --}}
                            <template x-if="updateLogs.length > 0">
                                <div x-ref="logConsole" class="mt-3 bg-gray-950 text-gray-100 rounded-xl p-3.5 font-mono text-xs space-y-1.5 max-h-60 overflow-y-auto border border-gray-800 shadow-inner">
                                    <div class="text-[11px] font-semibold text-gray-400 border-b border-gray-800 pb-1.5 flex items-center justify-between sticky top-0 bg-gray-950/95 backdrop-blur-xs z-10">
                                        <span>Update Process Log</span>
                                        <span class="size-2.5 rounded-full" :class="updateState === 'updating' ? 'bg-amber-400 animate-ping' : 'bg-emerald-400'"></span>
                                    </div>
                                    <template x-for="(log, idx) in updateLogs" :key="idx">
                                        <div class="leading-relaxed py-0.5" :class="log.includes('[ERROR]') ? 'text-red-400 font-bold' : (log.includes('✓') ? 'text-emerald-400 font-bold text-[13px] bg-emerald-950/40 p-1.5 rounded border border-emerald-500/30' : 'text-gray-200')" x-text="log"></div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection
