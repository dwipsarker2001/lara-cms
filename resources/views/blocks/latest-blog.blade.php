@php $d = $data; @endphp
<section data-block="latestBlog">
    <div class="max-w-6xl mx-auto px-6">
        @if($d['headline'] ?? false)
            <h2 data-edit="headline" class="text-2xl md:text-3xl font-bold text-gray-900">{{ $d['headline'] }}</h2>
        @endif
        @if($d['description'] ?? false)
            <p data-edit="description" class="mt-3 max-w-lg text-gray-500 text-sm md:text-base leading-relaxed">{{ $d['description'] }}</p>
        @endif
    </div>
</section>
