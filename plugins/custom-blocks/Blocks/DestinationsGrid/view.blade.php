@php
    $d = $data;
    $places = is_array($d['places'] ?? null) ? array_values($d['places']) : [];
    $spans = ['sm:col-span-1', 'sm:col-span-2', 'sm:col-span-1'];
@endphp
<section data-block="destinationsGrid" class="py-20">
    <div class="max-w-6xl mx-auto px-6">
        @if($d['headline'] ?? false)
            <h2 data-edit="headline" class="text-center text-3xl md:text-4xl font-bold text-gray-900">{{ $d['headline'] }}</h2>
        @endif
        @if($d['description'] ?? false)
            <p data-edit="description" class="mx-auto mt-3 max-w-xl text-center text-gray-500">{{ $d['description'] }}</p>
        @endif

        @if(empty($places))
            <p class="mt-10 text-center text-gray-500">No destinations selected.</p>
        @else
            <div class="mt-10 space-y-6">
                @if(count($places) >= 3)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
                        @foreach(array_slice($places, 0, 3) as $i => $place)
                            @php
                                $placeUrl = $place['link'] ?? ($place['_term_link'] ?? ($place['url'] ?? ($place['slug'] ?? '#')));
                                if ($placeUrl && $placeUrl !== '#' && !str_starts_with($placeUrl, '/') && !str_starts_with($placeUrl, 'http') && !str_starts_with($placeUrl, '?')) {
                                    $placeUrl = '/destinations/' . $placeUrl;
                                }
                            @endphp
                            <div class="{{ $spans[$i] ?? 'sm:col-span-1' }}">
                                <a href="{{ $placeUrl }}" data-list="places" data-list-index="{{ $i }}" class="group relative block h-[280px] overflow-hidden rounded-2xl bg-gray-100">
                                    <img src="{{ $place['image'] ?? '' }}" alt="{{ $place['name'] ?? '' }}" data-edit="image" class="absolute inset-0 w-full h-full object-cover {{ empty($place['image']) ? 'hidden' : '' }}" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                    <div class="absolute bottom-5 left-5 text-white">
                                        <h3 data-edit="name" class="text-base font-bold">{{ $place['name'] ?? '' }}</h3>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                @php
                    $remaining = count($places) >= 3 ? array_slice($places, 3) : $places;
                    $startIndex = count($places) >= 3 ? 3 : 0;
                @endphp
                @if(!empty($remaining))
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($remaining as $j => $place)
                            @php
                                $placeIndex = $startIndex + $j;
                                $placeUrl = $place['link'] ?? ($place['_term_link'] ?? ($place['url'] ?? ($place['slug'] ?? '#')));
                                if ($placeUrl && $placeUrl !== '#' && !str_starts_with($placeUrl, '/') && !str_starts_with($placeUrl, 'http') && !str_starts_with($placeUrl, '?')) {
                                    $placeUrl = '/destinations/' . $placeUrl;
                                }
                            @endphp
                            <a href="{{ $placeUrl }}" data-list="places" data-list-index="{{ $placeIndex }}" class="group relative block h-[280px] overflow-hidden rounded-2xl bg-gray-100">
                                <img src="{{ $place['image'] ?? '' }}" alt="{{ $place['name'] ?? '' }}" data-edit="image" class="absolute inset-0 w-full h-full object-cover {{ empty($place['image']) ? 'hidden' : '' }}" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                <div class="absolute bottom-5 left-5 text-white">
                                    <h3 data-edit="name" class="text-base font-bold">{{ $place['name'] ?? '' }}</h3>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
