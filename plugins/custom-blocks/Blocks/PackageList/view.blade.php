@php
    $d = $data;
    $currentPage = max(1, (int) request()->query('page', 1));
    $categoryTag = request()->query('category');
    $destinationTag = request()->query('destination');
    $searchQuery = request()->query('search', request()->query('q'));
    $maxPriceFilter = request()->query('max_price') !== null ? (int) request()->query('max_price') : null;
    $minPriceFilter = request()->query('min_price') !== null ? (int) request()->query('min_price') : null;
    $perPage = max(1, (int) ($d['packagesPerPage'] ?? 6));
    $maxPriceLimit = max(1, (int) ($d['priceMax'] ?? 500000));
    $selectedCollection = $d['listCollection'] ?? $d['packageCollection'] ?? null;

    $packages = collect();
    $sidebarDestinations = collect();
    $sidebarCategories = collect();
    $sidebarRecentPackages = collect();
    $totalPackages = 0;
    $totalPages = 1;

    if (\Illuminate\Support\Facades\Schema::hasTable('collection_entries')) {
        $query = \App\Models\CollectionEntry::where('published', true);

        if (! empty($selectedCollection)) {
            $query->whereHas('collection', fn ($q) => $q->where('slug', $selectedCollection));
        } else {
            $query->whereHas('collection', function ($q) {
                $q->whereIn('slug', ['packages', 'tours', 'travel-deals', 'destinations'])
                  ->orWhere('slug', '!=', 'pages');
            });
        }

        // Search query filter
        if ($searchQuery) {
            $q = trim($searchQuery);
            $query->where(function ($qry) use ($q) {
                $qry->where('slug', 'like', "%{$q}%")
                    ->orWhere('data->title', 'like', "%{$q}%")
                    ->orWhere('data->description', 'like', "%{$q}%")
                    ->orWhere('data->excerpt', 'like', "%{$q}%")
                    ->orWhere('data->location', 'like', "%{$q}%");
            });
        }

        // Category / Taxonomy filter
        $activeCategory = $categoryTag ?: ($d['termFilter'] ?? null);
        if ($activeCategory) {
            $c = trim($activeCategory);
            $query->where(function ($qry) use ($c) {
                $qry->where('data->category', 'like', "%{$c}%")
                    ->orWhere('data->tour_type', 'like', "%{$c}%")
                    ->orWhere('data->type', 'like', "%{$c}%")
                    ->orWhere('data->taxonomy', 'like', "%{$c}%")
                    ->orWhere('data->tags', 'like', "%{$c}%");
            });
        }

        // Destination filter
        if ($destinationTag) {
            $dest = trim($destinationTag);
            $query->where(function ($qry) use ($dest) {
                $qry->where('data->location', 'like', "%{$dest}%")
                    ->orWhere('data->destination', 'like', "%{$dest}%")
                    ->orWhere('data->city', 'like', "%{$dest}%");
            });
        }

        $allEntries = $query->latest()->get();

        // Price range filter in PHP map/filter
        $block = app(\App\Blocks\BlockRegistry::class)->get($data['_block'] ?? 'packageList')
            ?? new \Plugins\CustomBlocks\Blocks\PackageList\PackageList;

        $transformed = $allEntries->map(fn ($entry) => $block->resolveCard($entry, $d));

        // For price-based filtering, we still need to parse price values
        $parsePrice = function ($val) {
            if (is_numeric($val)) {
                return (float) $val;
            }
            if (is_string($val)) {
                $cleaned = preg_replace('/[^0-9.]/', '', $val);

                return is_numeric($cleaned) ? (float) $cleaned : 0;
            }

            return 0;
        };

        $transformed = $transformed->map(function ($card) use ($parsePrice) {
            $card->price = $parsePrice($card->price ?? 0);
            $card->originalPrice = $parsePrice($card->originalPrice ?? 0);

            if (! empty($card->excerpt)) {
                $card->excerpt = \Illuminate\Support\Str::limit(strip_tags((string) $card->excerpt), 140);
            }

            // Image fallback from gallery section if not mapped
            if (empty($card->image) && ! empty($card->_entry->sections)) {
                foreach ($card->_entry->sections as $sec) {
                    $gallery = $sec['data']['galleryImages'] ?? [];
                    if (is_array($gallery) && ! empty($gallery[0]['image'])) {
                        $card->image = $gallery[0]['image'];
                        break;
                    }
                }
            }

            return $card;
        });


        $filtered = $transformed->filter(function ($pkg) use ($minPriceFilter, $maxPriceFilter) {
            if ($minPriceFilter !== null && $pkg->price < $minPriceFilter) return false;
            if ($maxPriceFilter !== null && $pkg->price > $maxPriceFilter) return false;
            return true;
        });

        $totalPackages = $filtered->count();
        $totalPages = max(1, (int) ceil($totalPackages / $perPage));
        $currentPage = min($currentPage, $totalPages);

        $packages = $filtered->slice(($currentPage - 1) * $perPage, $perPage);

        // Selected Taxonomies from Block Config
        $destTaxonomy = $d['destinationTaxonomy'] ?? null;
        $catTaxonomy = $d['categoryTaxonomy'] ?? null;

        // Destination Sidebar Aggregation
        if (! empty($destTaxonomy) && \Illuminate\Support\Facades\Schema::hasTable('terms')) {
            try {
                $destTerms = \App\Models\Term::whereHas('taxonomy', fn ($q) => $q->where('slug', $destTaxonomy))->orderBy('position')->orderBy('title')->get();
                if ($destTerms->isNotEmpty()) {
                    $sidebarDestinations = $destTerms->map(function ($term) use ($transformed) {
                        $count = $transformed->filter(fn ($pkg) => strcasecmp($pkg->destination ?? '', $term->title) === 0 || strcasecmp($pkg->destination ?? '', $term->slug) === 0)->count();
                        return ['name' => $term->title, 'slug' => $term->slug, 'count' => $count];
                    });
                }
            } catch (\Throwable $e) {
                // fallback
            }
        }

        if ($sidebarDestinations->isEmpty()) {
            $sidebarDestinations = $transformed->pluck('destination')->filter()->groupBy(fn ($item) => $item)->map(function ($group, $key) {
                return ['name' => $key, 'count' => $group->count()];
            })->values();
        }

        // Category Sidebar Aggregation
        if (! empty($catTaxonomy) && \Illuminate\Support\Facades\Schema::hasTable('terms')) {
            try {
                $catTerms = \App\Models\Term::whereHas('taxonomy', fn ($q) => $q->where('slug', $catTaxonomy))->orderBy('position')->orderBy('title')->get();
                if ($catTerms->isNotEmpty()) {
                    $sidebarCategories = $catTerms->map(function ($term) use ($transformed) {
                        $count = $transformed->filter(fn ($pkg) => strcasecmp($pkg->category ?? '', $term->title) === 0 || strcasecmp($pkg->category ?? '', $term->slug) === 0)->count();
                        return ['name' => $term->title, 'slug' => $term->slug, 'count' => $count];
                    });
                }
            } catch (\Throwable $e) {
                // fallback
            }
        }

        if ($sidebarCategories->isEmpty()) {
            $sidebarCategories = $transformed->pluck('category')->filter()->groupBy(fn ($item) => $item)->map(function ($group, $key) {
                return ['name' => $key, 'count' => $group->count()];
            })->values();
        }

        $sidebarRecentPackages = $transformed->take(3);
    }

    // Fallback demo packages if no entries exist in database
    if ($packages->isEmpty() && empty($searchQuery) && empty($categoryTag) && empty($destinationTag) && $minPriceFilter === null && $maxPriceFilter === null) {
        $demoList = collect([
            (object) [
                'id' => 1,
                'title' => 'Sylhet Adventure & Hiking Tour',
                'slug' => 'sylhet-adventure-hiking-tour',
                'link' => '#',
                '_link' => '#',
                'image' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Hiking in Jaintiapur Hills, trekking to Hamham Waterfall, exploring Khadimnagar National Park & Khasi village life.',
                'price' => 8500,
                'originalPrice' => 9500,
                'destination' => 'Sylhet',
                'category' => 'Adventure',
                'duration' => '4 Days / 3 Nights',
                'badge' => '10% OFF',
            ],
            (object) [
                'id' => 2,
                'title' => 'Cox\'s Bazar Luxury Beach Resort Escape',
                'slug' => 'coxs-bazar-luxury-beach-resort',
                'link' => '#',
                '_link' => '#',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Relax on the world\'s longest natural sea beach with 5-star resort stay and sunset cruises.',
                'price' => 14500,
                'originalPrice' => 18000,
                'destination' => 'Cox\'s Bazar',
                'category' => 'Luxury',
                'duration' => '3 Days / 2 Nights',
                'badge' => '20% OFF',
            ],
            (object) [
                'id' => 3,
                'title' => 'Sreemangal Tea Garden & Wildlife Sanctuary',
                'slug' => 'sreemangal-tea-garden-wildlife',
                'link' => '#',
                '_link' => '#',
                'image' => 'https://images.unsplash.com/photo-1596895111956-bf1cf0599ce5?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Explore Lawachara Rain Forest, seven-layer tea cabin, cycling through tea estates and birdwatching.',
                'price' => 6500,
                'originalPrice' => 7500,
                'destination' => 'Sreemangal',
                'category' => 'Nature',
                'duration' => '3 Days / 2 Nights',
                'badge' => null,
            ],
            (object) [
                'id' => 4,
                'title' => 'Saint Martin Island Coral Beach Expedition',
                'slug' => 'saint-martin-island-coral-beach',
                'link' => '#',
                '_link' => '#',
                'image' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Experience crystal clear ocean water, night camping under starry sky, fresh seafood, and boat rides.',
                'price' => 11200,
                'originalPrice' => 13500,
                'destination' => 'Saint Martin',
                'category' => 'Beach',
                'duration' => '4 Days / 3 Nights',
                'badge' => 'Popular',
            ],
            (object) [
                'id' => 5,
                'title' => 'Bandarban Hill Tracts & Nilgiri Summit',
                'slug' => 'bandarban-hill-tracts-nilgiri',
                'link' => '#',
                '_link' => '#',
                'image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
                'excerpt' => 'Cloud watching at Nilgiri resort, Chimbuk hill viewpoint, Boga Lake trekking and Golden Temple exploration.',
                'price' => 9800,
                'originalPrice' => 12000,
                'destination' => 'Bandarban',
                'category' => 'Trekking',
                'duration' => '3 Days / 2 Nights',
                'badge' => 'Best Seller',
            ],
        ]);

        $totalPackages = $demoList->count();
        $totalPages = 1;
        $packages = $demoList;

        $sidebarDestinations = collect([
            ['name' => 'Sylhet', 'count' => 12],
            ['name' => 'Cox\'s Bazar', 'count' => 8],
            ['name' => 'Sreemangal', 'count' => 5],
            ['name' => 'Saint Martin', 'count' => 6],
            ['name' => 'Bandarban', 'count' => 9],
        ]);

        $sidebarCategories = collect([
            ['name' => 'Adventure', 'count' => 10],
            ['name' => 'Luxury', 'count' => 6],
            ['name' => 'Nature', 'count' => 9],
            ['name' => 'Beach', 'count' => 7],
            ['name' => 'Trekking', 'count' => 8],
        ]);

        $sidebarRecentPackages = $demoList->take(3)->map(fn($item) => (array) $item);
    }

    // Pagination numbers calculation matching blog-list.blade.php
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
        $current = request()->only(['category', 'destination', 'search', 'min_price', 'max_price']);
        return url()->current() . '?' . http_build_query(array_merge($current, $params));
    };
