@php
    $d = $data;
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    if (empty($bg) && isset($d['background']) && is_string($d['background'])) {
        try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    $hasBg = !empty($bg['image']) || !empty($bg['color']);
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;
    $bgImg = $bg['image'] ?? '';

    $email = $d['email'] ?? [];
    $contactPhone = $d['contactPhone'] ?? [];
    $office = $d['office'] ?? [];

    $cards = [
        ['icon' => 'mail', 'title' => $email['title'] ?? 'Email', 'description' => $email['description'] ?? '', 'value' => $email['value'] ?? '', 'href' => $email['value'] ?? null, 'type' => 'email'],
        ['icon' => 'phone', 'title' => $contactPhone['title'] ?? 'Phone', 'description' => $contactPhone['description'] ?? '', 'value' => $contactPhone['value'] ?? '', 'href' => $contactPhone['value'] ?? null, 'type' => 'contactPhone'],
        ['icon' => 'map', 'title' => $office['title'] ?? 'Office', 'description' => $office['description'] ?? '', 'value' => $office['value'] ?? '', 'href' => null, 'type' => 'office'],
    ];
@endphp
<section data-block="contact" class="py-20 px-6 relative overflow-hidden">
    @if($hasBg)
        @if($bgColor)
            <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
        @endif
        @if($bgImg)
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
        @endif
    @endif
    <div class="max-w-7xl mx-auto relative">
        @if($d['heading'] ?? $d['subheading'] ?? false)
            <div class="text-center mb-14">
                @if($d['heading'] ?? false)
                    <h2 data-edit="heading" class="text-3xl md:text-4xl font-bold text-foreground mb-4">{{ $d['heading'] }}</h2>
                @endif
                @if($d['subheading'] ?? false)
                    <p data-edit="subheading" class="text-muted-foreground max-w-2xl mx-auto text-base md:text-lg">{{ $d['subheading'] }}</p>
                @endif
            </div>
        @endif

        @if($d['mapEmbedUrl'] ?? false)
            <div data-edit="mapEmbedUrl" class="w-full rounded-xl mb-14 shadow-md overflow-hidden">
                <div class="relative w-full h-[420px] max-sm:h-[300px]">
                    <iframe
                        src="{{ $d['mapEmbedUrl'] }}"
                        class="absolute inset-0 w-full h-full"
                        style="border:0"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Office Location"
                        aria-label="Office Location Map"
                    ></iframe>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($cards as $card)
                @php
                    $isLink = !is_null($card['href']);
                @endphp
                <article data-group="{{ $card['type'] }}" class="bg-[#f4f4f6] rounded-2xl p-10">
                    <div class="flex items-center gap-3 mb-4">
                        @if($card['icon'] === 'mail')
                            <svg class="w-5 h-5 text-[#1a1a1a] shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        @elseif($card['icon'] === 'phone')
                            <svg class="w-5 h-5 text-[#1a1a1a] shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        @elseif($card['icon'] === 'map')
                            <svg class="w-5 h-5 text-[#1a1a1a] shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        @endif
                        <h3 class="text-lg font-bold text-[#1a1a1a]">{{ $card['title'] }}</h3>
                    </div>
                    @if($card['description'])
                        <p class="text-muted-foreground text-sm leading-relaxed mb-3">{{ $card['description'] }}</p>
                    @endif
                    @if($isLink)
                        <a href="{{ $card['type'] === 'email' ? 'mailto:' . $card['value'] : ($card['type'] === 'contactPhone' ? 'tel:' . $card['value'] : '#') }}" class="text-primary hover:text-primary/80 font-medium text-base transition-colors" aria-label="{{ $card['title'] }}: {{ $card['value'] }}">
                            {{ $card['value'] }}
                        </a>
                    @else
                        <p class="text-foreground font-medium text-base">{{ $card['value'] }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
