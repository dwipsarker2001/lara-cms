@php $d = $data; @endphp
@unless($d['topBarEnabled'] ?? true === false)
    <section data-block="siteTopBar" class="bg-neutral-900 text-white text-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2">
            <div class="flex items-center gap-4">
                @if(($d['promoText'] ?? false) || ($d['promoLinkText'] ?? false))
                    <span data-edit="promoText" class="text-neutral-300">{{ $d['promoText'] ?? '' }}</span>
                    @if($d['promoLinkText'] ?? false)
                        <a data-edit="promoLinkUrl" href="{{ $d['promoLinkUrl']['url'] ?? '#' }}" class="font-medium text-white underline underline-offset-2 hover:text-primary transition">{{ $d['promoLinkText'] }}</a>
                    @endif
                @endif
            </div>
            <div class="flex items-center gap-6">
                @if($d['topBarEmail'] ?? false)
                    <a href="mailto:{{ $d['topBarEmail'] }}" data-edit="topBarEmail" class="text-neutral-300 hover:text-white transition">{{ $d['topBarEmail'] }}</a>
                @endif
                @if(($d['topBarSocial'] ?? []))
                    <div data-list="topBarSocial" class="flex items-center gap-3">
                        @foreach($d['topBarSocial'] as $i => $social)
                            <a href="{{ $social['url']['url'] ?? '#' }}" data-edit="topBarSocial:{{ $i }}/url" class="text-neutral-400 hover:text-white transition" aria-label="{{ $social['platform'] ?? '' }}">
                                @if($social['icon'] ?? false)
                                    <img src="{{ $social['icon'] }}" alt="" class="h-4 w-4" />
                                @else
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z"/></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
@endunless
