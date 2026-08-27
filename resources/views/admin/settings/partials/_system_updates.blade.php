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
