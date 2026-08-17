@php
    $d = $data;
    $currentPage = max(1, (int) request()->query('page', 1));
    $categoryTag = request()->query('category');
    $searchQuery = request()->query('search');
    $perPage = max(1, (int) ($d['postsPerPage'] ?? 6));
    $layout = $d['layout'] ?? 'grid';
    $selectedCollection = $d['listCollection'] ?? $d['postCollection'] ?? null;

    $posts = collect();
    $sidebarCategories = [];
    $sidebarTags = [];
    $sidebarRecentPosts = [];
    $totalPosts = 0;
    $totalPages = 1;

    if (\Illuminate\Support\Facades\Schema::hasTable('collection_entries')) {
        $query = \App\Models\CollectionEntry::where('published', true);

        if (! empty($selectedCollection)) {
            $query->whereHas('collection', fn ($q) => $q->where('slug', $selectedCollection));
        } else {
            $query->whereHas('collection', function ($q) {
                $q->whereIn('slug', ['posts', 'blog', 'blogs', 'news', 'articles'])
                    ->orWhereNotIn('slug', ['pages', 'layouts']);
            });
        }

        if ($categoryTag) {
            $matchedTaxonomies = \App\Models\Taxonomy::where('slug', $categoryTag)->orWhere('title', $categoryTag)->get();
            $matchedTerms = \App\Models\Term::where('slug', $categoryTag)
                ->orWhere('title', $categoryTag)
                ->orWhere('id', is_numeric($categoryTag) ? (int) $categoryTag : 0)
                ->get();

            $matchedValues = array_filter(array_unique(array_merge(
                [$categoryTag],
                $matchedTaxonomies->pluck('id')->toArray(),
                $matchedTaxonomies->pluck('id')->map(fn ($id) => (string) $id)->toArray(),
                $matchedTaxonomies->pluck('title')->toArray(),
                $matchedTaxonomies->pluck('slug')->toArray(),
                $matchedTerms->pluck('id')->toArray(),
                $matchedTerms->pluck('id')->map(fn ($id) => (string) $id)->toArray(),
                $matchedTerms->pluck('title')->toArray(),
                $matchedTerms->pluck('slug')->toArray(),
            )));

            $query->where(function ($qry) use ($matchedValues) {
                foreach ($matchedValues as $val) {
                    $qry->orWhereJsonContains('data->category', $val)
                        ->orWhere('data->category', $val)
                        ->orWhere('data->category', 'like', "%{$val}%")
                        ->orWhereJsonContains('data->categories', $val)
                        ->orWhere('data->categories', 'like', "%{$val}%")
                        ->orWhereJsonContains('data->tags', $val)
                        ->orWhere('data->tags', 'like', "%{$val}%")
                        ->orWhereJsonContains('data->tag', $val)
                        ->orWhere('data->tag', 'like', "%{$val}%")
                        ->orWhere('data', 'like', '%"category":"'.$val.'"%')
                        ->orWhere('data', 'like', '%"categories":%"'.$val.'"%')
                        ->orWhere('data', 'like', '%"tag":%"'.$val.'"%')
                        ->orWhere('data', 'like', '%"tags":%"'.$val.'"%')
                        ->orWhere('sections', 'like', '%"category":"'.$val.'"%')
                        ->orWhere('sections', 'like', '%"category": "'.$val.'"%')
                        ->orWhere('sections', 'like', '%"categories":%"'.$val.'"%')
                        ->orWhere('sections', 'like', '%"tag":%"'.$val.'"%')
                        ->orWhere('sections', 'like', '%"tags":%"'.$val.'"%')
                        ->orWhere('sections', 'like', "%{$val}%");
                }
            });
        }

        if ($searchQuery) {
            $q = trim($searchQuery);
            $query->where(function ($qry) use ($q) {
                $qry->where('slug', 'like', "%{$q}%")
                    ->orWhere('data->title', 'like', "%{$q}%")
                    ->orWhere('data->content', 'like', "%{$q}%")
                    ->orWhere('data->excerpt', 'like', "%{$q}%")
                    ->orWhere('data', 'like', "%{$q}%")
                    ->orWhere('sections', 'like', "%{$q}%");
            });
        }

        $totalPosts = $query->count();
        $totalPages = max(1, (int) ceil($totalPosts / $perPage));
        $currentPage = min($currentPage, $totalPages);

        $entries = $query->latest()
            ->skip(($currentPage - 1) * $perPage)
            ->take($perPage)
            ->get();

        $blockInstance = new \App\Blocks\custom\BlogList();
        $posts = $entries->map(function ($entry) use ($blockInstance, $d) {
            $card = $blockInstance->resolveCard($entry, $d);
            $card->id = $entry->id;
            $card->slug = $entry->slug;
            $card->link = $entry->route();

            $cardDt = now();
            if (! empty($card->date)) {
                try {
                    $cardDt = \Illuminate\Support\Carbon::parse($card->date);
                } catch (\Throwable $e) {
                    $cardDt = $entry->created_at ? \Illuminate\Support\Carbon::parse($entry->created_at) : now();
                }
            } elseif ($entry->created_at) {
                $cardDt = \Illuminate\Support\Carbon::parse($entry->created_at);
            }
            $card->dt = $cardDt instanceof \DateTimeInterface ? $cardDt : now();

            $card->date = ! empty($card->date) ? $card->date : ($entry->created_at ? $entry->created_at->format('M d, Y') : 'Recent');
            $card->categoryName = $card->category ?: null;
            $card->body = $entry->data['content'] ?? $card->excerpt ?? '';

            return $card;
        });

        $categoryTaxonomy = $d['categoryTaxonomy'] ?? $d['category_taxonomy'] ?? null;
        $tagTaxonomy = $d['tagTaxonomy'] ?? $d['tag_taxonomy'] ?? null;

        $sidebarCategories = \App\Support\BlogSidebarData::getCategories($selectedCollection, $categoryTaxonomy);
        $sidebarTags = \App\Support\BlogSidebarData::getTags($selectedCollection, $tagTaxonomy);
        $sidebarRecentPosts = \App\Support\BlogSidebarData::getRecentPosts(3, $selectedCollection);
    }

    $total = $totalPages;
    $pageButtons = [];
    if ($total <= 7) {
        $pageButtons = range(1, $total);
    } else {
        $set = collect([1, 2, $total - 1, $total, $currentPage - 1, $currentPage, $currentPage + 1])
            ->filter(fn ($n) => $n >= 1 && $n <= $total)
            ->unique()
            ->sort()
            ->values()
            ->toArray();
        $prev = 0;
        foreach ($set as $n) {
            if ($prev && $n - $prev > 1) {
                $pageButtons[] = '…';
            }
            $pageButtons[] = $n;
            $prev = $n;
        }
    }

    $buildUrl = function ($params) {
        $current = request()->only(['category', 'search']);
        return url()->current() . '?' . http_build_query(array_merge($current, $params));
    };

    $estReadTime = function ($body) {
        $words = str_word_count(strip_tags($body ?? ''));
        return max(1, (int) ceil($words / 200)) . ' Min Read';
    };

    $excerptText = function ($post, $len = 120) {
        if (!empty($post->excerpt)) {
            return \Illuminate\Support\Str::limit($post->excerpt, $len);
        }
        return \Illuminate\Support\Str::limit(\Illuminate\Support\Str::squish(strip_tags($post->body ?? '')), $len);
    };
