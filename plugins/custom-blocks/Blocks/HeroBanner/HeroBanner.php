<?php

namespace Plugins\CustomBlocks\Blocks\HeroBanner;

use App\Blocks\Block;
use App\Blocks\Field;
use App\Models\Taxonomy;
use Illuminate\Support\Facades\Schema;

class HeroBanner extends Block
{
    public string $name = 'heroBanner';

    public string $label = 'Hero Banner';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::image('backgroundImage', 'Background Image'),
            Field::string('badge', 'Badge', default: 'Your Next Adventure'),
            Field::string('headline', 'Headline', default: 'Discover New Places, Create Lasting Memories'),
            Field::text('description', 'Description', default: 'Handpicked stays, seamless booking, and local experiences — everything you need to plan your next adventure with confidence.'),
            Field::link('searchUrl', 'Search URL', default: '/tours'),
            Field::select('destinationTaxonomy', 'Destination Taxonomy', self::taxonomyOptions('Select Destination Taxonomy'), default: ''),
            Field::string('searchPlaceholder', 'Search Placeholder', default: 'Where do you want to go?'),
            Field::string('datePlaceholder', 'Date Placeholder', default: 'Add dates'),
        ];
    }

    /** Helper to build taxonomy select options */
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
