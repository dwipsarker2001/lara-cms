<?php

namespace App\Blocks\custom;

use App\Blocks\Block;
use App\Blocks\Field;
use App\Models\Collection;
use App\Models\Taxonomy;
use Illuminate\Support\Facades\Schema;

class PackageList extends Block
{
    public string $name = 'packageList';

    public string $label = 'Package List';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::select('packageCollection', 'Select Collection', self::collectionOptions(), default: ''),
            Field::select('destinationTaxonomy', 'Destination Taxonomy', self::taxonomyOptions('Select Destination Taxonomy'), default: ''),
            Field::select('categoryTaxonomy', 'Category Taxonomy', self::taxonomyOptions('Select Category Taxonomy'), default: ''),
            Field::number('packagesPerPage', 'Packages Per Page', default: 6),
            Field::number('priceMax', 'Price Filter Max (৳)', default: 500000),
        ];
    }

    /** Helper to build collection select options */
    protected static function collectionOptions(): array
    {
        $options = [
            ['value' => '', 'label' => 'All Package Collections (Auto)'],
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
