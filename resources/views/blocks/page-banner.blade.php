@php $d = $data; @endphp
<section data-block="pageBanner">
    <div class="relative flex min-h-[320px] md:min-h-[400px] items-center justify-center overflow-hidden rounded-none md:mx-[5%] md:my-4 md:rounded-2xl">
        @if($d['backgroundImage'] ?? false)
            <img src="{{ $d['backgroundImage'] }}" alt="" data-edit="backgroundImage" class="absolute inset-0 w-full h-full object-cover" />
        @endif
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative z-10 flex flex-col items-center text-center px-6 py-20 max-w-3xl mx-auto">
            @if($d['title'] ?? false)
                <h1 data-edit="title" class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight">{{ $d['title'] }}</h1>
            @endif
        </div>
    </div>
</section>
