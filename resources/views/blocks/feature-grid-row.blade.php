@php $d = $data; $features = array_slice($d['features'] ?? [], 0, 2); @endphp
<section data-block="featureGridRow" class="bg-[#F2F0EE] py-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 flex flex-col lg:flex-row items-stretch gap-10 lg:gap-14">
        <div class="flex-1 order-2 lg:order-1">
            <div class="mb-8">
                @if($d['badge'] ?? false)
                    <span data-edit="badge" class="inline-block px-3 py-1 rounded-full border border-gray-300 text-xs font-semibold uppercase tracking-wider text-gray-600 bg-white shadow-sm mb-4">{{ $d['badge'] }}</span>
                @endif
                @if($d['headline'] ?? false)
                    <h2 data-edit="headline" class="text-2xl md:text-4xl font-semibold tracking-tight text-gray-900 mt-2" style="font-family: 'Switzer', sans-serif; line-height: 1.5;">
                        {{ $d['headline'] }}
                    </h2>
                @endif
                @if($d['description'] ?? false)
                    <p data-edit="description" class="mt-6 text-[17px] text-gray-500 font-medium leading-relaxed max-w-xl" style="font-family: 'Inter Display', sans-serif;">
                        {{ $d['description'] }}
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10 mt-12">
                @foreach($features as $f)
                    @if($f)
                    @php $iconColor = $f['iconColor'] ?? '#8B5CF6'; $iconName = $f['icon'] ?? 'Star'; @endphp
                    <div data-list="features" class="flex flex-col gap-4">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: {{ $iconColor }}15;">
                            @if($iconName === 'GitBranch')
                                <svg class="w-5 h-5" style="color: {{ $iconColor }};" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="6" x2="6" y1="3" y2="15"></line>
                                    <circle cx="18" cy="6" r="3"></circle>
                                    <circle cx="6" cy="18" r="3"></circle>
                                    <path d="M18 9a9 9 0 0 1-9 9"></path>
                                </svg>
                            @elseif($iconName === 'Zap')
                                <svg class="w-5 h-5" style="color: {{ $iconColor }};" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                </svg>
                            @elseif($iconName === 'Bell')
                                <svg class="w-5 h-5" style="color: {{ $iconColor }};" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5" style="color: {{ $iconColor }};" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 data-edit="title" class="text-[17px] font-semibold text-gray-900 tracking-tight mb-2" style="font-family: 'Inter Display', sans-serif;">{{ $f['title'] ?? 'Feature' }}</h3>
                            <p data-edit="description" class="text-[15px] leading-relaxed text-gray-500 font-medium" style="font-family: 'Inter Display', sans-serif;">{{ $f['description'] ?? 'Description' }}</p>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="flex-[1.2] order-1 lg:order-2 w-full">
            <div class="relative w-full aspect-[4/3] rounded-2xl">
                <div data-edit="image" class="w-full h-full rounded-2xl overflow-hidden">
                @if($d['image'] ?? false)
                    <img src="{{ $d['image'] }}" alt="{{ $d['headline'] ?? 'Mockup' }}" class="w-full h-full object-contain" />
                @endif
                </div>
            </div>
        </div>
    </div>
</section>
