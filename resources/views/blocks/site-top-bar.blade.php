@php $d = $data; @endphp
@if(($d['topBarEnabled'] ?? 'true') !== 'false')
    <div data-block="siteTopBar" class="hidden md:block bg-[#2a2a2a] text-white text-sm py-2.5">
        <div class="mx-auto max-w-7xl px-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 min-w-0 flex-1 md:flex-initial">
                <svg class="w-5 h-5 text-brand shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden>
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                </svg>
                @if($d['topBarEmail'] ?? false)
                    <span class="flex items-center gap-1 min-w-0">
                        <span class="font-semibold hidden sm:inline">Email:</span>
                        <a href="mailto:{{ $d['topBarEmail'] }}" data-edit="topBarEmail" class="hover:text-brand truncate" title="{{ $d['topBarEmail'] }}">{{ $d['topBarEmail'] }}</a>
                    </span>
                @endif
            </div>

            @if($d['promoText'] ?? false)
                <div class="hidden md:block text-center">
                    <span data-edit="promoText">{{ $d['promoText'] }} </span>
                    @if($d['promoLinkText'] ?? false)
                        <a href="{{ $d['promoLinkUrl'] ?? '#' }}" data-edit="promoLinkText" class="underline hover:text-brand">{{ $d['promoLinkText'] }}</a>
                    @endif
                </div>
            @endif

            <div class="flex items-center gap-3 shrink-0">
                @foreach(($d['topBarSocial'] ?? []) as $i => $item)
                    @if($item)
                        <a href="{{ $item['url'] ?? '#' }}" data-list="topBarSocial" data-edit="platform" aria-label="{{ $item['platform'] ?? 'social' }}" class="w-7 h-7 rounded-full border border-white/30 flex items-center justify-center hover:border-brand hover:text-brand">
                            @if($item['icon'] ?? false)
                                <i class="{{ $item['icon'] }}" style="font-size: 14px"></i>
                            @endif
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif
