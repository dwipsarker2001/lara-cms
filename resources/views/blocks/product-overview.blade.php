@php $d = $data; $features = $d['features'] ?? []; $otherFeatures = $d['otherFeatures'] ?? []; $colors = ['#6366f1', '#a855f7', '#ec4899', '#f59e0b', '#10b981', '#06b6d4', '#f97316']; @endphp
<section data-block="productOverview" style="padding: 100px 40px; position: relative; overflow: hidden; background: #111;">
    <div style="max-width: 1240px; margin: 0 auto; display: flex; flex-direction: column; align-items: center;">
        <div style="display: flex; flex-direction: column; align-items: center; gap: 10px; text-align: center; max-width: 650px;">
            @if($d['badge'] ?? false)
            <span style="display: inline-flex; border-radius: 9999px; background: linear-gradient(to right, #6366f1, #a855f7, #ec4899); padding: 2px;">
                <span data-edit="badge" style="display: inline-flex; align-items: center; padding: 8px 16px; border-radius: 9999px; background: #1a1a1a; font-family: 'Inter Display', sans-serif; font-size: 16px; font-weight: 500; color: white;">{{ $d['badge'] }}</span>
            </span>
            @endif
            @if($d['headline'] ?? false)
                <h2 data-edit="headline" style="font-family: Switzer, sans-serif; color: white; font-size: 52px; font-weight: 600; line-height: 62.4px; margin: 0; padding: 0;">{{ $d['headline'] }}</h2>
            @endif
            @if($d['description'] ?? false)
                <p data-edit="description" style="font-family: 'Inter Display', sans-serif; color: #ded8d3; font-size: 18px; font-weight: 500; line-height: 27px; margin: 0; padding: 0; max-width: 600px;">{{ $d['description'] }}</p>
            @endif
            @if($d['ctaLabel'] ?? false)
                <a href="#" data-edit="ctaLabel" data-edit-button style="display: inline-flex; align-items: center; justify-content: center; padding: 14px 20px; border-radius: 8px; background: white; color: #1e1e1e; font-family: 'Inter Display', sans-serif; font-size: 16px; font-weight: 500; text-decoration: none; margin-top: 8px; transition: background 0.2s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='white'">{{ $d['ctaLabel'] }}</a>
            @endif
        </div>

        @if($d['dashboardImage'] ?? false)
        <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; width: 100%; margin-top: 60px;">
            <div style="position: relative; width: 1016px; max-width: calc(100% - 32px); aspect-ratio: 16/9;">
                <div style="position: absolute; inset: 0; border-radius: 16px; padding: 2px; background: linear-gradient(to right, #6366f1, #a855f7, #ec4899);">
                    <div data-edit="dashboardImage" style="position: relative; width: 100%; height: 100%; border-radius: 14px; overflow: hidden;">
                        <img src="{{ $d['dashboardImage'] }}" alt="Dashboard" style="width: 100%; height: 100%; object-fit: cover;" />
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(count($features) > 0)
        <div style="display: flex; flex-direction: row; align-items: stretch; justify-content: center; flex-wrap: wrap; margin-top: 40px; position: relative;">
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 1px; background: linear-gradient(to right, transparent, #6366f1, transparent);"></div>
            @foreach($features as $i => $f)
                @if($f)
                <div data-list="features" style="display: flex; flex-direction: column; gap: 8px; position: relative; width: 280px; padding: 40px 24px 50px;">
                    @if($i < count($features) - 1)
                        <div style="position: absolute; top: 0; right: 0; width: 1px; height: 100%; background: linear-gradient(to bottom, #6366f1, #a855f7, transparent);"></div>
                    @endif
                    @if($f['icon'] ?? false)
                        <div style="color: white;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                    @endif
                    <h3 data-edit="title" style="font-family: Switzer, sans-serif; color: white; font-size: 16px; font-weight: 600; line-height: 24px; margin: 0; padding: 0;">{{ $f['title'] ?? '' }}</h3>
                    @if($f['description'] ?? false)
                        <p data-edit="description" style="font-family: 'Inter Display', sans-serif; color: #a3a3a3; font-size: 14px; font-weight: 500; line-height: 21px; margin: 0; padding: 0;">{{ $f['description'] }}</p>
                    @endif
                </div>
                @endif
            @endforeach
        </div>
        @endif

        @if(($d['otherFeaturesHeading'] ?? false) && count($otherFeatures) > 0)
        <div style="display: flex; flex-direction: column; align-items: center; gap: 24px; width: 100%;">
            <div style="display: flex; align-items: center; gap: 16px; width: 100%;">
                <div style="flex: 1; height: 1px; background: linear-gradient(to right, transparent, #555); position: relative;">
                    <span style="position: absolute; right: 0; top: 50%; transform: translateY(-50%) translateX(50%); width: 6px; height: 6px; border-radius: 50%; background: #555;"></span>
                </div>
                <span data-edit="otherFeaturesHeading" style="display: inline-flex; align-items: center; padding: 8px 16px; border-radius: 9999px; background: #1a1a1a; border: 1px solid #555; font-family: 'Inter Display', sans-serif; font-size: 16px; font-weight: 500; color: white; flex-shrink: 0;">{{ $d['otherFeaturesHeading'] }}</span>
                <div style="flex: 1; height: 1px; background: linear-gradient(to right, #555, transparent); position: relative;">
                    <span style="position: absolute; left: 0; top: 50%; transform: translateY(-50%) translateX(-50%); width: 6px; height: 6px; border-radius: 50%; background: #555;"></span>
                </div>
            </div>
            <div style="position: relative; width: 100%; overflow: hidden;">
                <style>
                    @keyframes other-features-marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
                    .other-features-track { animation: other-features-marquee 30s linear infinite; }
                </style>
                <div class="other-features-track" style="display: flex; gap: 32px; width: max-content;">
                    @foreach($otherFeatures as $i => $f)
                        @if($f)
                        <span data-list="otherFeatures" style="font-family: 'Inter Display', sans-serif; color: #a3a3a3; font-size: 14px; font-weight: 500; line-height: 21px; white-space: nowrap; background: #1a1a1a; border-radius: 8px; padding: 10px 16px;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 8px; background: {{ $colors[$i % 7] }};"></span>
                            <span data-edit="text">{{ $f['text'] ?? '' }}</span>
                        </span>
                        @endif
                    @endforeach
                    @foreach($otherFeatures as $i => $f)
                        @if($f)
                        <span style="font-family: 'Inter Display', sans-serif; color: #a3a3a3; font-size: 14px; font-weight: 500; line-height: 21px; white-space: nowrap; background: #1a1a1a; border-radius: 8px; padding: 10px 16px;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 8px; background: {{ $colors[$i % 7] }};"></span>
                            <span>{{ $f['text'] ?? '' }}</span>
                        </span>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
