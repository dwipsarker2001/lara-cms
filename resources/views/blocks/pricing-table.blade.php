@php $d = $data; $plans = $d['plans'] ?? []; $badges = $d['badges'] ?? []; $gradientBg = 'linear-gradient(90deg, rgb(255,47,47) 0%, rgb(239,123,22) 35.88%, rgb(138,67,225) 69.92%, rgb(213,17,253) 100%)'; $bg = is_array($d['background'] ?? null) ? $d['background'] : []; if (empty($bg) && isset($d['background']) && is_string($d['background'])) { try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; } } $bgImg = $bg['image'] ?? ''; $bgColor = $bg['color'] ?? ''; $bgOpacity = $bg['opacity'] ?? 100; @endphp
<section data-block="pricingTable" class="pricing-section" style="position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 100px 40px; background: #F4F2F1;">
    <style>
        @media (max-width: 768px) {
            .pricing-plans { flex-direction: column !important; align-items: center !important; }
            .pricing-plan { width: 100% !important; max-width: 400px !important; }
            .pricing-header { padding: 0 20px !important; }
            .pricing-headline { font-size: 36px !important; line-height: 44px !important; }
            .pricing-badges { flex-wrap: wrap !important; gap: 20px !important; }
            .pricing-startup { flex-direction: column !important; gap: 12px !important; text-align: center !important; }
            .pricing-section { padding: 60px 20px !important; }
        }
    </style>
    @if($bgColor)<div class="absolute inset-0" style="background-color: {{ $bgColor }};"></div>@endif
    @if($bgImg)<div class="absolute inset-0" style="background-image: url({{ $bgImg }}); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: {{ $bgOpacity / 100 }};"></div>@endif
    <div style="max-width: 1120px; width: 100%; display: flex; flex-direction: column; align-items: center; gap: 60px; position: relative;">
        <div class="pricing-header" style="display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 0 120px; width: 100%;">
            @if($d['badge'] ?? false)
                <div style="display: inline-flex; align-items: center; gap: 10px; border-radius: 9999px; background: white; padding: 8px 10px; box-shadow: 0px 1px 1px 0px rgba(0,0,0,0.1);">
                    <span data-edit="badge" style="font-family: 'Inter Display', sans-serif; padding: 0 2px; font-size: 16px; font-weight: 500; color: #111;">{{ $d['badge'] }}</span>
                </div>
            @endif
            @if($d['headline'] ?? false)
                <h2 data-edit="headline" class="pricing-headline" style="margin: 0; padding: 0; text-align: center; font-family: Switzer, sans-serif; font-weight: 600; color: #111; font-size: 52px; line-height: 62.4px;">{{ $d['headline'] }}</h2>
            @endif
            @if($d['subtitle'] ?? false)
                <p data-edit="subtitle" style="text-align: center; font-family: 'Inter Display', sans-serif; font-weight: 500; color: #4c4c4c; font-size: 17px; line-height: 25.5px; max-width: 660px;">{{ $d['subtitle'] }}</p>
            @endif
        </div>

        <div x-data="{ yearly: false }" style="display: flex; flex-direction: column; align-items: flex-start; width: 100%; gap: 20px;">
            <div style="display: flex; width: 100%; flex-direction: row; align-items: center; justify-content: center; gap: 14px;">
                <span data-edit="monthlyLabel" style="font-family: 'Inter Display', sans-serif; font-weight: 500; color: #000; font-size: 17px; line-height: 25.5px; cursor: pointer;" @click="yearly = false">{{ $d['monthlyLabel'] ?? 'Billed Monthly' }}</span>
                <div style="position: relative; display: flex; align-items: center; width: 46px; height: 28px; border-radius: 14px; background: {{ $gradientBg }}; cursor: pointer;" @click="yearly = !yearly">
                    <div style="position: absolute; inset: 2px; border-radius: 12px; background: #111;"></div>
                    <div style="width: 20px; height: 20px; border-radius: 10px; background: white; position: relative; left: 3px;" :style="yearly ? { left: '23px' } : { left: '3px' }"></div>
                </div>
                <span data-edit="yearlyLabel" style="font-family: 'Inter Display', sans-serif; font-weight: 500; color: #808080; font-size: 17px; line-height: 25.5px; cursor: pointer;" @click="yearly = true">{{ $d['yearlyLabel'] ?? 'Billed yearly' }}</span>
            </div>

            <div class="pricing-plans" style="display: flex; flex-direction: row; align-items: stretch; justify-content: center; width: 100%; gap: 20px;">
                @foreach($plans as $plan)
                    @if($plan)
                    @php $isFeatured = ($plan['featured'] ?? false) === 'true' || ($plan['featured'] ?? false) === true; @endphp
                    <div data-list="plans" class="pricing-plan" style="flex: 1; display: flex; flex-direction: column; overflow: hidden; border-radius: {{ $isFeatured ? '21px' : '20px' }}; position: relative;">
                        @if($isFeatured)
                            <div style="position: absolute; inset: 0; background: {{ $gradientBg }};"></div>
                        @endif
                        <div style="flex: 1; display: flex; flex-direction: column; background: #e8e4e2; margin: {{ $isFeatured ? '1px' : '0' }}; border-radius: 20px; overflow: hidden; position: relative;">
                            <div style="display: flex; flex-direction: column; background: white; padding: 30px; gap: 40px; border-radius: 0 0 16px 16px;">
                                <div style="display: flex; flex-direction: column; gap: 16px;">
                                    @if($plan['icon'] ?? false)
                                    <div data-edit="icon" style="position: relative; width: 48px; height: 48px; overflow: hidden; border-radius: 12px; flex-shrink: 0;">
                                        <img src="{{ $plan['icon'] }}" alt="" style="width: 100%; height: 100%; object-fit: contain;" />
                                    </div>
                                    @endif
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <h5 data-edit="name" style="font-family: 'Inter Display', sans-serif; font-weight: 600; color: #1e1e1e; font-size: 20px; line-height: 30px; margin: 0;">{{ $plan['name'] ?? '' }}</h5>
                                        @if($plan['description'] ?? false)
                                            <p data-edit="description" style="font-family: 'Inter Display', sans-serif; font-weight: 500; color: #4c4c4c; font-size: 17px; line-height: 25.5px; margin: 0;">{{ $plan['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 24px;">
                                    <div style="display: flex; align-items: baseline; gap: 6px;">
                                        <h2 data-edit="monthlyPrice" style="font-family: Switzer, sans-serif; font-weight: 600; color: #111; font-size: 52px; line-height: 62.4px; margin: 0;" x-text="yearly ? '{{ $plan['yearlyPrice'] ?? '' }}' : '{{ $plan['monthlyPrice'] ?? '' }}'">{{ $plan['monthlyPrice'] ?? '' }}</h2>
                                        @if($plan['period'] ?? false)
                                            <span data-edit="period" style="font-family: 'Inter Display', sans-serif; font-weight: 500; color: #4c4c4c; font-size: 17px; line-height: 25.5px;">{{ $plan['period'] }}</span>
                                        @endif
                                    </div>
                                    @if($plan['ctaLabel'] ?? false)
                                        <a href="{{ $plan['ctaUrl'] ?? '#' }}" data-edit="ctaLabel" data-edit-button style="display: block; width: 100%; border-radius: 8px; background: #111; text-align: center; font-family: 'Inter Display', sans-serif; font-weight: 500; color: white; text-decoration: none; padding: 16px 24px; font-size: 18px; line-height: 27px; transition: background 0.2s;" onmouseover="this.style.background='#333'" onmouseout="this.style.background='#111'">{{ $plan['ctaLabel'] }}</a>
                                    @endif
                                </div>
                            </div>
                            @if(count($plan['features'] ?? []) > 0)
                            <div style="display: flex; flex-direction: column; flex: 1; padding: 20px 30px; gap: 12px;">
                                @foreach($plan['features'] as $feat)
                                    @if($feat)
                                    <div data-list="features" style="display: flex; align-items: center; gap: 12px;">
                                        <div style="display: flex; width: 27px; height: 27px; flex-shrink: 0; align-items: center; justify-content: center; border-radius: 8px;">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M13.5 4L6 11.5L2.5 8" stroke="#111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </div>
                                        <span data-edit="text" style="font-family: 'Inter Display', sans-serif; font-weight: 500; color: #1e1e1e; font-size: 18px; line-height: 27px;">{{ $feat['text'] ?? '' }}</span>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            @if(($d['startupEnabled'] ?? 'true') !== 'false' && (($d['startupText'] ?? false) || ($d['startupHighlight'] ?? false)))
            <div class="pricing-startup" style="display: flex; width: 100%; flex-direction: row; align-items: center; justify-content: space-between; background: #e8e4e2; border-radius: 12px; padding: 10px 20px; border: 1px solid #d4d0cc;">
                <p data-edit="startupText" style="font-family: 'Inter Display', sans-serif; font-weight: 500; font-size: 17px; line-height: 25.5px; margin: 0; color: #000;">
                    {{ $d['startupText'] ?? '' }}
                    @if($d['startupHighlight'] ?? false)
                        <span data-edit="startupHighlight" style="font-family: 'Inter Display', sans-serif; font-weight: 500; font-size: 17px; line-height: 25.5px; background: {{ $gradientBg }}; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{ $d['startupHighlight'] }}</span>
                    @endif
                </p>
                @if($d['startupButtonLabel'] ?? false)
                    <a href="{{ $d['startupButtonUrl'] ?? '/#contact' }}" data-edit="startupButtonLabel" data-edit-button style="font-family: 'Inter Display', sans-serif; font-weight: 500; text-decoration: none; padding: 14px 20px; border-radius: 8px; background: white; font-size: 18px; line-height: 27px; color: #1e1e1e;">{{ $d['startupButtonLabel'] }}</a>
                @endif
            </div>
            @endif

            @if(count($badges) > 0)
            <div class="pricing-badges" style="display: flex; width: 100%; flex-direction: row; align-items: center; justify-content: center; gap: 40px; padding-top: {{ (($d['startupEnabled'] ?? 'true') !== 'false' && (($d['startupText'] ?? false) || ($d['startupHighlight'] ?? false))) ? '20px' : '40px' }};">
                @foreach($badges as $badge)
                    @if($badge)
                    <div data-list="badges" style="display: flex; flex-direction: row; align-items: center; gap: 8px;">
                        @if($badge['icon'] ?? false)
                            <span data-edit="icon" style="color: #111; opacity: 0.7; display: flex;">
                                <i class="{{ $badge['icon'] }}" style="font-size: 20px; width: 20px; text-align: center;"></i>
                            </span>
                        @endif
                        <span data-edit="text" style="font-family: 'Inter Display', sans-serif; font-weight: 500; color: #000; font-size: 17px; line-height: 25.5px;">{{ $badge['text'] ?? '' }}</span>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>
