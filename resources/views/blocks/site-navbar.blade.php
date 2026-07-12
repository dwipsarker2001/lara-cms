@php $d = $data; @endphp
<div data-block="siteNavbar" class="relative z-50" x-data="{ menuOpen: false, openDropdown: null }">
    <header class="bg-white border-b border-gray-300">
        <nav>
            <div class="mx-auto max-w-7xl px-6">
                <div class="flex items-center justify-between py-6">
                    <a href="/" class="flex items-center gap-2">
                        @if($d['logo'] ?? false)
                            <img src="{{ $d['logo'] }}" alt="{{ $d['brandName'] ?? 'Logo' }}" data-edit="logo" class="object-contain" style="height: {{ $d['logoHeight'] ?? 40 }}px; width: auto" />
                        @else
                            <span data-edit="brandName" class="text-xl font-bold text-gray-900">{{ $d['brandName'] ?? 'E CMS' }}</span>
                        @endif
                    </a>

                    <div class="hidden lg:flex items-center gap-8">
                        @foreach(($d['nav'] ?? []) as $index => $item)
                            @if($item)
                                @php $dropdown = $item['dropdown'] ?? []; @endphp
                                <div data-list="nav" class="relative"
                                    @mouseenter="openDropdown = {{ $index }}"
                                    @mouseleave="openDropdown = null">
                                    <a href="{{ $item['href'] ?? '#' }}" data-edit="label" class="text-sm font-medium flex items-center gap-1 transition-colors text-black hover:text-brand">
                                        {{ $item['label'] ?? '' }}
                                        @if(count($dropdown) > 0)
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    </a>
                                    @if(count($dropdown) > 0)
                                        <div x-show="openDropdown === {{ $index }}" x-cloak class="absolute top-full left-0 z-50 mt-2 w-48 bg-white border border-gray-100 rounded-lg shadow-lg py-2">
                                            @foreach($dropdown as $dropIndex => $dropItem)
                                                @if($dropItem)
                                                    <a href="{{ $dropItem['href'] ?? '#' }}" data-list="dropdown" data-edit="label" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand">{{ $dropItem['label'] ?? '' }}</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="hidden lg:flex items-center gap-2">
                        @if($d['contactIcon'] ?? false)
                            <img src="{{ $d['contactIcon'] }}" alt="" data-edit="contactIcon" class="w-9 h-9 object-contain" />
                        @endif
                        <div class="flex flex-col text-sm leading-tight">
                            @if($d['contactLabel'] ?? false)
                                <span data-edit="contactLabel" class="text-gray-500">{{ $d['contactLabel'] }}</span>
                            @endif
                            @if($d['contactNumber'] ?? false)
                                <a href="{{ $d['contactLink'] ?? '#' }}" target="_blank" rel="noopener noreferrer" data-edit="contactNumber" class="font-semibold text-brand">{{ $d['contactNumber'] }}</a>
                            @endif
                        </div>
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

                <div x-show="menuOpen" x-cloak class="lg:hidden pb-6 border-t border-gray-100 pt-4">
                    <ul class="space-y-4">
                        @foreach(($d['nav'] ?? []) as $index => $item)
                            @if($item)
                                @php $dropdown = $item['dropdown'] ?? []; @endphp
                                <li data-list="nav">
                                    <a href="{{ $item['href'] ?? '#' }}" class="block text-sm font-medium transition-colors text-black hover:text-brand" data-edit="label">{{ $item['label'] ?? '' }}</a>
                                    @if(count($dropdown) > 0)
                                        <ul class="ml-4 mt-2 space-y-2">
                                            @foreach($dropdown as $dropIndex => $dropItem)
                                                @if($dropItem)
                                                    <li data-list="dropdown">
                                                        <a href="{{ $dropItem['href'] ?? '#' }}" class="block text-sm text-gray-600" data-edit="label">{{ $dropItem['label'] ?? '' }}</a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</div>
