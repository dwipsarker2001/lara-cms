{{-- reCAPTCHA Settings Tab Panel --}}
<div x-show="activeTab === 'recaptcha'" x-cloak>
    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
        <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">reCAPTCHA v3 Protection</div>
        <p class="px-[18px] pb-3 text-sm text-text-muted">Configure Google reCAPTCHA v3 credentials to protect your admin login page from automated brute-force attacks.</p>
        <div class="px-1.5 pb-2">
            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                <div>
                    {{-- reCAPTCHA Site Key --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-text-heading">reCAPTCHA Site Key</label>
                            <div class="text-sm text-text-muted">Google reCAPTCHA v3 public site key for login protection.</div>
                        </div>
                        <div>
                            <input type="text" name="recaptcha_site_key" value="{{ old('recaptcha_site_key', $settings->recaptcha_site_key ?? '') }}"
                                placeholder="6L..."
                                class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                            @error('recaptcha_site_key') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- reCAPTCHA Secret Key --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-text-heading">reCAPTCHA Secret Key</label>
                            <div class="text-sm text-text-muted">Google reCAPTCHA v3 private secret key for server verification.</div>
                        </div>
                        <div>
                            <input type="password" name="recaptcha_secret_key" value="{{ old('recaptcha_secret_key', $settings->recaptcha_secret_key ?? '') }}"
                                placeholder="6L..."
                                class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                            @error('recaptcha_secret_key') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
