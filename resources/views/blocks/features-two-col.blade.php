@php $d = $data; $features = $d['features'] ?? []; $bg = is_array($d['background'] ?? null) ? $d['background'] : []; if (empty($bg) && isset($d['background']) && is_string($d['background'])) { try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; } } $bgImg = $bg['image'] ?? ''; $bgColor = $bg['color'] ?? ''; $bgOpacity = $bg['opacity'] ?? 100; @endphp
<section data-block="featuresTwoCol" style="position: relative; padding: 80px 40px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f6f5f4;">
    @if($bgColor)<div class="absolute inset-0" style="background-color: {{ $bgColor }};"></div>@endif
    @if($bgImg)<div class="absolute inset-0" style="background-image: url({{ $bgImg }}); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: {{ $bgOpacity / 100 }};"></div>@endif
    <div style="max-width: 1240px; width: 100%; display: flex; flex-direction: column; align-items: center; gap: 60px; position: relative;">
        <div style="width: 100%; display: flex; flex-direction: row; align-items: flex-start; gap: 60px;">
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

                @if(count($features) > 0)
                <div style="display: flex; flex-direction: column; gap: 24px; margin-top: 16px;">
                    @foreach($features as $f)
                        @if($f)
                        <div data-list="features" style="display: flex; align-items: flex-start; gap: 16px;">
                            <div style="width: 24px; height: 24px; border-radius: 50%; background: #111; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M11.5 3.5L5.5 9.5L2.5 6.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <p data-edit="title" style="font-family: Switzer, sans-serif; color: #111; font-size: 18px; font-weight: 600;">{{ $f['title'] ?? '' }}</p>
                                @if($f['description'] ?? false)
                                <p data-edit="description" style="font-family: 'Inter Display', sans-serif; color: #4c4c4c; font-size: 16px; font-weight: 500;">{{ $f['description'] }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>

            @if($d['image'] ?? false)
            <div style="flex: 1; min-height: 500px; position: relative; flex-shrink: 0;">
                <img data-edit="image" src="{{ $d['image'] }}" alt="" style="width: 100%; height: 100%; object-fit: contain;" />
            </div>
            @endif
        </div>
    </div>
</section>
