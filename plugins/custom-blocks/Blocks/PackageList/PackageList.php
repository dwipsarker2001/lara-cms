<?php

namespace Plugins\CustomBlocks\Blocks\PackageList;

use App\Blocks\CardSlot;
use App\Blocks\Field;
use App\Blocks\ListBlock;
use App\Models\Taxonomy;
use Illuminate\Support\Facades\Schema;

/**
 * PackageList Block
 *
 * A collection-powered listing block for tour packages.
 * Extends ListBlock — so all card slot field mapping is handled automatically.
 *
 * The admin selects:
 *   1. Which Collection to pull entries from.
 *   2. Which collection input maps to each card slot (Title, Image, Price, etc.).
 *   3. Taxonomy filters, per-page count, and price range.
 */
class PackageList extends ListBlock
{
    public string $name = 'packageList';

    public string $label = 'Package List';

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
            CardSlot::text('title', 'Card Title'),
            CardSlot::image('image', 'Card Thumbnail Image'),
            CardSlot::price('price', 'Current Price'),
            CardSlot::price('originalPrice', 'Original Price'),
            CardSlot::text('excerpt', 'Description / Excerpt'),
            CardSlot::text('destination', 'Destination'),
            CardSlot::text('category', 'Category'),
            CardSlot::text('duration', 'Duration'),
            CardSlot::text('badge', 'Discount Badge'),
        ];
    }

    /**
     * Package-specific block-level controls added after the card slot selectors.
     *
     * @return array<int, array>
     */
    protected function baseFields(): array
    {
        return [
            Field::select('destinationTaxonomy', 'Destination Taxonomy', self::taxonomyOptions('Select Destination Taxonomy'), default: ''),
            Field::select('categoryTaxonomy', 'Category Taxonomy', self::taxonomyOptions('Select Category Taxonomy'), default: ''),
            Field::number('packagesPerPage', 'Packages Per Page', default: 6),
            Field::number('priceMax', 'Price Filter Max (৳)', default: 500000),
        ];
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
}
