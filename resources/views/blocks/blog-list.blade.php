@php $d = $data; @endphp
<section data-block="blogList" class="bg-neutral-50 py-20 md:py-28">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mx-auto max-w-2xl text-center">
            @if($d['heading'] ?? false)
                <h2 data-edit="heading" class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl">{{ $d['heading'] }}</h2>
            @endif
            @if($d['description'] ?? false)
                <p data-edit="description" class="mt-4 text-neutral-600 leading-relaxed">{{ $d['description'] }}</p>
            @endif
        </div>

        @if(($d['posts'] ?? []))
            <div data-list="posts" class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($d['posts'] as $i => $post)
                    <article class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-neutral-100 transition hover:shadow-lg">
                        <a href="{{ $post['url'] ?? '#' }}" class="block aspect-[16/9] overflow-hidden">
                            <img src="{{ $post['image'] ?? '' }}" data-edit="posts:{{ $i }}/image" alt="{{ $post['title'] ?? '' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110" />
                        </a>
                        <div class="p-5">
                            <div class="mb-2 flex items-center gap-3 text-xs text-neutral-500">
                                @if($post['date'] ?? false)
                                    <time data-edit="posts:{{ $i }}/date" datetime="{{ $post['date'] }}">{{ $post['date'] }}</time>
                                @endif
                                @if($post['category'] ?? false)
                                    <span data-edit="posts:{{ $i }}/category" class="rounded-full bg-primary/10 px-2.5 py-0.5 font-medium text-primary">{{ $post['category'] }}</span>
                                @endif
                            </div>
                            <h3 data-edit="posts:{{ $i }}/title" class="text-lg font-semibold text-neutral-900 group-hover:text-primary transition">
                                <a href="{{ $post['url'] ?? '#' }}">{{ $post['title'] ?? '' }}</a>
                            </h3>
                            <p data-edit="posts:{{ $i }}/excerpt" class="mt-2 text-sm text-neutral-600 leading-relaxed line-clamp-2">{{ $post['excerpt'] ?? '' }}</p>
                            <a href="{{ $post['url'] ?? '#' }}" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-primary hover:underline">
                                Read More
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        @if(($d['pagination'] ?? false))
            <div class="mt-12 flex items-center justify-center gap-2">
                @if($d['prevPageUrl'] ?? false)
                    <a href="{{ $d['prevPageUrl'] }}" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-neutral-600 shadow-sm ring-1 ring-neutral-200 hover:bg-neutral-50 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif
                @for($p = 1; $p <= ($d['totalPages'] ?? 1); $p++)
                    <a href="{{ ($d['pageUrl'] ?? '?page=') . $p }}" class="flex h-10 w-10 items-center justify-center rounded-xl text-sm font-medium transition {{ $p == ($d['currentPage'] ?? 1) ? 'bg-primary text-white shadow-sm' : 'bg-white text-neutral-600 shadow-sm ring-1 ring-neutral-200 hover:bg-neutral-50' }}">{{ $p }}</a>
                @endfor
                @if($d['nextPageUrl'] ?? false)
                    <a href="{{ $d['nextPageUrl'] }}" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-neutral-600 shadow-sm ring-1 ring-neutral-200 hover:bg-neutral-50 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>
