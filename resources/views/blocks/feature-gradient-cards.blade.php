@php $d = $data; $cards = $d['cards'] ?? []; $gradientBg = 'linear-gradient(180deg, rgb(255,47,47) 0%, rgb(239,123,22) 35.88%, rgb(138,67,225) 69.92%, rgb(213,17,253) 100%)'; $bg = is_array($d['background'] ?? null) ? $d['background'] : []; if (empty($bg) && isset($d['background']) && is_string($d['background'])) { try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; } } $bgImg = $bg['image'] ?? ''; $bgColor = $bg['color'] ?? ''; $bgOpacity = $bg['opacity'] ?? 100; @endphp
<section data-block="featureGradientCards" style="position: relative; padding: 80px 40px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
    @if($bgColor)<div class="absolute inset-0" style="background-color: {{ $bgColor }};"></div>@endif
    @if($bgImg)<div class="absolute inset-0" style="background-image: url({{ $bgImg }}); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: {{ $bgOpacity / 100 }};"></div>@endif
    <div style="max-width: 1240px; width: 100%; display: flex; flex-direction: column; align-items: center; gap: 60px; position: relative;">
        <div style="width: 100%; display: flex; flex-direction: row; align-items: flex-start; gap: 60px;">
            @if($d['image'] ?? false)
            <div style="flex: 1; min-height: 500px; position: relative; flex-shrink: 0;">
                <img data-edit="image" src="{{ $d['image'] }}" alt="" style="width: 100%; height: 100%; object-fit: contain;" />
            </div>
            @endif

            <div style="flex: 1; display: flex; flex-direction: column; gap: 24px; padding-top: 40px;">
                @if($d['badge'] ?? false)
                <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 9999px; background: rgba(255,255,255,0.8); border: 1px solid #e8e4e2; width: fit-content;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #4c4c4c;"></span>
                    <span data-edit="badge" style="font-family: 'Inter Display', sans-serif; color: #3d3d3d; font-size: 16px; font-weight: 500;">{{ $d['badge'] }}</span>
                </div>
                @endif

                @if($d['headline'] ?? false)
                <h2 data-edit="headline" style="font-family: Switzer, sans-serif; color: #111; font-size: 48px; line-height: 1.2; font-weight: 600; letter-spacing: -0.025em; max-width: 576px;">{{ $d['headline'] }}</h2>
                @endif

                @if($d['description'] ?? false)
                <p data-edit="description" style="font-family: 'Inter Display', sans-serif; color: #4c4c4c; font-size: 16px; font-weight: 500; max-width: 512px;">{{ $d['description'] }}</p>
                @endif

                @if(count($cards) > 0)
                <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 8px;">
                    @foreach($cards as $card)
                        @if($card)
                        <div data-list="cards" style="border-radius: 8px; box-shadow: 0px 6px 12px 0px rgba(0,0,0,0.12), 0px 2px 5px 0px rgba(0,0,0,0.1), 0px 6px 11px 0px rgba(0,0,0,0.05); position: relative;">
                            <div style="position: absolute; inset: 0; pointer-events: none; border-radius: 9px; background: {{ $gradientBg }};"></div>
                            <div style="position: relative; border-radius: 8px; padding: 16px 20px; background: linear-gradient(180deg, rgb(76,76,76) 0%, rgb(17,17,17) 100%);">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; background: {{ $gradientBg }};"></div>
                                    <span data-edit="text" style="font-family: 'Inter Display', sans-serif; color: #4c4c4c; font-size: 14px; font-weight: 500;">{{ $card['text'] ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
