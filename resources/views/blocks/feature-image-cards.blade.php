@php $d = $data; @endphp
<section data-block="featureImageCards">
    <div class="max-w-6xl mx-auto px-6">
        @if($d['headline'] ?? false)
            <h2 data-edit="headline" class="text-center text-2xl md:text-3xl font-bold text-gray-900">{{ $d['headline'] }}</h2>
        @endif
        @if($d['description'] ?? false)
            <p data-edit="description" class="mx-auto mt-3 max-w-2xl text-center text-gray-500">{{ $d['description'] }}</p>
        @endif
        <div class="mt-10 flex gap-5 overflow-x-auto snap-x snap-mandatory sm:grid sm:grid-cols-2 lg:grid-cols-4 md:gap-6" style="-ms-overflow-style:none;scrollbar-width:none">
            @foreach(($d['cards'] ?? []) as $i => $card)
                @if($card)
                    <div data-list="cards" class="min-w-[260px] sm:min-w-0 snap-start rounded-2xl border-2 border-transparent">
                        <div data-edit="image" class="group relative aspect-[4/5] overflow-hidden rounded-2xl bg-gray-200">
                            @if($card['image'] ?? false)
                                <img src="{{ $card['image'] }}" alt="{{ $card['title'] ?? '' }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            @endif
                            <div class="absolute inset-x-0 bottom-0 h-3/5 bg-gradient-to-t from-black/85 via-black/50 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-5 md:p-6 text-white">
                                @if($card['title'] ?? false)
                                    <h3 data-edit="title" class="text-base md:text-lg font-bold leading-snug">{{ $card['title'] }}</h3>
                                @endif
                                @if($card['description'] ?? false)
                                    <p data-edit="description" class="mt-2 text-xs md:text-sm text-white/80 leading-relaxed">{{ $card['description'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
