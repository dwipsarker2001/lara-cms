@php
    $d = $data;
@endphp


<section data-block="travelDetails" class="py-12">
    <div class="max-w-6xl mx-auto px-6">

        @if(session('booking_success'))
            <div class="mb-6 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-4 shadow-sm">
                <svg class="w-6 h-6 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <div>
                    <p class="font-semibold text-sm">Booking Confirmed!</p>
                    <p class="text-sm text-emerald-700 mt-0.5">{{ session('booking_success') }}</p>
                </div>
            </div>
        @endif

        {{-- ── Section 1: Hero / Gallery ── --}}
        <div class="mb-8">
            @if($d['title'] ?? false)
            <div class="text-sm text-gray-500 mb-4">
                <a href="/" class="hover:text-primary">Home</a> &gt; {{ $d['title'] }}
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4" data-edit="title">{{ $d['title'] }}</h1>
            @endif

            <div class="flex items-center gap-4 text-sm text-gray-600 mb-6">
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @if($d['rating'] ?? false)<span class="font-bold text-gray-900" data-edit="rating">{{ $d['rating'] }}</span>@endif
                    @if($d['reviewCount'] ?? false)<span data-edit="reviewCount">({{ $d['reviewCount'] }})</span>@endif
                </div>
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    @if($d['location'] ?? false)<span data-edit="location">{{ $d['location'] }}</span>@endif
                </div>
            </div>

            @php
                $gallery = array_values(array_filter($d['galleryImages'] ?? []));
                $total = count($gallery);
                $allSrcs = collect($gallery)->pluck('image')->filter()->values()->all();
                $maxGrid = 5;
                $hiddenCount = max(0, $total - $maxGrid);
            @endphp

            <div x-data="{ lightbox: false, current: 0, images: @js($allSrcs) }">
                {{-- Gallery Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-4 md:grid-rows-2 gap-3 h-[350px]">
                    @foreach($gallery as $i => $g)
                        @if($g)
                        @php
                            $cls = match(true) {
                                $i === 0 && $total === 1 => 'col-span-2 md:col-span-4 row-span-2',
                                $i === 0 => 'col-span-2 row-span-2',
                                $total === 2 && $i === 1 => 'col-span-2 row-span-2',
                                $total === 3 && $i >= 1 => 'md:col-span-2',
                                $total === 4 && $i === 3 => 'col-span-2',
                                $i >= $maxGrid => 'hidden',
                                default => '',
                            };
                        @endphp
                        <div data-list="galleryImages"
                             class="rounded-xl overflow-hidden bg-gray-100 cursor-pointer relative {{ $cls }}"
                             @if(empty($preview)) @click="current = {{ $i }}; lightbox = true" @endif>
                            <div data-edit="image" class="w-full h-full">
                                @if($g['image'] ?? false)
                                <img src="{{ $g['image'] }}" alt="{{ $d['title'] ?? 'Gallery' }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            @if($i === $maxGrid - 1 && $hiddenCount > 0)
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                <span class="text-white text-xl font-bold">+{{ $hiddenCount }} more</span>
                            </div>
                            @endif
                        </div>
                        @endif
                    @endforeach
                </div>

                {{-- Lightbox Overlay --}}
                @if(empty($preview))
                <template x-teleport="body">
                    <div x-show="lightbox" x-transition.opacity
                         class="fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center p-4"
                         @keydown.escape.window="lightbox = false" @click.self="lightbox = false"
                         style="display: none;">
                        <button @click="lightbox = false"
                                class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 text-white text-2xl transition-colors z-10">&times;</button>
                        <button @click="current = (current - 1 + images.length) % images.length"
                                class="absolute left-4 w-12 h-12 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 text-white text-3xl transition-colors z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <img :src="images[current]" class="max-w-[90vw] max-h-[85vh] object-contain rounded-lg shadow-2xl" alt="">
                        <button @click="current = (current + 1) % images.length"
                                class="absolute right-4 w-12 h-12 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 text-white text-3xl transition-colors z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white text-sm bg-black/60 px-4 py-1.5 rounded-full" x-text="(current + 1) + ' / ' + images.length"></div>
                    </div>
                </template>
                @endif
            </div>
        </div>

        {{-- ── Section 2: Two-column layout ── --}}
        <div class="flex flex-col lg:flex-row gap-8 mt-10">

            {{-- Left Column --}}
            <div class="lg:w-3/5">

                {{-- 2a. About Tour Package --}}
                <div>
                    @if($d['aboutTitle'] ?? false)
                    <h2 class="text-xl font-bold text-gray-900" data-edit="aboutTitle">{{ $d['aboutTitle'] }}</h2>
                    @endif
                    @if($d['aboutDescription'] ?? false)
                    <div class="text-sm text-gray-900 mt-3 leading-relaxed" data-edit="aboutDescription">{!! $d['aboutDescription'] !!}</div>
                    @endif
                </div>

                {{-- 2b. Quick Info Grid --}}
                @if(!empty($d['quickInfo']))
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-6">
                    @foreach($d['quickInfo'] as $info)
                        @if($info)
                        <div class="bg-gray-50 rounded-lg p-3 flex gap-3 items-center" data-list="quickInfo">
                            <div class="text-gray-500 text-lg">
                                @if($info['icon'] ?? false)
                                <i class="{{ $info['icon'] }}" data-edit="icon"></i>
                                @endif
                            </div>
                            <div>
                                @if($info['label'] ?? false)
                                <div class="text-xs text-gray-400" data-edit="label">{{ $info['label'] }}</div>
                                @endif
                                @if($info['value'] ?? false)
                                <div class="text-sm font-semibold text-gray-800" data-edit="value">{{ $info['value'] }}</div>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif

                {{-- 2c. Explore Locations --}}
                <div class="mt-10">
                    @if($d['locationsTitle'] ?? false)
                    <h2 class="text-xl font-bold text-gray-900" data-edit="locationsTitle">{{ $d['locationsTitle'] }}</h2>
                    @endif
                    @if(!empty($d['locations']))
                    <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory px-1 py-1 pb-2 mt-4 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        @foreach($d['locations'] as $loc)
                            @if($loc)
                            <div class="min-w-[200px] sm:min-w-[220px] max-w-[240px] shrink-0 snap-start flex flex-col gap-2" data-list="locations">
                                <div class="rounded-xl h-40 overflow-hidden w-full bg-gray-100" data-edit="image">
                                    <img src="{{ $loc['image'] ?? '' }}" alt="{{ $loc['name'] ?? 'Location' }}" class="w-full h-full object-cover {{ empty($loc['image']) ? 'hidden' : '' }}">
                                </div>
                                @if($loc['name'] ?? false)
                                <h3 class="text-sm font-semibold text-gray-800" data-edit="name">{{ $loc['name'] }}</h3>
                                @endif
                                @if($loc['duration'] ?? false)
                                <p class="text-xs text-gray-500" data-edit="duration">{{ $loc['duration'] }}</p>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- 2d. Highlights of the Tour --}}
                <div class="mt-10">
                    @if($d['highlightsTitle'] ?? false)
                    <h2 class="text-xl font-bold text-gray-900" data-edit="highlightsTitle">{{ $d['highlightsTitle'] }}</h2>
                    @endif
                    @if(!empty($d['highlights']))
                    <div class="border border-gray-200 rounded-xl p-5 mt-4">
                        <ul class="space-y-3">
                            @foreach($d['highlights'] as $highlight)
                                @if($highlight)
                                <li class="flex items-start gap-3" data-list="highlights">
                                    <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @if($highlight['text'] ?? false)
                                    <span class="text-sm text-gray-700" data-edit="text">{{ $highlight['text'] }}</span>
                                    @endif
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                {{-- 2e. Tour Itinerary --}}
                <div class="mt-10">
                    @if($d['itineraryTitle'] ?? false)
                    <h2 class="text-xl font-bold text-gray-900" data-edit="itineraryTitle">{{ $d['itineraryTitle'] }}</h2>
                    @endif
                    @if(!empty($d['itinerary']))
                    <div class="space-y-3 mt-4">
                        @foreach($d['itinerary'] as $item)
                            @if($item)
                            @php
                                $hasStopName = !empty($item['stopName']);
                                $itinKey = 'itin-' . $loop->index;
                            @endphp

                            {{-- Stop Header (shown when stopName or departure is present) --}}
                            @if($hasStopName || ($item['departure'] ?? false))
                            <div class="flex items-center gap-2 mt-6 mb-2 pt-2" data-list="itinerary" data-list-index="{{ $loop->index }}">
                                <div class="w-5 h-5 rounded-full bg-[#00a651] text-white flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                @if($item['stopName'] ?? false)
                                <span class="font-bold text-gray-900 text-sm md:text-base" data-edit="stopName">{{ $item['stopName'] }}</span>
                                @endif
                                @if($item['departure'] ?? false)
                                <span class="text-xs md:text-sm text-gray-400 font-normal ml-0.5" data-edit="departure">( {{ $item['departure'] }} )</span>
                                @endif
                            </div>
                            @endif

                            {{-- Day Card Item --}}
                            @if(!empty($preview))
                            <div class="border border-gray-200 rounded-xl bg-white" data-list="itinerary" data-list-index="{{ $loop->index }}"
                                x-data="{ open: false }"
                                x-init="
                                    open = (window.__cms_acc = window.__cms_acc || {})['{{ $itinKey }}'] ?? false;
                                    $watch('open', v => { (window.__cms_acc = window.__cms_acc || {})['{{ $itinKey }}'] = v; });
                                ">
                            @else
                            <div class="border border-gray-200 rounded-xl bg-white" data-list="itinerary" x-data="{ open: false }">
                            @endif
                                <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left focus:outline-none group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-full border border-[#00a651] text-[#00a651] flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($item['dayLabel'] ?? false)
                                            <span class="font-bold text-gray-900 text-sm shrink-0" data-edit="dayLabel">{{ $item['dayLabel'] }}</span>
                                            @endif
                                            @if($item['dayTitle'] ?? false)
                                            <span class="text-gray-900 text-sm font-medium" data-edit="dayTitle">{{ $item['dayTitle'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0 ml-2" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition class="px-4 pb-4 text-sm text-gray-900">
                                    @if($item['dayDescription'] ?? false)
                                    <div class="pt-3 border-t border-gray-100" data-edit="dayDescription">{!! $item['dayDescription'] !!}</div>
                                    @else
                                    <div class="pt-3 border-t border-gray-100 text-gray-400 italic" data-edit="dayDescription">Add day details / description...</div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- 2f. Package Destination Map --}}
                <div class="mt-10">
                    @if($d['mapTitle'] ?? false)
                    <h2 class="text-xl font-bold text-gray-900" data-edit="mapTitle">{{ $d['mapTitle'] }}</h2>
                    @endif
                    <div class="relative w-full h-64 sm:h-72 bg-gray-100 rounded-xl overflow-hidden mt-4 flex items-center justify-center" data-edit="mapImage">
                        <img src="{{ $d['mapImage'] ?? '' }}" alt="Map" class="absolute inset-0 w-full h-full object-cover {{ empty($d['mapImage']) ? 'hidden' : '' }}">
                        <span class="text-gray-400 {{ !empty($d['mapImage']) ? 'hidden' : '' }}">Map Image</span>
                    </div>
                </div>

                {{-- 2g. Package Features List --}}
                <div class="mt-10">
                    @if($d['featuresTitle'] ?? false)
                    <h2 class="text-xl font-bold text-gray-900" data-edit="featuresTitle">{{ $d['featuresTitle'] }}</h2>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        {{-- Include Features --}}
                        <div>
                            @if($d['includeTitle'] ?? false)
                            <h3 class="font-semibold text-sm text-gray-900 mb-3" data-edit="includeTitle">{{ $d['includeTitle'] }}</h3>
                            @endif
                            @if(!empty($d['includeFeatures']))
                            <ul class="space-y-3">
                                @foreach($d['includeFeatures'] as $inc)
                                    @if($inc)
                                    <li class="flex items-start gap-2" data-list="includeFeatures">
                                        <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        @if($inc['text'] ?? false)
                                        <span class="text-sm text-gray-700" data-edit="text">{{ $inc['text'] }}</span>
                                        @endif
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        {{-- Exclude Features --}}
                        <div>
                            @if($d['excludeTitle'] ?? false)
                            <h3 class="font-semibold text-sm text-gray-900 mb-3" data-edit="excludeTitle">{{ $d['excludeTitle'] }}</h3>
                            @endif
                            @if(!empty($d['excludeFeatures']))
                            <ul class="space-y-3">
                                @foreach($d['excludeFeatures'] as $exc)
                                    @if($exc)
                                    <li class="flex items-start gap-2" data-list="excludeFeatures">
                                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        @if($exc['text'] ?? false)
                                        <span class="text-sm text-gray-700" data-edit="text">{{ $exc['text'] }}</span>
                                        @endif
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 2h. Additional Info --}}
                <div class="mt-10">
                    @if($d['infoTitle'] ?? false)
                    <h2 class="text-xl font-bold text-gray-900" data-edit="infoTitle">{{ $d['infoTitle'] }}</h2>
                    @endif
                    @if(!empty($d['additionalInfo']))
                    <div class="mt-4 space-y-4">
                        @foreach($d['additionalInfo'] as $info)
                            @if($info)
                            <div class="flex items-start gap-3" data-list="additionalInfo">
                                <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    @if($info['title'] ?? false)
                                    <span class="text-sm font-medium text-gray-900 underline" data-edit="title">{{ $info['title'] }}</span>
                                    @endif
                                    @if($info['description'] ?? false)
                                    <span class="text-sm text-gray-600" data-edit="description"> – {{ $info['description'] }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- 2i. Frequently Asked Questions --}}
                <div class="mt-10">
                    @if($d['faqTitle'] ?? false)
                    <h2 class="text-xl font-bold text-gray-900" data-edit="faqTitle">{{ $d['faqTitle'] }}</h2>
                    @endif
                    @if(!empty($d['faqs']))
                    <div class="space-y-3 mt-4">
                        @foreach($d['faqs'] as $faq)
                            @if($faq)
                            @php $faqKey = 'faq-' . $loop->index; @endphp
                            @if(!empty($preview))
                            <div class="border border-gray-200 rounded-lg" data-list="faqs" data-list-index="{{ $loop->index }}"
                                x-data="{ open: false }"
                                x-init="
                                    open = (window.__cms_acc = window.__cms_acc || {})['{{ $faqKey }}'] ?? false;
                                    $watch('open', v => { (window.__cms_acc = window.__cms_acc || {})['{{ $faqKey }}'] = v; });
                                ">
                            @else
                            <div class="border border-gray-200 rounded-lg" data-list="faqs" x-data="{ open: false }">
                            @endif
                                <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left focus:outline-none">
                                    @if($faq['question'] ?? false)
                                    <span class="text-sm font-medium text-gray-900" data-edit="question">{{ $faq['question'] }}</span>
                                    @endif
                                    <svg class="w-4 h-4 text-gray-500 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-transition class="px-4 pb-4 text-sm text-gray-900">
                                    @if($faq['answer'] ?? false)
                                    <div data-edit="answer">{!! $faq['answer'] !!}</div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>

            </div>

            {{-- Right Column (Booking Sidebar) --}}
            <div class="lg:w-2/5">
                <div class="lg:sticky lg:top-24">
                    <div class="bg-white rounded-2xl border border-gray-200 p-5">

                        {{-- Discount Badge --}}
                        @if(($d['originalPrice'] ?? false) && ($d['price'] ?? false))
                        @php
                            $orig = (int) preg_replace('/\D/', '', $d['originalPrice']);
                            $curr = (int) preg_replace('/\D/', '', $d['price']);
                            $discountPct = $orig > 0 ? round((($orig - $curr) / $orig) * 100) : 0;
                        @endphp
                            @if($discountPct > 0)
                            <span class="inline-block text-xs font-semibold text-primary bg-primary/10 px-3 py-1 rounded-full mb-3">{{ $discountPct }}% Off</span>
                            @endif
                        @endif

                        {{-- Price Label --}}
                        @if($d['priceLabel'] ?? false)
                        <div class="text-sm text-gray-500" data-edit="priceLabel">{{ $d['priceLabel'] }}</div>
                        @endif

                        {{-- Price Row --}}
                        <div class="flex items-baseline gap-2 mt-1 mb-5">
                            @if($d['originalPrice'] ?? false)
                            <span class="text-sm text-gray-400 line-through" data-edit="originalPrice">{{ $d['originalPrice'] }}</span>
                            @endif
                            @if($d['price'] ?? false)
                            <span class="text-2xl font-bold text-gray-900" data-edit="price">{{ $d['price'] }}</span>
                            @endif
                            @if($d['priceSuffix'] ?? false)
                            <span class="text-sm text-gray-400" data-edit="priceSuffix">/{{ $d['priceSuffix'] }}</span>
                            @endif
                        </div>

                        {{-- Features --}}
                        @if(!empty($d['priceFeatures']))
                        <ul class="space-y-2.5 mb-5">
                            @foreach($d['priceFeatures'] as $pf)
                                @if($pf)
                                <li class="flex items-center gap-2" data-list="priceFeatures">
                                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @if($pf['text'] ?? false)
                                    <span class="text-sm text-gray-600" data-edit="text">{{ $pf['text'] }}</span>
                                    @endif
                                </li>
                                @endif
                            @endforeach
                        </ul>
                        @endif

                        {{-- Book Now --}}
                        @if($d['bookNowLabel'] ?? false)
                        @php
                            $bookLink = $d['bookNowLink'] ?? '#';
                            $currentPackage = $entry ?? $page ?? null;
                            $packageId = isset($currentPackage->id) ? $currentPackage->id : null;
                            if (($bookLink === '#' || empty($bookLink) || $bookLink === '/checkout') && $packageId) {
                                $bookLink = url('/checkout') . '?package_id=' . $packageId;
                            }
                        @endphp
                        <a href="{{ $bookLink }}" class="block w-full bg-primary hover:bg-primary-hover text-white font-semibold py-3 rounded-full transition-colors text-center text-sm" data-edit="bookNowLabel" data-edit-button>{{ $d['bookNowLabel'] }}</a>
                        @endif

                        {{-- WhatsApp --}}
                        @if($d['whatsappLabel'] ?? false)
                        <a href="{{ $d['whatsappLink'] ?? '#' }}" class="mt-2.5 w-full bg-[#25D366] hover:bg-[#1ebe5c] text-white font-semibold py-3 rounded-full transition-colors flex items-center justify-center gap-2 text-sm" data-edit="whatsappLabel" data-edit-button>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            {{ $d['whatsappLabel'] }}
                        </a>
                        @endif

                        {{-- Booking Note --}}
                        @if($d['bookingNote'] ?? false)
                        <div class="text-xs text-gray-400 mt-4 text-center" data-edit="bookingNote">{{ $d['bookingNote'] }}</div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
