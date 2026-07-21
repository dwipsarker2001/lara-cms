@php $d = $data; $faqs = array_values(array_filter($d['faqs'] ?? [])); $bg = is_array($d['background'] ?? null) ? $d['background'] : []; if (empty($bg) && isset($d['background']) && is_string($d['background'])) { try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; } } $bgImg = $bg['image'] ?? ''; $bgColor = $bg['color'] ?? ''; $bgOpacity = $bg['opacity'] ?? 100; @endphp
<section data-block="faq" style="position: relative; padding-top: 80px; padding-bottom: 80px; overflow: hidden;">
    @if($bgColor)<div class="absolute inset-0" style="background-color: {{ $bgColor }};"></div>@endif
    @if($bgImg)<div class="absolute inset-0" style="background-image: url({{ $bgImg }}); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: {{ $bgOpacity / 100 }};"></div>@endif
    <div style="max-width: 1152px; margin: 0 auto; padding: 0 24px; position: relative;">
        <div style="margin-bottom: 56px; text-align: center;">
            @if($d['headline'] ?? false)
                <h2 data-edit="headline" style="font-size: 36px; font-weight: 600; color: #111827; font-family: 'Switzer', sans-serif;">{{ $d['headline'] }}</h2>
            @endif
            @if($d['subtitle'] ?? false)
                <p data-edit="subtitle" style="max-width: 576px; margin: 12px auto 0; color: #6b7280; font-size: 16px;">{{ $d['subtitle'] }}</p>
            @endif
        </div>

        @if(count($faqs) > 0)
            <div style="max-width: 768px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px;">
                @foreach($faqs as $i => $item)
                    @if($item)
                    <div data-list="faqs" class="overflow-hidden" style="border-radius: 16px; border: 1px solid #e5e7eb; background: white; box-shadow: 0 4px 24px -12px rgba(15,23,42,0.06); transition: box-shadow 0.3s;" onmouseover="this.style.boxShadow='0 8px 28px -12px rgba(15,23,42,0.12)'" onmouseout="this.style.boxShadow='0 4px 24px -12px rgba(15,23,42,0.06)'" x-data="{ open: {{ $i === 0 ? 'true' : 'false' }} }">
                        <button type="button" @click="open = !open" :aria-expanded="open" class="flex w-full items-center justify-between" style="gap: 24px; padding: 20px 24px; text-align: left; transition: background 0.2s;" onmouseover="this.style.background='rgba(249,250,251,0.6)'" onmouseout="this.style.background='transparent'">
                            <span data-edit="question" style="font-size: 16px; font-weight: 600; color: #111827;">{{ $item['question'] ?? '' }}</span>
                            <svg style="width: 20px; height: 20px; flex-shrink: 0; color: #6b7280; transition: transform 0.3s;" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div style="overflow: hidden; transition: max-height 0.3s, opacity 0.3s;" x-show="open" x-cloak>
                            <div style="padding: 4px 24px 24px;">
                                <div data-edit="answer" style="font-size: 15px; line-height: 1.625; color: #4b5563;">{!! $item['answer'] ?? '' !!}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        @else
            <p style="margin-top: 40px; text-align: center; font-size: 14px; color: #9ca3af;">No FAQs yet.</p>
        @endif
    </div>
</section>
