@php
    $d = $data;
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    if (empty($bg) && isset($d['background']) && is_string($d['background'])) {
        try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    $bgImg = $bg['image'] ?? '';
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;

    $adultPrice = (float) ($d['adultPrice'] ?? 99.00);
    $childPrice = (float) ($d['childPrice'] ?? 49.50);
    $extraService = (float) ($d['extraService'] ?? 0.00);
    $currencySymbol = \App\Models\Setting::getCurrencySymbol();
@endphp
<section data-block="checkoutForm" class="py-20 relative overflow-hidden">
    @if($bgColor)
        <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
    @endif
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
    @endif

    <div class="relative max-w-6xl mx-auto px-6" x-data="{
        adults: 2,
        children: 0,
        adultPrice: {{ $adultPrice }},
        childPrice: {{ $childPrice }},
        extraService: {{ $extraService }},
        currency: '{{ $currencySymbol }}',
        get adultTotal() { return this.adults * this.adultPrice; },
        get childTotal() { return this.children * this.childPrice; },
        get total() { return this.adultTotal + this.childTotal + this.extraService; },
        formatMoney(amount) {
            return this.currency + amount.toFixed(2);
        }
    }">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- Left Column: Traveler Details Form --}}
            <div class="lg:col-span-7">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6" data-edit="formTitle">
                        {{ $d['formTitle'] ?? 'Traveler Details' }}
                    </h2>

                    <form onsubmit="event.preventDefault()" class="space-y-4">
                        {{-- Full Name --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Full Name</label>
                            <input type="text" placeholder="John Doe" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                        </div>

                        {{-- Email & Phone --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Email Address</label>
                                <input type="email" placeholder="john@example.com" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Phone Number</label>
                                <input type="tel" placeholder="+1 (555) 000-0000" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                            </div>
                        </div>

                        {{-- Date & Time --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Travel Date</label>
                                <div class="relative group">
                                    <input type="date" class="custom-picker w-full rounded-xl border border-gray-200 pl-11 pr-4 py-3 text-sm text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer shadow-2xs hover:border-gray-300 [color-scheme:light]" />
                                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none text-gray-400 group-hover:text-primary transition-colors">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                            <line x1="16" y1="2" x2="16" y2="6"/>
                                            <line x1="8" y1="2" x2="8" y2="6"/>
                                            <line x1="3" y1="10" x2="21" y2="10"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Preferred Time</label>
                                <div class="relative group">
                                    <input type="time" class="custom-picker w-full rounded-xl border border-gray-200 pl-11 pr-4 py-3 text-sm text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer shadow-2xs hover:border-gray-300 [color-scheme:light]" />
                                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none text-gray-400 group-hover:text-primary transition-colors">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                    </div>
                                </div>
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
                            <textarea rows="3" placeholder="Enter any special requests or additional information..." class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none"></textarea>
                        </div>
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
                        <div data-edit="productImage" class="w-14 h-14 rounded-xl bg-gray-100 overflow-hidden shrink-0 border border-gray-100">
                            @if($d['productImage'] ?? false)
                                <img src="{{ $d['productImage'] }}" alt="{{ $d['productName'] ?? 'Product' }}" class="w-full h-full object-cover" />
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 leading-tight" data-edit="productName">
                                {{ $d['productName'] ?? 'Gourmet Coffee Beans' }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1 font-normal" data-edit="productDesc">
                                {{ $d['productDesc'] ?? 'Premium quality, ethically sourced.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Dynamic Price Breakdown Lines with Site Currency Symbol --}}
                    <div class="space-y-3.5 text-sm">
                        {{-- Adults Breakdown --}}
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-normal">Adults (<span x-text="adults"></span> × <span x-text="formatMoney(adultPrice)"></span>)</span>
                            <span class="font-semibold text-gray-900" x-text="formatMoney(adultTotal)"></span>
                        </div>

                        {{-- Children Breakdown --}}
                        <template x-if="children > 0">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 font-normal">Children (<span x-text="children"></span> × <span x-text="formatMoney(childPrice)"></span>)</span>
                                <span class="font-semibold text-gray-900" x-text="formatMoney(childTotal)"></span>
                            </div>
                        </template>

                        {{-- Extra Service --}}
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

                    {{-- Place Order Action Button --}}
                    <button type="button" data-edit="buttonText" data-edit-button class="block w-full bg-primary hover:bg-primary-hover text-white font-semibold py-3.5 rounded-xl transition-colors text-center text-sm cursor-pointer">
                        {{ $d['buttonText'] ?? 'Confirm Booking' }}
                    </button>
                </div>
            </div>

        </div>
    </div>
</section>
