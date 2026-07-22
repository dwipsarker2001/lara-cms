<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageLocations extends Block
{
    public string $name = 'packageLocations';

    public string $label = 'Package Locations';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Explore Locations'),
            Field::list('locations', 'Location', [
                Field::string('name', 'Name'),
                Field::string('duration', 'Duration'),
                Field::image('image', 'Image'),
            ]),
        ];
    }
}
