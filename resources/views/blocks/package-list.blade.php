@php $d = $data; @endphp
<section data-block="packageList" class="bg-neutral-50 py-20 md:py-28">
    <div class="mx-auto max-w-7xl px-4">
        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
            <div>
                @if($d['heading'] ?? false)
                    <h2 data-edit="heading" class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">{{ $d['heading'] }}</h2>
                @endif
                @if($d['description'] ?? false)
                    <p data-edit="description" class="mt-2 text-neutral-600">{{ $d['description'] }}</p>
                @endif
            </div>
            @if(($d['categories'] ?? []))
                <div data-list="categories" class="flex flex-wrap gap-2">
                    @foreach($d['categories'] as $i => $category)
                        <button data-edit="categories:{{ $i }}/label" class="rounded-full px-4 py-2 text-sm font-medium transition {{ ($category['active'] ?? false) ? 'bg-primary text-white shadow-sm' : 'bg-white text-neutral-600 shadow-sm ring-1 ring-neutral-200 hover:bg-neutral-50' }}">{{ $category['label'] ?? '' }}</button>
                    @endforeach
                </div>
            @endif
        </div>

        @if($d['showPriceRange'] ?? true)
            <div class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-100">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <span class="text-sm font-medium text-neutral-700">Price Range</span>
                    <div class="flex flex-1 items-center gap-4">
                        <input type="range" min="{{ $d['minPrice'] ?? 0 }}" max="{{ $d['maxPrice'] ?? 10000 }}" value="{{ $d['currentMinPrice'] ?? $d['minPrice'] ?? 0 }}" data-edit="currentMinPrice" class="h-2 w-full cursor-pointer appearance-none rounded-full bg-neutral-200 accent-primary" />
                        <input type="range" min="{{ $d['minPrice'] ?? 0 }}" max="{{ $d['maxPrice'] ?? 10000 }}" value="{{ $d['currentMaxPrice'] ?? $d['maxPrice'] ?? 10000 }}" data-edit="currentMaxPrice" class="h-2 w-full cursor-pointer appearance-none rounded-full bg-neutral-200 accent-primary" />
                    </div>
                    <div class="flex items-center gap-2 text-sm font-medium text-neutral-900">
                        <span>${{ $d['currentMinPrice'] ?? $d['minPrice'] ?? 0 }}</span>
                        <span class="text-neutral-400">-</span>
                        <span>${{ $d['currentMaxPrice'] ?? $d['maxPrice'] ?? 10000 }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if(($d['packages'] ?? []))
            <div data-list="packages" class="mt-8 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($d['packages'] as $i => $package)
                    <div class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-100 transition hover:shadow-lg">
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="{{ $package['image'] ?? '' }}" data-edit="packages:{{ $i }}/image" alt="{{ $package['title'] ?? '' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110" />
                            @if(($package['rating'] ?? false))
                                <div class="absolute right-3 top-3 flex items-center gap-1 rounded-full bg-white/90 px-2.5 py-1 text-xs font-medium text-neutral-800 shadow-sm backdrop-blur-sm">
                                    <svg class="h-3.5 w-3.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    {{ $package['rating'] }}
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="mb-2 flex items-center gap-2 text-xs text-neutral-500">
                                @if($package['location'] ?? false)
                                    <span data-edit="packages:{{ $i }}/location" class="flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $package['location'] }}
                                    </span>
                                @endif
                                @if($package['duration'] ?? false)
                                    <span data-edit="packages:{{ $i }}/duration" class="flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $package['duration'] }}
                                    </span>
                                @endif
                            </div>
                            <h3 data-edit="packages:{{ $i }}/title" class="text-lg font-semibold text-neutral-900">
                                <a href="{{ $package['url'] ?? '#' }}">{{ $package['title'] ?? '' }}</a>
                            </h3>
                            <p data-edit="packages:{{ $i }}/description" class="mt-2 text-sm text-neutral-600 leading-relaxed line-clamp-2">{{ $package['description'] ?? '' }}</p>
                            <div class="mt-4 flex items-baseline gap-2">
                                @if($package['salePrice'] ?? false)
                                    <span data-edit="packages:{{ $i }}/salePrice" class="text-xl font-bold text-primary">${{ $package['salePrice'] }}</span>
                                @endif
                                @if($package['originalPrice'] ?? false)
                                    <span data-edit="packages:{{ $i }}/originalPrice" class="text-sm text-neutral-400 line-through">${{ $package['originalPrice'] }}</span>
                                @endif
                            </div>
                            <a href="{{ $package['url'] ?? '#' }}" class="mt-4 block w-full rounded-xl bg-primary py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary/90 transition">
                                {{ $package['buttonLabel'] ?? 'View Details' }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if(($d['pagination'] ?? false))
            <div class="mt-12 flex items-center justify-center gap-2">
                @if($d['prevPageUrl'] ?? false)
                    <a href="{{ $d['prevPageUrl'] }}" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-neutral-600 shadow-sm ring-1 ring-neutral-200 hover:bg-neutral-50 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif
                @for($p = 1; $p <= ($d['totalPages'] ?? 1); $p++)
                    <a href="{{ ($d['pageUrl'] ?? '?page=') . $p }}" class="flex h-10 w-10 items-center justify-center rounded-xl text-sm font-medium transition {{ $p == ($d['currentPage'] ?? 1) ? 'bg-primary text-white shadow-sm' : 'bg-white text-neutral-600 shadow-sm ring-1 ring-neutral-200 hover:bg-neutral-50' }}">{{ $p }}</a>
                @endfor
                @if($d['nextPageUrl'] ?? false)
                    <a href="{{ $d['nextPageUrl'] }}" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-neutral-600 shadow-sm ring-1 ring-neutral-200 hover:bg-neutral-50 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>
