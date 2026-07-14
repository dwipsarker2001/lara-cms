@php
    $d = $data;
    $post = ($page ?? null) instanceof \App\Models\Post ? $page : null;
    $allPosts = \App\Models\Post::orderBy('date', 'desc')->get();
    $blogListHref = $d['blogListHref'] ?? '/blog';
    $recentCount = (int) ($d['recentCount'] ?? 4);
    $body = $d['body'] ?? '<p>Write your content here...</p>';

    $showFeatured = $post && ($post->hero_img || $post->banner_img);
    $bannerSrc = $post?->banner_img ?? $post?->hero_img ?? '';

    // Meta helpers
    $fmtDate = function ($date) {
        return $date ? \Carbon\Carbon::parse($date)->format('j F Y') : '';
    };
    $fmtRecentDate = function ($date) {
        return $date ? \Carbon\Carbon::parse($date)->format('d M Y') : '';
    };
    $readingTime = function ($body) {
        if (! $body) {
            return '1 Min Read';
        }
        $words = str_word_count(strip_tags($body));

        return max(1, (int) ceil($words / 200)).' Min Read';
    };
    $decorativeViews = function ($postId) {
        $n = ($postId * 2371 + 5432) % 9500 + 500;

        return $n >= 1000 ? number_format($n / 1000, 1).'k Views' : $n.' Views';
    };

    // Sidebar helpers
    $initial = $post ? strtoupper(trim($post->author ?: 'A')[0]) : '?';
    $postTags = collect($post?->tags ?? []);
    $categories = $allPosts
        ->flatMap(fn ($p) => $p->tags ?? [])
        ->filter()
        ->countBy()
        ->sortKeys();
    $recentPosts = $allPosts
        ->reject(fn ($p) => $post && $p->id === $post->id)
        ->sortByDesc(fn ($p) => $p->date)
        ->take($recentCount);
    $allTags = $allPosts
        ->flatMap(fn ($p) => $p->tags ?? [])
        ->filter()
        ->unique()
        ->sort()
        ->values();
    $postCardSrc = fn ($p) => $p->hero_img ?? $p->banner_img ?? '';
    $postPreviewText = function ($body, $len = 90) {
        if (! $body) {
            return '';
        }

        return str(word: strip_tags($body))->limit($len);
    };
    $categoryHref = fn ($tag) => $blogListHref.'?category='.urlencode($tag);
    $postUrl = fn ($slug) => '/blogs/'.$slug;
