@php $d = $data; @endphp
<footer data-block="siteFooter">
    @if($d['bannerImage'] ?? false)
        <div data-edit="bannerImage" class="h-48 w-full bg-cover bg-center" style="background-image: url('{{ $d['bannerImage'] }}')"></div>
    @endif

    <div class="bg-neutral-900 text-neutral-300">
        <div class="mx-auto max-w-7xl px-4 py-16">
            <div class="grid gap-12 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <div class="mb-4 flex items-center gap-3">
                        @if($d['logo'] ?? false)
                            <img src="{{ $d['logo'] }}" alt="{{ $d['brandName'] ?? '' }}" data-edit="logo" style="height: {{ $d['logoHeight'] ?? 40 }}px" class="w-auto brightness-0 invert" />
                        @else
                            <span data-edit="brandName" class="text-xl font-bold text-white">{{ $d['brandName'] ?? 'E CMS' }}</span>
                        @endif
                    </div>
                    <p data-edit="description" class="mb-6 text-sm leading-relaxed text-neutral-400">{{ $d['description'] ?? '' }}</p>
                    <div class="space-y-2 text-sm">
                        @if($d['email'] ?? false)
                            <p><a href="mailto:{{ $d['email'] }}" data-edit="email" class="text-neutral-400 hover:text-white transition">{{ $d['email'] }}</a></p>
                        @endif
                        @if($d['phone'] ?? false)
                            <p><a href="tel:{{ $d['phone'] }}" data-edit="phone" class="text-neutral-400 hover:text-white transition">{{ $d['phone'] }}</a></p>
                        @endif
                    </div>
                </div>

                @foreach($d['linkColumns'] ?? [] as $col => $column)
                    <div data-list="linkColumns">
                        <h3 data-edit="linkColumns:{{ $col }}/heading" class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">{{ $column['heading'] ?? '' }}</h3>
                        <ul class="space-y-3 text-sm">
                            @foreach($column['links'] ?? [] as $link => $lnk)
                                <li>
                                    <a href="{{ $lnk['href']['url'] ?? '#' }}" data-edit="linkColumns:{{ $col }}/links:{{ $link }}/href" class="text-neutral-400 hover:text-white transition">{{ $lnk['label'] ?? '' }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            @if(($d['social'] ?? []))
                <div class="mt-12 border-t border-neutral-800 pt-8">
                    <h3 data-edit="socialHeading" class="mb-4 text-sm font-semibold text-white">{{ $d['socialHeading'] ?? 'Connect with us' }}</h3>
                    <div data-list="social" class="flex items-center gap-4">
                        @foreach($d['social'] as $s => $social)
                            <a href="{{ $social['url']['url'] ?? '#' }}" data-edit="social:{{ $s }}/url" class="flex h-10 w-10 items-center justify-center rounded-full bg-neutral-800 text-neutral-400 hover:bg-primary hover:text-white transition" aria-label="{{ $social['label'] ?? '' }}">
                                @if($social['icon'] ?? false)
                                    <img src="{{ $social['icon'] }}" alt="" class="h-5 w-5" />
                                @else
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z"/></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="border-t border-neutral-800">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-6 text-sm text-neutral-500 md:flex-row">
                <p>&copy; {{ date('Y') }} <span data-edit="copyrightBrand">{{ $d['copyrightBrand'] ?? 'E CMS' }}</span>. <span data-edit="copyright">{{ $d['copyright'] ?? 'All rights reserved.' }}</span></p>
                <div class="flex items-center gap-6">
                    @foreach($d['legalLinks'] ?? [] as $l => $link)
                        <a href="{{ $link['href']['url'] ?? '#' }}" data-edit="legalLinks:{{ $l }}/href" class="hover:text-white transition">{{ $link['label'] ?? '' }}</a>
                    @endforeach
                    <span data-edit="languageCurrency" class="rounded-full border border-neutral-700 px-3 py-1 text-xs">{{ $d['languageCurrency'] ?? 'EN / USD' }}</span>
                </div>
            </div>
        </div>
    </div>
</footer>
