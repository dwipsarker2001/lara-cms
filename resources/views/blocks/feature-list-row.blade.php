@php $d = $data; $features = $d['features'] ?? []; $bg = is_array($d['background'] ?? null) ? $d['background'] : []; if (empty($bg) && isset($d['background']) && is_string($d['background'])) { try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; } } $bgImg = $bg['image'] ?? ''; $bgColor = $bg['color'] ?? ''; $bgOpacity = $bg['opacity'] ?? 100; @endphp
<section data-block="featureListRow" style="background: #F2F0EE; position: relative; padding-top: 80px; padding-bottom: 80px; overflow: hidden;">
    @if($bgColor)<div class="absolute inset-0" style="background-color: {{ $bgColor }};"></div>@endif
    @if($bgImg)<div class="absolute inset-0" style="background-image: url({{ $bgImg }}); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: {{ $bgOpacity / 100 }};"></div>@endif
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 48px; display: flex; flex-direction: column; align-items: center; gap: 64px; position: relative;">
        <div style="width: 100%; display: flex; flex-direction: column; align-items: center; gap: 64px;">
            @if($d['image'] ?? false)
            <div style="flex: 1.2; width: 100%;">
                <div data-edit="image" style="position: relative; width: 100%; aspect-ratio: 4/3; border-radius: 24px; overflow: hidden; background: #111;">
                    <img src="{{ $d['image'] }}" alt="{{ $d['headline'] ?? 'Mockup' }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9;" />
                </div>
            </div>
            @endif

            <div style="flex: 1; width: 100%;">
                <div style="margin-bottom: 32px;">
                    @if($d['badge'] ?? false)
                        <span data-edit="badge" style="padding: 6px 16px; border-radius: 16px; border: 1px solid #e5e7eb; font-size: 12px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #6b7280; background: white; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: inline-block; margin-bottom: 24px;">{{ $d['badge'] }}</span>
                    @endif
                    @if($d['headline'] ?? false)
                        <h2 data-edit="headline" style="font-size: 30px; font-weight: 600; letter-spacing: -0.025em; color: #111827; margin-top: 24px; line-height: 1.15;">{{ $d['headline'] }}</h2>
                    @endif
                    @if($d['description'] ?? false)
                        <p data-edit="description" style="margin-top: 24px; font-size: 17px; color: #6b7280; font-weight: 500; line-height: 1.625; max-width: 576px;">{{ $d['description'] }}</p>
                    @endif
                </div>

                @if(count($features) > 0)
                <div style="display: flex; flex-direction: column; gap: 24px; margin-top: 40px;">
                    @foreach($features as $f)
                        @if($f)
                        <div data-list="features" style="display: flex; align-items: flex-start; gap: 16px;">
                            <div style="padding: 2px; border-radius: 8px; overflow: hidden; background: linear-gradient(transparent,transparent),linear-gradient(to bottom right,#F97316,#EC4899,#8B5CF6); background-origin: padding-box,border-box; background-clip: padding-box,border-box;">
                                <div style="width: 20px; height: 20px; border-radius: 4px; background: #111; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    <svg style="width: 14px; height: 14px; color: white; stroke-width: 3;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: baseline;">
                                <h3 data-edit="title" style="font-size: 16px; font-weight: 600; color: #111827; letter-spacing: -0.025em;">{{ $f['title'] ?? 'Feature' }}</h3>
                                @if($f['description'] ?? false)
                                    <span data-edit="description" style="font-size: 16px; color: #6b7280; font-weight: 500; letter-spacing: -0.025em;">- {{ $f['description'] }}</span>
                                @endif
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
