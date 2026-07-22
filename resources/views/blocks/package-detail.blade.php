@php
    $s = $section ?? [];
    $d = $s['data'] ?? [];
    $type = $s['name'] ?? '';
    $key = $s['_key'] ?? '';
    $prose = 'prose prose-sm max-w-none text-gray-600 [&_a]:text-brand [&_a]:underline [&_p]:mb-3 [&_ul]:my-3 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:my-3 [&_ol]:list-decimal [&_ol]:pl-5';
@endphp

{{-- ============ HERO SLIDER ============ --}}
@if($type === 'packageHero')
@php
    $images = collect($d['images'] ?? [])->pluck('image')->filter()->values()->toArray();
    if (empty($images) && !empty($d['backgroundImage'])) $images = [$d['backgroundImage']];
    if (empty($images)) $images = [''];
    $isSlider = count($images) > 1;
@endphp
<div data-package-block="{{ $key }}">
<section class="relative w-full overflow-hidden"
    x-data="{
        activeIndex: 0,
        images: @js($images),
        isSlider: @js($isSlider),
        get activeSrc() { return this.images[this.activeIndex] },
        goPrev() { this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length },
        goNext() { this.activeIndex = (this.activeIndex + 1) % this.images.length },
        init() {
            if (!this.isSlider) return;
            this.autoplay = setInterval(() => { this.activeIndex = (this.activeIndex + 1) % this.images.length }, 5000)
        },
        destroy() { if (this.autoplay) clearInterval(this.autoplay) }
    }"
>
    <div class="relative flex min-h-[400px] items-center justify-center bg-gray-100" data-edit="images">
        <template x-for="(src, i) in images" :key="i">
            <div class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                x-bind:style="'opacity: ' + (activeSrc === src && activeIndex === i ? 1 : 0)"
                x-bind:aria-hidden="i !== activeIndex">
                <img :src="src" alt="" class="absolute inset-0 w-full h-full object-cover" />
            </div>
        </template>
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
        <template x-if="isSlider">
            <div>
                <button type="button" x-on:click="goPrev" aria-label="Previous image"
                    class="absolute left-4 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-gray-900 backdrop-blur transition-colors hover:bg-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button type="button" x-on:click="goNext" aria-label="Next image"
                    class="absolute right-4 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-gray-900 backdrop-blur transition-colors hover:bg-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                </button>
                <div class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2">
                    <template x-for="(_, i) in images" :key="i">
                        <button type="button" x-on:click="activeIndex = i"
                            x-bind:class="i === activeIndex ? 'h-2 w-6 bg-white' : 'h-2 w-2 bg-white/60 hover:bg-white/80'"
                            class="rounded-full transition-all"></button>
                    </template>
                </div>
            </div>
        </template>
        <div class="relative z-10 px-6 py-12 text-center text-white">
            @if($d['price'] ?? false)
                <p class="text-sm" data-edit="price">Starting From <span class="text-2xl font-bold">৳{{ number_format((float)$d['price']) }}</span>/per person</p>
            @endif
            @if($d['title'] ?? false)
                <h1 class="mt-2 text-3xl font-bold md:text-5xl" data-edit="title">{{ $d['title'] }}</h1>
            @endif
            @if($d['duration'] ?? false)
                <p class="mt-3 text-sm font-medium text-blue-300" data-edit="duration">{{ $d['duration'] }}</p>
            @endif
        </div>
    </div>
    @if(($d['badges'] ?? []) && count($d['badges'] ?? []) > 0)
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-6 border-b border-gray-200 px-6 py-4">
            @foreach($d['badges'] as $i => $badge)
                @if($badge && ($badge['text'] ?? false))
                    <span data-list="badges" data-edit="text" class="flex items-center gap-1.5 text-sm text-gray-600">
                        <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        {{ $badge['text'] }}
                    </span>
                @endif
            @endforeach
        </div>
    @endif
</section>
</div>

