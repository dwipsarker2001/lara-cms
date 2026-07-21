@php
    $d = $data;
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    if (empty($bg) && isset($d['background']) && is_string($d['background'])) {
        try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    $bgImg = $bg['image'] ?? '';
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;
    $cards = $d['cards'] ?? [];
@endphp
<section data-block="featureImageCards" class="relative overflow-hidden" style="padding: 80px 40px; background-color: {{ $bgColor ?: '#ffffff' }};">
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }};"></div>
    @endif
    <div class="relative mx-auto w-full" style="max-width: 1110px;">
        <div class="text-center" style="margin-bottom: 48px;">
            @if($d['headline'] ?? false)
                <h2 data-edit="headline" style="margin: 0 0 12px; font-weight: 600; color: #111; font-size: 40px; line-height: 48px;">{{ $d['headline'] }}</h2>
            @endif
            @if($d['description'] ?? false)
                <p data-edit="description" style="margin: 0; font-weight: 500; color: #4c4c4c; font-size: 17px; line-height: 25.5px;">{{ $d['description'] }}</p>
            @endif
        </div>
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
            @foreach($cards as $card)
                <div data-list="cards" style="background: #fff; border: 1px solid #f1f0ee; border-radius: 12px; overflow: hidden;">
                    <div data-edit="image" style="width: 100%; height: 180px; overflow: hidden; background: #f3f4f6;">
                        @if($card['image'] ?? false)
                            <img src="{{ $card['image'] }}" alt="{{ $card['title'] ?? '' }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @endif
                    </div>
                    <div style="padding: 20px;">
                        @if($card['title'] ?? false)
                            <h3 data-edit="title" style="margin: 0 0 8px; font-size: 18px; font-weight: 600; color: #111;">{{ $card['title'] }}</h3>
                        @endif
                        @if($card['description'] ?? false)
                            <p data-edit="description" style="margin: 0; font-size: 15px; color: #4c4c4c; line-height: 22.5px;">{{ $card['description'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
