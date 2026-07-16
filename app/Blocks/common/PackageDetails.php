<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageDetails extends Block
{
    public string $name = 'packageDetails';

    public string $label = 'Package Details';

    public function fields(): array
    {
        return [
            Field::list('items', 'Item', [
                Field::string('title', 'Title', default: 'Information'),
                Field::text('description', 'Description', default: 'Brief description.'),
                Field::image('mapImage', 'Map Image'),
            ]),
        ];
    }
}
