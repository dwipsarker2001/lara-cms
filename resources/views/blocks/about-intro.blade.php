@php $d = $data; @endphp
<section data-block="aboutIntro">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div class="relative grid grid-cols-2 gap-4">
            <div data-edit="badge" class="absolute -top-6 left-1/2 -translate-x-1/2 z-10 w-24 h-24">
                @if($d['badge'] ?? false)
                    <img src="{{ $d['badge'] }}" alt="" class="w-full h-auto object-contain" />
                @endif
            </div>
            <div data-edit="image1" class="relative rounded-2xl overflow-hidden h-48 bg-gray-100">
                @if($d['image1'] ?? false)
                    <img src="{{ $d['image1'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                @endif
            </div>
            <div class="h-48"></div>
            <div data-edit="image2" class="relative rounded-2xl overflow-hidden h-56 bg-gray-100">
                @if($d['image2'] ?? false)
                    <img src="{{ $d['image2'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                @endif
            </div>
            <div data-edit="image3" class="relative rounded-2xl overflow-hidden h-56 bg-gray-100">
                @if($d['image3'] ?? false)
                    <img src="{{ $d['image3'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                @endif
            </div>
        </div>
        <div>
            @if($d['heading'] ?? false)
                <h2 data-edit="heading" class="text-3xl font-bold text-gray-900 mb-4">{{ $d['heading'] }}</h2>
            @endif
            @if($d['subheading'] ?? false)
                <p data-edit="subheading" class="text-lg font-semibold text-gray-800 mb-4">{{ $d['subheading'] }}</p>
            @endif
            @if($d['body1'] ?? false)
                <p data-edit="body1" class="text-gray-600 text-sm leading-relaxed mb-4">{{ $d['body1'] }}</p>
            @endif
            @if($d['body2'] ?? false)
                <p data-edit="body2" class="text-gray-600 text-sm leading-relaxed mb-8">{{ $d['body2'] }}</p>
            @endif
            @if(($d['signature'] ?? false) || ($d['signerName'] ?? false))
                <div class="flex items-center gap-4">
                    @if($d['signature'] ?? false)
                        <img src="{{ $d['signature'] }}" alt="" data-edit="signature" class="object-contain" style="height:3rem;width:auto" />
                    @endif
                    <div>
                        @if($d['signerName'] ?? false)
                            <p data-edit="signerName" class="font-bold text-gray-900">{{ $d['signerName'] }}</p>
                        @endif
                        @if($d['signerTitle'] ?? false)
                            <p data-edit="signerTitle" class="text-sm text-gray-500">{{ $d['signerTitle'] }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
