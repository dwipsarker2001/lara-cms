@php $d = $data; $features = $d['features'] ?? []; $cards = $d['cards'] ?? []; $bg = is_array($d['background'] ?? null) ? $d['background'] : []; if (empty($bg) && isset($d['background']) && is_string($d['background'])) { try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; } } $bgImg = $bg['image'] ?? ''; $bgColor = $bg['color'] ?? ''; $bgOpacity = $bg['opacity'] ?? 100; @endphp
<section data-block="featuresCombined" style="position: relative; padding: 80px 40px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f6f5f4;">
    @if($bgColor)<div class="absolute inset-0" style="background-color: {{ $bgColor }};"></div>@endif
    @if($bgImg)<div class="absolute inset-0" style="background-image: url({{ $bgImg }}); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: {{ $bgOpacity / 100 }};"></div>@endif
    <div style="max-width: 1240px; width: 100%; display: flex; flex-direction: column; align-items: center; gap: 80px; position: relative;">
        <div style="display: grid; grid-template-columns: 1fr; align-items: center; gap: 48px; width: 100%;">
            <div style="display: flex; flex-direction: column; gap: 24px;">
                @if($d['badge'] ?? false)
                <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 9999px; background: rgba(255,255,255,0.8); border: 1px solid #e8e4e2; width: fit-content;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #4c4c4c;"></span>
                    <span data-edit="badge" style="font-family: 'Inter Display', sans-serif; color: #111; font-size: 16px; font-weight: 500;">{{ $d['badge'] }}</span>
                </div>
                @endif
                @if($d['headline'] ?? false)
                <h2 data-edit="headline" style="font-family: Switzer, sans-serif; color: #111; font-size: 52px; line-height: 1.2; font-weight: 600; letter-spacing: -0.025em; max-width: 576px;">{{ $d['headline'] }}</h2>
                @endif
                @if(count($features) > 0)
                <div style="display: flex; flex-direction: column; gap: 24px; margin-top: 8px;">
                    @foreach($features as $f)
                        @if($f)
                        <div data-list="features" style="display: flex; align-items: flex-start; gap: 16px;">
                            <div style="width: 24px; height: 24px; border-radius: 50%; background: #111; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 4px;">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M11.5 3.5L5.5 9.5L2.5 6.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <p data-edit="title" style="font-family: Switzer, sans-serif; color: #1e1e1e; font-size: 20px; font-weight: 600;">{{ $f['title'] ?? '' }}</p>
                                @if($f['description'] ?? false)
                                <p data-edit="description" style="font-family: 'Inter Display', sans-serif; color: #4c4c4c; font-size: 18px;">{{ $f['description'] }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
            @if($d['image'] ?? false)
            <div style="position: relative; width: 100%; min-height: 450px;">
                <img data-edit="image" src="{{ $d['image'] }}" alt="Task management dashboard" style="width: 100%; height: 100%; object-fit: contain;" />
            </div>
            @endif
        </div>

        <div style="display: flex; flex-direction: column; align-items: center; width: 100%; max-width: 768px; margin: 0 auto; gap: 24px;">
            @if($d['secondaryBadge'] ?? false)
            <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 9999px; background: rgba(255,255,255,0.8); border: 1px solid #e8e4e2; width: fit-content;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #4c4c4c;"></span>
                <span data-edit="secondaryBadge" style="font-family: 'Inter Display', sans-serif; color: #111; font-size: 16px; font-weight: 500;">{{ $d['secondaryBadge'] }}</span>
            </div>
            @endif
            @if($d['secondaryHeadline'] ?? false)
            <h2 data-edit="secondaryHeadline" style="font-family: Switzer, sans-serif; color: #111; font-size: 52px; line-height: 1.2; font-weight: 600; letter-spacing: -0.025em; text-align: center;">{{ $d['secondaryHeadline'] }}</h2>
            @endif
            @if($d['description'] ?? false)
            <p data-edit="description" style="font-family: 'Inter Display', sans-serif; color: #4c4c4c; font-size: 18px; text-align: center; max-width: 576px;">{{ $d['description'] }}</p>
            @endif
            @if(count($cards) > 0)
            <div style="display: grid; grid-template-columns: 1fr; gap: 16px; width: 100%; margin-top: 16px;">
                @foreach($cards as $card)
                    @if($card)
                    <div data-list="cards" style="display: flex; align-items: center; gap: 12px; border-radius: 8px; border: 1px solid #e8e4e2; background: white; padding: 16px 20px;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #4c4c4c; flex-shrink: 0;"></div>
                        <span data-edit="text" style="font-family: 'Inter Display', sans-serif; color: #4c4c4c; font-size: 18px; font-weight: 500;">{{ $card['text'] ?? '' }}</span>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>
