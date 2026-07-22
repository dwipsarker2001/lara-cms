@php
    $pkg = $package ?? null;
    $pkgSections = $pkg?->blocks ?? [];
    $blockSections = collect($pkgSections)->filter(fn ($s) => ($s['enabled'] ?? true))->values();

    $heroTypes = ['packageHero', 'packageGalleryHero'];
    $bookingType = 'packageBooking';

    $heroBlocks = $blockSections->filter(fn ($s) => in_array($s['name'] ?? '', $heroTypes));
    $bookingBlock = $blockSections->firstWhere('name', $bookingType);
    $contentBlocks = $blockSections->reject(fn ($s) => in_array($s['name'] ?? '', [...$heroTypes, $bookingType]));
@endphp

@if(!$pkg)
    <div data-block="packagePostSlot" class="max-w-4xl mx-auto px-6">
        @if($data['packageListHref'] ?? false)
            <a href="{{ $data['packageListHref'] }}" class="inline-flex items-center gap-2 text-sm font-medium text-brand hover:text-brand/80 mb-8">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7 7l-7-7 7-7"/></svg>
                Back to Packages
            </a>
        @endif
    </div>
@else
    @foreach($heroBlocks as $hero)
        @include('blocks.package-detail', ['section' => $hero])
    @endforeach

    @if($contentBlocks->isNotEmpty() || $bookingBlock)
        <div class="mx-auto max-w-7xl px-6 py-8">
            <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="space-y-12 lg:col-span-2">
                    @foreach($contentBlocks as $cb)
                        @include('blocks.package-detail', ['section' => $cb])
                    @endforeach
                </div>
                @if($bookingBlock)
                    <div class="lg:col-span-1">
                        <div class="sticky top-24">
                            @include('blocks.package-detail', ['section' => $bookingBlock])
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endif
