@php $d = $data; @endphp
<section data-block="clientTestimonials" class="bg-neutral-900 py-20 md:py-28" x-data="{ current: 0, autoplay: null }" x-init="autoplay = setInterval(() => { current = (current + 1) % ({{ count($d['testimonials'] ?? []) }} || 1) }, 5000)" @mouseenter="clearInterval(autoplay)" @mouseleave="autoplay = setInterval(() => { current = (current + 1) % ({{ count($d['testimonials'] ?? []) }} || 1) }, 5000)">
    <div class="mx-auto max-w-7xl px-4 text-center">
        @if($d['heading'] ?? false)
            <h2 data-edit="heading" class="text-3xl font-bold tracking-tight text-white md:text-4xl">{{ $d['heading'] }}</h2>
        @endif
        @if($d['description'] ?? false)
            <p data-edit="description" class="mx-auto mt-4 max-w-2xl text-neutral-400 leading-relaxed">{{ $d['description'] }}</p>
        @endif

        @if(($d['testimonials'] ?? []))
            <div data-list="testimonials" class="relative mt-12 overflow-hidden">
                <div class="mx-auto max-w-3xl">
                    @foreach($d['testimonials'] as $i => $testimonial)
                        <div x-show="current === {{ $i }}" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-8" class="px-4">
                            <div class="flex flex-col items-center">
                                <img src="{{ $testimonial['avatar'] ?? '' }}" data-edit="testimonials:{{ $i }}/avatar" alt="{{ $testimonial['name'] ?? '' }}" class="mb-4 h-16 w-16 rounded-full object-cover ring-4 ring-white/20" />
                                <div class="mb-3 flex items-center gap-1">
                                    @for($s = 0; $s < 5; $s++)
                                        <svg class="h-5 w-5 {{ $s < ($testimonial['rating'] ?? 5) ? 'text-yellow-400' : 'text-neutral-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <blockquote data-edit="testimonials:{{ $i }}/quote" class="text-lg leading-relaxed text-neutral-200 md:text-xl">&ldquo;{{ $testimonial['quote'] ?? '' }}&rdquo;</blockquote>
                                <div class="mt-6">
                                    <p data-edit="testimonials:{{ $i }}/name" class="font-semibold text-white">{{ $testimonial['name'] ?? '' }}</p>
                                    <p data-edit="testimonials:{{ $i }}/role" class="text-sm text-neutral-400">{{ $testimonial['role'] ?? '' }}</p>
                                </div>
                                @if(($testimonial['twitterUrl'] ?? false) && ($testimonial['twitterUrl']['url'] ?? false))
                                    <a href="{{ $testimonial['twitterUrl']['url'] }}" data-edit="testimonials:{{ $i }}/twitterUrl" class="mt-3 inline-flex items-center gap-1.5 text-sm text-neutral-400 hover:text-white transition" target="_blank">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                        X/Twitter
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex items-center justify-center gap-2">
                    @foreach($d['testimonials'] as $i => $testimonial)
                        <button @click="current = {{ $i }}" class="h-2 w-2 rounded-full transition" :class="current === {{ $i }} ? 'bg-primary w-6' : 'bg-neutral-600'"></button>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
