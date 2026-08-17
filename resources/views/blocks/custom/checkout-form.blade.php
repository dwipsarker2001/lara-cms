@php
    $d = $data;
    $bg = is_array($d['configuration'] ?? null) ? $d['configuration'] : (is_array($d['background'] ?? null) ? $d['background'] : []);
    if (empty($bg) && isset($d['configuration']) && is_string($d['configuration'])) {
        try { $bg = json_decode($d['configuration'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    if (empty($bg) && isset($d['background']) && is_string($d['background'])) {
        try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    $bgImg = $bg['image'] ?? '';
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;

    // Detect selected package from URL query string (ID or Slug)
    $packageQuery = request('package_id') ?? request('package') ?? request('package_slug');
    $packageEntry = null;
    if ($packageQuery) {
        $packageEntry = \App\Models\CollectionEntry::where('id', $packageQuery)
            ->orWhere('slug', $packageQuery)
            ->first();
    }
    $hasPackage = !empty($packageEntry) || !empty($preview);

    if ($packageEntry) {
        $rawName = $packageEntry->data['title'] ?? 'Tour Package';
        $productName = is_array($rawName) ? implode(' ', $rawName) : (string) $rawName;

        $rawDesc = $packageEntry->data['aboutDescription'] ?? $d['productDesc'] ?? 'Premium travel package.';
        $productDesc = is_array($rawDesc) ? implode(' ', $rawDesc) : (string) $rawDesc;

        $rawImg = $packageEntry->data['mapImage'] ?? $d['productImage'] ?? '';
        $productImage = is_string($rawImg) ? $rawImg : '';

        $adultPrice = (float) ($d['adultPrice'] ?? 99.00);
        $rawPrice = $packageEntry->data['price'] ?? $packageEntry->data['current_price'] ?? null;
        if ($rawPrice) {
            $parsedPrice = (float) preg_replace('/[^0-9.]/', '', (string) $rawPrice);
            if ($parsedPrice > 0) {
                $adultPrice = $parsedPrice;
            }
        }
        $childPrice = (float) ($d['childPrice'] ?? ($adultPrice * 0.5));
        $extraService = (float) ($d['extraService'] ?? 0.00);
    } elseif (!empty($preview)) {
        $rawName = $d['productName'] ?? 'Tour Package';
        $productName = is_array($rawName) ? implode(' ', $rawName) : (string) $rawName;
        $rawDesc = $d['productDesc'] ?? 'Premium travel package.';
        $productDesc = is_array($rawDesc) ? implode(' ', $rawDesc) : (string) $rawDesc;
        $rawImg = $d['productImage'] ?? '';
        $productImage = is_string($rawImg) ? $rawImg : '';
        $adultPrice = (float) ($d['adultPrice'] ?? 99.00);
        $childPrice = (float) ($d['childPrice'] ?? 49.50);
        $extraService = (float) ($d['extraService'] ?? 0.00);
    } else {
        $productName = 'No Package Selected';
        $productDesc = 'Please select a travel package to view pricing and booking details.';
        $productImage = '';
        $adultPrice = 0.00;
        $childPrice = 0.00;
        $extraService = 0.00;
    }

    $currencySymbol = \App\Models\Setting::getCurrencySymbol();

    $selectedForm = null;
    if (!empty($d['formId'])) {
        $selectedForm = \App\Models\Form::find($d['formId']);
    }

    $fullNameKey = !empty($d['mapFullName']) ? $d['mapFullName'] : 'full_name';
    $emailKey = !empty($d['mapEmail']) ? $d['mapEmail'] : 'email';
    $phoneKey = !empty($d['mapPhone']) ? $d['mapPhone'] : 'phone';
    $dateKey = !empty($d['mapTravelDate']) ? $d['mapTravelDate'] : 'travel_date';
    $timeKey = !empty($d['mapPreferredTime']) ? $d['mapPreferredTime'] : 'preferred_time';
    $adultsKey = !empty($d['mapAdults']) ? $d['mapAdults'] : 'adults';
    $childrenKey = !empty($d['mapChildren']) ? $d['mapChildren'] : 'children';
    $messageKey = !empty($d['mapMessage']) ? $d['mapMessage'] : 'additional_message';
@endphp


<section data-block="checkoutForm" class="py-20 relative overflow-hidden">
    @if($bgColor)
        <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
    @endif
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
    @endif

    <div class="relative max-w-6xl mx-auto px-6" x-data="{
        adults: {{ $hasPackage ? 2 : 0 }},
        children: 0,
        adultPrice: {{ $adultPrice }},
        childPrice: {{ $childPrice }},
        extraService: {{ $extraService }},
        currency: @js($currencySymbol),
        showSuccessModal: false,
        successMsg: '',
        get adultTotal() { return this.adults * this.adultPrice; },
        get childTotal() { return this.children * this.childPrice; },
        get total() { return this.adultTotal + this.childTotal + this.extraService; },
        formatMoney(a) { return this.currency + a.toFixed(2); }
    }">
        @if(!$hasPackage)
            <div class="mb-8 rounded-2xl bg-red-50 border border-red-200 p-5 text-red-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xs">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="size-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-red-900 leading-tight">No Package Selected</h3>
                        <p class="text-xs sm:text-sm text-red-700 mt-0.5">Please select a tour package first to see pricing and confirm your booking.</p>
                    </div>
                </div>
                <a href="/packages" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold transition-colors shrink-0 shadow-xs">
                    <span>Browse Packages</span>
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- Left Column: Form Section --}}
            <div class="lg:col-span-7">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8">
                    <h2 class="text-xl font-bold text-gray-900 @if($selectedForm && !empty($selectedForm->description)) mb-1 @else mb-6 @endif" data-edit="formTitle">
                        {{ $selectedForm ? $selectedForm->title : ($d['formTitle'] ?? 'Traveler Details') }}
                    </h2>
                    @if($selectedForm && !empty($selectedForm->description))
                        <p class="text-sm text-gray-500 mb-6 font-normal">{{ $selectedForm->description }}</p>
                    @endif

                    {{-- High-Converting Success Popup Modal (AJAX & Session) --}}
                    <div x-show="showSuccessModal || {{ session('success') ? 'true' : 'false' }}" x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                        >
                            <div class="relative w-full max-w-md bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-gray-100 text-center"
                                @click.outside="showSuccessModal = false"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                            >
                                <button type="button" @click="showSuccessModal = false" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>

                                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4 shadow-inner">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>

                                <h3 class="text-xl font-bold text-gray-900 mb-2">Submission Successful!</h3>
                                <p class="text-sm text-gray-600 mb-6 leading-relaxed" x-text="successMsg || '{{ session('success') }}'"></p>

                                <button type="button" @click="showSuccessModal = false" class="w-full bg-primary hover:bg-primary-hover text-white font-semibold py-3 px-6 rounded-xl transition-colors shadow-md text-sm cursor-pointer">
                                    Done
                                </button>
                            </div>
                        </div>

                    <form action="{{ $selectedForm ? route('forms.public-submit', $selectedForm) : route('forms.public-submit-default') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ request('package_id') }}">
                        <input type="hidden" name="{{ $adultsKey }}" :value="adults" />
                        <input type="hidden" name="{{ $childrenKey }}" :value="children" />

                        {{-- Full Name --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Full Name</label>
                            <input type="text" name="{{ $fullNameKey }}" value="{{ old($fullNameKey) }}" placeholder="John Doe" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                            @error($fullNameKey)
                                <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email & Phone --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Email Address</label>
                                <input type="email" name="{{ $emailKey }}" value="{{ old($emailKey) }}" placeholder="john@example.com" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                                @error($emailKey)
                                    <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Phone Number</label>
                                <input type="tel" name="{{ $phoneKey }}" value="{{ old($phoneKey) }}" placeholder="+1 (555) 000-0000" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                                @error($phoneKey)
                                    <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Date & Time --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Travel Date</label>
                                <div class="relative group">
                                    <input type="date" name="{{ $dateKey }}" value="{{ old($dateKey) }}" class="custom-picker w-full rounded-xl border border-gray-200 pl-11 pr-4 py-3 text-sm text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer shadow-2xs hover:border-gray-300 [color-scheme:light]" />
                                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none text-gray-400 group-hover:text-primary transition-colors">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                            <line x1="16" y1="2" x2="16" y2="6"/>
                                            <line x1="8" y1="2" x2="8" y2="6"/>
                                            <line x1="3" y1="10" x2="21" y2="10"/>
                                        </svg>
                                    </div>
                                </div>
                                @error($dateKey)
                                    <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Preferred Time</label>
                                <div class="relative group">
                                    <input type="time" name="{{ $timeKey }}" value="{{ old($timeKey) }}" class="custom-picker w-full rounded-xl border border-gray-200 pl-11 pr-4 py-3 text-sm text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer shadow-2xs hover:border-gray-300 [color-scheme:light]" />
                                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none text-gray-400 group-hover:text-primary transition-colors">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                    </div>
                                </div>
                                @error($timeKey)
                                    <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Select Guests Section --}}
                        <div class="pt-2">
                            <label class="block text-sm font-bold text-gray-900 mb-3">Select guests</label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Adult Box --}}
                                <div class="flex items-center justify-between px-4 py-3 rounded-xl border border-gray-200 bg-white">
                                    <div class="text-sm font-semibold text-gray-800">
                                        Adult <span class="text-xs font-normal text-gray-400 ml-1">(12 Years+)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="adults = Math.max(1, adults - 1)" class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-700 font-bold transition-colors select-none text-sm hover:bg-primary hover:text-white hover:border-primary">-</button>
                                        <span class="w-5 text-center text-sm font-bold text-gray-900" x-text="adults"></span>
                                        <button type="button" @click="adults++" class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-700 font-bold transition-colors select-none text-sm hover:bg-primary hover:text-white hover:border-primary">+</button>
                                    </div>
                                </div>

                                {{-- Children Box --}}
                                <div class="flex items-center justify-between px-4 py-3 rounded-xl border border-gray-200 bg-white">
                                    <div class="text-sm font-semibold text-gray-800">
                                        Children <span class="text-xs font-normal text-gray-400 ml-1">(0-12 Years)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="children = Math.max(0, children - 1)" class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-700 font-bold transition-colors select-none text-sm hover:bg-primary hover:text-white hover:border-primary">-</button>
                                        <span class="w-5 text-center text-sm font-bold text-gray-900" x-text="children"></span>
                                        <button type="button" @click="children++" class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-700 font-bold transition-colors select-none text-sm hover:bg-primary hover:text-white hover:border-primary">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Message --}}
                        <div class="pt-2">
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Additional Message</label>
                            <textarea name="{{ $messageKey }}" rows="3" placeholder="Enter any special requests or additional information..." class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none">{{ old($messageKey) }}</textarea>
                            @error($messageKey)
                                <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Hidden submit button to allow Enter key form submission --}}
                        <button type="submit" class="hidden" {{ !$hasPackage ? 'disabled' : '' }}></button>
                    </form>
                </div>
            </div>

            {{-- Right Column: Order Summary --}}
            <div class="lg:col-span-5">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6" data-edit="summaryTitle">
                        {{ $d['summaryTitle'] ?? 'Order Summary' }}
                    </h2>

                    {{-- Product Thumbnail --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div data-edit="productImage" class="w-14 h-14 rounded-xl bg-gray-100 overflow-hidden shrink-0 border border-gray-100 flex items-center justify-center text-primary font-bold">
                            @if($productImage)
                                <img src="{{ $productImage }}" alt="{{ $productName }}" class="w-full h-full object-cover" />
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V11a2 2 0 00-2-2h-1c-.552 0-1-.448-1-1V6.5a2.5 2.5 0 00-2.5-2.5H12"/></svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 leading-tight" data-edit="productName">
                                {{ $productName }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1 font-normal line-clamp-2" data-edit="productDesc">
                                {{ $productDesc }}
                            </p>
                        </div>
                    </div>

                    {{-- Dynamic Price Breakdown Lines --}}
                    <div class="space-y-3.5 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-normal">Adults (<span x-text="adults"></span> × <span x-text="formatMoney(adultPrice)"></span>)</span>
                            <span class="font-semibold text-gray-900" x-text="formatMoney(adultTotal)"></span>
                        </div>

                        <template x-if="children > 0">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 font-normal">Children (<span x-text="children"></span> × <span x-text="formatMoney(childPrice)"></span>)</span>
                                <span class="font-semibold text-gray-900" x-text="formatMoney(childTotal)"></span>
                            </div>
                        </template>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-normal">Extra Service</span>
                            <span class="font-semibold text-gray-900" x-text="formatMoney(extraService)"></span>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-200 my-5"></div>

                    {{-- Dynamic Total Line --}}
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-base font-bold text-gray-900">Total</span>
                        <span class="text-xl font-bold text-gray-900" x-text="formatMoney(total)"></span>
                    </div>

                    {{-- Place Order Button —— triggers the native form submit --}}
                    @if($hasPackage)
                        <button type="button"
                            onclick="this.closest('.grid').querySelector('form').requestSubmit()"
                            data-edit="buttonText" data-edit-button
                            class="block w-full bg-primary hover:bg-primary-hover text-white font-semibold py-3.5 rounded-xl transition-colors text-center text-sm cursor-pointer shadow-sm"
                        >
                            {{ $selectedForm->submit_text ?? ($d['buttonText'] ?? 'Confirm Booking') }}
                        </button>
                    @else
                        <button type="button" disabled
                            data-edit="buttonText" data-edit-button
                            class="block w-full bg-gray-200 text-gray-400 font-semibold py-3.5 rounded-xl text-center text-sm cursor-not-allowed select-none shadow-none"
                            title="Please select a package first"
                        >
                            {{ $selectedForm->submit_text ?? ($d['buttonText'] ?? 'Confirm Booking') }}
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>
