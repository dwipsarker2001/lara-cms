@php $d = $data; @endphp
<section data-block="featureImageCards" class="bg-neutral-50 py-20 md:py-28">
    <div class="mx-auto max-w-7xl px-4 text-center">
        @if($d['heading'] ?? false)
            <h2 data-edit="heading" class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">{{ $d['heading'] }}</h2>
        @endif
        @if($d['description'] ?? false)
            <p data-edit="description" class="mx-auto mt-4 max-w-2xl text-neutral-600 leading-relaxed">{{ $d['description'] }}</p>
        @endif
        @if(($d['cards'] ?? []))
            <div data-list="cards" class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($d['cards'] as $i => $card)
                    <div class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-100 transition hover:shadow-md">
                        <div class="aspect-[4/3] w-full overflow-hidden">
                            <img src="{{ $card['image'] ?? '' }}" data-edit="cards:{{ $i }}/image" alt="{{ $card['title'] ?? '' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110" />
                        </div>
                        <div class="p-5 text-left">
                            <h3 data-edit="cards:{{ $i }}/title" class="text-lg font-semibold text-neutral-900">{{ $card['title'] ?? '' }}</h3>
                            <p data-edit="cards:{{ $i }}/description" class="mt-2 text-sm text-neutral-600 leading-relaxed">{{ $card['description'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
