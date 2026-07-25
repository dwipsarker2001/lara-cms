<?php

namespace App\Support;

use App\Models\CollectionEntry;
use App\Models\Taxonomy;
use App\Models\Term;
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
                    $q->whereIn('slug', ['posts', 'blog', 'news', 'articles'])
                        ->orWhere('slug', '!=', 'pages');
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

                return [
                    'id' => $entry->id,
                    'title' => $entry->title,
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
    public static function getCategories(?string $collectionSlug = null): array
    {
        if ((! Schema::hasTable('terms') && ! Schema::hasTable('taxonomies')) || ! Schema::hasTable('collection_entries')) {
            return self::defaultCategories();
        }

        try {
            $categories = Term::orderBy('title')->get();
            if ($categories->isEmpty()) {
                $categories = Taxonomy::orderBy('title')->get();
            }

            if ($categories->isEmpty()) {
                return self::defaultCategories();
            }

            $query = CollectionEntry::where('published', true);
            if (! empty($collectionSlug)) {
                $query->whereHas('collection', fn ($q) => $q->where('slug', $collectionSlug));
            } else {
                $query->whereHas('collection', fn ($q) => $q->where('slug', '!=', 'pages'));
            }
            $entries = $query->get();

            $result = [];
            foreach ($categories as $cat) {
                $count = 0;
                foreach ($entries as $entry) {
                    $catVal = $entry->data['category'] ?? null;
                    if ($catVal) {
                        $catIds = is_array($catVal)
                            ? $catVal
                            : (is_string($catVal) && (str_starts_with(trim($catVal), '[') || str_starts_with(trim($catVal), '{')) ? (json_decode($catVal, true) ?: [$catVal]) : [$catVal]);
                        if (! is_array($catIds)) {
                            $catIds = [$catIds];
                        }
                        $catIdsStr = array_map('strval', $catIds);
                        if (
                            in_array((string) $cat->id, $catIdsStr, true) ||
                            in_array((string) $cat->slug, $catIdsStr, true) ||
                            in_array((string) $cat->title, $catIdsStr, true)
                        ) {
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
     * Get tags dynamically from blog entries.
     *
     * @return array<int, string>
     */
    public static function getTags(?string $collectionSlug = null): array
    {
        if (! Schema::hasTable('collection_entries')) {
            return self::defaultTags();
        }

        try {
            $query = CollectionEntry::where('published', true);
            if (! empty($collectionSlug)) {
                $query->whereHas('collection', fn ($q) => $q->where('slug', $collectionSlug));
            } else {
                $query->whereHas('collection', fn ($q) => $q->whereIn('slug', ['posts', 'blog', 'news']));
            }
            $entries = $query->get();

            $tagsSet = [];
            foreach ($entries as $entry) {
                $tags = $entry->data['tags'] ?? $entry->data['tag'] ?? [];
                if (is_string($tags)) {
                    $tags = array_map('trim', explode(',', $tags));
                }
                if (is_array($tags)) {
                    foreach ($tags as $t) {
                        if ($t && is_string($t)) {
                            $tagsSet[trim($t)] = true;
                        }
                    }
                }
            }

            if (! empty($tagsSet)) {
                return array_keys($tagsSet);
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
                'date' => '01 Jun 2026',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=150&q=80',
                'link' => '#',
            ],
            [
                'id' => 2,
                'title' => 'বান্দরবান ট্র্যাকিং: পাহাড়ের চূড়ায় মেঘের সাথে খেলা',
                'date' => '01 Jun 2026',
                'image' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=150&q=80',
                'link' => '#',
            ],
            [
                'id' => 3,
                'title' => 'বান্দরবান ট্র্যাকিং: ২০২৩ সালের চ্যালেঞ্জ ও পাহাড়চূড়ার পার্টি',
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
