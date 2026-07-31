@php
    $d = $data;
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    if (empty($bg) && isset($d['background']) && is_string($d['background'])) {
        try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    $bgImg = $bg['image'] ?? '';
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;
@endphp
<section data-block="simpleText" class="py-20 relative overflow-hidden">
    @if($bgColor)
        <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
    @endif
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
    @endif
    <div class="relative max-w-4xl mx-auto px-6">
        @if($d['heading'] ?? false)
            <h2 data-edit="heading" class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ $d['heading'] }}</h2>
        @endif
        @if($d['body'] ?? false)
            <div data-edit="body" class="prose prose-gray max-w-none text-lg leading-relaxed">
                {!! $d['body'] !!}
            </div>
        @endif
    </div>
</section>
