<?php

namespace App\Support;

use App\Blocks\custom\BlogList;
use App\Models\CollectionEntry;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

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

                if (empty($image) && ! empty($entry->sections)) {
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

                $categoryRaw = $data['categories'] ?? $data['category'] ?? $data['category_id'] ?? $data['cat'] ?? null;
                $category = null;
                if ($categoryRaw) {
                    $category = BlogList::formatSlotValue($categoryRaw);
                    if (is_array($category)) {
                        $category = implode(', ', array_filter($category));
                    }
                }

                $author = $data['created_by']
                    ?? $data['author']
                    ?? $data['user']
                    ?? ($entry->meta['author'] ?? ($entry->meta['created_by'] ?? null));

                if (empty($author) && isset($entry->user_id) && class_exists(User::class)) {
                    $author = User::find($entry->user_id)?->name;
                }
                if (empty($author)) {
                    $author = 'Admin';
                }

                return [
                    'id' => $entry->id,
                    'title' => $entry->title,
                    'author' => is_string($author) && $author !== '' ? $author : 'Admin',
                    'category' => is_string($category) && $category !== '' ? $category : null,
                    'date' => $entry->created_at ? $entry->created_at->format('d M Y') : 'Recent',
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
        if ((! Schema::hasTable('terms') && ! Schema::hasTable('taxonomies')) || ! Schema::hasTable('collection_entries')) {
            return self::defaultCategories();
        }

        try {
            $categories = collect();

            if (! empty($categoryTaxonomy)) {
                $categoryTaxonomyModel = Taxonomy::where('slug', $categoryTaxonomy)
                    ->orWhere('id', is_numeric($categoryTaxonomy) ? (int) $categoryTaxonomy : 0)
                    ->orWhere('title', $categoryTaxonomy)
                    ->first();

                if ($categoryTaxonomyModel) {
                    $categories = Term::where('taxonomy_id', $categoryTaxonomyModel->id)
                        ->orderBy('position')
                        ->orderBy('title')
                        ->get();
                }
            }

            if ($categories->isEmpty()) {
                // If no specific taxonomy selected, find category taxonomies (excluding tags and destinations)
                $categoryTaxonomies = Taxonomy::whereIn('slug', ['categories', 'category', 'blog-categories', 'blog-category', 'post-categories', 'post-category'])
                    ->orWhereIn('title', ['Categories', 'Category', 'Blog Categories', 'Blog Category'])
                    ->get();

                if ($categoryTaxonomies->isNotEmpty()) {
                    $categories = Term::whereIn('taxonomy_id', $categoryTaxonomies->pluck('id'))
                        ->orderBy('position')
                        ->orderBy('title')
                        ->get();
                }
            }

            // Fallback: If still empty and no category taxonomy passed, fetch terms not in tag/destination taxonomies
            if ($categories->isEmpty() && empty($categoryTaxonomy)) {
                $excludeTaxonomyIds = Taxonomy::whereIn('slug', ['tags', 'tag', 'blog-tags', 'destinations', 'destination'])
                    ->orWhereIn('title', ['Tags', 'Tag', 'Destinations', 'Destination'])
                    ->pluck('id');

                $termQuery = Term::query();
                if ($excludeTaxonomyIds->isNotEmpty()) {
                    $termQuery->whereNotIn('taxonomy_id', $excludeTaxonomyIds);
                }
                $categories = $termQuery->orderBy('position')->orderBy('title')->get();

                if ($categories->isEmpty()) {
                    $categories = Taxonomy::whereNotIn('id', $excludeTaxonomyIds)->orderBy('title')->get();
                }
            }

            if ($categories->isEmpty()) {
                return self::defaultCategories();
            }

            $query = CollectionEntry::where('published', true);
            if (! empty($collectionSlug)) {
                $query->whereHas('collection', fn ($q) => $q->where('slug', $collectionSlug));
            } else {
                $query->whereHas('collection', fn ($q) => $q->whereNotIn('slug', ['pages', 'layouts', 'packages', 'tours']));
            }
            $entries = $query->get();

            $result = [];
            foreach ($categories as $cat) {
                $count = 0;
                $catIdStr = (string) $cat->id;
                $catSlugLower = strtolower((string) ($cat->slug ?? ''));
                $catTitleLower = strtolower((string) ($cat->title ?? ''));

                foreach ($entries as $entry) {
                    $eData = $entry->data ?? [];
                    $rawCat = $eData['categories'] ?? $eData['category'] ?? $eData['category_id'] ?? $eData['cat'] ?? null;
                    if ($rawCat !== null) {
                        $catVals = is_array($rawCat)
                            ? $rawCat
                            : (is_string($rawCat) && (str_starts_with(trim($rawCat), '[') || str_starts_with(trim($rawCat), '{')) ? (json_decode($rawCat, true) ?: [$rawCat]) : [$rawCat]);

                        if (! is_array($catVals)) {
                            $catVals = [$catVals];
                        }

                        $matched = false;
                        foreach ($catVals as $val) {
                            if ($val === null || $val === '') {
                                continue;
                            }
                            $valStr = (string) $val;
                            $valLower = strtolower($valStr);
                            if (
                                $valStr === $catIdStr ||
                                ($catSlugLower !== '' && $valLower === $catSlugLower) ||
                                ($catTitleLower !== '' && $valLower === $catTitleLower)
                            ) {
                                $matched = true;
                                break;
                            }
                        }

                        if ($matched) {
                            $count++;
                        }
                    }
                }

                $slug = ! empty($cat->slug) ? $cat->slug : str($cat->title)->slug()->toString();

                $result[] = [
                    'name' => $cat->title,
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
                $query->whereHas('collection', fn ($q) => $q->whereNotIn('slug', ['pages', 'layouts', 'packages', 'tours']));
            }
            $entries = $query->get();

            $tagsSet = [];
            foreach ($entries as $entry) {
                $tags = $entry->data['tags'] ?? $entry->data['tag'] ?? [];
                if (is_string($tags)) {
                    if (str_starts_with(trim($tags), '[') || str_starts_with(trim($tags), '{')) {
                        $decoded = json_decode($tags, true);
                        $tags = is_array($decoded) ? $decoded : array_map('trim', explode(',', $tags));
                    } else {
                        $tags = array_map('trim', explode(',', $tags));
                    }
                }
                if (is_array($tags)) {
                    foreach ($tags as $t) {
                        if ($t !== null && $t !== '') {
                            $tagsSet[trim((string) $t)] = true;
                        }
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
            ['name' => 'Adventure', 'count' => 8, 'active' => true, 'link' => '#'],
            ['name' => 'Heritage', 'count' => 8, 'active' => true, 'link' => '#'],
            ['name' => 'International', 'count' => 6, 'active' => true, 'link' => '#'],
            ['name' => 'Nature', 'count' => 11, 'active' => false, 'link' => '#'],
            ['name' => 'Sylhet', 'count' => 5, 'active' => false, 'link' => '#'],
            ['name' => 'Travel Tips', 'count' => 5, 'active' => false, 'link' => '#'],
            ['name' => 'Umrah', 'count' => 3, 'active' => false, 'link' => '#'],
        ];
    }

    /** Default fallback tags */
    protected static function defaultTags(): array
    {
        return ['Adventure', 'Heritage', 'International', 'Nature', 'Sylhet', 'Travel Tips', 'Umrah'];
    }
}
