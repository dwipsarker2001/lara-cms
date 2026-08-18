<?php

namespace Plugins\CustomBlocks\Support;

use App\Models\CollectionEntry;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Plugins\CustomBlocks\Blocks\BlogList\BlogList;

class BlogSidebarData
{
    /**
     * Get recent blog posts dynamically from the database.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getRecentPosts(int $limit = 5, ?string $collectionSlug = null): array
    {
        if (! Schema::hasTable('collection_entries')) {
            return self::defaultRecentPosts();
        }

        try {
            $query = CollectionEntry::where('published', true);

            if (! empty($collectionSlug)) {
                $query->whereHas('collection', fn ($q) => $q->where('slug', $collectionSlug));
            } else {
                $query->whereHas('collection', function ($q) {
                    $q->whereIn('slug', ['posts', 'blog', 'blogs', 'news', 'articles'])
                        ->orWhereNotIn('slug', ['pages', 'layouts', 'packages', 'tours', 'destinations']);
                });
            }

            $entries = $query->latest()
                ->take($limit)
                ->get();

            if ($entries->isEmpty()) {
                return self::defaultRecentPosts();
            }

            return $entries->map(function (CollectionEntry $entry) {
                $data = $entry->data ?? [];

                // 1. Image resolution (data -> sections -> meta)
                $image = $data['featured_image']
                    ?? $data['image']
                    ?? $data['hero_image']
                    ?? $data['socialImage']
                    ?? $data['banner_img']
                    ?? $data['cover_image']
                    ?? $data['thumbnail']
                    ?? $data['thumb']
                    ?? $entry->meta['featured_image']
                    ?? $entry->meta['image']
                    ?? null;

                if (empty($image) && ! empty($entry->sections) && is_array($entry->sections)) {
                    foreach ($entry->sections as $sec) {
                        $secImg = $sec['data']['featured_image']
                            ?? $sec['data']['image']
                            ?? $sec['data']['hero_image']
                            ?? null;
                        if (! empty($secImg)) {
                            $image = $secImg;
                            break;
                        }
                    }
                }

                // 2. Title resolution
                $title = $data['title'] ?? $entry->title ?? null;
                if ((empty($title) || $title === 'Entry #'.$entry->id) && ! empty($entry->sections) && is_array($entry->sections)) {
                    foreach ($entry->sections as $sec) {
                        if (! empty($sec['data']['title'])) {
                            $title = $sec['data']['title'];
                            break;
                        }
                    }
                }
                if (empty($title)) {
                    $title = $entry->title;
                }

                // 3. Category resolution
                $categoryRaw = $data['categories'] ?? $data['category'] ?? $data['category_id'] ?? $data['cat'] ?? null;
                if (empty($categoryRaw) && ! empty($entry->sections) && is_array($entry->sections)) {
                    foreach ($entry->sections as $sec) {
                        $secCat = $sec['data']['category'] ?? $sec['data']['categories'] ?? $sec['data']['category_id'] ?? null;
                        if (! empty($secCat)) {
                            $categoryRaw = $secCat;
                            break;
                        }
                    }
                }

                $category = null;
                if ($categoryRaw) {
                    $category = BlogList::formatSlotValue($categoryRaw);
                    if (is_array($category)) {
                        $category = implode(', ', array_filter($category));
                    }
                }

                // 4. Author resolution
                $author = $data['created_by']
                    ?? $data['author']
                    ?? $data['user']
                    ?? ($entry->meta['author'] ?? ($entry->meta['created_by'] ?? null));

                if (empty($author) && ! empty($entry->sections) && is_array($entry->sections)) {
                    foreach ($entry->sections as $sec) {
                        if (! empty($sec['data']['author'])) {
                            $author = $sec['data']['author'];
                            break;
                        }
                    }
                }

                if (empty($author) && isset($entry->user_id) && class_exists(User::class)) {
                    $author = User::find($entry->user_id)?->name;
                }
                if (empty($author)) {
                    $author = 'Admin';
                }

                // 5. Date resolution
                $date = $data['date'] ?? $data['publish_date'] ?? null;
                if (empty($date) && ! empty($entry->sections) && is_array($entry->sections)) {
                    foreach ($entry->sections as $sec) {
                        if (! empty($sec['data']['date'])) {
                            $date = $sec['data']['date'];
                            break;
                        }
                    }
                }
                if (empty($date)) {
                    $date = $entry->created_at ? $entry->created_at->format('d M Y') : 'Recent';
                }

                return [
                    'id' => $entry->id,
                    'title' => $title,
                    'author' => is_string($author) && $author !== '' ? $author : 'Admin',
                    'category' => is_string($category) && $category !== '' ? $category : null,
                    'date' => $date,
                    'image' => $image,
                    'link' => $entry->route(),
                ];
            })->all();
        } catch (\Throwable $e) {
            return self::defaultRecentPosts();
        }
    }

    /**
     * Get blog categories with post counts dynamically.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getCategories(?string $collectionSlug = null, ?string $categoryTaxonomy = null): array
    {
        if (! Schema::hasTable('collection_entries')) {
            return self::defaultCategories();
        }

        try {
            $taxonomies = collect();
            $terms = collect();

            if (Schema::hasTable('taxonomies')) {
                if (! empty($categoryTaxonomy)) {
                    $tax = Taxonomy::where('slug', $categoryTaxonomy)
                        ->orWhere('id', is_numeric($categoryTaxonomy) ? (int) $categoryTaxonomy : 0)
                        ->orWhere('title', $categoryTaxonomy)
                        ->first();

                    if ($tax) {
                        $taxonomies = collect([$tax]);
                        if (Schema::hasTable('terms')) {
                            $terms = Term::where('taxonomy_id', $tax->id)
                                ->orderBy('position')
                                ->orderBy('title')
                                ->get();
                        }
                    }
                }

                if ($taxonomies->isEmpty()) {
                    $foundTax = Taxonomy::whereIn('slug', ['categories', 'category', 'blog-categories', 'blog-category', 'post-categories', 'post-category'])
                        ->orWhereIn('title', ['Categories', 'Category', 'Blog Categories', 'Blog Category'])
                        ->get();

                    if ($foundTax->isNotEmpty()) {
                        $taxonomies = $foundTax;
                        if (Schema::hasTable('terms')) {
                            $terms = Term::whereIn('taxonomy_id', $foundTax->pluck('id'))
                                ->orderBy('position')
                                ->orderBy('title')
                                ->get();
                        }
                    }
                }

                if ($taxonomies->isEmpty() && empty($categoryTaxonomy)) {
                    $excludeTaxIds = Taxonomy::whereIn('slug', ['tags', 'tag', 'blog-tags', 'destinations', 'destination'])
                        ->orWhereIn('title', ['Tags', 'Tag', 'Destinations', 'Destination'])
                        ->pluck('id');

                    $taxonomies = Taxonomy::whereNotIn('id', $excludeTaxIds)->orderBy('title')->get();
                    if (Schema::hasTable('terms') && $taxonomies->isNotEmpty()) {
                        $terms = Term::whereIn('taxonomy_id', $taxonomies->pluck('id'))
                            ->orderBy('position')
                            ->orderBy('title')
                            ->get();
                    }
                }
            }

            // Query published blog entries
            $query = CollectionEntry::where('published', true);
            if (! empty($collectionSlug)) {
                $query->whereHas('collection', fn ($q) => $q->where('slug', $collectionSlug));
            } else {
                $query->whereHas('collection', function ($q) {
                    $q->whereIn('slug', ['posts', 'blog', 'blogs', 'news', 'articles'])
                        ->orWhereNotIn('slug', ['pages', 'layouts', 'packages', 'tours', 'destinations']);
                });
            }
            $entries = $query->get();

            // Extract all entry categories mapped by entry ID
            $entryCategoryMap = [];
            $allDiscoveredCategoryStrings = [];
            foreach ($entries as $entry) {
                $cats = self::extractEntryCategories($entry);
                $entryCategoryMap[$entry->id] = $cats;
                foreach ($cats as $c) {
                    if (! is_numeric($c)) {
                        $allDiscoveredCategoryStrings[] = (string) $c;
                    }
                }
            }

            // Build category descriptors
            $categoryList = [];
            $addedSlugs = [];

            // 1. Add terms from taxonomy
            foreach ($terms as $term) {
                $slug = ! empty($term->slug) ? $term->slug : Str::slug($term->title);
                $slugLower = strtolower($slug);
                if (isset($addedSlugs[$slugLower])) {
                    continue;
                }
                $addedSlugs[$slugLower] = true;

                $matchKeys = [
                    (string) $term->id,
                    strtolower($term->title),
                    $slugLower,
                    (string) $term->slug,
                ];

                $categoryList[] = [
                    'name' => $term->title,
                    'slug' => $slug,
                    'match_keys' => array_values(array_filter(array_unique($matchKeys))),
                ];
            }

            // 2. If no terms were added from taxonomies, but taxonomy models exist
            if (empty($categoryList) && $taxonomies->isNotEmpty()) {
                foreach ($taxonomies as $tax) {
                    $slug = ! empty($tax->slug) ? $tax->slug : Str::slug($tax->title);
                    $slugLower = strtolower($slug);
                    if (isset($addedSlugs[$slugLower])) {
                        continue;
                    }
                    $addedSlugs[$slugLower] = true;

                    $matchKeys = [
                        (string) $tax->id,
                        strtolower($tax->title),
                        $slugLower,
                        (string) $tax->slug,
                    ];

                    $categoryList[] = [
                        'name' => $tax->title,
                        'slug' => $slug,
                        'match_keys' => array_values(array_filter(array_unique($matchKeys))),
                    ];
                }
            }

            // 3. Add any categories discovered directly from entries (e.g. from blogDetails section or custom data)
            foreach (array_unique($allDiscoveredCategoryStrings) as $catStr) {
                $slug = Str::slug($catStr);
                $slugLower = strtolower($slug);
                if ($slugLower === '' || isset($addedSlugs[$slugLower])) {
                    continue;
                }

                $matchedTerm = Schema::hasTable('terms')
                    ? Term::where('slug', $slug)->orWhere('title', $catStr)->first()
                    : null;

                $name = $matchedTerm ? $matchedTerm->title : Str::title(str_replace(['-', '_'], ' ', $catStr));
                $catSlug = $matchedTerm ? ($matchedTerm->slug ?: $slug) : $slug;
                $catSlugLower = strtolower($catSlug);

                if (isset($addedSlugs[$catSlugLower])) {
                    continue;
                }
                $addedSlugs[$catSlugLower] = true;

                $matchKeys = [
                    strtolower($catStr),
                    $slugLower,
                    $catSlugLower,
                ];
                if ($matchedTerm) {
                    $matchKeys[] = (string) $matchedTerm->id;
                    $matchKeys[] = strtolower($matchedTerm->title);
                }

                $categoryList[] = [
                    'name' => $name,
                    'slug' => $catSlug,
                    'match_keys' => array_values(array_filter(array_unique($matchKeys))),
                ];
            }

            // If completely empty (no terms, no taxonomies, no entry categories found)
            if (empty($categoryList)) {
                return self::defaultCategories();
            }

            // Calculate precise counts for each category
            $result = [];
            foreach ($categoryList as $catItem) {
                $count = 0;
                $matchKeys = $catItem['match_keys'];

                foreach ($entries as $entry) {
                    $entryCats = $entryCategoryMap[$entry->id] ?? [];
                    $isMatched = false;

                    foreach ($entryCats as $val) {
                        $valStr = strtolower(trim((string) $val));
                        $valSlug = Str::slug((string) $val);

                        foreach ($matchKeys as $mk) {
                            $mkStr = strtolower(trim((string) $mk));
                            if ($valStr === $mkStr || $valSlug === $mkStr) {
                                $isMatched = true;
                                break 2;
                            }
                        }
                    }

                    if ($isMatched) {
                        $count++;
                    }
                }

                $slug = $catItem['slug'];
                $result[] = [
                    'name' => $catItem['name'],
                    'slug' => $slug,
                    'count' => $count,
                    'link' => '?category='.urlencode($slug),
                ];
            }

            return $result;
        } catch (\Throwable $e) {
            return self::defaultCategories();
        }
    }

    /**
     * Get tags dynamically from blog entries or tag taxonomy.
     *
     * @return array<int, string>
     */
    public static function getTags(?string $collectionSlug = null, ?string $tagTaxonomy = null): array
    {
        if (! Schema::hasTable('collection_entries')) {
            return self::defaultTags();
        }

        try {
            if (! empty($tagTaxonomy) && (Schema::hasTable('terms') || Schema::hasTable('taxonomies'))) {
                $tagTaxModel = Taxonomy::where('slug', $tagTaxonomy)
                    ->orWhere('id', is_numeric($tagTaxonomy) ? (int) $tagTaxonomy : 0)
                    ->orWhere('title', $tagTaxonomy)
                    ->first();

                if ($tagTaxModel) {
                    $tagTerms = Term::where('taxonomy_id', $tagTaxModel->id)
                        ->orderBy('position')
                        ->orderBy('title')
                        ->pluck('title')
                        ->toArray();

                    if (! empty($tagTerms)) {
                        return $tagTerms;
                    }
                }
            }

            // If no tag taxonomy passed, check if a general Tags taxonomy exists
            if (empty($tagTaxonomy) && Schema::hasTable('taxonomies') && Schema::hasTable('terms')) {
                $tagTaxModel = Taxonomy::whereIn('slug', ['tags', 'tag', 'blog-tags', 'blog-tag', 'post-tags', 'post-tag'])
                    ->orWhereIn('title', ['Tags', 'Tag', 'Blog Tags', 'Blog Tag'])
                    ->first();

                if ($tagTaxModel) {
                    $tagTerms = Term::where('taxonomy_id', $tagTaxModel->id)
                        ->orderBy('position')
                        ->orderBy('title')
                        ->pluck('title')
                        ->toArray();

                    if (! empty($tagTerms)) {
                        return $tagTerms;
                    }
                }
            }

            $query = CollectionEntry::where('published', true);
            if (! empty($collectionSlug)) {
                $query->whereHas('collection', fn ($q) => $q->where('slug', $collectionSlug));
            } else {
                $query->whereHas('collection', function ($q) {
                    $q->whereIn('slug', ['posts', 'blog', 'blogs', 'news', 'articles'])
                        ->orWhereNotIn('slug', ['pages', 'layouts', 'packages', 'tours', 'destinations']);
                });
            }
            $entries = $query->get();

            $tagsSet = [];
            foreach ($entries as $entry) {
                $tags = self::extractEntryTags($entry);
                foreach ($tags as $t) {
                    if ($t !== null && $t !== '') {
                        $tagsSet[trim((string) $t)] = true;
                    }
                }
            }

            if (! empty($tagsSet)) {
                $rawTags = array_keys($tagsSet);
                $numericIds = array_map('intval', array_filter($rawTags, fn ($t) => is_numeric($t)));
                $resolvedMap = [];
                if (! empty($numericIds) && Schema::hasTable('terms')) {
                    $terms = Term::whereIn('id', $numericIds)->get();
                    foreach ($terms as $term) {
                        $resolvedMap[(string) $term->id] = $term->title;
                    }
                }

                $finalTags = [];
                foreach ($rawTags as $t) {
                    $name = $resolvedMap[$t] ?? $t;
                    if ($name && ! in_array($name, $finalTags, true)) {
                        $finalTags[] = (string) $name;
                    }
                }

                if (! empty($finalTags)) {
                    return $finalTags;
                }
            }

            return self::defaultTags();
        } catch (\Throwable $e) {
            return self::defaultTags();
        }
    }

