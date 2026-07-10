@php $d = $data; @endphp
<section data-block="travelDeals" class="bg-white py-20 md:py-28">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mx-auto max-w-2xl text-center">
            @if($d['heading'] ?? false)
                <h2 data-edit="heading" class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">{{ $d['heading'] }}</h2>
            @endif
            @if($d['description'] ?? false)
                <p data-edit="description" class="mt-4 text-neutral-600 leading-relaxed">{{ $d['description'] }}</p>
            @endif
            @if(($d['ctaLabel'] ?? false) && ($d['ctaLink']['url'] ?? false))
                <a href="{{ $d['ctaLink']['url'] }}" data-edit="ctaLink" class="mt-6 inline-flex items-center gap-2 rounded-full bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 transition">
                    <span data-edit="ctaLabel">{{ $d['ctaLabel'] }}</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            @endif
        </div>

        @if(($d['cards'] ?? []))
            <div data-list="cards" class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($d['cards'] as $i => $card)
                    <div class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-100 transition hover:shadow-lg">
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="{{ $card['image'] ?? '' }}" data-edit="cards:{{ $i }}/image" alt="{{ $card['title'] ?? '' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110" />
                            @if($card['badge'] ?? false)
                                <span data-edit="cards:{{ $i }}/badge" class="absolute left-3 top-3 rounded-full bg-primary px-3 py-1 text-xs font-semibold text-white shadow-sm">{{ $card['badge'] }}</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 data-edit="cards:{{ $i }}/title" class="text-lg font-semibold text-neutral-900">{{ $card['title'] ?? '' }}</h3>
                            <p data-edit="cards:{{ $i }}/description" class="mt-2 text-sm text-neutral-600 leading-relaxed">{{ $card['description'] ?? '' }}</p>
                            <div class="mt-4 flex items-baseline gap-2">
                                @if($card['salePrice'] ?? false)
                                    <span data-edit="cards:{{ $i }}/salePrice" class="text-2xl font-bold text-primary">${{ $card['salePrice'] }}</span>
                                @endif
                                @if($card['originalPrice'] ?? false)
                                    <span data-edit="cards:{{ $i }}/originalPrice" class="text-sm text-neutral-400 line-through">${{ $card['originalPrice'] }}</span>
                                @endif
                            </div>
                            @if(($card['features'] ?? []))
                                <ul data-list="cards:{{ $i }}/features" class="mt-4 space-y-2 border-t border-neutral-100 pt-4">
                                    @foreach($card['features'] as $f => $feature)
                                        <li class="group/feature relative flex items-center gap-2 text-sm text-neutral-600">
                                            <svg class="h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span data-edit="cards:{{ $i }}/features:{{ $f }}/label">{{ $feature['label'] ?? '' }}</span>
                                            @if(($feature['tooltip'] ?? false))
                                                <div class="invisible absolute -top-8 left-1/2 z-10 -translate-x-1/2 whitespace-nowrap rounded-lg bg-neutral-900 px-3 py-1.5 text-xs text-white opacity-0 shadow-sm transition group-hover/feature:visible group-hover/feature:opacity-100">
                                                    {{ $feature['tooltip'] }}
                                                    <div class="absolute left-1/2 top-full -translate-x-1/2 border-4 border-transparent border-t-neutral-900"></div>
                                                </div>
                                                <svg class="h-3.5 w-3.5 shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <a href="{{ $card['buttonUrl']['url'] ?? '#' }}" data-edit="cards:{{ $i }}/buttonUrl" class="mt-5 block w-full rounded-xl bg-primary py-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary/90 transition">
                                {{ $card['buttonLabel'] ?? 'Book Now' }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
