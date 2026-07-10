@php $d = $data; @endphp
<section data-block="aboutIntro" class="bg-white py-20 md:py-28">
    <div class="mx-auto max-w-7xl px-4">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div class="relative grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <div data-edit="image1" class="aspect-[4/3] w-full rounded-2xl bg-neutral-200 bg-cover bg-center" style="background-image: url('{{ $d['image1'] ?? '' }}')"></div>
                </div>
                <div data-edit="image2" class="aspect-square w-full rounded-2xl bg-neutral-200 bg-cover bg-center" style="background-image: url('{{ $d['image2'] ?? '' }}')"></div>
                <div class="relative">
                    <div data-edit="image3" class="aspect-square w-full rounded-2xl bg-neutral-200 bg-cover bg-center" style="background-image: url('{{ $d['image3'] ?? '' }}')"></div>
                    <div class="absolute -bottom-4 -left-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-primary text-white shadow-lg">
                        @if($d['badgeIcon'] ?? false)
                            <img src="{{ $d['badgeIcon'] }}" data-edit="badgeIcon" alt="" class="h-8 w-8" />
                        @else
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <span data-edit="badge" class="mb-3 inline-block text-sm font-semibold uppercase tracking-widest text-primary">{{ $d['badge'] ?? 'About Us' }}</span>
                <h2 data-edit="heading" class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl lg:text-5xl">{{ $d['heading'] ?? '' }}</h2>
                <p data-edit="subheading" class="mt-4 text-lg font-medium text-primary">{{ $d['subheading'] ?? '' }}</p>
                <div data-edit="body" class="mt-6 space-y-4 text-neutral-600 leading-relaxed">
                    @foreach(($d['body'] ?? []) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
                @if($d['signatureImage'] ?? false)
                    <div class="mt-8 flex items-center gap-4">
                        <img src="{{ $d['signatureImage'] }}" data-edit="signatureImage" alt="Signature" class="h-12 w-auto" />
                        <div>
                            <p data-edit="signerName" class="font-semibold text-neutral-900">{{ $d['signerName'] ?? '' }}</p>
                            <p data-edit="signerTitle" class="text-sm text-neutral-500">{{ $d['signerTitle'] ?? '' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
