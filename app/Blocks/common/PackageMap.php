<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageMap extends Block
{
    public string $name = 'packageMap';

    public string $label = 'Package Map';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Package Destination Map'),
            Field::image('mapImage', 'Map Image'),
        ];
    }
}
