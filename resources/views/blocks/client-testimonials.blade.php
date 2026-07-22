@php $d = $data; @endphp
<section data-block="clientTestimonials">
    <div class="max-w-6xl mx-auto px-6">
        @if($d['headline'] ?? false)
            <h2 data-edit="headline" class="text-center text-2xl md:text-3xl font-bold text-gray-900">{{ $d['headline'] }}</h2>
        @endif
        @if($d['description'] ?? false)
            <p data-edit="description" class="mx-auto mt-3 max-w-2xl text-center text-gray-500">{{ $d['description'] }}</p>
        @endif
        <div class="mt-12 flex gap-5 overflow-x-auto snap-x snap-mandatory sm:grid sm:grid-cols-2 xl:grid-cols-3 md:gap-6" style="-ms-overflow-style:none;scrollbar-width:none">
            @foreach(($d['testimonials'] ?? []) as $i => $t)
                @if($t)
                    <div data-list="testimonials" class="min-w-[280px] sm:min-w-0 snap-start rounded-2xl bg-gray-100/80 p-6 md:p-7 transition-shadow hover:shadow-sm">
                        @if(($t['rating'] ?? 0) > 0)
                            <div class="flex items-center gap-1 text-amber-400 mb-3">
                                @for($s = 0; $s < min($t['rating'], 5); $s++)
                                    <svg class="size-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                        @endif
                        @if($t['quote'] ?? false)
                            <blockquote data-edit="quote" class="text-sm text-gray-600 leading-relaxed">&ldquo;{{ $t['quote'] }}&rdquo;</blockquote>
                        @endif
                        <div class="mt-5 flex items-center gap-3">
                            @if($t['avatar'] ?? false)
                                <div class="relative h-10 w-10 shrink-0 overflow-hidden rounded-full bg-gray-200">
                                    <img src="{{ $t['avatar'] }}" alt="" data-edit="avatar" class="absolute inset-0 w-full h-full object-cover" />
                                </div>
                            @endif
                            <div>
                                @if($t['name'] ?? false)
                                    <p data-edit="name" class="text-sm font-bold text-gray-900">{{ $t['name'] }}</p>
                                @endif
                                @if($t['role'] ?? false)
                                    <p data-edit="role" class="text-xs text-gray-500">{{ $t['role'] }}</p>
                                @endif
                                @if($t['handle'] ?? false)
                                    <p data-edit="handle" class="text-xs text-gray-400">{{ $t['handle'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
