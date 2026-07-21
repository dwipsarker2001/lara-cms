@php
    $d = $data;
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    if (empty($bg) && isset($d['background']) && is_string($d['background'])) {
        try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    $bgImg = $bg['image'] ?? '';
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;
    $testimonials = $d['testimonials'] ?? [];
    $statCards = $d['statCards'] ?? [];

    $items = [];
    if (isset($statCards[0])) $items[] = ['type' => 'stat', 'data' => $statCards[0]];
    if (isset($statCards[1])) $items[] = ['type' => 'stat', 'data' => $statCards[1]];
    if (isset($testimonials[0])) $items[] = ['type' => 'testimonial', 'data' => $testimonials[0]];
    if (isset($testimonials[1])) $items[] = ['type' => 'testimonial', 'data' => $testimonials[1]];
    if (isset($testimonials[2])) $items[] = ['type' => 'testimonial', 'data' => $testimonials[2]];
    if (isset($statCards[2])) $items[] = ['type' => 'stat', 'data' => $statCards[2]];
    if (isset($testimonials[3])) $items[] = ['type' => 'testimonial', 'data' => $testimonials[3]];
    if (isset($statCards[3])) $items[] = ['type' => 'stat', 'data' => $statCards[3]];
@endphp
<section data-block="testimonialsGrid" id="testimonials" class="relative overflow-hidden" style="padding: 100px 40px; background: #f6f5f4;">
    @if($bgColor)
        <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
    @endif
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
    @endif
    <div class="relative flex flex-col items-center w-full mx-auto" style="max-width: 1110px; gap: 60px;">
        <div class="flex flex-col items-center text-center" style="gap: 10px; padding: 0 120px;">
            @if($d['badge'] ?? false)
                <div style="display: inline-flex; align-items: center; gap: 10px; border-radius: 9999px; background: white; padding: 8px 10px; box-shadow: 0px 1px 1px 0px rgba(0,0,0,0.1);">
                    <span data-edit="badge" style="font-family: 'Inter Display', sans-serif; padding: 0 2px; font-size: 16px; font-weight: 500; color: #111;">{{ $d['badge'] }}</span>
                </div>
            @endif
            @if($d['headline'] ?? false)
                <h2 data-edit="headline" style="margin: 0; padding: 0; text-align: center; font-family: Switzer, sans-serif; font-weight: 600; color: #111; font-size: 52px; line-height: 62.4px;">{{ $d['headline'] }}</h2>
            @endif
            @if($d['subtitle'] ?? false)
                <p data-edit="subtitle" style="text-align: center; font-family: 'Inter Display', sans-serif; font-weight: 500; color: #4c4c4c; font-size: 17px; line-height: 25.5px; max-width: 660px;">{{ $d['subtitle'] }}</p>
            @endif
        </div>

        <div class="grid w-full" style="grid-template-columns: repeat(4, 242.5px); grid-template-rows: repeat(3, 250px); gap: 20px;">
            @foreach($items as $i => $item)
                @php
                    $gridStyle = '';
                    if ($item['type'] === 'stat') {
                        $gridStyle = 'grid-column: span 1; grid-row: span 1;';
                    } else {
                        $gridStyle = 'grid-column: span 2; grid-row: span 1;';
                    }
                @endphp
                @if($item['type'] === 'stat')
                    <div data-list="statCards" style="{{ $gridStyle }} background: #fdf1e7; border-radius: 12px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between; align-items: flex-start; border: 1px solid #fad6b7;">
                        <div style="display: flex; flex-direction: column; gap: 14px;">
                            <p data-edit="value" style="font-family: 'Inter Display', sans-serif; font-size: 32px; font-weight: 600; color: #111; margin: 0; padding: 0; line-height: 1;">{{ $item['data']['value'] ?? '' }}</p>
                            <p data-edit="label" style="font-family: 'Inter Display', sans-serif; font-size: 17px; font-weight: 500; color: #4c4c4c; margin: 0; padding: 0; line-height: 25.5px;">{{ $item['data']['label'] ?? '' }}</p>
                        </div>
                    </div>
                @else
                    <div data-list="testimonials" style="{{ $gridStyle }} background: #fff; border-radius: 12px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between; align-items: flex-start; border: 1px solid #f1f0ee;">
                        <p data-edit="quote" style="font-family: 'Inter Display', sans-serif; font-size: 18px; font-weight: 500; color: #1e1e1e; line-height: 27px; margin: 0; padding: 0; max-width: 457px;">&ldquo;{{ $item['data']['quote'] ?? '' }}&rdquo;</p>
                        <div style="display: flex; flex-direction: row; align-items: center; gap: 12px; width: 100%;">
                            <div style="width: 46px; height: 46px; border-radius: 9999px; overflow: hidden; flex-shrink: 0; position: relative;">
                                @if($item['data']['avatar'] ?? false)
                                    <img src="{{ $item['data']['avatar'] }}" alt="{{ $item['data']['name'] ?? '' }}" style="width: 100%; height: 100%; object-fit: cover;" sizes="46px" />
                                @else
                                    <div style="width: 100%; height: 100%; background: #e5e7eb; border-radius: 9999px;"></div>
                                @endif
                            </div>
                            <div style="display: flex; flex-direction: column; flex: 1;">
                                <p data-edit="name" style="font-family: 'Inter Display', sans-serif; font-size: 17px; font-weight: 500; color: #000; margin: 0; padding: 0; line-height: 25.5px;">{{ $item['data']['name'] ?? '' }}</p>
                                <p data-edit="role" style="font-family: 'Inter Display', sans-serif; font-size: 16px; font-weight: 400; color: #4c4c4c; margin: 0; padding: 0; line-height: 19.2px;">{{ $item['data']['role'] ?? '' }}</p>
                            </div>
                            @if($item['data']['twitterUrl'] ?? false)
                                <a href="{{ $item['data']['twitterUrl'] }}" target="_blank" rel="noopener" aria-label="Twitter Link" style="width: 44px; height: 44px; border-radius: 8px; background: #111; display: flex; align-items: center; justify-content: center; flex-shrink: 0;" onmouseover="this.style.background='#333'" onmouseout="this.style.background='#111'">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