{{-- ============ GALLERY HERO ============ --}}
@elseif($type === 'packageGalleryHero')
@php
    $displayImages = collect($d['images'] ?? [])->pluck('image')->filter()->values()->toArray();
    $title = $d['title'] ?? '';
    $location = $d['location'] ?? '';
@endphp
<div data-package-block="{{ $key }}">
<section class="border-b border-gray-100 bg-white"
    x-data="{ previewIndex: null, images: @js($displayImages) }"
    x-on:keydown.escape.window="previewIndex = null">
    <div class="mx-auto max-w-7xl px-6 pb-8 pt-16">
        <nav class="mb-4 flex items-center gap-2 text-sm text-gray-500">
            <a href="/" class="hover:text-brand">Home</a>
            <span>›</span>
            <a href="/packages" class="hover:text-brand">Packages</a>
            <span>›</span>
            <span class="font-medium text-gray-900">{{ $title }}</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 md:text-3xl" data-edit="title">{{ $title }}</h1>
        <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-gray-600" data-edit="rating">
            @if(isset($d['rating']) && $d['rating'] !== '' && $d['rating'] !== null)
                <span class="flex items-center gap-1">
                    <svg class="h-4 w-4 fill-orange-400 text-orange-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <span class="font-semibold text-orange-500">{{ $d['rating'] }}</span>
                    @if(isset($d['reviewCount']) && $d['reviewCount'] !== '' && $d['reviewCount'] !== null)
                        <span>({{ $d['reviewCount'] }} reviews)</span>
                    @endif
                </span>
            @endif
            @if($location)
                <span class="flex items-center gap-1" data-edit="location">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $location }}
                </span>
            @endif
        </div>
        @if(count($displayImages) > 0)
            <div class="mt-6 grid h-[400px] grid-cols-1 gap-3 {{ count($displayImages) > 1 ? 'md:grid-cols-3' : '' }}">
                <div class="relative h-[400px] cursor-pointer overflow-hidden rounded-xl {{ count($displayImages) > 1 ? 'md:col-span-2' : 'md:col-span-full' }}"
                    x-on:click="previewIndex = 0" data-list="images" data-edit="image">
                    <img src="{{ $displayImages[0] }}" alt="{{ $title }}" class="absolute inset-0 w-full h-full object-cover" />
                </div>
                @if(count($displayImages) > 1)
                    <div class="hidden h-[400px] grid-cols-2 grid-rows-2 gap-3 md:grid">
                        @foreach(array_slice($displayImages, 1, 4) as $i => $src)
                            <div class="relative cursor-pointer overflow-hidden rounded-xl"
                                x-on:click="previewIndex = {{ $i + 1 }}"
                                data-list="images" data-edit="image">
                                <img src="{{ $src }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                                @if($i === 3 && count($displayImages) > 5)
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                                        <span class="text-sm font-semibold text-white">All photos</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
    <template x-if="previewIndex !== null && images[previewIndex] != null">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/90" x-on:click="previewIndex = null">
            <template x-if="images.length > 1">
                <div>
                    <button class="absolute left-4 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-white backdrop-blur hover:bg-white/40"
                        x-on:click.stop="previewIndex = (previewIndex - 1 + images.length) % images.length" aria-label="Previous">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <button class="absolute right-4 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-white backdrop-blur hover:bg-white/40"
                        x-on:click.stop="previewIndex = (previewIndex + 1) % images.length" aria-label="Next">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                </div>
            </template>
            <div class="relative h-[80vh] w-[90vw] max-w-5xl" x-on:click.stop>
                <img :src="images[previewIndex]" alt="" class="absolute inset-0 w-full h-full object-contain" />
            </div>
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-sm text-white" x-text="(previewIndex + 1) + ' / ' + images.length"></div>
        </div>
    </template>
</section>
</div>

