@php $d = $data; @endphp
<section data-block="siteNavbar" class="sticky top-0 z-50 border-b border-neutral-200/50 bg-white/95 backdrop-blur-sm" x-data="{ mobileOpen: false }">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 lg:py-4">
        <a href="/" class="flex items-center gap-3">
            @if($d['logo'] ?? false)
                <img src="{{ $d['logo'] }}" alt="{{ $d['brandName'] ?? '' }}" data-edit="logo" style="height: {{ $d['logoHeight'] ?? 40 }}px" class="w-auto" />
            @else
                <span data-edit="brandName" class="text-xl font-bold tracking-tight text-neutral-900">{{ $d['brandName'] ?? 'E CMS' }}</span>
            @endif
        </a>

        <div class="hidden items-center gap-1 lg:flex" x-data="{ dropdownOpen: null }">
            @foreach($d['nav'] ?? [] as $i => $item)
                <div class="relative">
                    @if(($item['dropdown'] ?? []))
                        <button @click="dropdownOpen = dropdownOpen === {{ $i }} ? null : {{ $i }}" class="flex items-center gap-1 rounded-lg px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 transition">
                            <span data-edit="nav:{{ $i }}/label">{{ $item['label'] ?? '' }}</span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="dropdownOpen === {{ $i }}" @click.outside="dropdownOpen = null" x-transition class="absolute left-0 top-full mt-1 w-48 rounded-xl border border-neutral-200 bg-white py-2 shadow-lg" style="display: none">
                            @foreach($item['dropdown'] as $j => $dropItem)
                                <li>
                                    <a href="{{ $dropItem['href']['url'] ?? '#' }}" data-edit="nav:{{ $i }}/dropdown:{{ $j }}/href" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50 hover:text-neutral-900 transition">{{ $dropItem['label'] ?? '' }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ $item['href']['url'] ?? '#' }}" data-edit="nav:{{ $i }}/href" class="rounded-lg px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 transition">{{ $item['label'] ?? '' }}</a>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="hidden items-center gap-3 lg:flex">
            <a href="{{ $d['contactLink']['url'] ?? '#' }}" data-edit="contactLink" class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 transition">
                @if($d['contactIcon'] ?? false)
                    <img src="{{ $d['contactIcon'] }}" alt="" data-edit="contactIcon" class="h-4 w-4" />
                @endif
                <span data-edit="contactLabel">{{ $d['contactLabel'] ?? 'Chat with us' }}</span>
            </a>
        </div>

        <button @click="mobileOpen = !mobileOpen" class="inline-flex items-center justify-center rounded-lg p-2 text-neutral-700 hover:bg-neutral-100 lg:hidden" aria-label="Toggle menu">
            <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </nav>

    <div x-show="mobileOpen" x-transition class="border-t border-neutral-200 bg-white lg:hidden" style="display: none">
        <div class="space-y-1 px-4 py-4">
            @foreach($d['nav'] ?? [] as $i => $item)
                @if(($item['dropdown'] ?? []))
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="flex w-full items-center justify-between rounded-lg px-4 py-2.5 text-sm font-medium text-neutral-700 hover:bg-neutral-100 transition">
                            <span data-edit="nav:{{ $i }}/label">{{ $item['label'] ?? '' }}</span>
                            <svg class="h-4 w-4" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" class="ml-4 space-y-1 pb-2">
                            @foreach($item['dropdown'] as $j => $dropItem)
                                <li>
                                    <a href="{{ $dropItem['href']['url'] ?? '#' }}" data-edit="nav:{{ $i }}/dropdown:{{ $j }}/href" class="block rounded-lg px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50 transition">{{ $dropItem['label'] ?? '' }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <a href="{{ $item['href']['url'] ?? '#' }}" data-edit="nav:{{ $i }}/href" class="block rounded-lg px-4 py-2.5 text-sm font-medium text-neutral-700 hover:bg-neutral-100 transition">{{ $item['label'] ?? '' }}</a>
                @endif
            @endforeach
            <a href="{{ $d['contactLink']['url'] ?? '#' }}" class="mt-2 flex items-center justify-center gap-2 rounded-full bg-primary px-5 py-2.5 text-sm font-semibold text-white transition">
                @if($d['contactIcon'] ?? false)
                    <img src="{{ $d['contactIcon'] }}" alt="" class="h-4 w-4" />
                @endif
                <span data-edit="contactLabel">{{ $d['contactLabel'] ?? 'Chat with us' }}</span>
            </a>
        </div>
    </div>
</section>
