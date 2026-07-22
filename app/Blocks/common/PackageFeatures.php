<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageFeatures extends Block
{
    public string $name = 'packageFeatures';

    public string $label = 'Package Features';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Package Features'),
            Field::list('includes', 'Include', [
                Field::text('text', 'Text', default: 'Accommodation and meals as per itinerary'),
            ]),
            Field::list('excludes', 'Exclude', [
                Field::text('text', 'Text', default: 'International flights and personal expenses'),
            ]),
        ];
    }
}
