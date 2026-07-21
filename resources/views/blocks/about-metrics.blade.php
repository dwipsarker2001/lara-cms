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
<section data-block="aboutMetrics" class="py-20 relative overflow-hidden">
    @if($bgColor)
        <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
    @endif
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
    @endif
    <div class="relative">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
                <div class="flex-1">
                    @if($d['badge'] ?? false)
                        <span data-edit="badge" class="inline-block rounded-full bg-primary/10 text-primary px-4 py-1.5 text-sm font-semibold">{{ $d['badge'] }}</span>
                    @endif
                    @if($d['headline'] ?? false)
                        <h2 data-edit="headline" class="mt-4 text-3xl md:text-4xl font-semibold text-gray-900 leading-tight">{{ $d['headline'] }}</h2>
                    @endif
                    @if($d['description'] ?? false)
                        <p data-edit="description" class="mt-4 text-gray-500 leading-relaxed max-w-lg">{{ $d['description'] }}</p>
                    @endif
                    @if($d['ctaLabel'] ?? false)
                        <a href="{{ $d['ctaUrl'] ?? '#' }}" data-edit="ctaLabel" data-edit-button class="mt-6 inline-flex items-center gap-2 rounded-lg bg-primary text-white px-6 py-3 text-sm font-semibold transition-colors hover:bg-primary/90">
                            {{ $d['ctaLabel'] }}
                            <svg class="w-4 h-4 -rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                    @if($d['ratingText'] ?? false)
                        <div class="mt-6 flex items-center gap-2">
                            <div class="flex gap-0.5">
                                @foreach(range(1, 5) as $star)
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endforeach
                            </div>
                            <span data-edit="ratingText" class="text-sm font-medium text-gray-600">{{ $d['ratingText'] }}</span>
                        </div>
                    @endif
                </div>
                @if($d['image'] ?? false)
                    <div class="shrink-0">
                        <img src="{{ $d['image'] }}" alt="" data-edit="image" class="w-full max-w-md rounded-2xl object-cover" />
                    </div>
                @endif
            </div>
            @if(count($d['metrics'] ?? []) > 0)
                <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8">
                    @foreach($d['metrics'] as $i => $metric)
                        @if($metric)
                            <div data-list="metrics" class="text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary/10">
                                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </div>
                                @if($metric['value'] ?? false)
                                    <p data-edit="value" class="mt-3 text-3xl font-semibold text-gray-900">{{ $metric['value'] }}</p>
                                @endif
                                @if($metric['label'] ?? false)
                                    <p data-edit="label" class="mt-1 text-sm text-gray-500">{{ $metric['label'] }}</p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
