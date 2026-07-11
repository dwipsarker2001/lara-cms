@php $d = $data; @endphp
@php $btn = $d['button'] ?? []; @endphp
<section data-block="travelDeals">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4">
            <div>
                @if($d['headline'] ?? false)
                    <h2 data-edit="headline" class="text-2xl md:text-3xl font-bold text-gray-900">{{ $d['headline'] }}</h2>
                @endif
                @if($d['description'] ?? false)
                    <p data-edit="description" class="mt-3 max-w-lg text-gray-500 text-sm md:text-base leading-relaxed">{{ $d['description'] }}</p>
                @endif
            </div>
            @if($btn['label'] ?? false)
                <a href="{{ $btn['link'] ?? '#' }}" data-edit="label" class="inline-flex shrink-0 items-center gap-2 rounded-full border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-800 transition-all hover:border-brand hover:bg-brand hover:text-brand-foreground hover:shadow-md">
                    {{ $btn['label'] }}
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endif
        </div>
        <div class="mt-10 flex gap-5 overflow-x-auto snap-x snap-mandatory sm:grid sm:grid-cols-2 xl:grid-cols-4 md:gap-6" style="-ms-overflow-style:none;scrollbar-width:none">
            @foreach(($d['cards'] ?? []) as $i => $deal)
                @if($deal)
                    <div data-list="cards" class="group block min-w-[260px] sm:min-w-0 snap-start rounded-2xl bg-gray-100/80 overflow-hidden transition-shadow hover:shadow-sm">
                        <div class="relative aspect-[4/5] overflow-hidden bg-gray-200">
                            @if($deal['image'] ?? false)
                                <img src="{{ $deal['image'] }}" alt="" data-edit="image" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            @endif
                            @if($deal['badge'] ?? false)
                                <span data-edit="badge" class="absolute top-3 left-3 rounded-full bg-brand px-3 py-1 text-xs font-semibold text-brand-foreground shadow-sm">{{ $deal['badge'] }}</span>
                            @endif
                        </div>
                        <div class="p-4 md:p-5">
                            @if($deal['title'] ?? false)
                                <h3 data-edit="title" class="text-base font-bold text-gray-900 leading-snug group-hover:text-brand transition-colors">{{ $deal['title'] }}</h3>
                            @endif
                            @if($deal['description'] ?? false)
                                <p data-edit="description" class="mt-2 text-sm text-gray-500 leading-relaxed line-clamp-2">{{ $deal['description'] }}</p>
                            @endif
                            <div class="mt-3 flex items-center gap-2">
                                @if($deal['price'] ?? false)
                                    <span data-edit="price" class="text-lg font-extrabold text-gray-900">${{ number_format($deal['price']) }}</span>
                                @endif
                                @if($deal['originalPrice'] ?? false)
                                    <span data-edit="originalPrice" class="text-sm text-gray-400 line-through">${{ number_format($deal['originalPrice']) }}</span>
                                @endif
                            </div>
                            @if($deal['priceLabel'] ?? false)
                                <p data-edit="priceLabel" class="mt-1 text-xs text-gray-400">{{ $deal['priceLabel'] }}</p>
                            @endif
                            @if($deal['buttonLabel'] ?? false)
                                <a href="{{ $d['button']['link'] ?? '#' }}" data-edit="buttonLabel" class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-brand py-2.5 text-sm font-semibold text-brand-foreground transition-all hover:bg-brand/90">{{ $deal['buttonLabel'] }}</a>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
