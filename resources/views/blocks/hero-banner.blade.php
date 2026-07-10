@php $d = $data; @endphp
<section data-block="heroBanner" class="relative flex min-h-[80vh] items-center justify-center overflow-hidden bg-neutral-900">
    @if($d['backgroundImage'] ?? false)
        <div data-edit="backgroundImage" class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $d['backgroundImage'] }}')"></div>
        <div class="absolute inset-0 bg-neutral-900/60"></div>
    @endif
    <div class="relative z-10 mx-auto max-w-4xl px-4 text-center">
        @if($d['badge'] ?? false)
            <span data-edit="badge" class="mb-6 inline-block rounded-full bg-white/20 px-4 py-1.5 text-sm font-medium text-white backdrop-blur-sm">{{ $d['badge'] }}</span>
        @endif
        @if($d['headline'] ?? false)
            <h1 data-edit="headline" class="text-4xl font-bold italic leading-tight text-white md:text-5xl lg:text-7xl">{{ $d['headline'] }}</h1>
        @endif
        @if($d['description'] ?? false)
            <p data-edit="description" class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-neutral-200 md:text-xl">{{ $d['description'] }}</p>
        @endif
        <form class="mx-auto mt-10 flex max-w-2xl flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <input type="text" data-edit="searchDestination" placeholder="{{ $d['searchDestinationPlaceholder'] ?? 'Where to?' }}" class="w-full rounded-xl border-0 bg-white py-3.5 pl-10 pr-4 text-sm text-neutral-900 shadow-sm ring-1 ring-neutral-300 focus:ring-2 focus:ring-primary" />
            </div>
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <input type="date" data-edit="searchDate" class="w-full rounded-xl border-0 bg-white py-3.5 pl-10 pr-4 text-sm text-neutral-900 shadow-sm ring-1 ring-neutral-300 focus:ring-2 focus:ring-primary" />
            </div>
            <button type="submit" class="rounded-xl bg-primary px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 transition">
                {{ $d['searchButtonText'] ?? 'Search' }}
            </button>
        </form>
    </div>
</section>
