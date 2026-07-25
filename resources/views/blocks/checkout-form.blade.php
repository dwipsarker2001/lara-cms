@php
    $d = $data;
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    if (empty($bg) && isset($d['background']) && is_string($d['background'])) {
        try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    $bgImg = $bg['image'] ?? '';
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;
@endphp
<section data-block="checkoutForm" class="py-20 relative overflow-hidden">
    @if($bgColor)
        <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
    @endif
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
    @endif

    <div class="relative max-w-6xl mx-auto px-6">
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

                        {{-- Select Guests Section --}}
                        <div class="pt-2" x-data="{ adults: 2, children: 0 }">
                            <label class="block text-sm font-bold text-gray-900 mb-3">Select guests</label>
                            
                            <div class="space-y-3">
                                {{-- Adult Row --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-700">Adult (12 Years+)</span>
                                    <div class="flex items-center justify-between w-32 px-3 py-2 rounded-2xl border border-gray-200 bg-white shadow-2xs">
                                        <button type="button" @click="adults = Math.max(1, adults - 1)" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-gray-900 font-bold transition-colors select-none text-base">-</button>
                                        <span class="text-sm font-bold text-gray-900" x-text="adults"></span>
                                        <button type="button" @click="adults++" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-gray-900 font-bold transition-colors select-none text-base">+</button>
                                    </div>
                                </div>

                                {{-- Children Row --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-700">Children (0-12 Years)</span>
                                    <div class="flex items-center justify-between w-32 px-3 py-2 rounded-2xl border border-gray-200 bg-white shadow-2xs">
                                        <button type="button" @click="children = Math.max(0, children - 1)" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-gray-900 font-bold transition-colors select-none text-base">-</button>
                                        <span class="text-sm font-bold text-gray-900" x-text="children"></span>
                                        <button type="button" @click="children++" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-gray-900 font-bold transition-colors select-none text-base">+</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Summary Line --}}
                            <div class="mt-4 flex items-center gap-6 text-sm font-medium text-amber-600">
                                <span>Adult: <strong class="font-bold text-amber-700" x-text="adults"></strong></span>
                                <span>Children: <strong class="font-bold text-amber-700" x-text="children"></strong></span>
                            </div>
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

                    {{-- Price Breakdown Lines --}}
                    <div class="space-y-3.5 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-normal">Subtotal</span>
                            <span class="font-semibold text-gray-900" data-edit="subtotal">{{ $d['subtotal'] ?? '$99.00' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-normal">Service Fee</span>
                            <span class="font-semibold text-gray-900" data-edit="shipping">{{ $d['shipping'] ?? '$5.00' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 font-normal">Tax</span>
                            <span class="font-semibold text-gray-900" data-edit="tax">{{ $d['tax'] ?? '$8.92' }}</span>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-200 my-5"></div>

                    {{-- Total Line --}}
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-base font-bold text-gray-900">Total</span>
                        <span class="text-xl font-bold text-gray-900" data-edit="total">{{ $d['total'] ?? '$112.92' }}</span>
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
