@php $d = $data; @endphp
<section data-block="whyChooseUs" class="bg-neutral-50 py-20 md:py-28">
    <div class="mx-auto max-w-7xl px-4">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div class="overflow-hidden rounded-2xl shadow-sm">
                <img src="{{ $d['image'] ?? '' }}" data-edit="image" alt="" class="h-full w-full object-cover" />
            </div>

            <div>
                @if($d['badge'] ?? false)
                    <span data-edit="badge" class="mb-3 inline-block text-sm font-semibold uppercase tracking-widest text-primary">{{ $d['badge'] }}</span>
                @endif
                @if($d['heading'] ?? false)
                    <h2 data-edit="heading" class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl lg:text-5xl">{{ $d['heading'] }}</h2>
                @endif
                @if(($d['features'] ?? []))
                    <div data-list="features" class="mt-10 space-y-8">
                        @foreach($d['features'] as $i => $feature)
                            <div class="flex gap-5">
                                <span data-edit="features:{{ $i }}/number" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-sm font-bold text-primary">{{ $feature['number'] ?? sprintf('%02d', $i + 1) }}</span>
                                <div>
                                    <h3 data-edit="features:{{ $i }}/title" class="text-lg font-semibold text-neutral-900">{{ $feature['title'] ?? '' }}</h3>
                                    <p data-edit="features:{{ $i }}/description" class="mt-1 text-neutral-600 leading-relaxed">{{ $feature['description'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
