@php $d = $data; @endphp
<section data-block="heroBanner" class="relative w-full overflow-hidden md:rounded-xl">
    <div data-edit="backgroundImage" class="relative min-h-[560px] md:min-h-[650px] md:mx-[5%] md:rounded-2xl overflow-hidden flex items-center justify-center">
        @if($d['backgroundImage'] ?? false)
            <img src="{{ $d['backgroundImage'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
        @endif
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="relative z-10 flex flex-col items-center text-center px-6 py-20 max-w-4xl mx-auto">
            @if($d['badge'] ?? false)
                <span data-edit="badge" class="inline-flex items-center gap-2 rounded-full bg-orange-400 px-6 py-2 text-sm font-medium text-white mb-6">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    {{ $d['badge'] }}
                </span>
            @endif
            @if($d['headline'] ?? false)
                <h1 data-edit="headline" class="text-3xl md:text-5xl lg:text-6xl font-bold text-white italic leading-tight">{{ $d['headline'] }}</h1>
            @endif
            @if($d['description'] ?? false)
                <p data-edit="description" class="mt-6 text-sm md:text-base text-gray-200 max-w-2xl capitalize">{{ $d['description'] }}</p>
            @endif
            <form action="{{ $d['searchUrl'] ?? '#' }}" method="get" class="mt-10 w-full max-w-2xl flex items-center bg-white rounded-full shadow-lg overflow-hidden px-4 py-2">
                <span class="flex flex-1 items-center gap-2 px-3 py-2 border-r border-gray-200">
                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" /><circle cx="12" cy="10" r="3" /></svg>
                    <span class="text-sm text-gray-700">{{ $d['searchPlaceholder'] ?? 'Where do you want to go?' }}</span>
                </span>
                <span class="flex flex-1 items-center gap-2 px-3 py-2">
                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="text-sm text-gray-700">{{ $d['datePlaceholder'] ?? 'Add dates' }}</span>
                </span>
                <button type="submit" aria-label="Search" class="ml-2 flex h-10 w-10 items-center justify-center rounded-full bg-orange-500 transition-colors hover:bg-orange-600 shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>
            </form>
        </div>
    </div>
</section>
