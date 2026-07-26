@php
    $d = $data;
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
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

    $rawName = $packageEntry?->data['title'] ?? $d['productName'] ?? 'Tour Package';
    $productName = is_array($rawName) ? implode(' ', $rawName) : (string) $rawName;

    $rawDesc = $packageEntry?->data['aboutDescription'] ?? $d['productDesc'] ?? 'Premium travel package.';
    $productDesc = is_array($rawDesc) ? implode(' ', $rawDesc) : (string) $rawDesc;

    $rawImg = $packageEntry?->data['mapImage'] ?? $d['productImage'] ?? '';
    $productImage = is_string($rawImg) ? $rawImg : '';

    $adultPrice = (float) ($d['adultPrice'] ?? 99.00);
    if ($packageEntry) {
        $rawPrice = $packageEntry->data['price'] ?? $packageEntry->data['current_price'] ?? null;
        if ($rawPrice) {
            $parsedPrice = (float) preg_replace('/[^0-9.]/', '', (string) $rawPrice);
            if ($parsedPrice > 0) {
                $adultPrice = $parsedPrice;
            }
        }
    }

    $childPrice = (float) ($d['childPrice'] ?? 49.50);
    $extraService = (float) ($d['extraService'] ?? 0.00);
    $currencySymbol = \App\Models\Setting::getCurrencySymbol();

    $selectedForm = null;
    if (!empty($d['formId'])) {
        $selectedForm = \App\Models\Form::find($d['formId']);
    }
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
        currency: @js($currencySymbol),
        get adultTotal() { return this.adults * this.adultPrice; },
        get childTotal() { return this.children * this.childPrice; },
        get total() { return this.adultTotal + this.childTotal + this.extraService; },
        formatMoney(a) { return this.currency + a.toFixed(2); }
    }">
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
                        <input type="hidden" name="adults" :value="adults" />
                        <input type="hidden" name="children" :value="children" />

                        @if($selectedForm && !empty($selectedForm->fields))
                            @foreach($selectedForm->fields as $field)
                                @php
                                    if (!is_array($field)) continue;
                                    $fieldKey = $field['name'] ?? ('field_'.$loop->index);
                                    $fieldType = $field['type'] ?? 'text';
                                    $fieldLabel = $field['label'] ?? '';
                                    $fieldPlaceholder = $field['placeholder'] ?? '';
                                    $fieldRequired = !empty($field['required']);
                                    $fieldOptions = is_array($field['options'] ?? null) ? $field['options'] : [];
                                @endphp

                                <div>
                                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                                        {{ $fieldLabel }}
                                        @if($fieldRequired)<span class="text-red-500">*</span>@endif
                                    </label>

                                    @if($fieldType === 'textarea')
                                        <textarea name="{{ $fieldKey }}" rows="3" placeholder="{{ $fieldPlaceholder }}" {{ $fieldRequired ? 'required' : '' }} class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none">{{ old($fieldKey) }}</textarea>
                                    @elseif($fieldType === 'select')
                                        <select name="{{ $fieldKey }}" {{ $fieldRequired ? 'required' : '' }} class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                            @if($fieldPlaceholder)
                                                <option value="">{{ $fieldPlaceholder }}</option>
                                            @endif
                                            @foreach($fieldOptions as $opt)
                                                <option value="{{ $opt }}" @selected(old($fieldKey) == $opt)>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($fieldType === 'checkbox')
                                        <div class="space-y-2 pt-1">
                                            @foreach($fieldOptions as $opt)
                                                <label class="flex items-center gap-2 text-sm text-gray-800 cursor-pointer">
                                                    <input type="checkbox" name="{{ $fieldKey }}[]" value="{{ $opt }}" @checked(in_array($opt, (array) old($fieldKey, []))) class="rounded border-gray-300 text-primary focus:ring-primary">
                                                    <span>{{ $opt }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($fieldType === 'radio')
                                        <div class="space-y-2 pt-1">
                                            @foreach($fieldOptions as $opt)
                                                <label class="flex items-center gap-2 text-sm text-gray-800 cursor-pointer">
                                                    <input type="radio" name="{{ $fieldKey }}" value="{{ $opt }}" @checked(old($fieldKey) == $opt) class="border-gray-300 text-primary focus:ring-primary">
                                                    <span>{{ $opt }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($fieldType === 'file')
                                        <input type="file" name="{{ $fieldKey }}" {{ $fieldRequired ? 'required' : '' }} class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                                    @else
                                        <input type="{{ $fieldType === 'phone' ? 'tel' : $fieldType }}" name="{{ $fieldKey }}" value="{{ old($fieldKey) }}" placeholder="{{ $fieldPlaceholder }}" {{ $fieldRequired ? 'required' : '' }} class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                                    @endif

                                    @error($fieldKey)
                                        <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        @else
                            {{-- Full Name --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Full Name</label>
                                <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="John Doe" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                                @error('full_name')
                                    <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email & Phone --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-800 mb-2">Email Address</label>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                                    @error('email')
                                        <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-800 mb-2">Phone Number</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                                    @error('phone')
                                        <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Date & Time --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-800 mb-2">Travel Date</label>
                                    <div class="relative group">
                                        <input type="date" name="travel_date" value="{{ old('travel_date') }}" class="custom-picker w-full rounded-xl border border-gray-200 pl-11 pr-4 py-3 text-sm text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer shadow-2xs hover:border-gray-300 [color-scheme:light]" />
                                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none text-gray-400 group-hover:text-primary transition-colors">
                                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="16" y1="2" x2="16" y2="6"/>
                                                <line x1="8" y1="2" x2="8" y2="6"/>
                                                <line x1="3" y1="10" x2="21" y2="10"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('travel_date')
                                        <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-800 mb-2">Preferred Time</label>
                                    <div class="relative group">
                                        <input type="time" name="preferred_time" value="{{ old('preferred_time') }}" class="custom-picker w-full rounded-xl border border-gray-200 pl-11 pr-4 py-3 text-sm text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer shadow-2xs hover:border-gray-300 [color-scheme:light]" />
                                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none text-gray-400 group-hover:text-primary transition-colors">
                                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('preferred_time')
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
                                <textarea name="additional_message" rows="3" placeholder="Enter any special requests or additional information..." class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none">{{ old('additional_message') }}</textarea>
                                @error('additional_message')
                                    <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Hidden submit button to allow Enter key form submission --}}
                        <button type="submit" class="hidden"></button>
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
                    <button type="button"
                        onclick="this.closest('.grid').querySelector('form').requestSubmit()"
                        data-edit="buttonText" data-edit-button
                        class="block w-full bg-primary hover:bg-primary-hover text-white font-semibold py-3.5 rounded-xl transition-colors text-center text-sm cursor-pointer"
                    >
                        {{ $selectedForm->submit_text ?? ($d['buttonText'] ?? 'Confirm Booking') }}
                    </button>
                </div>
            </div>

        </div>
    </div>
</section>
