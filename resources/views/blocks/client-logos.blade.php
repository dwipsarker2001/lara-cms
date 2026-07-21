@php $d = $data; $logos = $d['logos'] ?? []; $speed = $d['speed'] ?? 30; @endphp
@if(count($logos) > 0)
<section data-block="clientLogos" id="clients" class="w-full overflow-hidden" style="background: #F2F0EE; padding-top: 48px; padding-bottom: 48px; opacity: 1;" aria-label="Trusted clients">
    <div class="max-w-6xl mx-auto px-6">
        <p data-edit="caption" class="text-center" style="font-size: 14px; color: #6b7280; margin-bottom: 24px;">{{ $d['caption'] ?? 'Backed by the best' }}</p>
    </div>
    <div style="max-width: 1152px; margin: 0 auto; overflow: hidden; -webkit-mask-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 12.5%, rgba(0,0,0,1) 87.5%, rgba(0,0,0,0) 100%); mask-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 12.5%, rgba(0,0,0,1) 87.5%, rgba(0,0,0,0) 100%);">
        <style>
            @keyframes client-logo-scroll { from { transform: translateX(0); } to { transform: translateX(-33.333333%); } }
            .client-logos-track:hover { animation-play-state: paused !important; }
        </style>
        <div class="client-logos-track flex w-max flex-row" style="gap: 48px; animation: client-logo-scroll {{ $speed }}s linear infinite; will-change: transform;">
            <div class="flex flex-row shrink-0" style="gap: 48px;" aria-hidden="true">
                @foreach($logos as $logo)
                    @if($logo)
                        <div data-list="logos" data-edit="image" style="height: 32px; position: relative;">
                            <img src="{{ $logo['image'] ?? '' }}" alt="{{ $logo['alt'] ?? '' }}" style="height: 100%; width: auto;" />
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="flex flex-row shrink-0" style="gap: 48px;" aria-hidden="true">
                @foreach($logos as $logo)
                    @if($logo)
                        <div style="height: 32px; position: relative;">
                            <img src="{{ $logo['image'] ?? '' }}" alt="{{ $logo['alt'] ?? '' }}" style="height: 100%; width: auto;" />
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="flex flex-row shrink-0" style="gap: 48px;" aria-hidden="true">
                @foreach($logos as $logo)
                    @if($logo)
                        <div style="height: 32px; position: relative;">
                            <img src="{{ $logo['image'] ?? '' }}" alt="{{ $logo['alt'] ?? '' }}" style="height: 100%; width: auto;" />
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
