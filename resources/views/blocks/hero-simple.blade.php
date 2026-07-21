@php $d = $data; $ctaUrl = $d['ctaUrl'] ?? '#'; $rating = $d['rating'] ?? '4.9'; @endphp
<section id="hero" class="relative pt-[160px] pb-[60px] px-10 flex items-center justify-center overflow-hidden" style="min-height: 100vh;">
    <div class="pointer-events-none absolute inset-0 z-0" style="background: #f4f2f1;">
        <div class="absolute top-0 left-0" style="width: 378px; height: 571px;">
            <div class="absolute" style="width: 420px; height: 571px; top: 0; left: -42px;">
                <div class="absolute rounded-full" style="width: 207px; height: 207px; background: rgb(255,47,47); filter: blur(100px); top: 80px; left: 9px;"></div>
                <div class="absolute rounded-full" style="width: 207px; height: 207px; background: rgb(239,123,22); filter: blur(100px); top: 0; left: 213px;"></div>
                <div class="absolute rounded-full" style="width: 207px; height: 207px; background: rgb(138,67,225); filter: blur(100px); top: 207px; left: 0;"></div>
                <div class="absolute rounded-full" style="width: 207px; height: 207px; background: rgb(213,17,253); filter: blur(100px); top: 363px; left: 37px;"></div>
            </div>
        </div>
        <div class="absolute" style="width: 378px; height: 571px; top: 0; right: 0; transform: scale(-1, -1);">
            <div class="absolute" style="width: 420px; height: 571px; top: 0; left: -42px;">
                <div class="absolute rounded-full" style="width: 207px; height: 207px; background: rgb(255,47,47); filter: blur(100px); top: 80px; left: 9px;"></div>
                <div class="absolute rounded-full" style="width: 207px; height: 207px; background: rgb(239,123,22); filter: blur(100px); top: 0; left: 213px;"></div>
                <div class="absolute rounded-full" style="width: 207px; height: 207px; background: rgb(138,67,225); filter: blur(100px); top: 207px; left: 0;"></div>
                <div class="absolute rounded-full" style="width: 207px; height: 207px; background: rgb(213,17,253); filter: blur(100px); top: 363px; left: 37px;"></div>
            </div>
        </div>
        <div class="absolute top-0 left-0 right-0" style="height: 415px; background: linear-gradient(rgb(242,240,238) 0%, rgba(242,240,238,0) 100%);"></div>
        <div class="absolute inset-0 flex flex-row">
            @foreach(range(1, 24) as $i)
                <div class="h-full shrink-0" style="width: 66.25px; background: linear-gradient(270deg, rgba(242,240,238,0.2) 0%, rgba(242,240,238,0) 100%);"></div>
            @endforeach
        </div>
        <div class="absolute inset-0" style="background: linear-gradient(rgba(242,240,238,0) 0%, rgb(242,240,238) 100%);"></div>
        <div class="absolute inset-0 mix-blend-overlay" style="opacity: 0.75; background-image: url('/images/noise.png'); background-repeat: repeat; background-size: 128px auto;"></div>
    </div>
    <div class="flex flex-col items-center text-center relative z-1" style="max-width: 1240px; gap: 40px;">
        <div class="flex flex-col items-center gap-6" style="max-width: 900px;">
            @if($d['badge'] ?? false)
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 border border-[#e8e4e2]">
                    <span class="w-2 h-2 rounded-full" style="background: #4c4c4c;"></span>
                    <span data-edit="badge" style="font-family: 'Inter Display', sans-serif; color: #3d3d3d; font-size: 16px; font-weight: 500;">{{ $d['badge'] }}</span>
                </div>
            @endif

            @if($d['headline'] ?? false)
                <h1 data-edit="headline" style="font-family: Switzer, sans-serif; color: #111; font-size: 60px; line-height: 1.2; font-weight: 600; letter-spacing: -0.025em; max-width: 56rem;">{{ $d['headline'] }}</h1>
            @endif

            @if($d['subtitle'] ?? false)
                <p data-edit="subtitle" style="font-family: 'Inter Display', sans-serif; color: #3d3d3d; font-size: 18px; line-height: 1.5; font-weight: 500; max-width: 42rem;">{{ $d['subtitle'] }}</p>
            @endif
        </div>

        <div class="flex flex-col items-center gap-6">
            <div class="flex items-center gap-4">
                @if($d['ctaLabel'] ?? false)
                    <a href="{{ $ctaUrl }}" data-edit="ctaLabel" data-edit-button style="font-family: 'Inter Display', sans-serif; color: white; font-size: 16px; font-weight: 500; text-decoration: none; padding: 16px 32px; border-radius: 8px; background: #111; transition: background 0.2s;" onmouseover="this.style.background='#333'" onmouseout="this.style.background='#111'">{{ $d['ctaLabel'] }}</a>
                @endif
                <div class="flex items-center">
                    @foreach(range(1, 5) as $i)
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#111"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                @if($d['rating'] ?? false)
                    <span data-edit="rating" style="font-family: 'Inter Display', sans-serif; color: #3d3d3d; font-size: 16px; font-weight: 500;">{{ $d['rating'] }}</span>
                @endif
                @if(($d['rating'] ?? false) && ($d['ratingLabel'] ?? false))
                    <span style="width: 4px; height: 4px; border-radius: 50%; background: #3d3d3d;"></span>
                @endif
                @if($d['ratingLabel'] ?? false)
                    <span data-edit="ratingLabel" style="font-family: 'Inter Display', sans-serif; color: #3d3d3d; font-size: 16px; font-weight: 500;">{{ $d['ratingLabel'] }}</span>
                @endif
            </div>
        </div>

        @if($d['dashboardImage'] ?? false)
            <div class="relative w-full" style="max-width: 1280px; margin-top: 16px; padding: 4px; border-radius: 17px; overflow: hidden; box-shadow: rgba(128,121,106,0.05) 0px 7px 15px, rgba(128,121,106,0.05) 0px 25px 80px, rgba(128,121,106,0.05) 0px 12px 20px;">
                <div class="absolute inset-0 pointer-events-none" style="z-index: 1; opacity: 0.5; background: linear-gradient(179deg, rgb(255,47,47) 0%, rgb(239,123,22) 35.8783%, rgb(138,67,225) 69.922%, rgb(213,17,253) 100%); border-radius: 17px;"></div>
                <div data-edit="dashboardImage" class="relative mx-auto" style="z-index: 1; max-width: 900px; border-radius: 15px; overflow: hidden;">
                    <img src="{{ $d['dashboardImage'] }}" alt="Dashboard Image" class="w-full h-auto" />
                </div>
            </div>
        @endif
    </div>
</section>
