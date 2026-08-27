{{-- Cloudflare R2 Object Storage Settings Tab Panel --}}
<div x-show="activeTab === 'cloudflare_r2'" x-cloak x-data="cloudflareR2Settings()">
    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
        <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Cloudflare R2 Storage Configuration</div>
        <p class="px-[18px] pb-3 text-xs text-text-muted">Configure your Cloudflare R2 S3-compatible credentials to store uploaded media files directly on Cloudflare with zero egress fees.</p>
        <div class="px-1.5 pb-2">
            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                <div>
                    {{-- Enable Cloudflare R2 Storage --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-text-heading">Enable Cloudflare R2 Storage</label>
                            <div class="text-sm text-text-muted">When enabled, newly uploaded media files in the Assets manager will be saved to your Cloudflare R2 bucket.</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="cloudflare_r2_enabled" :value="enabled ? '1' : '0'">
                                <input
                                    type="checkbox"
                                    class="sr-only peer"
                                    x-model="enabled"
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                            <span class="text-xs font-medium text-text-muted" x-text="enabled ? 'Active (Uploading to R2)' : 'Disabled (Local storage)'"></span>
                        </div>
                    </div>

                    {{-- Cloudflare Account ID --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-text-heading">Cloudflare Account ID</label>
                            <div class="text-sm text-text-muted">Your Cloudflare Account ID found in Cloudflare Dashboard &rarr; R2 &rarr; Overview.</div>
                        </div>
                        <div>
                            <input
                                type="text"
                                name="cloudflare_r2_account_id"
                                id="cloudflare_r2_account_id"
                                x-model="accountId"
                                placeholder="e.g. 7a8b9c0d1e2f3a4b5c6d7e8f9a0b1c2d"
                                class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs"
                            >
                            @error('cloudflare_r2_account_id') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- R2 Bucket Name --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-text-heading">R2 Bucket Name</label>
                            <div class="text-sm text-text-muted">The exact name of the target bucket created in Cloudflare R2.</div>
                        </div>
                        <div>
                            <input
                                type="text"
                                name="cloudflare_r2_bucket"
                                id="cloudflare_r2_bucket"
                                x-model="bucket"
                                placeholder="e.g. lara-cms-media"
                                class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs"
                            >
                            @error('cloudflare_r2_bucket') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Access Key ID --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-text-heading">Access Key ID</label>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">S3 Token</span>
                            </div>
                            <div class="text-sm text-text-muted">From Cloudflare Dashboard &rarr; R2 &rarr; Manage R2 API Tokens (Object Read &amp; Write).</div>
                        </div>
                        <div>
                            <input
                                type="text"
                                name="cloudflare_r2_access_key_id"
                                id="cloudflare_r2_access_key_id"
                                x-model="accessKeyId"
                                placeholder="{{ $settings->getMaskedR2AccessKey() ?? 'e.g. 7f8a9b0c1d2e3f4a5b6c7d8e' }}"
                                class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs"
                            >
                            @error('cloudflare_r2_access_key_id') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Secret Access Key --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-text-heading">Secret Access Key</label>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">Encrypted</span>
                            </div>
                            <div class="text-sm text-text-muted">Your Cloudflare R2 Secret Access Key. Leave blank to keep existing key.</div>
                        </div>
                        <div>
                            <div class="relative">
                                <input
                                    :type="showSecret ? 'text' : 'password'"
                                    name="cloudflare_r2_secret_access_key"
                                    id="cloudflare_r2_secret_access_key"
                                    x-model="secretAccessKey"
                                    placeholder="{{ $settings->getMaskedR2SecretKey() ? '•••••••••••••••• (Leave blank to keep unchanged)' : 'Enter Secret Access Key' }}"
                                    autocomplete="off"
                                    class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg pl-3 pr-10 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs"
                                >
                                <button
                                    type="button"
                                    @click="showSecret = !showSecret"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer"
                                    tabindex="-1"
                                    :title="showSecret ? 'Hide key' : 'Show key'"
                                >
                                    <template x-if="!showSecret">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </template>
                                    <template x-if="showSecret">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                        </svg>
                                    </template>
                                </button>
                            </div>
                            @error('cloudflare_r2_secret_access_key') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Public Domain / R2.dev URL --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-text-heading">Public Domain / R2.dev URL</label>
                            <div class="text-sm text-text-muted">Public URL for CDN delivery (e.g. <code>https://pub-xxx.r2.dev</code> or custom domain <code>https://media.yourdomain.com</code>).</div>
                        </div>
                        <div>
                            <input
                                type="url"
                                name="cloudflare_r2_public_url"
                                id="cloudflare_r2_public_url"
                                x-model="publicUrl"
                                placeholder="https://pub-xxxxxxxx.r2.dev"
                                class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs"
                            >
                            @error('cloudflare_r2_public_url') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Test Configuration & Connection Status --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-text-heading">Test Configuration</label>
                            <div class="text-sm text-text-muted">Verify that your R2 credentials and bucket connection are working properly.</div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    @click="testConnection()"
                                    :disabled="isTesting"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-text-heading text-xs font-medium transition-colors shadow-2xs cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed h-9"
                                >
                                    <svg x-show="isTesting" class="animate-spin size-3.5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg x-show="!isTesting" class="size-3.5 text-text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <span x-text="isTesting ? 'Testing Connection...' : 'Test Connection'"></span>
                                </button>

                                <a
                                    href="https://dash.cloudflare.com/?to=/:account/r2"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-text-muted hover:text-text-heading transition-colors"
                                >
                                    <span>Cloudflare R2 Console</span>
                                    <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/>
                                    </svg>
                                </a>
                            </div>

                            {{-- Inline Test Feedback --}}
                            <template x-if="testResult">
                                <div
                                    class="p-3 rounded-lg border flex items-start gap-2.5 text-xs transition-all"
                                    :class="testResult.success ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-red-50 border-red-200 text-red-900'"
                                >
                                    <div class="shrink-0 mt-0.5">
                                        <template x-if="testResult.success">
                                            <svg class="size-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                            </svg>
                                        </template>
                                        <template x-if="!testResult.success">
                                            <svg class="size-4 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                        </template>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold" x-text="testResult.success ? 'Verified' : 'Failed'"></p>
                                        <p class="mt-0.5 opacity-90 text-[11px] font-mono" x-text="testResult.message"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
