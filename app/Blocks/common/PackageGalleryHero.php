<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageGalleryHero extends Block
{
    public string $name = 'packageGalleryHero';

    public string $label = 'Package Gallery Hero';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::string('title', 'Title'),
            Field::string('location', 'Location'),
            Field::number('rating', 'Rating'),
            Field::number('reviewCount', 'Reviews'),
            Field::list('images', 'Image', [
                Field::image('image', 'Image'),
            ]),
        ];
    }
}
