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
<section data-block="aboutIntro" class="py-20 relative overflow-hidden">
    @if($bgColor)
        <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
    @endif
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
    @endif
    <div class="relative">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="relative">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-4">
                            @if($d['image1'] ?? false)
                                <img src="{{ $d['image1'] }}" alt="" data-edit="image1" class="w-full rounded-2xl object-cover h-64" />
                            @endif
                            @if($d['image2'] ?? false)
                                <img src="{{ $d['image2'] }}" alt="" data-edit="image2" class="w-full rounded-2xl object-cover h-48" />
                            @endif
                        </div>
                        <div class="pt-8 space-y-4">
                            @if($d['image3'] ?? false)
                                <img src="{{ $d['image3'] }}" alt="" data-edit="image3" class="w-full rounded-2xl object-cover h-48" />
                            @endif
                        </div>
                    </div>
                    @if($d['badge'] ?? false)
                        <div data-edit="badge" class="absolute -bottom-4 -right-4 lg:-bottom-6 lg:-right-6">
                            <img src="{{ $d['badge'] }}" alt="" class="w-24 h-24 lg:w-32 lg:h-32 object-contain drop-shadow-xl" />
                        </div>
                    @endif
                </div>
                <div>
                    @if($d['heading'] ?? false)
                        <h2 data-edit="heading" class="text-3xl md:text-4xl lg:text-5xl font-semibold text-gray-900 leading-tight">{{ $d['heading'] }}</h2>
                    @endif
                    @if($d['subheading'] ?? false)
                        <p data-edit="subheading" class="mt-4 text-lg text-gray-600">{{ $d['subheading'] }}</p>
                    @endif
                    @if($d['body1'] ?? false)
                        <p data-edit="body1" class="mt-6 text-gray-500 leading-relaxed">{{ $d['body1'] }}</p>
                    @endif
                    @if($d['body2'] ?? false)
                        <p data-edit="body2" class="mt-4 text-gray-500 leading-relaxed">{{ $d['body2'] }}</p>
                    @endif
                    <div class="mt-8 flex items-center gap-4">
                        @if($d['signature'] ?? false)
                            <img src="{{ $d['signature'] }}" alt="" data-edit="signature" class="h-14 object-contain" />
                        @endif
                        <div>
                            @if($d['signerName'] ?? false)
                                <p data-edit="signerName" class="font-semibold text-gray-900">{{ $d['signerName'] }}</p>
                            @endif
                            @if($d['signerTitle'] ?? false)
                                <p data-edit="signerTitle" class="text-sm text-gray-500">{{ $d['signerTitle'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
