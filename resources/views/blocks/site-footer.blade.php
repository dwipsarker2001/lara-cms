@php $d = $data; @endphp
<footer data-block="siteFooter" class="relative text-white" style="background-color: #070b18">
    @if($d['bannerImage'] ?? false)
        <div data-edit="bannerImage" class="relative w-full overflow-hidden">
            <div class="relative h-[220px] w-full md:h-[320px] lg:h-[360px]">
                <img src="{{ $d['bannerImage'] }}" alt="" class="absolute inset-0 w-full h-full object-cover object-center" />
                <div class="pointer-events-none absolute inset-x-0 top-0 h-32" style="background: linear-gradient(to bottom, rgba(255,255,255,0.85) 0%, rgba(255,255,255,0) 100%)"></div>
                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-2/3" style="background: linear-gradient(to bottom, rgba(7,11,24,0) 0%, rgba(7,11,24,0.55) 45%, #070b18 100%)"></div>
            </div>
        </div>
    @endif
    <div class="mx-auto max-w-6xl px-6 pb-10 pt-16 md:pt-20 lg:pt-24">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-7 lg:gap-8">
            <div class="md:col-span-2 lg:col-span-2">
                <a href="/" class="inline-flex items-center">
                    @if($d['logo'] ?? false)
                        <img src="{{ $d['logo'] }}" alt="{{ $d['brandName'] ?? 'Logo' }}" data-edit="logo" class="object-contain" style="height: {{ $d['logoHeight'] ?? 40 }}px; width: auto" />
                    @else
                        <span data-edit="brandName" class="text-4xl font-extrabold lowercase tracking-tight text-brand">{{ $d['brandName'] ?? 'E CMS' }}</span>
                    @endif
                </a>
                @if($d['description'] ?? false)
                    <p data-edit="description" class="mt-4 max-w-sm text-[15px] leading-7 text-white/75 lg:mt-6">{{ $d['description'] }}</p>
                @endif
                <ul class="mt-5 space-y-3 text-[15px] text-white/85 lg:mt-7 lg:space-y-4">
                    @if($d['email'] ?? false)
                        <li class="flex items-center gap-3">
                            <span class="font-semibold uppercase">E.</span>
                            <a href="mailto:{{ $d['email'] }}" data-edit="email" class="transition-colors hover:text-brand">{{ $d['email'] }}</a>
                        </li>
                    @endif
                    @if($d['phone'] ?? false)
                        <li class="flex items-center gap-3">
                            <span class="font-semibold uppercase">P.</span>
                            <a href="tel:{{ preg_replace('/\s+/', '', $d['phone']) }}" data-edit="phone" class="transition-colors hover:text-brand">{{ $d['phone'] }}</a>
                        </li>
                    @endif
                </ul>
            </div>
            @foreach(($d['linkColumns'] ?? []) as $i => $col)
                @if($col)
                    <div data-list="linkColumns" class="lg:col-span-1">
                        <h4 data-edit="heading" class="mb-4 text-base font-semibold uppercase tracking-wide text-white lg:mb-6">{{ $col['heading'] ?? '' }}</h4>
                        @if(count($col['links'] ?? []) > 0)
                            <ul class="space-y-3 lg:space-y-4">
                                @foreach($col['links'] as $j => $link)
                                    @if($link)
                                        <li data-list="links">
                                            <a href="{{ $link['href'] ?? '#' }}" data-edit="label" class="text-[15px] text-white/80 transition-colors hover:text-brand">{{ $link['label'] ?? 'Link' }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            @endforeach
            <div class="md:col-span-2 lg:col-span-1">
                @if($d['socialHeading'] ?? false)
                    <h4 data-edit="socialHeading" class="mb-4 text-base font-semibold uppercase tracking-wide text-white lg:mb-6">{{ $d['socialHeading'] }}</h4>
                @endif
                <ul class="flex flex-wrap gap-3 lg:block lg:space-y-4">
                    @foreach(($d['social'] ?? []) as $idx => $item)
                        @if($item)
                            <li data-list="social">
                                <a href="{{ $item['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" data-edit="label" class="group inline-flex items-center justify-center gap-3 text-[15px] text-white/85 transition-colors hover:text-brand lg:inline-flex">
                                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-white/25 text-white transition-colors group-hover:border-brand group-hover:text-brand">
                                        @if($item['icon'] ?? false)
                                            <i class="{{ $item['icon'] }}" style="font-size: 14px"></i>
                                        @endif
                                    </span>
                                    <span>{{ $item['label'] ?? '' }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="mt-10 border-t border-white/10 lg:mt-14"></div>
        <div class="flex flex-col items-center justify-between gap-4 pt-4 md:flex-row md:pt-6">
            <div class="flex flex-col items-center gap-2 md:flex-row md:gap-6">
                <p class="text-[15px] text-white/70">
                    @if($d['copyrightBrand'] ?? false)
                        <span data-edit="copyrightBrand" class="font-semibold text-brand">{{ $d['copyrightBrand'] }}</span>
                    @endif
                    @if(($d['copyrightBrand'] ?? false) && ($d['copyright'] ?? false))
                        {{ ' ' }}
                    @endif
                    @if($d['copyright'] ?? false)
                        <span data-edit="copyright">{{ $d['copyright'] }}</span>
                    @endif
                </p>
                @if(count($d['legalLinks'] ?? []) > 0)
                    <ul class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-[13px] text-white/60">
                        @foreach($d['legalLinks'] as $i => $link)
                            @if($link)
                                <li data-list="legalLinks">
                                    <a href="{{ $link['href'] ?? '#' }}" data-edit="label" class="transition-colors hover:text-brand">{{ $link['label'] ?? '' }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>
            @if($d['languageCurrency'] ?? false)
                <button type="button" data-edit="languageCurrency" data-edit-button class="inline-flex items-center gap-2 rounded-full border border-white/40 px-5 py-2 text-sm font-medium text-white transition-colors hover:border-white hover:text-brand">
                    <span>{{ $d['languageCurrency'] }}</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
</footer>
