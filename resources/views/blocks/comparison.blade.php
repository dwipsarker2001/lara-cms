@php
    $d = $data;
    $otherItems = $d['otherItems'] ?? [];
    $prismoItems = $d['prismoItems'] ?? [];
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    if (empty($bg) && isset($d['background']) && is_string($d['background'])) {
        try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    $bgImg = $bg['image'] ?? '';
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;
@endphp
<section data-block="comparison" style="background: #F9F9F9; position: relative;">
    <style>
        @media (min-width: 1024px) {
            .comparison-grid { flex-direction: row !important; align-items: stretch !important; gap: 16px !important; }
            .comparison-card { width: 50% !important; max-width: none !important; }
            .comparison-card-padding { padding: 40px !important; }
        }
    </style>
    @if($bgColor)<div class="absolute inset-0" style="background-color: {{ $bgColor }};"></div>@endif
    @if($bgImg)<div class="absolute inset-0" style="background-image: url({{ $bgImg }}); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: {{ $bgOpacity / 100 }};"></div>@endif
    <div style="max-width: 1024px; margin: 0 auto; padding: 48px 24px; position: relative;">
        <div style="text-align: center; margin-bottom: 40px;">
            <span data-edit="badgeText" style="padding: 6px 16px; border-radius: 16px; border: 1px solid #e5e7eb; font-size: 12px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #6b7280; background: white; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: inline-block; font-family: 'Inter Display', sans-serif;">{{ $d['badgeText'] ?? 'Comparison' }}</span>
            @if($d['headline'] ?? false)
                <h2 data-edit="headline" style="font-size: 52px; font-weight: 600; letter-spacing: -0.025em; color: #111827; margin-top: 32px; margin-bottom: 24px; font-family: 'Switzer', sans-serif; line-height: 1.5;">{{ $d['headline'] }}</h2>
            @endif
            @if($d['description'] ?? false)
                <p data-edit="description" style="color: #6b7280; max-width: 576px; margin: 0 auto; font-size: 16px; font-weight: 500; line-height: 1.625; font-family: 'Inter Display', sans-serif;">{{ $d['description'] }}</p>
            @endif
        </div>

        <div class="comparison-grid" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 48px; position: relative;">
            <div class="comparison-card" style="width: 100%; max-width: 512px; z-index: 0;">
                <div style="border-radius: 16px; background: #EAE7E4; overflow: hidden; border: 1px solid rgba(0,0,0,0.05); height: 100%;">
                    <div style="background: #E2DFDD; height: 64px; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid rgba(0,0,0,0.05);">
                        <h3 data-edit="otherTitle" style="font-size: 14px; font-weight: 700; color: #374151; font-family: 'Inter Display', sans-serif;">{{ $d['otherTitle'] ?? 'OTHER PLATFORMS' }}</h3>
                    </div>
                    <div class="comparison-card-padding" style="padding: 24px 40px; display: flex; flex-direction: column; gap: 20px;">
                        @foreach($otherItems as $item)
                            @if($item)
                            <div data-list="otherItems" style="display: flex; align-items: center; gap: 16px; color: rgba(75,85,99,0.8);">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" focusable="false" style="width: 20px; height: 20px; flex-shrink: 0; display: inline-block;"><g weight="duotone"><path d="M216,48v55.77C216,174.6,176.6,232,128,232S40,174.6,40,103.79V48a8,8,0,0,1,10.89-7.47C66,46.41,95.11,55.71,128,55.71s62-9.3,77.11-15.16A8,8,0,0,1,216,48Z" opacity="0.2"></path><path d="M158.66,188.43a8,8,0,0,1-11.09,2.23C141.07,186.34,136,184,128,184s-13.07,2.34-19.57,6.66a8,8,0,0,1-8.86-13.32C108,171.73,116.06,168,128,168s20,3.73,28.43,9.34A8,8,0,0,1,158.66,188.43ZM189.34,114a8,8,0,0,0-11.3.62c-2.68,3-8.85,5.34-14,5.34s-11.36-2.35-14-5.34A8,8,0,0,0,138,125.33c5.71,6.38,16.14,10.67,26,10.67s20.25-4.29,26-10.67A8,8,0,0,0,189.34,114ZM224,48v55.77c0,35.84-9.65,69.65-27.18,95.18-18.16,26.46-42.6,41-68.82,41s-50.66-14.57-68.82-41C41.65,173.44,32,139.63,32,103.79V48A16,16,0,0,1,53.79,33.09C67.84,38.55,96.18,47.71,128,47.71s60.15-9.16,74.21-14.62A16,16,0,0,1,224,48Zm-16,0v0c-15.1,5.89-45.57,15.73-80,15.73S63.1,53.87,48,48v55.79c0,32.64,8.66,63.23,24.37,86.13C87.46,211.9,107.21,224,128,224s40.54-12.1,55.63-34.08C199.34,167,208,136.43,208,103.79Zm-90,77.31A8,8,0,0,0,106,114.66c-2.68,3-8.85,5.34-14,5.34s-11.36-2.35-14-5.34A8,8,0,0,0,66,125.33C71.75,131.71,82.18,136,92,136S112.25,131.71,118,125.33Z"></path></g></svg>
                                <span data-edit="text" style="font-size: 14px; font-weight: 700; letter-spacing: -0.025em; font-family: 'Inter Display', sans-serif;">{{ $item['text'] ?? 'Feature' }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="comparison-card" style="width: 100%; max-width: 512px; z-index: 10;">
                <div style="position: relative; height: 100%; padding: 6px; border-radius: 16px; overflow: hidden; background: linear-gradient(transparent,transparent),linear-gradient(to bottom right,#F97316,#EC4899,#8B5CF6); background-origin: padding-box,border-box; background-clip: padding-box,border-box;">
                    <div style="height: 100%; background: white; border-radius: 10px; overflow: hidden;">
                        <div data-edit="prismoIcon" style="height: 64px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: center; gap: 12px;">
                            @if($d['prismoIcon'] ?? false)
                                <img src="{{ $d['prismoIcon'] }}" alt="" style="height: 32px; width: auto;" />
                            @endif
                        </div>
                        <div class="comparison-card-padding" style="padding: 24px 40px; display: flex; flex-direction: column; gap: 20px;">
                            @foreach($prismoItems as $item)
                                @if($item)
                                <div data-list="prismoItems" style="display: flex; align-items: center; gap: 16px; color: #111827;">
                                    <div style="padding: 2px; border-radius: 8px; overflow: hidden; background: linear-gradient(transparent,transparent),linear-gradient(to bottom right,#F97316,#EC4899,#8B5CF6); background-origin: padding-box,border-box; background-clip: padding-box,border-box;">
                                        <div style="width: 20px; height: 20px; border-radius: 4px; background: #111; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                            <svg style="width: 14px; height: 14px; color: white; stroke-width: 3;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                    <span data-edit="text" style="font-size: 14px; font-weight: 700; letter-spacing: -0.025em; font-family: 'Inter Display', sans-serif;">{{ $item['text'] ?? 'Feature Benefit' }}</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($d['ctaText'] ?? false)
            <div style="margin-top: 48px; text-align: center;">
                <a href="{{ $d['ctaUrl'] ?? '#' }}" data-edit="ctaText" data-edit-button style="display: inline-flex; align-items: center; gap: 8px; padding: 16px 32px; background: black; color: white; border-radius: 8px; font-size: 14px; font-weight: 900; font-family: 'Inter Display', sans-serif; transition: background 0.2s;" onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#000'">{{ $d['ctaText'] }}</a>
            </div>
        @endif
    </div>
</section>
