<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageHero extends Block
{
    public string $name = 'packageHero';

    public string $label = 'Package Hero';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::string('title', 'Title'),
            Field::string('duration', 'Duration'),
            Field::number('price', 'Price'),
            Field::list('images', 'Image', [
                Field::image('image', 'Image'),
            ]),
        ];
    }
}
