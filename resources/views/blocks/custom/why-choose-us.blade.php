@php $d = $data; @endphp
@php
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    if (empty($bg) && isset($d['background']) && is_string($d['background'])) {
        try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    $hasBg = !empty($bg['image']) || !empty($bg['color']);
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;
    $bgImg = $bg['image'] ?? '';
@endphp

<section data-block="whyChooseUs" class="relative overflow-hidden py-20">
    @if($hasBg)
        @if($bgColor)
            <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
        @endif
        @if($bgImg)
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
        @endif
    @endif
    <div class="relative max-w-6xl mx-auto px-6">
        <div class="mb-14">
            @if($d['heading'] ?? false)
                <h2 data-edit="heading" class="text-center text-2xl md:text-3xl font-bold text-gray-900">{{ $d['heading'] }}</h2>
            @endif
            @if($d['subtitle'] ?? false)
                <p data-edit="subtitle" class="mx-auto mt-3 max-w-xl text-center text-gray-500">{{ $d['subtitle'] }}</p>
            @endif
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
            <div data-edit="image" class="relative overflow-hidden rounded-3xl min-h-[400px] lg:min-h-full cursor-pointer bg-gray-100">
                @if($d['image'] ?? false)
                    <img src="{{ $d['image'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                @endif
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach(($d['features'] ?? []) as $i => $feature)
                    @if($feature)
                        <div data-list="features" class="bg-gray-100/80 rounded-2xl p-6 flex flex-col transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <span data-edit="number" class="text-4xl font-bold text-brand mb-3 leading-none">{{ $feature['number'] ?? '' }}</span>
                            <h3 data-edit="title" class="font-bold text-gray-900 text-lg mb-2">{{ $feature['title'] ?? '' }}</h3>
                            <p data-edit="description" class="text-gray-500 text-sm leading-relaxed">{{ $feature['description'] ?? '' }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