    /**
     * Extract all category values from an entry (checking data, sections, and meta).
     *
     * @param  CollectionEntry|object  $entry
     * @return array<int, string|int>
     */
    public static function extractEntryCategories(object $entry): array
    {
        $values = [];
        $eData = is_array($entry->data ?? null) ? $entry->data : [];

        foreach (['categories', 'category', 'category_id', 'category_ids', 'cat', 'post_category', 'blog_category', 'taxonomy'] as $k) {
            if (isset($eData[$k]) && $eData[$k] !== '' && $eData[$k] !== null) {
                $values[] = $eData[$k];
            }
        }

        if (! empty($entry->sections) && is_array($entry->sections)) {
            foreach ($entry->sections as $sec) {
                $sData = is_array($sec['data'] ?? null) ? $sec['data'] : [];
                foreach (['categories', 'category', 'category_id', 'category_ids', 'cat', 'post_category', 'blog_category', 'taxonomy'] as $k) {
                    if (isset($sData[$k]) && $sData[$k] !== '' && $sData[$k] !== null) {
                        $values[] = $sData[$k];
                    }
                }
            }
        }

        if (! empty($entry->meta) && is_array($entry->meta)) {
            foreach (['category', 'categories', 'category_id'] as $k) {
                if (isset($entry->meta[$k]) && $entry->meta[$k] !== '' && $entry->meta[$k] !== null) {
                    $values[] = $entry->meta[$k];
                }
            }
        }

        $normalized = [];
        foreach ($values as $val) {
            self::flattenValues($val, $normalized);
        }

        return array_values(array_unique(array_filter($normalized, fn ($v) => $v !== null && $v !== '')));
    }

