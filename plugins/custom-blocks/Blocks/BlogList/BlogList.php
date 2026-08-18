<?php

namespace Plugins\CustomBlocks\Blocks\BlogList;

use App\Blocks\CardSlot;
use App\Blocks\Field;
use App\Blocks\ListBlock;
use App\Models\Collection;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * BlogList Block
 *
 * A collection-powered listing block for blog posts/articles/news.
 * Extends ListBlock — so card slot field mapping is handled automatically in the admin panel.
 *
 * The admin selects:
 *   1. Which Collection to pull entries from (or auto-detect post collections).
 *   2. Which collection input maps to each card slot (Title, Image, Excerpt, Author, Date, Category).
 *   3. Layout (grid/list), per-page count, Category Taxonomy, and Tag Taxonomy.
 */
class BlogList extends ListBlock
{
    public string $name = 'blogList';

    public string $label = 'Blog List';

    public bool $background = false;

    /**
     * Declare the card slots this block needs to fill.
     * ListBlock will auto-generate one Field::select() per slot.
     *
     * @return CardSlot[]
     */
    protected function cardSchema(): array
    {
        return [
            CardSlot::text('title', 'Post Title'),
            CardSlot::image('image', 'Featured Image'),
            CardSlot::text('excerpt', 'Excerpt / Description'),
            CardSlot::text('author', 'Author Name'),
            CardSlot::text('date', 'Publish Date'),
            CardSlot::text('category', 'Category'),
        ];
    }

    /**
     * Blog-specific block-level controls added after the card slot selectors.
     *
     * @return array<int, array>
     */
    protected function baseFields(): array
    {
        return [
            Field::select('layout', 'Posts Layout', [
                ['value' => 'grid', 'label' => 'Grid (Box)'],
                ['value' => 'list', 'label' => 'List'],
            ], default: 'grid'),
            Field::number('postsPerPage', 'Posts Per Page', default: 6),
            Field::select('categoryTaxonomy', 'Category Taxonomy', self::taxonomyOptions('Select Category Taxonomy'), default: ''),
            Field::select('tagTaxonomy', 'Tag Taxonomy', self::taxonomyOptions('Select Tag Taxonomy'), default: ''),
        ];
    }