{{-- ============ ABOUT ============ --}}
@elseif($type === 'packageAbout')
@php $infoItems = $d['infoItems'] ?? []; @endphp
<div data-package-block="{{ $key }}">
<section>
    <h2 class="text-2xl font-bold text-gray-900" data-edit="headline">{{ $d['headline'] ?? 'About Tour Package' }}</h2>
    @if($d['description'] ?? false)
        <div class="mt-4 {{ $prose }}" data-edit="description">{!! $d['description'] !!}</div>
    @endif
    @if(count($infoItems) > 0)
        <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-8">
            <div class="grid grid-cols-1 gap-x-8 gap-y-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($infoItems as $i => $item)
                    @if($item)
                        <div class="flex items-center gap-4" data-list="infoItems">
                            <div class="shrink-0">
                                @php
                                    $lk = strtolower($item['label'] ?? '');
                                    $ic = 'h-10 w-10 text-brand';
                                @endphp
                                @if(str_contains($lk, 'destination') || str_contains($lk, 'location'))
                                    <svg class="{{ $ic }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                @elseif(str_contains($lk, 'duration'))
                                    <svg class="{{ $ic }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                @elseif(str_contains($lk, 'category'))
                                    <svg class="{{ $ic }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><path d="M7 7h.01"/></svg>
                                @else
                                    <svg class="{{ $ic }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-400" data-edit="label">{{ $item['label'] ?? '' }}</p>
                                <p class="truncate text-base font-semibold text-gray-900" data-edit="value">{{ $item['value'] ?? '' }}</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</section>
</div>

{{-- ============ BOOKING ============ --}}
@elseif($type === 'packageBooking')
@php
    $orig = $d['originalPrice'] ?? null;
    $sale = $d['salePrice'] ?? null;
    $showOrig = $orig !== null && $sale !== null && $orig != $sale;
    $guarantees = $d['guarantees'] ?? [];
    $bookLabel = ($d['bookLabel'] ?? '') ?: 'Book Now';
    $wp = $d['whatsappPhone'] ?? '';
    $phoneDigits = preg_replace('/\D/', '', $wp);
@endphp
<div data-package-block="{{ $key }}">
<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    @if($d['discountBadge'] ?? false)
        <span data-edit="booking.discountBadge" class="mb-3 inline-block rounded-md bg-brand/10 px-3 py-1 text-xs font-semibold text-brand">{{ $d['discountBadge'] }}</span>
    @endif
    <p class="text-sm text-gray-500">Starting From</p>
    <div class="mt-1 flex flex-wrap items-baseline gap-2">
        @if($showOrig)
            <span data-edit="booking.originalPrice" class="text-sm text-gray-400 line-through">৳{{ number_format((float)$orig) }}</span>
        @endif
        <span data-edit="booking.salePrice" class="text-3xl font-bold text-gray-900">৳{{ $sale !== null ? number_format((float)$sale) : '—' }}</span>
        <span class="text-sm text-gray-500">/per person</span>
    </div>
    @if(count($guarantees) > 0)
        <div class="mt-4 space-y-2" data-edit="booking.guarantees">
            @foreach($guarantees as $g)
                @if($g && ($g['text'] ?? false))
                    <p class="flex items-center gap-2 text-sm text-gray-700">
                        <svg class="h-4 w-4 shrink-0 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        <span data-edit="text">{{ $g['text'] }}</span>
                    </p>
                @endif
            @endforeach
        </div>
    @endif
    <a href="/contact" data-edit="booking.bookLabel"
        class="mt-6 block w-full rounded-full bg-brand py-3 text-center text-sm font-semibold text-white transition-colors hover:bg-brand-hover">{{ $bookLabel }}</a>
    @if($phoneDigits)
        <a href="https://wa.me/{{ $phoneDigits }}" target="_blank" rel="noopener noreferrer"
            class="mt-3 flex w-full items-center justify-center gap-2 rounded-full bg-[#25D366] py-3 text-center text-sm font-semibold text-white transition-colors hover:bg-[#1ebe5d]">
            <svg viewBox="0 0 32 32" class="h-4 w-4" fill="currentColor" aria-hidden>
                <path d="M19.11 17.205c-.372 0-1.088 1.39-1.518 1.39a.63.63 0 0 1-.315-.1c-.802-.402-1.504-.817-2.163-1.447-.545-.516-1.146-1.29-1.46-1.963a.426.426 0 0 1-.073-.215c0-.33.99-.945.99-1.49 0-.143-.73-2.09-.832-2.335-.143-.372-.214-.487-.6-.487-.187 0-.36-.043-.53-.043-.302 0-.53.115-.746.315-.688.645-1.032 1.318-1.06 2.264v.114c-.015.99.472 1.977 1.017 2.78 1.23 1.82 2.506 3.41 4.554 4.34.616.287 2.035.888 2.722.888.817 0 2.15-.515 2.478-1.318.214-.487.214-.916.144-1.018-.087-.13-.298-.2-.628-.358-.33-.155-1.96-.97-2.27-1.084zM16.075 0h-.04C7.247 0 .07 7.18.07 16c0 3.5 1.131 6.74 3.058 9.382L1.038 31.395l6.225-1.992a15.972 15.972 0 0 0 8.812 2.605c8.788 0 15.965-7.18 15.965-16C32.04 7.182 24.863.0 16.075.0zm0 28.532c-2.692 0-5.32-.74-7.6-2.135l-5.32 1.7L5.875 23.04A12.832 12.832 0 0 1 3.247 16c.0-7.087 5.74-12.833 12.833-12.833 7.083.0 12.83 5.745 12.83 12.833 0 7.085-5.74 12.83-12.83 12.83l.001.002z"/>
            </svg>
            Chat on WhatsApp
        </a>
    @endif
    @if($d['bonusNote'] ?? false)
        <p data-edit="booking.bonusNote" class="mt-4 border-t border-gray-100 pt-4 text-center text-xs text-gray-500">⊕ {{ $d['bonusNote'] }}</p>
    @endif
