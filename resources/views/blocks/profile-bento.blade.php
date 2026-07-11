@php $d = $data; @endphp
@php
    $social = $d['profileSocial'] ?? [];
    $stats = $d['stats'] ?? [];
    $stat = fn($i) => $stats[$i] ?? ['count' => '0', 'handle' => ''];
@endphp
<section data-block="profileBento">
    <div class="max-w-6xl mx-auto px-6 space-y-4 md:space-y-5">
        {{-- TOP --}}
        <div class="grid grid-cols-1 gap-4 md:gap-5 lg:grid-cols-6">
            {{-- Profile card --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl bg-gray-100/80 p-6 md:p-7 transition-shadow hover:shadow-sm flex h-full flex-col">
                    <div class="flex items-start justify-between gap-4">
                        <div data-edit="profileImage" class="relative h-20 w-20 shrink-0 overflow-hidden rounded-full bg-gray-200">
                            @if($d['profileImage'] ?? false)
                                <img src="{{ $d['profileImage'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                            @endif
                        </div>
                        @if($d['profileStatus'] ?? false)
                            <span data-edit="profileStatus" class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600 shadow-sm">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                {{ $d['profileStatus'] }}
                            </span>
                        @endif
                    </div>
                    @if($d['profileName'] ?? false)
                        <h3 data-edit="profileName" class="mt-6 text-2xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $d['profileName'] }}</h3>
                    @endif
                    @if($d['profileRole'] ?? false)
                        <p data-edit="profileRole" class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $d['profileRole'] }}</p>
                    @endif
                    @if(count($social) > 0)
                        <ul class="mt-auto flex flex-wrap items-center gap-2 pt-6">
                            @foreach($social as $i => $s)
                                @if($s)
                                    <li data-list="profileSocial">
                                        <a href="{{ $s['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" data-edit="platform" aria-label="{{ $s['platform'] ?? 'social link' }}" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:border-brand hover:bg-brand hover:text-brand-foreground">
                                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9l6 6-6 6"/></svg>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            {{-- About + stats --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-5 lg:col-span-2">
                <div class="sm:col-span-2">
                    <div class="rounded-2xl bg-gray-100/80 p-6 md:p-7 transition-shadow hover:shadow-sm flex h-full items-start">
                        @if($d['aboutText'] ?? false)
                            <p data-edit="aboutText" class="text-sm md:text-base text-gray-600 leading-relaxed">{{ $d['aboutText'] }}</p>
                        @endif
                    </div>
                </div>
                @for($i = 0; $i < 2; $i++)
                    @php $s = $stat($i); @endphp
                    <div data-list="stats" class="rounded-2xl bg-gray-100/80 p-6 md:p-7 transition-shadow hover:shadow-sm flex h-full flex-col border-2 border-transparent">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-brand text-brand-foreground shadow-sm" aria-hidden="true">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9l6 6-6 6"/></svg>
                        </div>
                        <p data-edit="count" class="mt-6 text-base font-bold text-gray-900">{{ $s['count'] ?? '0' }} Followers</p>
                        @if($s['handle'] ?? false)
                            <p data-edit="handle" class="mt-1 text-xs text-gray-400">{{ $s['handle'] }}</p>
                        @endif
                    </div>
                @endfor
            </div>
            {{-- Top right image --}}
            <div class="lg:col-span-2">
                <div data-edit="imageTopRight" class="relative h-full min-h-[260px] overflow-hidden rounded-2xl bg-gray-200">
                    @if($d['imageTopRight'] ?? false)
                        <img src="{{ $d['imageTopRight'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                    @endif
                </div>
            </div>
        </div>
        {{-- BOTTOM --}}
        <div class="grid grid-cols-1 gap-4 md:gap-5 lg:grid-cols-6">
            {{-- Bottom left image --}}
            <div class="lg:col-span-2">
                <div data-edit="imageBottomLeft" class="relative h-full min-h-[260px] overflow-hidden rounded-2xl bg-gray-200">
                    @if($d['imageBottomLeft'] ?? false)
                        <img src="{{ $d['imageBottomLeft'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                    @endif
                </div>
            </div>
            {{-- Quote --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl bg-gray-100/80 p-6 md:p-7 transition-shadow hover:shadow-sm flex h-full items-start">
                    @if($d['quoteText'] ?? false)
                        <p data-edit="quoteText" class="text-sm md:text-base text-gray-600 leading-relaxed">{{ $d['quoteText'] }}</p>
                    @endif
                </div>
            </div>
            {{-- Bottom stats --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-5 lg:col-span-2">
                @for($i = 2; $i < 4; $i++)
                    @php $s = $stat($i); @endphp
                    <div data-list="stats" class="rounded-2xl bg-gray-100/80 p-6 md:p-7 transition-shadow hover:shadow-sm flex h-full flex-col border-2 border-transparent">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-brand text-brand-foreground shadow-sm" aria-hidden="true">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9l6 6-6 6"/></svg>
                        </div>
                        <p data-edit="count" class="mt-6 text-base font-bold text-gray-900">{{ $s['count'] ?? '0' }} Followers</p>
                        @if($s['handle'] ?? false)
                            <p data-edit="handle" class="mt-1 text-xs text-gray-400">{{ $s['handle'] }}</p>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>
</section>
