@php
    $bannerTitle = $data['title'] ?? $page?->data['title'] ?? $page?->title ?? 'Page Banner';
    $bgImage = $data['backgroundImage'] ?? null;

    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
    ];

    $pathSegments = array_filter(explode('/', request()->path()));

    if (isset($page->collection) && $page->collection && $page->collection->slug !== 'pages') {
        $collectionName = $page->collection->name;
        $collectionSlug = $page->collection->slug;

        $breadcrumbs[] = [
            'label' => $collectionName,
            'url' => '/' . $collectionSlug,
        ];

        $breadcrumbs[] = [
            'label' => $bannerTitle,
            'url' => null,
        ];
    } elseif (count($pathSegments) > 1) {
        $cumulativeUrl = '';
        $segmentCount = count($pathSegments);
        $i = 0;
        foreach ($pathSegments as $seg) {
            $i++;
            $cumulativeUrl .= '/' . $seg;
            if ($i === $segmentCount) {
                $breadcrumbs[] = [
                    'label' => $bannerTitle,
                    'url' => null,
                ];
            } else {
                $matchedCol = \App\Models\Collection::where('slug', $seg)->first();
                $label = $matchedCol ? $matchedCol->name : \Illuminate\Support\Str::title(str_replace('-', ' ', $seg));
                $breadcrumbs[] = [
                    'label' => $label,
                    'url' => $cumulativeUrl,
                ];
            }
        }
    } else {
        $breadcrumbs[] = [
            'label' => $bannerTitle,
            'url' => null,
        ];
    }
@endphp

<section data-block="pageBanner" class="relative z-0 block w-full overflow-hidden" style="height: 350px">
    @if($bgImage)
        <img src="{{ $bgImage }}" alt="{{ $bannerTitle }}" class="absolute inset-0 w-full h-full object-cover" data-edit="backgroundImage" />
    @else
        <div class="absolute inset-0 bg-gray-800"></div>
    @endif
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative flex h-full flex-col items-center justify-center text-white px-4 text-center">
        <h1 data-edit="title" class="mb-2 text-3xl md:text-4xl font-bold tracking-tight">{{ $bannerTitle }}</h1>
        <nav aria-label="Breadcrumb" class="flex flex-wrap items-center justify-center gap-2 text-sm text-gray-200">
            @foreach($breadcrumbs as $index => $crumb)
                @if(!$loop->first)
                    <span class="text-gray-400 select-none">→</span>
                @endif
                @if($crumb['url'])
                    <a href="{{ $crumb['url'] }}" class="hover:text-white transition-colors underline-offset-4 hover:underline">{{ $crumb['label'] }}</a>
                @else
                    <span class="font-medium text-white" data-edit="title">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>
    </div>
</section>
