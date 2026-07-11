@php $d = $data; @endphp
<header data-block="siteNavbar" x-data="{ mobileOpen: false }" class="sticky top-0 z-50 w-full border-b border-gray-200 bg-white/95 backdrop-blur-md shadow-sm">
    <nav class="mx-auto flex h-[4.25rem] max-w-7xl items-center justify-between gap-5 px-6">
        <a href="/" class="flex shrink-0 items-center gap-2 text-xl font-extrabold tracking-tight text-gray-900">
            @if($d['logo'] ?? false)
                <img src="{{ $d['logo'] }}" alt="" data-edit="logo" class="object-contain" style="height:{{ $d['logoHeight'] ?? 40 }}px;width:auto" />
            @endif
            @if(!($d['logo'] ?? false))
                <span class="text-brand">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            @endif
            {{ $d['brandName'] ?? 'Brand' }}
        </a>
        <ul class="hidden lg:flex items-center gap-1">
            @foreach(($d['nav'] ?? []) as $i => $link)
                @if($link)
                    <li data-list="nav">
                        @if(count($link['dropdown'] ?? []) > 0)
                            <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                                <button class="inline-flex items-center gap-1 rounded-full px-5 py-2.5 text-sm font-medium text-gray-500 transition-all hover:bg-gray-100 hover:text-gray-900">
                                    {{ $link['label'] ?? 'Link' }}
                                    <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-cloak class="absolute left-0 top-full mt-1 w-48 rounded-xl bg-white shadow-lg ring-1 ring-gray-200 py-2 z-50">
                                    @foreach($link['dropdown'] as $j => $item)
                                        @if($item)
                                            <a href="{{ $item['href'] ?? '#' }}" data-list="dropdown" data-edit="label" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ $item['label'] ?? 'Item' }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $link['href'] ?? '#' }}" data-edit="label" class="inline-flex items-center rounded-full px-5 py-2.5 text-sm font-medium text-gray-500 transition-all hover:bg-gray-100 hover:text-gray-900">{{ $link['label'] ?? 'Link' }}</a>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
        <div class="hidden lg:flex items-center gap-3">
            @if($d['contactLabel'] ?? false)
                <div class="flex items-center gap-2">
                    @if($d['contactIcon'] ?? false)
                        <img src="{{ $d['contactIcon'] }}" alt="" data-edit="contactIcon" class="h-5 w-5 object-contain" />
                    @endif
                    <div class="text-right text-xs">
                        <p data-edit="contactLabel" class="text-gray-500">{{ $d['contactLabel'] }}</p>
                        @if($d['contactNumber'] ?? false)
                            <a href="{{ $d['contactLink'] ?? '#' }}" data-edit="contactNumber" class="font-semibold text-gray-900 hover:text-brand transition-colors">{{ $d['contactNumber'] }}</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <button @click="mobileOpen = !mobileOpen" class="lg:hidden relative z-50 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-700" aria-label="Toggle menu">
            <svg x-show="!mobileOpen" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="mobileOpen" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </nav>
    <div x-cloak x-show="mobileOpen" x-transition:enter="transition-transform duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition-transform duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed inset-0 top-0 z-40 h-full w-full bg-white lg:hidden">
        <ul class="mt-24 flex flex-col items-center gap-2 px-6">
            @foreach(($d['nav'] ?? []) as $i => $link)
                @if($link)
                    <li class="w-full text-center">
                        <a href="{{ $link['href'] ?? '#' }}" data-edit="label" class="block rounded-xl px-5 py-3 text-lg font-medium text-gray-700 transition-colors hover:bg-gray-100">{{ $link['label'] ?? 'Link' }}</a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</header>
