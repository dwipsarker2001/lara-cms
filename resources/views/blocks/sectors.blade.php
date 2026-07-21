@php $d = $data; @endphp
<section data-block="sectors" class="bg-[#F4F2F1] py-20">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex flex-col items-center mb-16">
            @if($d['badgeText'] ?? false)
                <span data-edit="badgeText" class="px-3 py-1 rounded-full border border-gray-200 text-[10px] font-semibold uppercase tracking-wider text-gray-500 bg-white shadow-sm mb-6">{{ $d['badgeText'] }}</span>
            @endif
            @if($d['headline'] ?? false)
                <h2 data-edit="headline" class="mx-auto max-w-3xl text-center text-3xl md:text-4xl lg:text-5xl font-semibold tracking-tight text-gray-900" style="font-family: 'Switzer', sans-serif; line-height: 1.2;">{{ $d['headline'] }}</h2>
            @endif
        </div>

        <div class="relative mt-12" x-data="{ activeTab: 0 }">
            <div style="position: absolute; top: -100px; left: 16px; pointer-events: none;" class="hidden sm:block md:left-20">
                <img src="/instights.svg" alt="" class="opacity-80">
            </div>
            <div role="tablist" aria-label="Sector categories" class="mx-auto flex flex-wrap items-center justify-center gap-4 py-4">
                @foreach(($d['sectors'] ?? []) as $i => $s)
                    @if($s)
                        <button type="button" role="tab" data-list="sectors"
                            :aria-selected="activeTab === {{ $i }}"
                            @click="activeTab = {{ $i }}"
                            class="flex items-center gap-3 rounded-lg px-6 py-4 text-sm font-semibold transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand/40"
                            :class="activeTab === {{ $i }}
                                ? 'border-2 border-transparent bg-origin-border scale-[1.02]'
                                : 'bg-gray-50 ring-1 ring-black/5 text-gray-500 hover:bg-white hover:text-gray-900 hover:shadow-md'"
                            :style="activeTab === {{ $i }} ? { backgroundImage: 'linear-gradient(#fff,#fff),linear-gradient(to right,#9333ea,#2563eb)', backgroundClip: 'padding-box,border-box' } : {}">
                            @if($s['icon'] ?? false)
                                <i class="{{ $s['icon'] }}" :style="{ color: activeTab === {{ $i }} ? '{{ $s['iconColor'] ?? '#F87171' }}' : '#9CA3AF' }" style="font-size: 20px; width: 20px; text-align: center;"></i>
                            @endif
                            <span data-edit="tabName" :class="activeTab === {{ $i }} ? 'text-gray-900' : ''">{{ $s['tabName'] ?? 'Tab' }}</span>
                        </button>
                    @endif
                @endforeach
            </div>

            @foreach(($d['sectors'] ?? []) as $i => $s)
                @if($s)
                    <div x-show="activeTab === {{ $i }}" x-cloak class="mt-8">
                        <div role="tabpanel" class="mx-auto max-w-5xl rounded-xl bg-white p-10 ring-1 ring-black/5 md:p-16">
                            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:gap-20">
                                <div class="flex flex-col items-center text-center md:items-start md:text-left">
                                    @if($s['panelTitle'] ?? false)
                                        <h3 data-edit="panelTitle" class="text-2xl font-semibold leading-tight text-gray-900 md:text-3xl">{{ $s['panelTitle'] }}</h3>
                                    @endif
                                    @if($d['ctaText'] ?? false)
                                        <div class="mt-8">
                                            <a href="{{ $d['ctaUrl'] ?? '#' }}" data-edit="ctaText" data-edit-button class="group inline-flex items-center gap-2 rounded-lg border border-gray-900 px-6 py-3 text-sm font-semibold text-gray-900 transition-all duration-200 hover:bg-gray-900 hover:text-white active:scale-95">
                                                {{ $d['ctaText'] }}
                                                <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-8">
                                    @if($s['stat1Headline'] ?? false)
                                        <div class="flex flex-col gap-2">
                                            <h4 data-edit="stat1Headline" class="text-lg font-semibold text-gray-900 md:text-xl">{{ $s['stat1Headline'] }}</h4>
                                            @if($s['stat1Description'] ?? false)
                                                <p data-edit="stat1Description" class="text-sm leading-relaxed text-gray-500">{{ $s['stat1Description'] }}</p>
                                            @endif
                                        </div>
                                    @endif
                                    @if(($s['stat1Headline'] ?? false) && ($s['stat2Headline'] ?? false))
                                        <hr class="border-gray-100">
                                    @endif
                                    @if($s['stat2Headline'] ?? false)
                                        <div class="flex flex-col gap-2">
                                            <h4 data-edit="stat2Headline" class="text-lg font-semibold text-gray-900 md:text-xl">{{ $s['stat2Headline'] }}</h4>
                                            @if($s['stat2Description'] ?? false)
                                                <p data-edit="stat2Description" class="text-sm leading-relaxed text-gray-500">{{ $s['stat2Description'] }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        @if(($d['footerFeatures'] ?? []) && count($d['footerFeatures'] ?? []) > 0)
            <div class="mt-16 flex flex-wrap justify-center gap-x-10 gap-y-6 px-6">
                @foreach($d['footerFeatures'] as $feature)
                    @if($feature)
                        <div data-list="footerFeatures" class="flex items-center gap-3">
                            <div style="padding: 2px; border-radius: 8px; overflow: hidden; background: linear-gradient(transparent,transparent),linear-gradient(to bottom right,#F97316,#EC4899,#8B5CF6); background-origin: padding-box,border-box; background-clip: padding-box,border-box;">
                                <div style="width: 20px; height: 20px; border-radius: 4px; background: #111; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    <svg style="width: 14px; height: 14px; color: white; stroke-width: 3;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                            <span data-edit="text" class="text-sm font-semibold text-gray-700">{{ $feature['text'] ?? 'Feature' }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>