@endphp

<section data-block="blogList"
         x-data="{
             fetchBlogResults(targetUrl) {
                 const root = document.getElementById('blog-block-root');
                 if (root) {
                     root.classList.add('opacity-50', 'pointer-events-none');
                 }
                 window.history.replaceState({}, '', targetUrl);
                 fetch(targetUrl, {
                     headers: { 'X-Requested-With': 'XMLHttpRequest' }
                 })
                 .then(r => r.text())
                 .then(html => {
                     const parser = new DOMParser();
                     const doc = parser.parseFromString(html, 'text/html');
                     const newRoot = doc.getElementById('blog-block-root');
                     if (root && newRoot) {
                         root.innerHTML = newRoot.innerHTML;
                         root.scrollIntoView({ behavior: 'smooth', block: 'start' });
                     }
                 })
                 .catch(err => console.error('Blog fetch error:', err))
                 .finally(() => {
                     if (root) {
                         root.classList.remove('opacity-50', 'pointer-events-none');
                     }
                 });
             }
         }">
    {{-- Content --}}
    <div id="blog-block-root" class="mx-auto max-w-7xl px-6 py-16">
        @if($categoryTag)
            <div class="mb-6 flex items-center gap-2">
                <span class="text-gray-600">Filtering by:</span>
                <span class="inline-flex items-center gap-1 bg-brand text-white px-3 py-1 rounded text-sm">
                    {{ $categoryTag }}
                    <a href="{{ $buildUrl(['category' => null, 'page' => null]) }}"
                       @click.prevent="fetchBlogResults($el.href)"
                       class="hover:bg-brand-hover rounded p-0.5" aria-label="Clear filter">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/></svg>
                    </a>
                </span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- Posts column --}}
            <div id="blog-posts-container" class="lg:col-span-2 transition-opacity duration-200">
                @if($posts->isEmpty())
                    <p class="text-gray-500 text-center py-12">{{ $searchQuery ? 'No posts match your search.' : 'No posts found.' }}</p>
                @else
                    @if($layout === 'grid')
                        <div class="grid grid-cols-1 gap-7 md:grid-cols-2">
                            @foreach($posts as $post)
                                <a href="{{ $post->link }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-gray-200 hover:shadow-xl hover:shadow-brand/5">
                                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                                        @php $img = $post->image; @endphp
                                        @if($img)
                                            <img src="{{ $img }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-110" />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                                <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                            </div>
                                        @endif
                                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                                        @if($post->date)
                                            @php $dt = $post->dt; @endphp
                                            <div class="absolute left-4 top-4 flex h-14 w-14 flex-col items-center justify-center rounded-xl bg-white/95 text-gray-900 shadow-lg backdrop-blur-sm ring-1 ring-black/5">
                                                <span class="text-base font-bold leading-none">{{ $dt->format('j') }}</span>
                                                <span class="mt-0.5 text-[10px] font-semibold uppercase tracking-wider text-brand">{{ $dt->format('M') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-1 flex-col p-5 sm:p-6">
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                                <span class="font-medium text-gray-700">{{ $post->author ?: 'Admin' }}</span>
                                            </span>
                                            @if($post->categoryName)
                                                <span class="text-gray-300">•</span>
                                                <span class="inline-flex items-center gap-1 text-brand">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                                                    {{ $post->categoryName }}
                                                </span>
                                            @endif
                                            @if($post->date)
                                                <span class="text-gray-300">•</span>
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                    {{ $post->date }}
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="mt-3 line-clamp-2 text-lg font-bold leading-snug tracking-tight text-gray-900 transition-colors duration-200 group-hover:text-brand sm:text-xl">{{ $post->title }}</h3>
                                        <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-gray-500">{{ $excerptText($post) }}</p>
                                        <div class="mt-auto flex items-center justify-between pt-5">
                                            <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand transition-all duration-200 group-hover:gap-2.5">
                                                Read more
                                                <svg class="h-4 w-4 transition-transform duration-200 group-hover:rotate-45" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M7 17l9.2-9.2M17 17V7H7"/></svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col gap-4">
                            @foreach($posts as $post)
                                <a href="{{ $post->link }}" class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white p-2.5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-gray-200 hover:shadow-lg sm:flex-row sm:items-center sm:gap-4">
                                    @php $img = $post->image; @endphp
                                    <div class="relative aspect-[16/9] w-full shrink-0 overflow-hidden rounded-xl bg-gray-100 sm:aspect-auto sm:h-36 sm:w-52">
                                        @if($img)
                                            <img src="{{ $img }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-110" />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                                <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                            </div>
                                        @endif
                                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                                        @if($post->date)
                                            @php $dt = $post->dt; @endphp
                                            <div class="absolute left-3 top-3 flex h-11 w-11 flex-col items-center justify-center rounded-lg bg-white/95 text-gray-900 shadow-md backdrop-blur-sm ring-1 ring-black/5">
                                                <span class="text-sm font-bold leading-none">{{ $dt->format('j') }}</span>
                                                <span class="mt-0.5 text-[9px] font-semibold uppercase tracking-wider text-brand">{{ $dt->format('M') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-1 flex-col justify-between p-3 sm:p-1 sm:pr-2">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                                    <span class="font-medium text-gray-700">{{ $post->author ?: 'Admin' }}</span>
                                                </span>
                                                @if($post->categoryName)
                                                    <span class="text-gray-300">•</span>
                                                    <span class="inline-flex items-center gap-1 text-brand">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                                                        {{ $post->categoryName }}
                                                    </span>
                                                @endif
                                                @if($post->date)
                                                    <span class="text-gray-300">•</span>
                                                    <span class="inline-flex items-center gap-1">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                        {{ $post->date }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h3 class="mt-1.5 line-clamp-1 text-base font-bold leading-snug tracking-tight text-gray-900 transition-colors duration-200 group-hover:text-brand sm:text-lg">{{ $post->title }}</h3>
                                            <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-gray-500 sm:text-sm">{{ $excerptText($post, 110) }}</p>
                                        </div>
                                        <div class="mt-2.5 flex items-center justify-between">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand transition-all duration-200 group-hover:gap-2.5 sm:text-sm">
                                                Read more
                                                <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-45" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M7 17l9.2-9.2M17 17V7H7"/></svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Pagination --}}
                    @if($totalPages > 1)
                        <nav aria-label="Blog pagination" class="mt-14 flex flex-col items-center gap-4">
                            <div class="flex items-center justify-center gap-1.5 sm:gap-2">
                                <a href="{{ $buildUrl(['page' => $currentPage - 1]) }}"
                                   @click.prevent="fetchBlogResults($el.href)"
                                   class="inline-flex h-10 items-center justify-center gap-1 rounded-full border border-gray-200 bg-white px-3 text-sm font-medium text-gray-700 transition-all hover:border-brand hover:text-brand {{ $currentPage <= 1 ? 'pointer-events-none opacity-40' : '' }}"
                                   aria-label="Previous page">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                                    <span class="hidden sm:inline">Prev</span>
                                </a>

                                @foreach($pageButtons as $b)
                                    @if($b === '…')
                                        <span class="px-1 text-sm text-gray-400">…</span>
                                    @else
                                        <a href="{{ $buildUrl(['page' => $b]) }}"
                                           @click.prevent="fetchBlogResults($el.href)"
                                           aria-current="{{ $b === $currentPage ? 'page' : '' }}"
                                           class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-semibold transition-all duration-200 {{ $b === $currentPage ? 'scale-105 bg-gradient-to-br from-brand to-brand-hover text-white shadow-md shadow-brand/30' : 'border border-gray-200 bg-white text-gray-700 hover:border-brand hover:text-brand' }}">
                                            {{ $b }}
                                        </a>
                                    @endif
                                @endforeach

                                <a href="{{ $buildUrl(['page' => $currentPage + 1]) }}"
                                   @click.prevent="fetchBlogResults($el.href)"
                                   class="inline-flex h-10 items-center justify-center gap-1 rounded-full border border-gray-200 bg-white px-3 text-sm font-medium text-gray-700 transition-all hover:border-brand hover:text-brand {{ $currentPage >= $totalPages ? 'pointer-events-none opacity-40' : '' }}"
                                   aria-label="Next page">
                                    <span class="hidden sm:inline">Next</span>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                                </a>
                            </div>
                            <p class="text-xs font-medium text-gray-400">
                                Page <span class="text-gray-600">{{ $currentPage }}</span> of {{ $totalPages }}
                                @if($totalPosts > 0)
                                    · <span class="text-gray-600">{{ $totalPosts }}</span> posts
                                @endif
                            </p>
                        </nav>
                    @endif
                @endif
            </div>

            <aside class="space-y-8 lg:sticky lg:top-24 lg:self-start">
                {{-- Search --}}
                <div>
                    <h3 class="mb-4 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                        <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Search Here
                    </h3>
                    <form method="GET" action="{{ url()->current() }}"
                          @submit.prevent="fetchBlogResults($el.action + '?' + new URLSearchParams(new FormData($el)).toString())"
                          class="group relative">
                        @if($categoryTag)
                            <input type="hidden" name="category" value="{{ $categoryTag }}">
                        @endif
                        <input type="text" name="search" placeholder="Search this page..." value="{{ request()->query('search') }}"
                               class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 pr-10 text-sm shadow-sm transition-all placeholder:text-gray-400 hover:border-gray-300 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20" />
                        <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 transition-colors group-focus-within:text-brand" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        @if(request()->query('search'))
                            <a href="{{ $buildUrl(['search' => null, 'page' => null]) }}"
                               @click.prevent="fetchBlogResults($el.href)"
                               class="absolute right-9 top-1/2 -translate-y-1/2 rounded-full p-0.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600"
                               aria-label="Clear search">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Categories --}}
                <div>
                    <h3 class="mb-4 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                        <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                        Categories
                    </h3>
                    <ul class="space-y-1 rounded-xl border border-gray-100 bg-white p-2 shadow-sm">
                        @forelse($sidebarCategories as $cat)
                            @php
                                $catSlug = $cat['slug'] ?? \Illuminate\Support\Str::slug($cat['name']);
                                $active = $categoryTag && (mb_strtolower($categoryTag) === mb_strtolower($catSlug) || mb_strtolower($categoryTag) === mb_strtolower($cat['name']));
                            @endphp
                            <li>
                                <a href="{{ $buildUrl(['category' => $active ? null : $catSlug, 'page' => null]) }}"
                                   @click.prevent="fetchBlogResults($el.href)"
                                   class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ $active ? 'bg-brand text-white shadow-sm shadow-brand/20' : 'text-gray-700 hover:bg-brand/5 hover:text-brand' }}">
                                    <span class="flex items-center gap-2.5">
                                        <span class="h-2 w-2 rounded-full transition-all {{ $active ? 'bg-white scale-110' : 'bg-gray-300' }}"></span>
                                        {{ $cat['name'] }}
                                    </span>
                                    <span class="text-xs font-mono opacity-80">{{ $cat['count'] }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="px-3 py-2 text-sm text-gray-400">No categories yet</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Recent Posts --}}
                <div>
                    <h3 class="mb-4 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                        <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 16 14"/></svg>
                        Recent Post
                    </h3>
                    <div class="space-y-2 rounded-xl border border-gray-100 bg-white p-2 shadow-sm">
                        @forelse($sidebarRecentPosts as $rPost)
                            <a href="{{ $rPost['link'] ?? '#' }}"
                               class="group flex gap-3 rounded-lg p-1.5 transition-colors hover:bg-gray-50">
                                @if($rPost['image'] ?? null)
                                    <div class="relative h-16 w-20 shrink-0 overflow-hidden rounded-lg">
                                        <img src="{{ $rPost['image'] }}" alt="{{ $rPost['title'] }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                    </div>
                                @else
                                    <div class="flex h-16 w-20 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-gray-100 to-gray-200">
                                        <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="mb-1 flex flex-wrap items-center gap-1.5 text-[11px] font-medium text-gray-400">
                                        @if($rPost['category'] ?? null)
                                            <span class="font-semibold text-brand">{{ $rPost['category'] }}</span>
                                            <span>•</span>
                                        @endif
                                        @if($rPost['date'] ?? null)
                                            <span class="inline-flex items-center gap-0.5">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                                {{ $rPost['date'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="line-clamp-2 text-xs font-semibold leading-snug text-gray-800 transition-colors group-hover:text-brand">{{ $rPost['title'] }}</p>
                                    @if($rPost['author'] ?? null)
                                        <p class="mt-0.5 text-[10px] text-gray-400">By {{ $rPost['author'] }}</p>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <p class="text-sm text-gray-400">No posts yet</p>
                        @endforelse
                    </div>
                </div>

                {{-- Tags --}}
                <div>
                    <h3 class="mb-4 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                        <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5.24 8.7-5.24"/><path d="M12 22V12"/></svg>
                        Tags
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($sidebarTags as $tag)
                            @php $active = $categoryTag === $tag; @endphp
                            <a href="{{ $buildUrl(['category' => $active ? null : $tag, 'page' => null]) }}"
                               @click.prevent="fetchBlogResults($el.href)"
                               class="rounded-full border px-3.5 py-1.5 text-xs font-medium transition-all duration-200 {{ $active ? 'border-brand bg-brand text-white shadow-sm shadow-brand/20' : 'border-gray-200 bg-white text-gray-600 hover:border-brand hover:bg-brand/5 hover:text-brand' }}">
                                {{ $tag }}
                            </a>
                        @empty
                            <p class="text-sm text-gray-400">No tags yet</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
