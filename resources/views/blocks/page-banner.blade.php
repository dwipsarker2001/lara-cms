<section data-block="pageBanner" class="relative z-0 block w-full overflow-hidden" style="height: 350px">
    @if($data['backgroundImage'] ?? false)
        <img src="{{ $data['backgroundImage'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
    @else
        <div class="absolute inset-0 bg-gray-800"></div>
    @endif
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative flex h-full flex-col items-center justify-center text-white">
        <h1 data-edit="title" class="mb-2 text-3xl md:text-4xl font-bold">{{ $data['title'] }}</h1>
        <div class="flex items-center gap-2 text-sm">
            <a href="/" class="hover:text-brand">Home</a>
            <span>→</span>
            <span data-edit="title">{{ $data['title'] }}</span>
        </div>
    </div>
</section>