    /**
     * Extract all tag values from an entry (checking data and sections).
     *
     * @param  CollectionEntry|object  $entry
     * @return array<int, string|int>
     */
    public static function extractEntryTags(object $entry): array
    {
        $values = [];
        $eData = is_array($entry->data ?? null) ? $entry->data : [];

        foreach (['tags', 'tag', 'post_tag', 'blog_tags', 'blog_tag'] as $k) {
            if (isset($eData[$k]) && $eData[$k] !== '' && $eData[$k] !== null) {
                $values[] = $eData[$k];
            }
        }

        if (! empty($entry->sections) && is_array($entry->sections)) {
            foreach ($entry->sections as $sec) {
                $sData = is_array($sec['data'] ?? null) ? $sec['data'] : [];
                foreach (['tags', 'tag', 'post_tag', 'blog_tags', 'blog_tag'] as $k) {
                    if (isset($sData[$k]) && $sData[$k] !== '' && $sData[$k] !== null) {
                        $values[] = $sData[$k];
                    }
                }
            }
        }

        $normalized = [];
        foreach ($values as $val) {
            self::flattenValues($val, $normalized);
        }

        return array_values(array_unique(array_filter($normalized, fn ($v) => $v !== null && $v !== '')));
    }

    /**
     * Recursively flattens complex structures (arrays, JSON strings, comma-separated lists, objects)
     * into scalar values.
     */
    public static function flattenValues(mixed $value, array &$output): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (is_string($value) && (str_starts_with(trim($value), '[') || str_starts_with(trim($value), '{'))) {
            $decoded = json_decode($value, true);
            if ($decoded !== null) {
                $value = $decoded;
            }
        }

