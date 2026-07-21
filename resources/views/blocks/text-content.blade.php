@php $d = $data; @endphp
<section data-block="textContent" class="bg-gray-50 min-h-screen px-4 pt-[72px] pb-16" style="background-image: radial-gradient(#e5e7eb 1px, transparent 1px); background-size: 18px 18px;">
    <article class="max-w-5xl mx-auto px-8 md:px-16 py-16 bg-white border border-gray-200">
        @if($d['title'] ?? false)
        <header class="mb-8">
            <h1 class="text-[28px] font-bold text-gray-900 mb-2" data-edit="title">{{ $d['title'] }}</h1>
            @if($d['subtitle'] ?? false)
                <p class="text-sm text-gray-400" data-edit="subtitle">{{ $d['subtitle'] }}</p>
            @endif
        </header>
        @endif

        @if($d['body'] ?? false)
            <div data-edit="body" class="mt-8 space-y-6 text-[14.5px] leading-relaxed text-gray-600">
                {!! $d['body'] !!}
            </div>
        @endif
    </article>
</section>
