@php $d = $data; @endphp
<section data-block="pageBanner" class="relative flex items-center justify-center overflow-hidden bg-neutral-900 py-24 md:py-36">
    @if($d['backgroundImage'] ?? false)
        <div data-edit="backgroundImage" class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $d['backgroundImage'] }}')"></div>
        <div class="absolute inset-0 bg-neutral-900/60"></div>
    @endif
    <div class="relative z-10 px-4 text-center">
        <h1 data-edit="title" class="text-4xl font-bold tracking-tight text-white md:text-5xl lg:text-6xl">{{ $d['title'] ?? 'Page Title' }}</h1>
    </div>
</section>
