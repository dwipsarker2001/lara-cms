@php $d = $data; $logoHeight = $d['logoHeight'] ?? 40; @endphp
<footer data-block="footer" style="background-color: #111">
    <div class="mx-auto max-w-7xl px-6 xl:px-0">
        <div class="flex flex-col items-center border-b border-[#3d3d3d] py-16 text-center md:py-20 lg:py-24">
            <h2 data-edit="ctaHeading" class="font-semibold text-white" style="font-family: 'Switzer', sans-serif; font-size: 52px; line-height: 1.2">
                {{ $d['ctaHeading'] ?? 'Start your 7-day free trial' }}
            </h2>
            @if($d['ctaDescription'] ?? false)
                <p data-edit="ctaDescription" class="mx-auto mt-4 max-w-xl text-base font-medium text-[#808080]" style="font-family: 'Inter Display', sans-serif;">{{ $d['ctaDescription'] }}</p>
            @endif
            <form class="mt-8 flex w-full max-w-md items-center gap-3" onsubmit="event.preventDefault()">
                <input data-edit="ctaPlaceholder" type="email" placeholder="{{ $d['ctaPlaceholder'] ?? 'Enter your email' }}" class="flex-1 rounded-xl border-0 bg-[#1e1e1e] px-4 py-3.5 text-[#f6f5f4] outline-none placeholder:text-[#808080]" style="font-family: 'Inter Display', sans-serif; font-size: 15px;">
                <button data-edit="ctaButtonLabel" data-edit-button type="submit" class="rounded-xl bg-white px-6 py-3.5 text-sm font-medium text-[#111] transition hover:opacity-90" style="font-family: 'Inter Display', sans-serif;">{{ $d['ctaButtonLabel'] ?? 'Get Started' }}</button>
            </form>
            <div class="mt-6 flex items-center gap-2">
                <a href="https://support.google.com/business/thread/254152408/google-reviews?hl=en" target="_blank" rel="noopener" class="flex items-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                </a>
                <span data-edit="rating" class="text-sm font-medium text-white" style="font-family: 'Inter Display', sans-serif;">{{ $d['rating'] ?? '4.9 rating' }}</span>
                <span data-edit="ratingLabel" class="text-sm font-medium text-[#808080]" style="font-family: 'Inter Display', sans-serif;">{{ $d['ratingLabel'] ?? 'Based on 300k Users' }}</span>
            </div>
        </div>

        <div class="flex flex-col gap-10 border-b border-[#3d3d3d] py-14 md:flex-row md:justify-between lg:py-16">
            <div class="max-w-xs">
                <a href="/" class="inline-flex items-center">
                    @if($d['logo'] ?? false)
                        <img src="{{ $d['logo'] }}" alt="Logo" data-edit="logo" class="object-contain" style="height: {{ $logoHeight }}px; width: auto; border-radius: 9999px;">
                    @else
                        <span data-edit="brandName" class="text-2xl font-semibold text-white">{{ $d['brandName'] ?? 'Lara CMS' }}</span>
                    @endif
                </a>
                @if($d['tagline'] ?? false)
                    <p data-edit="tagline" class="mt-4 text-sm font-medium text-[#808080]" style="font-family: 'Inter Display', sans-serif;">{{ $d['tagline'] }}</p>
                @endif
                @if($d['email'] ?? false)
                    <a href="mailto:{{ $d['email'] }}" data-edit="email" class="mt-6 inline-flex items-center gap-2 rounded-lg border border-[#3d3d3d] px-4 py-3 text-sm font-medium text-[#808080] transition hover:text-white" style="font-family: 'Inter Display', sans-serif;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        {{ $d['email'] }}
                    </a>
                @endif
            </div>

            @foreach(($d['linkColumns'] ?? []) as $col)
                @if($col)
                    <div data-list="linkColumns">
                        <p data-edit="heading" class="mb-5 text-sm font-medium text-white" style="font-family: 'Inter Display', sans-serif;">{{ $col['heading'] ?? '' }}</p>
                        @if(count($col['links'] ?? []) > 0)
                            <ul class="space-y-3">
                                @foreach($col['links'] as $link)
                                    @if($link)
                                        <li data-list="links">
                                            <a href="{{ $link['href'] ?? '#' }}" data-edit="label" class="text-sm font-medium text-[#808080] transition hover:text-white no-underline" style="font-family: 'Inter Display', sans-serif;">{{ $link['label'] ?? '' }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            @endforeach

            <div>
                @if(($d['social'] ?? []) && count($d['social'] ?? []) > 0)
                    <p data-edit="socialHeading" class="mb-5 text-sm font-medium text-white" style="font-family: 'Inter Display', sans-serif;">{{ $d['socialHeading'] ?? 'Social' }}</p>
                    <div class="flex flex-col gap-3">
                        @foreach($d['social'] as $item)
                            @if($item)
                                <a href="{{ $item['url'] ?? '#' }}" target="_blank" rel="noopener" data-list="social" class="inline-flex items-center gap-2.5 rounded-lg bg-[#1e1e1e] px-4 py-2.5 text-sm font-medium text-[#808080] transition hover:text-white no-underline" style="font-family: 'Inter Display', sans-serif;">
                                    @if($item['icon'] ?? false)
                                        <i class="{{ $item['icon'] }}" style="font-size: 16px; width: 16px; text-align: center;"></i>
                                    @endif
                                    <span data-edit="label">{{ $item['label'] ?? '' }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="flex flex-col items-center justify-between gap-4 py-6 md:flex-row">
            <p data-edit="copyright" class="text-sm text-[#808080]" style="font-family: 'Inter Display', sans-serif;">{{ $d['copyright'] ?? 'c 2026 Lara CMS. All rights reserved.' }}</p>
            <div class="inline-flex items-center gap-2 rounded-full border border-[#3d3d3d] bg-[#1e1e1e] px-4 py-2">
                <span class="inline-block h-2.5 w-2.5 rounded-full" style="background-color: rgb(96, 201, 79); box-shadow: 0 0 0 4px rgba(96, 201, 79, 0.2);"></span>
                <span data-edit="systemsStatus" class="text-sm font-medium text-white" style="font-family: 'Inter Display', sans-serif;">{{ $d['systemsStatus'] ?? 'All Systems Operational' }}</span>
            </div>
            @if($d['privacyLabel'] ?? false)
                <a href="{{ $d['privacyLink'] ?? '#' }}" data-edit="privacyLabel" class="text-sm text-[#808080] transition hover:text-white no-underline" style="font-family: 'Inter Display', sans-serif;">{{ $d['privacyLabel'] }}</a>
            @endif
        </div>
    </div>
</footer>