</div>
</div>

{{-- ============ LOCATIONS ============ --}}
@elseif($type === 'packageLocations')
@php
    $locations = collect($d['locations'] ?? [])->filter()->values()->toArray();
    $locIsSlider = count($locations) > 3;
@endphp
<div data-package-block="{{ $key }}">
<section x-data="{ previewImage: null, scrollContainer: null }" x-init="scrollContainer = $el.querySelector('[data-scroll]')" x-on:keydown.escape.window="previewImage = null">
    <h2 class="text-2xl font-bold text-gray-900" data-edit="headline">{{ $d['headline'] ?? 'Explore Locations' }}</h2>
    @if(count($locations) === 0)
        <p class="mt-6 text-sm text-gray-400">No locations added yet.</p>
    @else
        <div class="relative mt-6">
            @if($locIsSlider)
                <button x-on:click="if(scrollContainer) scrollContainer.scrollBy({ left: -280, behavior: 'smooth' })"
                    class="absolute -left-4 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-md hover:bg-gray-50" aria-label="Previous">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
            @endif
            <div data-scroll class="{{ $locIsSlider ? 'flex gap-4 overflow-x-auto scroll-smooth pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden' : 'grid grid-cols-3 gap-4' }}">
                @foreach($locations as $loc)
                    @if($loc)
                        <div class="{{ $locIsSlider ? 'min-w-[calc(33.333%-0.67rem)] shrink-0' : '' }} text-center" data-list="locations">
                            <div class="relative h-36 cursor-pointer overflow-hidden rounded-xl" x-on:click="previewImage = '{{ $loc['image'] ?? '' }}'">
                                <img src="{{ $loc['image'] ?? '' }}" alt="{{ $loc['name'] ?? '' }}" class="absolute inset-0 w-full h-full object-cover" />
                            </div>
                            @if($loc['name'] ?? false)
                                <p class="mt-2 text-sm font-semibold text-gray-900" data-edit="name">{{ $loc['name'] }}</p>
                            @endif
                            @if($loc['duration'] ?? false)
                                <p class="text-xs text-gray-500" data-edit="duration">({{ $loc['duration'] }})</p>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
            @if($locIsSlider)
                <button x-on:click="if(scrollContainer) scrollContainer.scrollBy({ left: 280, behavior: 'smooth' })"
                    class="absolute -right-4 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-md hover:bg-gray-50" aria-label="Next">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                </button>
            @endif
        </div>
    @endif
    <template x-if="previewImage">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80" x-on:click="previewImage = null">
            <button class="absolute right-4 top-4 text-white hover:text-gray-300" x-on:click="previewImage = null" aria-label="Close">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="relative h-[80vh] w-[90vw] max-w-4xl">
                <img :src="previewImage" alt="Preview" class="absolute inset-0 w-full h-full object-contain" />
            </div>
        </div>
    </template>
