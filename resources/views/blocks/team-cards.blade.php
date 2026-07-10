@php $d = $data; @endphp
<section data-block="teamCards" class="bg-white py-20 md:py-28">
    <div class="mx-auto max-w-7xl px-4 text-center">
        @if($d['heading'] ?? false)
            <h2 data-edit="heading" class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">{{ $d['heading'] }}</h2>
        @endif
        @if($d['description'] ?? false)
            <p data-edit="description" class="mx-auto mt-4 max-w-2xl text-neutral-600 leading-relaxed">{{ $d['description'] }}</p>
        @endif
        @if(($d['members'] ?? []))
            <div data-list="members" class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($d['members'] as $i => $member)
                    <div class="group text-center">
                        <div class="mx-auto mb-4 h-40 w-40 overflow-hidden rounded-full shadow-sm ring-4 ring-neutral-50 transition group-hover:ring-primary">
                            <img src="{{ $member['image'] ?? '' }}" data-edit="members:{{ $i }}/image" alt="{{ $member['name'] ?? '' }}" class="h-full w-full object-cover" />
                        </div>
                        <h3 data-edit="members:{{ $i }}/name" class="text-lg font-semibold text-neutral-900">{{ $member['name'] ?? '' }}</h3>
                        <p data-edit="members:{{ $i }}/role" class="mt-1 text-sm text-neutral-500">{{ $member['role'] ?? '' }}</p>
                        @if(($member['social'] ?? []))
                            <div data-list="members:{{ $i }}/social" class="mt-3 flex items-center justify-center gap-2">
                                @foreach($member['social'] as $s => $social)
                                    <a href="{{ $social['url']['url'] ?? '#' }}" data-edit="members:{{ $i }}/social:{{ $s }}/url" class="flex h-8 w-8 items-center justify-center rounded-full bg-neutral-100 text-neutral-500 hover:bg-primary hover:text-white transition">
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
                @endforeach
            </div>
        @endif
    </div>
</section>
