<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageAbout extends Block
{
    public string $name = 'packageAbout';

    public string $label = 'Package About';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'About Tour Package'),
            Field::richText('description', 'Description'),
            Field::list('infoItems', 'Info Item', [
                Field::string('label', 'Label', default: 'Destination'),
                Field::string('value', 'Value', default: 'Paris, France'),
            ]),
        ];
    }
}