@endphp

<section data-block="packageList">
    {{-- Main Content --}}
    <div class="mx-auto max-w-7xl px-6 py-16">
        
        {{-- Active Filters Bar --}}
        @if($categoryTag || $destinationTag || $searchQuery || $maxPriceFilter !== null || $minPriceFilter !== null)
            <div class="mb-6 flex flex-wrap items-center gap-2">
                <span class="text-gray-600 font-medium text-sm">Filtering by:</span>
                
                @if($destinationTag)
                    <span class="inline-flex items-center gap-1 bg-brand text-white px-3 py-1 rounded text-sm shadow-sm">
                        Destination: {{ $destinationTag }}
                        <a href="{{ $buildUrl(['destination' => null, 'page' => null]) }}" class="hover:bg-brand-hover rounded p-0.5" aria-label="Clear destination filter">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/></svg>
                        </a>
                    </span>
                @endif

                @if($categoryTag)
                    <span class="inline-flex items-center gap-1 bg-brand text-white px-3 py-1 rounded text-sm shadow-sm">
                        Category: {{ $categoryTag }}
                        <a href="{{ $buildUrl(['category' => null, 'page' => null]) }}" class="hover:bg-brand-hover rounded p-0.5" aria-label="Clear category filter">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/></svg>
                        </a>
                    </span>
                @endif

                @if($searchQuery)
                    <span class="inline-flex items-center gap-1 bg-brand text-white px-3 py-1 rounded text-sm shadow-sm">
                        Search: "{{ $searchQuery }}"
                        <a href="{{ $buildUrl(['search' => null, 'page' => null]) }}" class="hover:bg-brand-hover rounded p-0.5" aria-label="Clear search filter">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/></svg>
                        </a>
                    </span>
                @endif

                @if($maxPriceFilter !== null || $minPriceFilter !== null)
                    <span class="inline-flex items-center gap-1 bg-brand text-white px-3 py-1 rounded text-sm shadow-sm">
                        Price: ৳{{ $minPriceFilter ?? 0 }} - ৳{{ $maxPriceFilter ?? $maxPriceLimit }}
                        <a href="{{ $buildUrl(['min_price' => null, 'max_price' => null, 'page' => null]) }}" class="hover:bg-brand-hover rounded p-0.5" aria-label="Clear price filter">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/></svg>
                        </a>
                    </span>
                @endif

                <a href="{{ url()->current() }}" class="text-xs font-semibold text-gray-500 hover:text-brand underline ml-2">Clear all</a>
            </div>
        @endif

        {{-- 2-Column Grid Layout: Sidebar LEFT Column & List Packages RIGHT Column --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            {{-- Sidebar LEFT Column --}}
            <aside class="space-y-8 lg:sticky lg:top-24 lg:self-start">
                
                {{-- Search Here --}}
                <div>
                    <h3 class="mb-4 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                        <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Search Here
                    </h3>
                    <form method="GET" action="{{ url()->current() }}" class="group relative">
                        @if($categoryTag) <input type="hidden" name="category" value="{{ $categoryTag }}"> @endif
                        @if($destinationTag) <input type="hidden" name="destination" value="{{ $destinationTag }}"> @endif
                        <input type="text" name="search" placeholder="Search this page..." value="{{ request()->query('search') }}"
                               class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 pr-10 text-sm shadow-sm transition-all placeholder:text-gray-400 hover:border-gray-300 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20" />
                        <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 transition-colors group-focus-within:text-brand" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        @if(request()->query('search'))
                            <a href="{{ $buildUrl(['search' => null, 'page' => null]) }}"
                               class="absolute right-9 top-1/2 -translate-y-1/2 rounded-full p-0.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600"
                               aria-label="Clear search">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Price Filter --}}
                <div>
                    <h3 class="mb-4 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                        <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        Price Filter (৳)
                    </h3>
                    <form method="GET" action="{{ url()->current() }}" class="space-y-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                        @if($searchQuery) <input type="hidden" name="search" value="{{ $searchQuery }}"> @endif
                        @if($categoryTag) <input type="hidden" name="category" value="{{ $categoryTag }}"> @endif
                        @if($destinationTag) <input type="hidden" name="destination" value="{{ $destinationTag }}"> @endif
                        
                        <input type="range" 
                               min="0" 
                               max="{{ $maxPriceLimit }}" 
                               step="500" 
                               name="max_price"
                               value="{{ $maxPriceFilter ?? $maxPriceLimit }}" 
                               onchange="this.form.submit()"
                               class="w-full accent-brand cursor-pointer" />

                        <div class="flex items-center gap-2 text-sm">
                            <div class="relative flex-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">৳</span>
                                <input type="number" name="min_price" value="{{ $minPriceFilter ?? 0 }}" class="w-full rounded-lg border border-gray-200 bg-white py-1.5 pl-6 pr-2 text-xs font-medium focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20" />
                            </div>
                            <span class="text-gray-300">—</span>
                            <div class="relative flex-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">৳</span>
                                <input type="number" name="max_price" value="{{ $maxPriceFilter ?? $maxPriceLimit }}" class="w-full rounded-lg border border-gray-200 bg-white py-1.5 pl-6 pr-2 text-xs font-medium focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20" />
                            </div>
                            <button type="submit" class="rounded-lg bg-brand px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-brand-hover">
                                Go
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Destinations --}}
                @if($sidebarDestinations->isNotEmpty())
                    <div>
                        <h3 class="mb-4 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                            <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                            Destinations
                        </h3>
                        <ul class="space-y-1 rounded-xl border border-gray-100 bg-white p-2 shadow-sm">
                            @foreach($sidebarDestinations as $dest)
                                @php
                                    $active = $destinationTag && strcasecmp($destinationTag, $dest['name']) === 0;
                                @endphp
                                <li>
                                    <a href="{{ $buildUrl(['destination' => $active ? null : $dest['name'], 'page' => null]) }}"
                                       class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ $active ? 'bg-brand text-white shadow-sm shadow-brand/20' : 'text-gray-700 hover:bg-brand/5 hover:text-brand' }}">
                                        <span class="flex items-center gap-2.5">
                                            <span class="h-2 w-2 rounded-full transition-all {{ $active ? 'bg-white scale-110' : 'bg-gray-300' }}"></span>
                                            {{ $dest['name'] }}
                                        </span>
                                        <span class="text-xs font-mono opacity-80">({{ $dest['count'] }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Categories / Tour Type --}}
                @if($sidebarCategories->isNotEmpty())
                    <div>
                        <h3 class="mb-4 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                            <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                            Categories
                        </h3>
                        <ul class="space-y-1 rounded-xl border border-gray-100 bg-white p-2 shadow-sm">
                            @foreach($sidebarCategories as $cat)
                                @php
                                    $active = $categoryTag && strcasecmp($categoryTag, $cat['name']) === 0;
                                @endphp
                                <li>
                                    <a href="{{ $buildUrl(['category' => $active ? null : $cat['name'], 'page' => null]) }}"
                                       class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition-all duration-200 {{ $active ? 'bg-brand text-white shadow-sm shadow-brand/20' : 'text-gray-700 hover:bg-brand/5 hover:text-brand' }}">
                                        <span class="flex items-center gap-2.5">
                                            <span class="h-2 w-2 rounded-full transition-all {{ $active ? 'bg-white scale-110' : 'bg-gray-300' }}"></span>
                                            {{ $cat['name'] }}
                                        </span>
                                        <span class="text-xs font-mono opacity-80">({{ $cat['count'] }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Recent Packages --}}
                @if($sidebarRecentPackages->isNotEmpty())
                    <div>
                        <h3 class="mb-4 flex items-center gap-2 text-base font-bold tracking-tight text-gray-900">
                            <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 16 14"/></svg>
                            Recent Packages
                        </h3>
                        <div class="space-y-4 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                            @foreach($sidebarRecentPackages as $rPkg)
                                @php $rPkg = (object) $rPkg; @endphp
                                <a href="{{ $rPkg->_link ?? '#' }}" class="group flex gap-3 rounded-lg p-1.5 transition-colors hover:bg-gray-50">
                                    @if(!empty($rPkg->image))
                                        <div class="relative h-16 w-20 shrink-0 overflow-hidden rounded-lg">
                                            <img src="{{ $rPkg->image }}" alt="{{ $rPkg->title }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                        </div>
                                    @else
                                        <div class="flex h-16 w-20 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-gray-100 to-gray-200">
                                            <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/></svg>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <p class="mb-1 inline-flex items-center gap-1 text-[11px] font-semibold text-brand">
                                            ৳{{ number_format($rPkg->price) }} / person
                                        </p>
                                        <p class="line-clamp-2 text-sm font-semibold leading-snug text-gray-800 transition-colors group-hover:text-brand">{{ $rPkg->title }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </aside>

            {{-- Packages RIGHT Column — List Mode Only --}}
            <div class="lg:col-span-2">
                @if($packages->isEmpty())
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 bg-white py-16 text-center shadow-sm">
                        <svg class="mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/>
                        </svg>
                        <p class="text-base font-semibold text-gray-700">No tour packages match your search.</p>
                        <p class="mt-1 text-xs text-gray-500">Try adjusting your filters or search criteria</p>
                        <a href="{{ url()->current() }}" class="mt-4 rounded-full bg-brand px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-hover">
                            Reset Filters
                        </a>
                    </div>
                @else
                    {{-- Exclusive List View Layout --}}
                    <div class="flex flex-col gap-5">
                        @foreach($packages as $pkg)
                            <a href="{{ $pkg->_link }}" class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white p-3 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-gray-200 hover:shadow-xl hover:shadow-brand/5 sm:flex-row sm:items-center sm:gap-5">
                                {{-- Thumbnail Image --}}
                                <div class="relative aspect-[16/9] w-full shrink-0 overflow-hidden rounded-xl bg-gray-100 sm:aspect-auto sm:h-44 sm:w-64">
                                    @if($pkg->image)
                                        <img src="{{ $pkg->image }}" alt="{{ $pkg->title }}" data-edit="image" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-110" />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                            <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/></svg>
                                        </div>
                                    @endif
                                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                                    
                                    {{-- Duration Badge --}}
                                    @if($pkg->duration)
                                        <div class="absolute left-3 top-3 flex items-center gap-1 rounded-lg bg-white/95 px-2.5 py-1 text-xs font-bold text-gray-900 shadow-md backdrop-blur-sm ring-1 ring-black/5">
                                            <svg class="h-3.5 w-3.5 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            <span>{{ $pkg->duration }}</span>
                                        </div>
                                    @endif

                                    {{-- Discount Badge --}}
                                    @if($pkg->badge)
                                        <span class="absolute top-2 right-2 rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white shadow">
                                            {{ $pkg->badge }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Details --}}
                                <div class="flex flex-1 flex-col justify-between p-3 sm:p-2 sm:pr-4">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                            @if($pkg->destination)
                                                <span class="inline-flex items-center gap-1 font-semibold text-brand">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                                                    <span data-edit="destination">{{ $pkg->destination }}</span>
                                                </span>
                                            @endif
                                            @if($pkg->category)
                                                @if($pkg->destination) <span class="text-gray-300">•</span> @endif
                                                <span class="inline-flex items-center gap-1 text-gray-600">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                                                    <span data-edit="category">{{ $pkg->category }}</span>
                                                </span>
                                            @endif
                                        </div>

                                        <h3 data-edit="title" class="mt-2 line-clamp-1 text-lg font-bold leading-snug tracking-tight text-gray-900 transition-colors duration-200 group-hover:text-brand sm:text-xl">{{ $pkg->title }}</h3>
                                        @if($pkg->excerpt)
                                            <p data-edit="excerpt" class="mt-1.5 line-clamp-2 text-xs leading-relaxed text-gray-500 sm:text-sm">{{ $pkg->excerpt }}</p>
                                        @endif
                                    </div>

                                    <div class="mt-4 flex items-center justify-between pt-3 border-t border-gray-100/80">
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="text-xl font-bold tracking-tight text-gray-900">৳{{ number_format($pkg->price) }}</span>
                                            @if($pkg->originalPrice > $pkg->price)
                                                <span class="text-xs text-gray-400 line-through">৳{{ number_format($pkg->originalPrice) }}</span>
                                            @endif
                                            <span class="text-[11px] font-medium text-gray-400">/person</span>
                                        </div>

                                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand transition-all duration-200 group-hover:gap-2.5">
                                            Book now
                                            <svg class="h-4 w-4 transition-transform duration-200 group-hover:rotate-45" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M7 17l9.2-9.2M17 17V7H7"/></svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Pagination Controls matching blog-list.blade.php --}}
                    @if($totalPages > 1)
                        <nav aria-label="Package pagination" class="mt-14 flex flex-col items-center gap-4">
                            <div class="flex items-center justify-center gap-1.5 sm:gap-2">
                                <a href="{{ $buildUrl(['page' => $currentPage - 1]) }}"
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
                                           aria-current="{{ $b === $currentPage ? 'page' : '' }}"
                                           class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-semibold transition-all duration-200 {{ $b === $currentPage ? 'scale-105 bg-gradient-to-br from-brand to-brand-hover text-white shadow-md shadow-brand/30' : 'border border-gray-200 bg-white text-gray-700 hover:border-brand hover:text-brand' }}">
                                            {{ str_pad($b, 2, '0', STR_PAD_LEFT) }}
                                        </a>
                                    @endif
                                @endforeach

                                <a href="{{ $buildUrl(['page' => $currentPage + 1]) }}"
                                   class="inline-flex h-10 items-center justify-center gap-1 rounded-full border border-gray-200 bg-white px-3 text-sm font-medium text-gray-700 transition-all hover:border-brand hover:text-brand {{ $currentPage >= $totalPages ? 'pointer-events-none opacity-40' : '' }}"
                                   aria-label="Next page">
                                    <span class="hidden sm:inline">Next</span>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                                </a>
                            </div>
                            <p class="text-xs font-medium text-gray-400">
                                Page <span class="text-gray-600">{{ $currentPage }}</span> of {{ $totalPages }}
                                @if($totalPackages > 0)
                                    · <span class="text-gray-600">{{ $totalPackages }}</span> packages
                                @endif
                            </p>
                        </nav>
                    @endif
                @endif
            </div>

        </div>
    </div>
</section>
