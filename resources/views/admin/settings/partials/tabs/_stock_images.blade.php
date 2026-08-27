{{-- Stock Images & Media API Settings Tab Panel --}}
<div x-show="activeTab === 'stock_images'" x-cloak>
    <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
        <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Stock Images & Media Keys</div>
        <p class="px-[18px] pb-3 text-sm text-text-muted">Connect free stock photo APIs so your AI agent and editor can automatically find and place high-resolution, relevant photos.</p>
        <div class="px-1.5 pb-2">
            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                <div>
                    {{-- Image Provider --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-text-heading">Default Image Provider</label>
                            <div class="text-sm text-text-muted">Choose which service the AI and Media Library should use for stock images.</div>
                        </div>
                        <div>
                            <select
                                name="image_provider"
                                class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg px-3 py-2 h-9 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs"
                            >
                                <option value="auto" {{ old('image_provider', $settings->image_provider ?? 'auto') === 'auto' ? 'selected' : '' }}>Auto (Local library first, then active stock API)</option>
                                <option value="unsplash" {{ old('image_provider', $settings->image_provider ?? '') === 'unsplash' ? 'selected' : '' }}>Unsplash</option>
                                <option value="pexels" {{ old('image_provider', $settings->image_provider ?? '') === 'pexels' ? 'selected' : '' }}>Pexels (Recommended - fast & generous free tier)</option>
                                <option value="pixabay" {{ old('image_provider', $settings->image_provider ?? '') === 'pixabay' ? 'selected' : '' }}>Pixabay</option>
                                <option value="local" {{ old('image_provider', $settings->image_provider ?? '') === 'local' ? 'selected' : '' }}>Local Media Only (No external stock API)</option>
                            </select>
                            @error('image_provider') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Unsplash Access Key --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-text-heading">Unsplash Access Key</label>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">Unsplash API</span>
                            </div>
                            <div class="text-sm text-text-muted">
                                Your Unsplash Application Access Key. Get a free key at
                                <a href="https://unsplash.com/developers" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-medium">unsplash.com/developers</a>.
                            </div>
                        </div>
                        <div>
                            <div class="relative">
                                <input
                                    :type="showUnsplashKey ? 'text' : 'password'"
                                    name="unsplash_access_key"
                                    value="{{ old('unsplash_access_key', !empty($settings->unsplash_access_key) ? $settings->getMaskedUnsplashKey() : '') }}"
                                    placeholder="{{ !empty($settings->unsplash_access_key) ? $settings->getMaskedUnsplashKey() : 'Enter Unsplash Access Key' }}"
                                    autocomplete="off"
                                    class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg pl-3 pr-10 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs"
                                >
                                <button
                                    type="button"
                                    @click="showUnsplashKey = !showUnsplashKey"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer"
                                    tabindex="-1"
                                    :title="showUnsplashKey ? 'Hide key' : 'Show key'"
                                >
                                    <template x-if="!showUnsplashKey">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </template>
                                    <template x-if="showUnsplashKey">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                        </svg>
                                    </template>
                                </button>
                            </div>
                            @error('unsplash_access_key') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Pexels API Key --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5 border-b border-content-border">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-text-heading">Pexels API Key</label>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700">Free 25k/mo</span>
                            </div>
                            <div class="text-sm text-text-muted">
                                Your Pexels API Key. Get a free instant key at
                                <a href="https://www.pexels.com/api/" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-medium">pexels.com/api</a>.
                            </div>
                        </div>
                        <div>
                            <div class="relative">
                                <input
                                    :type="showPexelsKey ? 'text' : 'password'"
                                    name="pexels_api_key"
                                    value="{{ old('pexels_api_key', !empty($settings->pexels_api_key) ? $settings->getMaskedPexelsKey() : '') }}"
                                    placeholder="{{ !empty($settings->pexels_api_key) ? $settings->getMaskedPexelsKey() : 'Enter Pexels API Key' }}"
                                    autocomplete="off"
                                    class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg pl-3 pr-10 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs"
                                >
                                <button
                                    type="button"
                                    @click="showPexelsKey = !showPexelsKey"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer"
                                    tabindex="-1"
                                    :title="showPexelsKey ? 'Hide key' : 'Show key'"
                                >
                                    <template x-if="!showPexelsKey">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </template>
                                    <template x-if="showPexelsKey">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                        </svg>
                                    </template>
                                </button>
                            </div>
                            @error('pexels_api_key') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Pixabay API Key --}}
                    <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-text-heading">Pixabay API Key</label>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">Pixabay API</span>
                            </div>
                            <div class="text-sm text-text-muted">
                                Your Pixabay API Key. Get a free key at
                                <a href="https://pixabay.com/api/docs/" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-medium">pixabay.com/api/docs</a>.
                            </div>
                        </div>
                        <div>
                            <div class="relative">
                                <input
                                    :type="showPixabayKey ? 'text' : 'password'"
                                    name="pixabay_api_key"
                                    value="{{ old('pixabay_api_key', !empty($settings->pixabay_api_key) ? $settings->getMaskedPixabayKey() : '') }}"
                                    placeholder="{{ !empty($settings->pixabay_api_key) ? $settings->getMaskedPixabayKey() : 'Enter Pixabay API Key' }}"
                                    autocomplete="off"
                                    class="w-full block bg-white border border-gray-300 text-text-primary text-sm rounded-lg pl-3 pr-10 py-2 h-9 font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs"
                                >
                                <button
                                    type="button"
                                    @click="showPixabayKey = !showPixabayKey"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer"
                                    tabindex="-1"
                                    :title="showPixabayKey ? 'Hide key' : 'Show key'"
                                >
                                    <template x-if="!showPixabayKey">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </template>
                                    <template x-if="showPixabayKey">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                        </svg>
                                    </template>
                                </button>
                            </div>
                            @error('pixabay_api_key') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