@endphp
<section data-block="blogSection" class="bg-gray-50/80">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-6 py-16 lg:grid-cols-3">
        <article class="lg:col-span-2">
            @if($showFeatured)
                <div class="relative mb-8 aspect-[16/9] overflow-hidden rounded-2xl" data-edit="hero_img">
                    <img src="{{ $post->hero_img }}" alt="" class="h-full w-full object-cover" />
                </div>
            @endif

            {{-- Blog Meta --}}
            <div class="border-y border-gray-200 py-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand/10 text-sm font-bold text-brand ring-2 ring-white">
                            {{ $initial }}
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Written by</p>
                            @if($post)
                                <p data-edit="author" class="text-sm font-bold text-gray-900">{{ $post->author ?: 'Anonymous' }}</p>
                            @else
                                <p class="text-sm font-bold text-gray-900">Author</p>
                            @endif
                        </div>
                    </div>
                    @if($post)
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-gray-500">
                            @if($post->date)
                                <span data-edit="date" class="inline-flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar h-4 w-4 text-brand"></i>
                                    {{ $fmtDate($post->date) }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-clock h-4 w-4 text-gray-400"></i>
                                {{ $readingTime($post->body) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-eye h-4 w-4 text-gray-400"></i>
                                {{ $decorativeViews($post->id) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-comment h-4 w-4 text-gray-400"></i>
                                (0) Comments
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Blog Content --}}
            <div class="mt-8">
                <div
                    data-edit="body"
                    class="max-w-none text-[15px] leading-[1.85] text-gray-700 [&_a]:text-brand [&_a]:underline [&_blockquote]:my-6 [&_blockquote]:border-l-4 [&_blockquote]:border-brand/30 [&_blockquote]:pl-4 [&_blockquote]:italic [&_h1]:mb-5 [&_h1]:mt-10 [&_h1]:text-3xl [&_h1]:font-bold [&_h1]:leading-tight [&_h1]:text-gray-900 [&_h2]:mb-4 [&_h2]:mt-9 [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:text-gray-900 [&_h3]:mb-3 [&_h3]:mt-7 [&_h3]:text-xl [&_h3]:font-bold [&_h3]:text-gray-900 [&_img]:my-8 [&_img]:w-full [&_img]:rounded-2xl [&_li]:mb-2 [&_ol]:my-5 [&_ol]:list-decimal [&_ol]:pl-6 [&_p]:mb-5 [&_ul]:my-5 [&_ul]:list-disc [&_ul]:pl-6"
                >
                    {!! $body !!}
                </div>
            </div>
        </article>

        {{-- Blog Sidebar --}}
        <aside class="space-y-8">
            <div>
                <h3 class="mb-4 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                    <i class="fa-solid fa-tag h-4 w-4 text-brand"></i>
                    Categories
                </h3>
                <ul class="space-y-1 rounded-xl border border-gray-100 bg-white p-2 shadow-sm">
                    @forelse($categories as $tag => $count)
                        @php $active = $postTags->contains($tag); @endphp
                        <li>
                            <a href="{{ $categoryHref($tag) }}"
                               class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ $active ? 'bg-brand font-medium text-white' : 'text-gray-700 hover:bg-brand/5 hover:text-brand' }}">
                                <span class="flex items-center gap-2.5">
                                    <span class="h-2 w-2 rounded-full {{ $active ? 'bg-white' : 'bg-gray-300' }}"></span>
                                    {{ $tag }}
                                </span>
                                <span class="{{ $active ? 'text-white/90' : 'text-gray-400' }}">({{ $count }})</span>
                            </a>
                        </li>
                    @empty
                        <li class="px-3 py-2 text-sm text-gray-400">No categories yet</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <h3 class="mb-2 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                    <i class="fa-regular fa-clock h-4 w-4 text-brand"></i>
                    Recent Post
                </h3>
                <div class="space-y-1 rounded-xl border border-gray-100 bg-white p-1.5 shadow-sm">
                    @forelse($recentPosts as $item)
                        @php
                            $img = $postCardSrc($item);
                            $preview = $postPreviewText($item->body, 90);
                        @endphp
                        <a href="{{ $postUrl($item->slug) }}"
                           class="group flex items-start gap-2 rounded-lg py-0.5 transition-colors hover:bg-gray-50">
                            <div class="relative h-16 w-20 shrink-0 overflow-hidden rounded-md">
                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                @if($item->date)
                                    <p class="mb-0.5 inline-flex items-center gap-1 text-[11px] font-medium text-gray-400">
                                        <i class="fa-regular fa-calendar h-3 w-3"></i>
                                        {{ $fmtRecentDate($item->date) }}
                                    </p>
                                @endif
                                <p class="line-clamp-1 text-sm font-semibold leading-snug text-gray-800 transition-colors group-hover:text-brand">
                                    {{ $item->title }}
                                </p>
                                @if($preview)
                                    <p class="mt-0.5 line-clamp-1 text-xs leading-snug text-gray-500">{{ $preview }}</p>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">No posts yet</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h3 class="mb-4 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                    <i class="fa-solid fa-hashtag h-4 w-4 text-brand"></i>
                    Tags
                </h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($allTags as $tag)
                        @php $active = $postTags->contains($tag); @endphp
                        <a href="{{ $categoryHref($tag) }}"
                           class="rounded-full border px-3.5 py-1.5 text-xs font-medium transition-all duration-200 {{ $active ? 'border-brand bg-brand text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-brand hover:bg-brand/5 hover:text-brand' }}">
                            {{ $tag }}
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">No tags yet</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</section>