    /** Helper to build collection select options */
    protected static function collectionOptions(): array
    {
        $options = [
            ['value' => '', 'label' => 'All Post Collections (Auto)'],
        ];

        try {
            if (Schema::hasTable('collections')) {
                $cols = Collection::select('slug', 'name')->get();
                foreach ($cols as $col) {
                    if ($col->slug !== 'pages') {
                        $options[] = [
                            'value' => $col->slug,
                            'label' => $col->name,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return $options;
    }

    /** Taxonomy select options helper. */
    protected static function taxonomyOptions(string $placeholder = 'Select Taxonomy'): array
    {
        $options = [
            ['value' => '', 'label' => '-- '.$placeholder.' (Empty) --'],
        ];

        try {
            if (Schema::hasTable('taxonomies')) {
                $taxonomies = Taxonomy::select('slug', 'title')->orderBy('title')->get();
                foreach ($taxonomies as $tax) {
                    $options[] = [
                        'value' => $tax->slug,
                        'label' => $tax->title,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return $options;
    }

    /**
     * Resolves a card object from a CollectionEntry for BlogList.
     * Provides smart fallbacks for unmapped fields and ensures all card properties
     * are formatted into safe scalar values for Blade templates.
     */
    public function resolveCard(object $entry, array $mappings): object
    {
        $card = parent::resolveCard($entry, $mappings);
        $eData = $entry->data ?? [];

        if (empty($card->title) || $card->title === 'Untitled Post') {
            $card->title = $eData['title'] ?? $entry->title ?? null;
            if ((empty($card->title) || $card->title === 'Entry #'.$entry->id) && ! empty($entry->sections) && is_array($entry->sections)) {
                foreach ($entry->sections as $sec) {
                    if (! empty($sec['data']['title'])) {
                        $card->title = $sec['data']['title'];
                        break;
                    }
                }
            }
            if (empty($card->title)) {
                $card->title = $entry->title ?? 'Untitled Post';
            }
        }
        if (empty($card->image)) {
            $card->image = $eData['featured_image']
                ?? $eData['image']
                ?? $eData['hero_image']
                ?? $eData['socialImage']
                ?? $eData['banner_img']
                ?? $eData['cover_image']
                ?? $eData['thumbnail']
                ?? $eData['thumb']
                ?? $entry->meta['featured_image']
                ?? $entry->meta['image']
                ?? null;

            if (empty($card->image) && ! empty($entry->sections) && is_array($entry->sections)) {
                foreach ($entry->sections as $sec) {
                    $secImg = $sec['data']['featured_image']
                        ?? $sec['data']['image']
                        ?? $sec['data']['hero_image']
                        ?? null;
                    if (! empty($secImg)) {
                        $card->image = $secImg;
                        break;
                    }
                }
            }
        }
        if (empty($card->excerpt)) {
            $rawDescription = $eData['excerpt']
                ?? $eData['description']
                ?? $eData['summary']
                ?? $eData['content']
                ?? $eData['body']
                ?? $entry->meta['metaDescription']
                ?? null;

            if (empty($rawDescription) && ! empty($entry->sections) && is_array($entry->sections)) {
                foreach ($entry->sections as $sec) {
                    if (! empty($sec['data']['content'])) {
                        $rawDescription = $sec['data']['content'];
                        break;
                    }
                    if (! empty($sec['data']['description'])) {
                        $rawDescription = $sec['data']['description'];
                        break;
                    }
                    if (! empty($sec['data']['excerpt'])) {
                        $rawDescription = $sec['data']['excerpt'];
                        break;
                    }
                }
            }

            $card->excerpt = ! empty($rawDescription)
                ? Str::limit(Str::squish(strip_tags($rawDescription)), 140)
                : '';
        }
        if (empty($card->author)) {
            $card->author = $eData['created_by'] ?? $eData['author'] ?? ($entry->meta['author'] ?? null);
            if (empty($card->author) && ! empty($entry->sections) && is_array($entry->sections)) {
                foreach ($entry->sections as $sec) {
                    if (! empty($sec['data']['author'])) {
                        $card->author = $sec['data']['author'];
                        break;
                    }
                }
            }
            if (empty($card->author)) {
                $card->author = auth('admin')->user()->name ?? 'Admin';
            }
        }
        if (empty($card->category)) {
            $card->category = $eData['category'] ?? $eData['category_id'] ?? $eData['categories'] ?? $eData['cat'] ?? null;
            if (empty($card->category) && ! empty($entry->sections) && is_array($entry->sections)) {
                foreach ($entry->sections as $sec) {
                    $secCat = $sec['data']['category'] ?? $sec['data']['categories'] ?? $sec['data']['category_id'] ?? $sec['data']['cat'] ?? null;
                    if (! empty($secCat)) {
                        $card->category = $secCat;
                        break;
                    }
                }
            }
        }
        if (empty($card->date)) {
            $card->date = $eData['date'] ?? $eData['publish_date'] ?? null;
            if (empty($card->date) && ! empty($entry->sections) && is_array($entry->sections)) {
                foreach ($entry->sections as $sec) {
                    if (! empty($sec['data']['date'])) {
                        $card->date = $sec['data']['date'];
                        break;
                    }
                }
            }
        }

        foreach (get_object_vars($card) as $key => $val) {
            if ($key === '_entry') {
                continue;
            }
            $card->$key = static::formatSlotValue($val);
        }

        return $card;
    }

    /**
     * Formats slot values into clean scalar strings for Blade rendering.
     */
    public static function formatSlotValue(mixed $value): mixed
    {
        if (is_string($value) && (str_starts_with(trim($value), '[') || str_starts_with(trim($value), '{'))) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            }
        }

        if (is_numeric($value) && Schema::hasTable('terms')) {
            $term = Term::find((int) $value);
            if ($term) {
                return $term->title;
            }
        }

        if (is_string($value) && Schema::hasTable('terms')) {
            $term = Term::where('slug', $value)->orWhere('title', $value)->first();
            if ($term) {
                return $term->title;
            }
        }

        if (is_array($value)) {
            if (Schema::hasTable('terms')) {
                $numericIds = array_map('intval', array_filter($value, fn ($v) => is_numeric($v)));
                $stringVals = array_filter($value, fn ($v) => is_string($v) && ! is_numeric($v));

                $termTitles = [];
                if (! empty($numericIds)) {
                    $termTitles = array_merge($termTitles, Term::whereIn('id', $numericIds)->pluck('title')->toArray());
                }
                if (! empty($stringVals)) {
                    $termTitles = array_merge($termTitles, Term::whereIn('slug', $stringVals)->orWhereIn('title', $stringVals)->pluck('title')->toArray());
                }

                if (! empty($termTitles)) {
                    return implode(', ', array_unique($termTitles));
                }
            }

            if (isset($value['formatted']) && is_string($value['formatted']) && $value['formatted'] !== '') {
                return $value['formatted'];
            }
            if (isset($value['url']) && is_string($value['url'])) {
                return $value['url'];
            }
            if (isset($value['path']) && is_string($value['path'])) {
                return $value['path'];
            }
            if (isset($value['name']) && is_string($value['name'])) {
                return $value['name'];
            }
            if (isset($value['title']) && is_string($value['title'])) {
                return $value['title'];
            }
            if (isset($value['label']) && is_string($value['label'])) {
                return $value['label'];
            }

            $items = [];
            foreach ($value as $item) {
                if (is_string($item) || is_numeric($item)) {
                    $items[] = (string) $item;
                } elseif (is_array($item)) {
                    $extracted = $item['formatted'] ?? $item['name'] ?? $item['title'] ?? $item['label'] ?? null;
                    if ($extracted && (is_string($extracted) || is_numeric($extracted))) {
                        $items[] = (string) $extracted;
                    }
                }
            }

            if (! empty($items)) {
                return implode(', ', $items);
            }

            return '';
        }

        return $value;
    }
}
