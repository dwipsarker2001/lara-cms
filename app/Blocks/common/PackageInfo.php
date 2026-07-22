<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageInfo extends Block
{
    public string $name = 'packageInfo';

    public string $label = 'Package Additional Info';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Additional Info'),
            Field::list('items', 'Info', [
                Field::string('title', 'Title', default: 'Information'),
                Field::text('description', 'Description', default: 'Brief description of this info item.'),
            ]),
        ];
    }
}