        if (is_array($value)) {
            if (isset($value['title']) && is_string($value['title'])) {
                $output[] = $value['title'];
            }
            if (isset($value['name']) && is_string($value['name'])) {
                $output[] = $value['name'];
            }
            if (isset($value['slug']) && is_string($value['slug'])) {
                $output[] = $value['slug'];
            }
            if (isset($value['id']) && (is_numeric($value['id']) || is_string($value['id']))) {
                $output[] = (string) $value['id'];
            }
            foreach ($value as $k => $item) {
                if (in_array($k, ['title', 'name', 'slug', 'id'], true)) {
                    continue;
                }
                self::flattenValues($item, $output);
            }

            return;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if (str_contains($trimmed, ',') && ! str_starts_with($trimmed, '{') && ! str_starts_with($trimmed, '[')) {
                foreach (explode(',', $trimmed) as $part) {
                    $partTrimmed = trim($part);
                    if ($partTrimmed !== '') {
                        $output[] = $partTrimmed;
                    }
                }

                return;
            }
            $output[] = $trimmed;

            return;
        }

        if (is_numeric($value)) {
            $output[] = (string) $value;

            return;
        }
    }

    /** Default fallback recent posts */
    protected static function defaultRecentPosts(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'New Blog Post',
                'category' => 'Adventure',
                'author' => 'Admin',
                'date' => '01 Jun 2026',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=150&q=80',
                'link' => '#',
            ],
            [
                'id' => 2,
                'title' => 'বান্দরবান ট্র্যাকিং: পাহাড়ের চূড়ায় মেঘের সাথে খেলা',
                'category' => 'Trekking',
                'author' => 'Admin',
                'date' => '01 Jun 2026',
                'image' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=150&q=80',
                'link' => '#',
            ],
            [
                'id' => 3,
                'title' => 'বান্দরবান ট্র্যাকিং: ২০২৩ সালের চ্যালেঞ্জ ও পাহাড়চূড়ার পার্টি',
                'category' => 'Heritage',
                'author' => 'Admin',
                'date' => '10 Apr 2026',
                'image' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=150&q=80',
                'link' => '#',
            ],
        ];
    }

    /** Default fallback categories */
    protected static function defaultCategories(): array
    {
        return [
            ['name' => 'Adventure', 'slug' => 'adventure', 'count' => 0, 'link' => '?category=adventure'],
            ['name' => 'Heritage', 'slug' => 'heritage', 'count' => 0, 'link' => '?category=heritage'],
            ['name' => 'International', 'slug' => 'international', 'count' => 0, 'link' => '?category=international'],
            ['name' => 'Nature', 'slug' => 'nature', 'count' => 0, 'link' => '?category=nature'],
            ['name' => 'Sylhet', 'slug' => 'sylhet', 'count' => 0, 'link' => '?category=sylhet'],
            ['name' => 'Travel Tips', 'slug' => 'travel-tips', 'count' => 0, 'link' => '?category=travel-tips'],
            ['name' => 'Umrah', 'slug' => 'umrah', 'count' => 0, 'link' => '?category=umrah'],
        ];
    }

    /** Default fallback tags */
    protected static function defaultTags(): array
    {
        return ['Adventure', 'Heritage', 'International', 'Nature', 'Sylhet', 'Travel Tips', 'Umrah'];
    }
}
