@php $d = $data; @endphp
<section data-block="contact" class="bg-white py-20 md:py-28">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mx-auto max-w-2xl text-center">
            @if($d['heading'] ?? false)
                <h2 data-edit="heading" class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">{{ $d['heading'] }}</h2>
            @endif
            @if($d['subheading'] ?? false)
                <p data-edit="subheading" class="mt-4 text-neutral-600 leading-relaxed">{{ $d['subheading'] }}</p>
            @endif
        </div>

        @if($d['mapEmbed'] ?? false)
            <div data-edit="mapEmbed" class="mt-10 overflow-hidden rounded-2xl shadow-sm ring-1 ring-neutral-100">
                <iframe src="{{ $d['mapEmbed'] }}" width="100%" height="400" style="border:0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        @endif

        @if(($d['infoCards'] ?? []))
            <div data-list="infoCards" class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach($d['infoCards'] as $i => $card)
                    <div class="rounded-2xl bg-neutral-50 p-8 text-center shadow-sm ring-1 ring-neutral-100">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10 text-primary">
                            @if($card['icon'] ?? false)
                                <img src="{{ $card['icon'] }}" data-edit="infoCards:{{ $i }}/icon" alt="" class="h-7 w-7" />
                            @else
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            @endif
                        </div>
                        <h3 data-edit="infoCards:{{ $i }}/title" class="text-lg font-semibold text-neutral-900">{{ $card['title'] ?? '' }}</h3>
                        <p data-edit="infoCards:{{ $i }}/description" class="mt-1 text-sm text-neutral-500">{{ $card['description'] ?? '' }}</p>
                        <p data-edit="infoCards:{{ $i }}/value" class="mt-3 font-medium text-primary">{{ $card['value'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
