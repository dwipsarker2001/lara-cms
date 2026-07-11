@php $d = $data; @endphp
<footer data-block="siteFooter" class="w-full bg-gray-900 text-gray-300 mt-auto">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:py-20">
        @if($d['bannerImage'] ?? false)
            <div class="relative w-full h-48 overflow-hidden rounded-2xl mb-12 bg-gray-800">
                <img src="{{ $d['bannerImage'] }}" alt="" data-edit="bannerImage" class="absolute inset-0 w-full h-full object-cover" />
            </div>
        @endif
        <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-12 lg:gap-8">
            <div class="lg:col-span-4">
                <a href="/" class="flex items-center gap-2 text-xl font-extrabold tracking-tight text-white">
                    @if($d['logo'] ?? false)
                        <img src="{{ $d['logo'] }}" alt="" data-edit="logo" class="object-contain" style="height:{{ $d['logoHeight'] ?? 40 }}px;width:auto" />
                    @endif
                    {{ $d['brandName'] ?? 'Brand' }}
                </a>
                @if($d['description'] ?? false)
                    <p data-edit="description" class="mt-4 text-sm leading-relaxed text-gray-400">{{ $d['description'] }}</p>
                @endif
                @if(count($d['social'] ?? []) > 0)
                    <div class="mt-6">
                        @if($d['socialHeading'] ?? false)
                            <p data-edit="socialHeading" class="text-sm font-medium text-gray-400 mb-3">{{ $d['socialHeading'] }}</p>
                        @endif
                        <ul class="flex items-center gap-3">
                            @foreach($d['social'] as $i => $s)
                                @if($s)
                                    <li data-list="social">
                                        <a href="{{ $s['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" data-edit="label" aria-label="{{ $s['label'] ?? 'social' }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gray-800 text-gray-400 transition-all hover:bg-brand hover:text-white">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9l6 6-6 6"/></svg>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            @foreach(($d['linkColumns'] ?? []) as $ci => $col)
                @if($col)
                    <div data-list="linkColumns" class="lg:col-span-2">
                        @if($col['heading'] ?? false)
                            <h3 data-edit="heading" class="text-sm font-bold text-white uppercase tracking-wider">{{ $col['heading'] }}</h3>
                        @endif
                        @if(count($col['links'] ?? []) > 0)
                            <ul class="mt-5 space-y-3">
                                @foreach($col['links'] as $li => $link)
                                    @if($link)
                                        <li data-list="links">
                                            <a href="{{ $link['href'] ?? '#' }}" data-edit="label" class="text-sm text-gray-400 transition-colors hover:text-white">{{ $link['label'] ?? 'Link' }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
        <div class="mt-16 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-800 pt-8 text-xs text-gray-500">
            @if($d['copyright'] ?? false)
                <p data-edit="copyright">{{ $d['copyright'] }}</p>
            @elseif($d['copyrightBrand'] ?? false)
                <p>&copy; {{ date('Y') }} {{ $d['copyrightBrand'] }}. All rights reserved.</p>
            @endif
            @if(count($d['legalLinks'] ?? []) > 0)
                <ul class="flex items-center gap-5">
                    @foreach($d['legalLinks'] as $i => $ll)
                        @if($ll)
                            <li data-list="legalLinks">
                                <a href="{{ $ll['href'] ?? '#' }}" data-edit="label" class="transition-colors hover:text-gray-300">{{ $ll['label'] ?? '' }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
            @if($d['languageCurrency'] ?? false)
                <p data-edit="languageCurrency" class="text-gray-500">{{ $d['languageCurrency'] }}</p>
            @endif
        </div>
        @if(($d['email'] ?? false) || ($d['phone'] ?? false))
            <div class="mt-4 flex items-center justify-center gap-6 text-xs text-gray-500">
                @if($d['email'] ?? false)
                    <a href="mailto:{{ $d['email'] }}" data-edit="email" class="hover:text-gray-300 transition-colors">{{ $d['email'] }}</a>
                @endif
                @if($d['phone'] ?? false)
                    <a href="tel:{{ $d['phone'] }}" data-edit="phone" class="hover:text-gray-300 transition-colors">{{ $d['phone'] }}</a>
                @endif
            </div>
        @endif
    </div>
</footer>
