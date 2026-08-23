@php $d = $data; @endphp
<section data-block="teamCards" class="py-20">
    <div class="max-w-6xl mx-auto px-6">
        @if($d['headline'] ?? false)
            <h2 data-edit="headline" class="text-center text-2xl md:text-3xl font-bold text-gray-900">{{ $d['headline'] }}</h2>
        @endif
        @if($d['description'] ?? false)
            <p data-edit="description" class="mx-auto mt-3 max-w-2xl text-center text-gray-500">{{ $d['description'] }}</p>
        @endif
        <div class="mt-12 flex gap-5 overflow-x-auto snap-x snap-mandatory sm:grid sm:grid-cols-2 lg:grid-cols-4 md:gap-6 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach(($d['members'] ?? []) as $i => $member)
                @if($member)
                    <div data-list="members" data-list-index="{{ $i }}" class="min-w-[260px] sm:min-w-0 snap-start rounded-2xl border-2 border-transparent">
                        <div data-edit="image" class="group relative aspect-[4/5] overflow-hidden rounded-2xl bg-gray-200">
                            @if($member['image'] ?? false)
                                <img src="{{ $member['image'] }}" alt="{{ $member['name'] ?? '' }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.03]" />
                            @endif
                            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-5 text-white">
                                @if($member['name'] ?? false)
                                    <h3 data-edit="name" class="text-base md:text-lg font-bold leading-tight">{{ $member['name'] }}</h3>
                                @endif
                                @if($member['role'] ?? false)
                                    <p data-edit="role" class="mt-0.5 text-xs md:text-sm text-white/80">{{ $member['role'] }}</p>
                                @endif
                                @if(count($member['social'] ?? []) > 0)
                                    <ul class="mt-3 flex items-center gap-2">
                                        @foreach($member['social'] as $j => $s)
                                            @if($s)
                                                <li data-list="social">
                                                    <a href="{{ $s['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" data-edit="platform" aria-label="{{ $s['platform'] ?? 'social link' }}" class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-white/15 backdrop-blur-sm text-white transition-colors hover:bg-brand hover:text-brand-foreground">
                                                        @if($s['icon'] ?? false)
                                                            <i class="{{ $s['icon'] }}" data-edit="icon" style="font-size: 12px"></i>
                                                        @endif
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
