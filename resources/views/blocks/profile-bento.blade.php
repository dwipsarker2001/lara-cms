@php $d = $data; @endphp
<section data-block="profileBento" class="bg-neutral-50 py-20 md:py-28">
    <div class="mx-auto max-w-6xl px-4">
        <div class="grid gap-4 md:grid-cols-4 md:grid-rows-3">
            <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-100 md:col-span-2 md:row-span-2">
                <div class="flex flex-col items-center gap-4 sm:flex-row">
                    <div class="h-24 w-24 shrink-0 overflow-hidden rounded-2xl">
                        <img src="{{ $d['profileImage'] ?? '' }}" data-edit="profileImage" alt="" class="h-full w-full object-cover" />
                    </div>
                    <div>
                        <h2 data-edit="name" class="text-2xl font-bold text-neutral-900">{{ $d['name'] ?? 'John Doe' }}</h2>
                        <p data-edit="role" class="text-neutral-500">{{ $d['role'] ?? '' }}</p>
                        @if($d['status'] ?? false)
                            <span data-edit="status" class="mt-2 inline-block rounded-full bg-green-100 px-3 py-0.5 text-xs font-medium text-green-700">{{ $d['status'] }}</span>
                        @endif
                    </div>
                </div>
                @if(($d['socialLinks'] ?? []))
                    <div data-list="socialLinks" class="mt-5 flex items-center gap-3 border-t border-neutral-100 pt-5">
                        @foreach($d['socialLinks'] as $s => $link)
                            <a href="{{ $link['url']['url'] ?? '#' }}" data-edit="socialLinks:{{ $s }}/url" class="flex h-10 w-10 items-center justify-center rounded-xl bg-neutral-100 text-neutral-600 hover:bg-primary hover:text-white transition">
                                @if($link['icon'] ?? false)
                                    <img src="{{ $link['icon'] }}" alt="" class="h-5 w-5" />
                                @else
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z"/></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="overflow-hidden rounded-2xl shadow-sm ring-1 ring-neutral-100 md:col-span-2 md:row-span-1">
                <img src="{{ $d['decorativeImage1'] ?? '' }}" data-edit="decorativeImage1" alt="" class="h-full w-full object-cover" />
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-100">
                <div class="flex h-full flex-col justify-center">
                    @if(($d['stats'] ?? []))
                        <div data-list="stats" class="space-y-4">
                            @foreach($d['stats'] as $t => $stat)
                                <div class="flex items-center gap-3">
                                    @if($stat['icon'] ?? false)
                                        <img src="{{ $stat['icon'] }}" data-edit="stats:{{ $t }}/icon" alt="" class="h-6 w-6 text-primary" />
                                    @endif
                                    <div>
                                        <p data-edit="stats:{{ $t }}/count" class="text-xl font-bold text-neutral-900">{{ $stat['count'] ?? '0' }}</p>
                                        <p data-edit="stats:{{ $t }}/handle" class="text-xs text-neutral-500">{{ $stat['handle'] ?? '' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-100 md:col-span-1 md:row-span-1">
                <div class="flex h-full flex-col items-center justify-center text-center">
                    <p data-edit="quote" class="text-sm italic leading-relaxed text-neutral-600">"{{ $d['quote'] ?? '' }}"</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl shadow-sm ring-1 ring-neutral-100 md:col-span-1 md:row-span-2">
                <img src="{{ $d['decorativeImage2'] ?? '' }}" data-edit="decorativeImage2" alt="" class="h-full w-full object-cover" />
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral-100 md:col-span-2 md:row-span-1">
                <div class="flex h-full flex-col justify-center">
                    <h3 class="mb-2 text-sm font-semibold uppercase tracking-wider text-neutral-500">About</h3>
                    <p data-edit="about" class="text-neutral-700 leading-relaxed">{{ $d['about'] ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
