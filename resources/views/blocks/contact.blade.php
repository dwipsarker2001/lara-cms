@php $d = $data; @endphp
@php
    $emailTitle = $d['emailTitle'] ?? 'Email';
    $emailDesc = $d['emailDescription'] ?? '';
    $emailValue = $d['emailValue'] ?? '';
    $phoneTitle = $d['phoneTitle'] ?? 'Phone';
    $phoneDesc = $d['phoneDescription'] ?? '';
    $phoneValue = $d['phoneValue'] ?? '';
    $officeTitle = $d['officeTitle'] ?? 'Office';
    $officeDesc = $d['officeDescription'] ?? '';
    $officeValue = $d['officeValue'] ?? '';
@endphp
<section data-block="contact">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-20">
            <div>
                @if($d['heading'] ?? false)
                    <h2 data-edit="heading" class="text-2xl md:text-3xl font-bold text-gray-900">{{ $d['heading'] }}</h2>
                @endif
                @if($d['subheading'] ?? false)
                    <p data-edit="subheading" class="mt-4 text-sm md:text-base text-gray-500 leading-relaxed">{{ $d['subheading'] }}</p>
                @endif
            </div>
            <div class="space-y-6">
                @if($emailValue)
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p data-edit="emailTitle" class="text-sm font-semibold text-gray-900">{{ $emailTitle }}</p>
                            @if($emailDesc)
                                <p data-edit="emailDescription" class="text-sm text-gray-500">{{ $emailDesc }}</p>
                            @endif
                            <a href="mailto:{{ $emailValue }}" data-edit="emailValue" class="text-sm font-medium text-brand hover:text-brand/80">{{ $emailValue }}</a>
                        </div>
                    </div>
                @endif
                @if($phoneValue)
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <p data-edit="phoneTitle" class="text-sm font-semibold text-gray-900">{{ $phoneTitle }}</p>
                            @if($phoneDesc)
                                <p data-edit="phoneDescription" class="text-sm text-gray-500">{{ $phoneDesc }}</p>
                            @endif
                            <a href="tel:{{ $phoneValue }}" data-edit="phoneValue" class="text-sm font-medium text-brand hover:text-brand/80">{{ $phoneValue }}</a>
                        </div>
                    </div>
                @endif
                @if($officeValue)
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p data-edit="officeTitle" class="text-sm font-semibold text-gray-900">{{ $officeTitle }}</p>
                            @if($officeDesc)
                                <p data-edit="officeDescription" class="text-sm text-gray-500">{{ $officeDesc }}</p>
                            @endif
                            <p data-edit="officeValue" class="text-sm font-medium text-gray-700">{{ $officeValue }}</p>
                        </div>
                    </div>
                @endif
                @if($d['mapEmbedUrl'] ?? false)
                    <div class="mt-6 overflow-hidden rounded-2xl">
                        <iframe src="{{ $d['mapEmbedUrl'] }}" width="100%" height="240" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
