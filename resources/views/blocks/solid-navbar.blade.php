@php $d = $data; $isPreview = $preview ?? false; $posClass = $isPreview ? 'relative' : 'fixed top-0 left-0 w-full'; @endphp
<div data-block="solidNavbar" class="{{ $posClass }} z-50 bg-white shadow-sm"
    x-data="{ menuOpen: false }">
    <header>
        <nav>
            <div class="mx-auto max-w-7xl px-6 flex items-center justify-between" style="padding-top: 16px; padding-bottom: 16px;">
                <a href="/" class="flex items-center gap-2 shrink-0">
                    @if($d['logo'] ?? false)
                        <img src="{{ $d['logo'] }}" alt="{{ $d['brandName'] ?? 'Logo' }}" data-edit="logo" class="object-contain" style="height: {{ $d['logoHeight'] ?? 32 }}px; width: auto" />
                    @else
                        <span data-edit="brandName" class="text-xl font-semibold tracking-tight">{{ $d['brandName'] ?? 'Lara CMS' }}</span>
                    @endif
                </a>

                <div class="hidden lg:flex items-center gap-1">
                    @foreach(($d['nav'] ?? []) as $item)
                        @if($item)
                            <a href="{{ $item['href'] ?? '/' }}" data-list="nav" data-edit="label"
                                class="rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200"
                                style="padding: 8px 12px;">
                                {{ $item['label'] ?? 'Link' }}
                            </a>
                        @endif
                    @endforeach
                </div>

                <div class="hidden lg:flex items-center gap-2">
                    @if($d['cta1Label'] ?? false)
                        <a href="{{ $d['cta1Link'] ?? '/waitlist' }}" data-edit="cta1Label" data-edit-button
                            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-all duration-200 hover:bg-gray-100"
                            style="padding: 12px 14px;">
                            {{ $d['cta1Label'] }}
                        </a>
                    @endif
                    @if($d['cta2Label'] ?? false)
                        <a href="{{ $d['cta2Link'] ?? '/#contact' }}" data-edit="cta2Label" data-edit-button
                            class="rounded-lg text-sm font-medium text-white transition-all duration-200 hover:opacity-90"
                            style="background-color: rgb(17, 17, 17); padding: 12px 14px;">
                            {{ $d['cta2Label'] }}
                        </a>
                    @endif
                </div>

                <button type="button" @click="menuOpen = !menuOpen" aria-label="Toggle menu" class="relative z-20 -m-2.5 block cursor-pointer p-2.5 lg:hidden">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden>
                        <template x-if="!menuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </template>
                        <template x-if="menuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </template>
                    </svg>
                </button>
            </div>

            <div x-show="menuOpen" x-cloak class="lg:hidden px-6 pb-6 bg-white">
                <div class="space-y-2 pt-4">
                    @foreach(($d['nav'] ?? []) as $item)
                        @if($item)
                            <a href="{{ $item['href'] ?? '/' }}" class="block rounded-lg px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-100">{{ $item['label'] ?? 'Link' }}</a>
                        @endif
                    @endforeach
                </div>
                <div class="mt-4 flex flex-col gap-2">
                    @if($d['cta1Label'] ?? false)
                        <a href="{{ $d['cta1Link'] ?? '/waitlist' }}" class="block rounded-lg border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">{{ $d['cta1Label'] }}</a>
                    @endif
                    @if($d['cta2Label'] ?? false)
                        <a href="{{ $d['cta2Link'] ?? '/#contact' }}" class="block rounded-lg px-4 py-3 text-center text-sm font-medium text-white" style="background-color: rgb(17, 17, 17);">{{ $d['cta2Label'] }}</a>
                    @endif
                </div>
            </div>
        </nav>
    </header>
</div>
