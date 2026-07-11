@php $d = $data; @endphp
@if($d['topBarEnabled'] ?? true)
<div data-block="siteTopBar" class="bg-gray-100 text-gray-600 text-xs">
    <div class="mx-auto flex h-10 max-w-7xl items-center justify-between gap-4 px-6">
        <div class="flex items-center gap-4">
            @if($d['topBarEmail'] ?? false)
                <a href="mailto:{{ $d['topBarEmail'] }}" data-edit="topBarEmail" class="hidden sm:inline-flex items-center gap-1.5 transition-colors hover:text-gray-900">
                    <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    {{ $d['topBarEmail'] }}
                </a>
            @endif
        </div>
        <div class="flex items-center gap-4">
            @if($d['promoText'] ?? false)
                <p data-edit="promoText" class="hidden sm:block text-gray-500">
                    @if($d['promoLinkUrl'] ?? false)
                        <a href="{{ $d['promoLinkUrl'] }}" data-edit="promoLinkText" class="font-medium text-brand hover:text-brand/80">{{ $d['promoLinkText'] ?? $d['promoText'] }}</a>
                    @else
                        {{ $d['promoText'] }}
                    @endif
                </p>
            @endif
            <ul class="flex items-center gap-2">
                @foreach(($d['topBarSocial'] ?? []) as $i => $s)
                    @if($s)
                        <li data-list="topBarSocial">
                            <a href="{{ $s['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" data-edit="platform" aria-label="{{ $s['platform'] ?? 'social' }}" class="inline-flex h-6 w-6 items-center justify-center rounded-full text-gray-400 transition-all hover:bg-gray-200 hover:text-gray-600">
                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9l6 6-6 6"/></svg>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