</section>
</div>

{{-- ============ HIGHLIGHTS ============ --}}
@elseif($type === 'packageHighlights')
<div data-package-block="{{ $key }}">
<section>
    <h2 class="text-2xl font-bold text-gray-900" data-edit="highlights.headline">{{ $d['headline'] ?? 'Highlights of the Tour' }}</h2>
    @if(($d['items'] ?? []) && count($d['items'] ?? []) > 0)
        <div class="mt-6 space-y-4 rounded-xl border border-gray-200 p-6">
            @foreach($d['items'] as $item)
                @if($item && ($item['text'] ?? false))
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        <p class="text-sm text-gray-700" data-edit="text">{{ $item['text'] }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</section>
</div>

{{-- ============ ITINERARY ============ --}}
@elseif($type === 'packageItinerary')
@php $sections = $d['sections'] ?? []; $firstDay = true; @endphp
<div data-package-block="{{ $key }}">
<section>
    <h2 class="text-2xl font-bold text-gray-900" data-edit="headline">{{ $d['headline'] ?? 'Tour Itinerary' }}</h2>
    @foreach($sections as $si => $section)
        @if($section)
            <div class="mt-6" data-list="sections">
                <div class="mb-2 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-brand">
                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="font-bold text-gray-900" data-edit="location">{{ $section['location'] ?? '' }}</span>
                    @if($section['departure'] ?? false)
                        <span class="text-sm text-gray-500" data-edit="departure">( Departure: {{ $section['departure'] }} )</span>
                    @endif
                </div>
                <div class="ml-2.5 rounded-xl border border-l-0 border-gray-100 border-gray-200 bg-white pl-6">
                    @foreach(($section['days'] ?? []) as $di => $day)
                        @if($day)
                            @php $defaultOpen = $firstDay; $firstDay = false; @endphp
                            <div data-list="days" x-data="{ open: {{ $defaultOpen ? 'true' : 'false' }} }" class="border-b border-gray-200 last:border-0">
                                <button x-on:click.stop="open = !open" class="flex w-full items-center justify-between py-4 text-left">
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm text-brand">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        </span>
                                        <span class="text-sm font-semibold text-gray-900" data-edit="dayLabel">{{ $day['dayLabel'] ?? '' }}</span>
                                        <span class="text-sm text-gray-700" data-edit="title">{{ $day['title'] ?? '' }}</span>
                                    </div>
                                    <svg class="mr-4 h-4 w-4 shrink-0 text-gray-500 transition-transform" x-bind:class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <template x-if="open">
                                    <div class="pb-4 pl-10 {{ $prose }}" data-edit="body">{!! $day['body'] ?? '' !!}</div>
                                </template>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</section>
</div>

{{-- ============ FEATURES ============ --}}
@elseif($type === 'packageFeatures')
<div data-package-block="{{ $key }}">
<section>
    <h2 class="text-2xl font-bold text-gray-900" data-edit="features.headline">{{ $d['headline'] ?? 'Package Features List' }}</h2>
    <div class="mt-6 grid grid-cols-1 gap-8 sm:grid-cols-2">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Include Features</h3>
            <div class="mt-4 space-y-3">
                @foreach(($d['includes'] ?? []) as $item)
                    @if($item && ($item['text'] ?? false))
                        <p class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            <span data-edit="text">{{ $item['text'] }}</span>
                        </p>
                    @endif
                @endforeach
            </div>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Exclude Features</h3>
            <div class="mt-4 space-y-3">
                @foreach(($d['excludes'] ?? []) as $item)
                    @if($item && ($item['text'] ?? false))
                        <p class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                            <span data-edit="text">{{ $item['text'] }}</span>
                        </p>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
</div>

{{-- ============ MAP ============ --}}
@elseif($type === 'packageMap')
<div data-package-block="{{ $key }}">
<section>
    <h2 class="text-2xl font-bold text-gray-900" data-edit="map.headline">{{ $d['headline'] ?? 'Package Destination Map' }}</h2>
    @if($d['mapImage'] ?? false)
        <div class="relative mt-6 h-72 overflow-hidden rounded-xl border border-gray-200" data-edit="map.mapImage">
            <img src="{{ $d['mapImage'] }}" alt="Destination Map" class="absolute inset-0 w-full h-full object-cover" />
        </div>
    @endif
</section>
</div>

{{-- ============ INFO ============ --}}
@elseif($type === 'packageInfo')
<div data-package-block="{{ $key }}">
<section>
    <h2 class="text-2xl font-bold text-gray-900" data-edit="headline">{{ $d['headline'] ?? 'Additional Info' }}</h2>
    @if(($d['items'] ?? []) && count($d['items'] ?? []) > 0)
        <div class="mt-6 space-y-4" data-list="items">
            @foreach($d['items'] as $item)
                @if($item)
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold underline" data-edit="title">{{ $item['title'] ?? '' }}</span> – <span data-edit="description">{{ $item['description'] ?? '' }}</span>
                        </p>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</section>
</div>

{{-- ============ FAQ ============ --}}
@elseif($type === 'packageFaq')
<div data-package-block="{{ $key }}">
<section x-data="{ activeIndex: null }">
    <h2 class="text-2xl font-bold text-gray-900" data-edit="headline">{{ $d['headline'] ?? 'Frequently Asked & Question' }}</h2>
    <div class="mt-6 space-y-3">
        @foreach(($d['items'] ?? []) as $i => $item)
            @if($item)
                <div class="overflow-hidden rounded-lg border border-gray-200" data-list="items">
                    <button x-on:click="activeIndex = activeIndex === {{ $i }} ? null : {{ $i }}"
                        class="flex w-full items-center justify-between px-5 py-4 text-left hover:bg-gray-50">
                        <span class="text-sm font-medium text-gray-900" data-edit="question">{{ $item['question'] ?? '' }}</span>
                        <svg class="h-4 w-4 text-gray-500 transition-transform" x-bind:class="activeIndex === {{ $i }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <template x-if="activeIndex === {{ $i }}">
                        <div class="px-5 pb-4 {{ $prose }}" data-edit="answer">{!! $item['answer'] ?? '' !!}</div>
                    </template>
                </div>
            @endif
        @endforeach
    </div>
</section>
</div>

{{-- ============ DETAILS ============ --}}
@elseif($type === 'packageDetails')
<div data-package-block="{{ $key }}">
<section>
    @if(($d['items'] ?? []) && count($d['items'] ?? []) > 0)
        <div class="space-y-8" data-list="items">
            @foreach($d['items'] as $item)
                @if($item)
                    <div class="rounded-xl border border-gray-200 bg-white p-6">
                        @if($item['title'] ?? false)
                            <h3 class="text-lg font-bold text-gray-900" data-edit="title">{{ $item['title'] }}</h3>
                        @endif
                        @if($item['description'] ?? false)
                            <p class="mt-2 text-sm text-gray-600" data-edit="description">{{ $item['description'] }}</p>
                        @endif
                        @if($item['mapImage'] ?? false)
                            <div class="relative mt-4 h-48 overflow-hidden rounded-lg border border-gray-100" data-edit="mapImage">
                                <img src="{{ $item['mapImage'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</section>
</div>
@endif
