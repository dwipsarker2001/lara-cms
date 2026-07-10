@php $d = $data; @endphp
<article data-block="packagePostSlot">
    <div class="mx-auto max-w-4xl px-4 py-8">
        <a href="{{ $d['packageListUrl'] ?? '/packages' }}" class="mb-8 inline-flex items-center gap-2 text-sm font-medium text-neutral-600 hover:text-primary transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7 7l-7-7 7-7"/></svg>
            Back to Packages
        </a>

        @if($d['image'] ?? false)
            <div data-edit="image" class="mb-8 overflow-hidden rounded-2xl shadow-sm">
                <img src="{{ $d['image'] }}" alt="{{ $d['title'] ?? '' }}" class="w-full object-cover" />
            </div>
        @endif

        <header class="mb-8">
            <h1 data-edit="title" class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl lg:text-5xl">{{ $d['title'] ?? '' }}</h1>
            <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-neutral-500">
                @if($d['duration'] ?? false)
                    <span data-edit="duration" class="flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $d['duration'] }}
                    </span>
                @endif
                @if($d['location'] ?? false)
                    <span data-edit="location" class="flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $d['location'] }}
                    </span>
                @endif
                @if($d['price'] ?? false)
                    <span data-edit="price" class="text-lg font-bold text-primary">${{ $d['price'] }}</span>
                @endif
            </div>
        </header>

        @if(($d['highlights'] ?? []))
            <div data-list="highlights" class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($d['highlights'] as $i => $highlight)
                    <div class="rounded-xl bg-neutral-50 p-4 text-center">
                        @if($highlight['icon'] ?? false)
                            <img src="{{ $highlight['icon'] }}" data-edit="highlights:{{ $i }}/icon" alt="" class="mx-auto mb-2 h-8 w-8" />
                        @endif
                        <p data-edit="highlights:{{ $i }}/value" class="text-lg font-bold text-neutral-900">{{ $highlight['value'] ?? '' }}</p>
                        <p data-edit="highlights:{{ $i }}/label" class="text-xs text-neutral-500">{{ $highlight['label'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div data-edit="body" class="prose prose-neutral max-w-none">
            {!! $d['body'] ?? '' !!}
        </div>

        <div class="mt-8 flex flex-col gap-4 border-t border-neutral-100 pt-8 sm:flex-row sm:items-center sm:justify-between">
            <div>
                @if($d['salePrice'] ?? false)
                    <p class="text-sm text-neutral-500">Price</p>
                    <div class="flex items-baseline gap-2">
                        <span data-edit="salePrice" class="text-3xl font-bold text-primary">${{ $d['salePrice'] }}</span>
                        @if($d['originalPrice'] ?? false)
                            <span data-edit="originalPrice" class="text-lg text-neutral-400 line-through">${{ $d['originalPrice'] }}</span>
                        @endif
                    </div>
                @endif
            </div>
            <a href="{{ $d['bookingUrl']['url'] ?? '#' }}" data-edit="bookingUrl" class="inline-flex items-center gap-2 rounded-full bg-primary px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 transition">
                {{ $d['bookingLabel'] ?? 'Book This Package' }}
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</article>
