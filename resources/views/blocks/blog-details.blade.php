@php
    $d = $data;
    $heroImage = $d['image'] ?? 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=80';
    $title = $d['title'] ?? 'নেপাল ভ্রমণ: সাগরমাথার দেশে অ্যাডভেঞ্চার ও শান্তির সন্ধানে';
    $content = $d['content'] ?? '';

    $categoryValue = $d['category'] ?? null;
    $categoryNames = [];

    if ($categoryValue) {
        $categoryIds = [];
        if (is_array($categoryValue)) {
            $categoryIds = $categoryValue;
        } elseif (is_string($categoryValue) && (str_starts_with(trim($categoryValue), '[') || str_starts_with(trim($categoryValue), '{'))) {
            $decoded = json_decode($categoryValue, true);
            $categoryIds = is_array($decoded) ? $decoded : [$categoryValue];
        } else {
            $categoryIds = [$categoryValue];
        }

        $categoryIds = array_filter(array_map('strval', $categoryIds));

        if (!empty($categoryIds)) {
            $categoryNames = \App\Models\Term::whereIn('id', $categoryIds)->pluck('title')->toArray();

            if (empty($categoryNames)) {
                $categoryNames = \App\Models\Taxonomy::whereIn('id', $categoryIds)->pluck('title')->toArray();
            }

            if (empty($categoryNames)) {
                $categoryNames = \App\Models\Term::whereIn('slug', $categoryIds)->orWhereIn('title', $categoryIds)->pluck('title')->toArray();
            }

            if (empty($categoryNames)) {
                $categoryNames = \App\Models\Taxonomy::whereIn('slug', $categoryIds)->orWhereIn('title', $categoryIds)->pluck('title')->toArray();
            }
        }
    }

    if (empty($categoryNames) && is_string($categoryValue) && !is_numeric($categoryValue)) {
        $categoryNames = [$categoryValue];
    }

    if (empty($categoryNames)) {
        $categoryNames = \App\Models\Term::pluck('title')->take(3)->toArray();
        if (empty($categoryNames)) {
            $categoryNames = \App\Models\Taxonomy::pluck('title')->take(3)->toArray();
        }
    }

    if (empty($categoryNames)) {
        $categoryNames = ['Travel'];
    }

    $tags = $d['tag'] ?? ['International', 'Adventure', 'Heritage'];
    if (is_string($tags)) {
        $tags = json_decode($tags, true) ?: array_map('trim', explode(',', $tags));
    }
    $tags = is_array($tags) ? array_filter($tags) : [];

    $showRelatedPosts = filter_var($d['showRelatedPosts'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $relatedTitle = $d['relatedTitle'] ?? 'Related Posts';
    $relatedPosts = $d['relatedPosts'] ?? [
        [
            'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
            'date' => 'Jun 1, 2026',
            'title' => 'বান্দরবান ট্র্যাকিং: পাহাড়ের চূড়ায় মেঘের সাথে খেলা',
            'link' => '#',
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=600&q=80',
            'date' => 'Apr 10, 2026',
            'title' => 'বান্দরবান ট্র্যাকিং: ২০২৩ সালের চ্যালেঞ্জ ও পাহাড়চূড়ার পার্টি',
            'link' => '#',
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=600&q=80',
            'date' => 'Apr 22, 2026',
            'title' => 'হামহাম জলপ্রপাতে এক দিন: বাংলাদেশের সবচেয়ে উঁচু ঝরনা',
            'link' => '#',
        ],
    ];

    $contactTitle = $d['contactTitle'] ?? 'Join the conversation';
    $contactSubtitle = $d['contactSubtitle'] ?? 'Comments are coming soon. Have a question or story to share? Reach out via our contact page.';
    $contactButtonText = $d['contactButtonText'] ?? 'Contact us';
    $contactButtonLink = $d['contactButtonLink'] ?? '/contact';
@endphp

<div class="w-full bg-white py-10 lg:py-16 text-gray-800 antialiased">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
            
            {{-- ════════════════════════════════════════════════════════════ --}}
            {{-- LEFT MAIN CONTENT AREA (~8 cols)                             --}}
            {{-- ════════════════════════════════════════════════════════════ --}}
            <div class="lg:col-span-8 space-y-8">

                {{-- Featured Hero Image --}}
                @if($heroImage)
                    <div class="relative w-full h-[320px] sm:h-[420px] lg:h-[460px] rounded-2xl overflow-hidden shadow-sm bg-gray-100 border border-gray-100">
                        <img src="{{ $heroImage }}" alt="{{ $title }}" class="w-full h-full object-cover" data-edit="image">
                    </div>
                @endif

                {{-- Rich Text Body Content --}}
                <div class="prose prose-lg prose-emerald max-w-none text-gray-700 leading-relaxed font-normal space-y-6 pt-2" data-edit="content">
                    {!! $content !!}
                </div>

                {{-- Bottom Tags & Share Bar --}}
                <div class="border-t border-b border-gray-100 py-4 flex flex-wrap items-center justify-between gap-4">
                    {{-- Tags --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1">
                            <svg class="size-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                <line x1="7" y1="7" x2="7.01" y2="7"/>
                            </svg>
                            Tags:
                        </span>
                        @foreach($tags as $t)
                            <a href="?tag={{ urlencode($t) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">{{ $t }}</a>
                        @endforeach
                    </div>

                    {{-- Social Share --}}
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1">
                            <svg class="size-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="18" cy="5" r="3"/>
                                <circle cx="6" cy="12" r="3"/>
                                <circle cx="18" cy="19" r="3"/>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                            </svg>
                            Share:
                        </span>
                        <a href="#" class="size-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:border-emerald-500 hover:bg-emerald-50 hover:text-emerald-600 transition-colors" title="Copy link">
                            <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                        </a>
                        <a href="#" class="size-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:border-emerald-500 hover:bg-emerald-50 hover:text-emerald-600 transition-colors" title="Facebook">
                            <svg class="size-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="size-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:border-emerald-500 hover:bg-emerald-50 hover:text-emerald-600 transition-colors" title="Twitter / X">
                            <svg class="size-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Related Posts Section --}}
                @if($showRelatedPosts)
                    <div class="pt-6 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900" data-edit="relatedTitle">{{ $relatedTitle }}</h3>
                            <a href="/blog" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 group transition-colors">
                                <span>View all</span>
                                <svg class="size-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            @foreach($relatedPosts as $index => $item)
                                @php
                                    $rImg = $item['image'] ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80';
                                    $rDate = $item['date'] ?? 'Jun 1, 2026';
                                    $rTitle = $item['title'] ?? 'Untitled Post';
                                    $rLink = $item['link'] ?? '#';
                                @endphp
                                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-shadow group flex flex-col">
                                    <div class="aspect-[4/3] w-full overflow-hidden bg-gray-100">
                                        <img src="{{ $rImg }}" alt="{{ $rTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <div class="p-4 flex flex-col flex-1">
                                        <div class="text-[11px] text-gray-400 font-medium flex items-center gap-1 mb-1.5">
                                            <svg class="size-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                            <span>{{ $rDate }}</span>
                                        </div>
                                        <h4 class="text-xs font-bold text-gray-900 group-hover:text-emerald-600 transition-colors line-clamp-2 mb-3 flex-1 leading-snug">
                                            {{ $rTitle }}
                                        </h4>
                                        <a href="{{ $rLink }}" class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                            <span>Read more</span>
                                            <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- ════════════════════════════════════════════════════════════ --}}
            {{-- RIGHT SIDEBAR (Dynamic / Automatic / Not editable in block)  --}}
            {{-- ════════════════════════════════════════════════════════════ --}}
            @php
                $selectedCollection = $d['postCollection'] ?? null;
                $sidebarRecentPosts = \App\Support\BlogSidebarData::getRecentPosts(3, $selectedCollection);
                $sidebarCategories = \App\Support\BlogSidebarData::getCategories($selectedCollection);
                $sidebarTags = \App\Support\BlogSidebarData::getTags($selectedCollection);
            @endphp
            <div class="lg:col-span-4 space-y-8">
                
                {{-- 1. Search Box Widget --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-900 uppercase tracking-wider mb-3">
                        <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <span>Search Here</span>
                    </div>
                    <form action="{{ url('/blog') }}" method="GET" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search this page..." class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 bg-white transition-all">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-emerald-600 transition-colors">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- 2. Categories Widget --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-900 uppercase tracking-wider mb-4">
                        <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                            <line x1="7" y1="7" x2="7.01" y2="7"/>
                        </svg>
                        <span>Categories</span>
                    </div>

                    <div class="space-y-1.5">
                        @foreach($sidebarCategories as $cat)
                            @php
                                $catSlug = $cat['slug'] ?? \Illuminate\Support\Str::slug($cat['name']);
                                $reqCat = request('category');
                                $isCatActive = $reqCat && (mb_strtolower($reqCat) === mb_strtolower($catSlug) || mb_strtolower($reqCat) === mb_strtolower($cat['name']));
                                $catLink = $cat['link'] ?? '?category='.urlencode($catSlug);
                            @endphp
                            <a href="{{ $catLink }}" class="flex items-center justify-between px-3.5 py-2 text-xs font-semibold rounded-xl transition-colors shadow-xs {{ $isCatActive ? 'bg-emerald-600 text-white font-bold' : 'text-gray-600 hover:text-emerald-600 hover:bg-emerald-50/50' }}">
                                <span>{{ $cat['name'] }}</span>
                                <span class="text-[11px] font-mono {{ $isCatActive ? 'text-white/80' : 'text-gray-400' }}">({{ $cat['count'] }})</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Recent Posts Widget --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-900 uppercase tracking-wider mb-4">
                        <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>Recent Post</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($sidebarRecentPosts as $post)
                            <a href="{{ $post['link'] ?? '#' }}" class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-gray-50 transition-colors group">
                                <div class="size-14 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                                    <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[11px] text-gray-400 font-medium flex items-center gap-1 mb-0.5">
                                        <svg class="size-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        <span>{{ $post['date'] }}</span>
                                    </div>
                                    <h5 class="text-xs font-bold text-gray-900 group-hover:text-emerald-600 transition-colors line-clamp-2 leading-snug">
                                        {{ $post['title'] }}
                                    </h5>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- 4. Tags Cloud Widget --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-900 uppercase tracking-wider mb-4">
                        <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                            <line x1="7" y1="7" x2="7.01" y2="7"/>
                        </svg>
                        <span>Tags</span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @foreach($sidebarTags as $index => $tag)
                            @php $isTagActive = $index < 3 || request('tag') === $tag; @endphp
                            <a href="?tag={{ urlencode($tag) }}" class="px-3 py-1 text-xs font-semibold rounded-full shadow-xs transition-colors {{ $isTagActive ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 font-medium' }}">
                                {{ $tag }}
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
