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
@endphp
<section data-block="clientTestimonials" id="client-testimonials" class="relative overflow-hidden" style="padding: 80px 40px; background-color: {{ $bgColor ?: '#ffffff' }};">
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
            @foreach($testimonials as $t)
                <div data-list="testimonials" style="background: #fff; border: 1px solid #f1f0ee; border-radius: 12px; padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                    <p data-edit="quote" style="margin: 0; font-size: 16px; font-weight: 500; color: #1e1e1e; line-height: 24px;">&ldquo;{{ $t['quote'] ?? '' }}&rdquo;</p>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div data-edit="avatar" style="width: 40px; height: 40px; border-radius: 9999px; overflow: hidden; flex-shrink: 0; background: #e5e7eb;">
                            @if($t['avatar'] ?? false)
                                <img src="{{ $t['avatar'] }}" alt="{{ $t['name'] ?? '' }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @endif
                        </div>
                        <div style="display: flex; flex-direction: column; flex: 1;">
                            <span data-edit="name" style="font-size: 15px; font-weight: 500; color: #000;">{{ $t['name'] ?? '' }}</span>
                            <span data-edit="role" style="font-size: 13px; color: #4c4c4c;">{{ $t['role'] ?? '' }}</span>
                        </div>
                        @if(!empty($t['rating']))
                            <span data-edit="rating" style="font-size: 14px; font-weight: 600; color: #111;">{{ $t['rating'] }}★</span>
                        @endif
                    </div>
                    @if($t['twitterUrl'] ?? false)
                        <a href="{{ $t['twitterUrl'] }}" data-edit="mentionLabel" target="_blank" rel="noopener" style="font-size: 13px; color: #2563eb; text-decoration: none;">{{ $t['mentionLabel'] ?? '@' }}</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
