@php
    $d = $data;
    $btn = $d['button'] ?? [];
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    if (empty($bg) && isset($d['background']) && is_string($d['background'])) {
        try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    $bgImg = $bg['image'] ?? '';
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;
    $currencySymbol = \App\Models\Setting::getCurrencySymbol();
@endphp
<section data-block="travelDeals" class="py-20 relative overflow-hidden">
    @if($bgColor)
        <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
    @endif
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
    @endif
    <div class="relative">
        <div class="max-w-6xl mx-auto px-6">
            <div class="relative">
                @if($d['headline'] ?? false)
                    <h2 data-edit="headline" class="text-center text-2xl md:text-3xl font-bold text-gray-900">{{ $d['headline'] }}</h2>
                @endif
                @if($d['description'] ?? false)
                    <p data-edit="description" class="mx-auto mt-3 max-w-xl text-center text-gray-500">{{ $d['description'] }}</p>
                @endif
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach(($d['cards'] ?? []) as $i => $card)
                        @if($card)
                            <div data-list="cards" class="group flex h-full flex-col overflow-hidden rounded-xl p-3 border border-gray-100 bg-white shadow-sm">
                                <div data-edit="image" class="relative h-52 overflow-hidden rounded-xl bg-gray-100">
                                    @if($card['image'] ?? false)
                                        <img src="{{ $card['image'] }}" alt="{{ $card['title'] ?? '' }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-100">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    @if($card['badge'] ?? false)
                                        <span data-edit="badge" data-edit-button class="absolute top-3 right-3 rounded-md bg-red-500 px-3 py-1 text-xs font-semibold text-white">{{ $card['badge'] }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col px-2 pt-4 pb-2">
                                    @if($card['title'] ?? false)
                                        <h3 data-edit="title" class="text-lg font-bold text-gray-900 transition-colors group-hover:text-primary">{{ $card['title'] }}</h3>
                                    @endif
                                    @if($card['description'] ?? false)
                                        <p data-edit="description" class="mt-1.5 text-sm text-gray-500">{{ $card['description'] }}</p>
                                    @endif
                                    <div class="mt-3 flex items-end justify-between">
                                        @if($card['buttonLabel'] ?? false)
                                            <span data-edit="buttonLabel" data-edit-button class="inline-flex items-center gap-1.5 rounded-lg bg-primary text-white px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-primary/90">
                                                {{ $card['buttonLabel'] }}
                                                <svg class="w-3.5 h-3.5 -rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                                            </span>
                                        @endif
                                        <div class="text-right">
                                            @if($card['priceLabel'] ?? false)
                                                <span data-edit="priceLabel" class="text-xs text-gray-400">{{ $card['priceLabel'] }}</span>
                                            @endif
                                            <div class="flex items-baseline gap-1.5">
                                                @if(isset($card['originalPrice']) && (string)$card['originalPrice'] !== '')
                                                    @php
                                                        $origVal = $card['originalPrice'];
                                                        $origFormatted = is_numeric($origVal) ? number_format((float) $origVal) : $origVal;
                                                        if ($currencySymbol && !str_starts_with((string)$origFormatted, $currencySymbol)) {
                                                            $origFormatted = $currencySymbol . $origFormatted;
                                                        }
                                                    @endphp
                                                    <span data-edit="originalPrice" data-currency="{{ $currencySymbol }}" class="text-sm text-gray-400 line-through">{{ $origFormatted }}</span>
                                                @endif
                                                @if(isset($card['price']) && (string)$card['price'] !== '')
                                                    @php
                                                        $priceVal = $card['price'];
                                                        $priceFormatted = is_numeric($priceVal) ? number_format((float) $priceVal) : $priceVal;
                                                        if ($currencySymbol && !str_starts_with((string)$priceFormatted, $currencySymbol)) {
                                                            $priceFormatted = $currencySymbol . $priceFormatted;
                                                        }
                                                    @endphp
                                                    <span data-edit="price" data-currency="{{ $currencySymbol }}" class="text-2xl font-bold text-gray-900">{{ $priceFormatted }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if(($card['features'] ?? []) && count($card['features'] ?? []) > 0)
                                        <div class="mt-4 border-t border-dashed border-gray-200"></div>
                                        <ul class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2">
                                            @foreach($card['features'] as $fi => $feature)
                                                @if($feature)
                                                    <li data-list="features" class="flex items-center">
                                                        <span class="flex items-center gap-1.5 rounded-md text-sm font-medium text-gray-700 cursor-default">
                                                            @if($feature['icon'] ?? false)
                                                                 <i class="{{ $feature['icon'] }} size-4" data-edit="icon"></i>
                                                            @endif
                                                            <span data-edit="text">{{ $feature['text'] }}</span>
                                                        </span>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                @if($btn['label'] ?? false)
                    <div class="mt-10 flex justify-center">
                        <a href="{{ $btn['link'] ?? '#' }}" data-edit="label" class="inline-flex items-center gap-2 rounded-lg bg-primary text-white px-6 py-3 text-sm font-semibold transition-colors hover:bg-primary/90">
                            {{ $btn['label'] }}
                            <svg class="w-4 h-4 -rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
