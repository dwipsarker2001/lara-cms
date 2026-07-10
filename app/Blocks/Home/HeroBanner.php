<?php

namespace App\Blocks\Home;

use App\Blocks\Block;
use App\Blocks\Field;

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
            Field::string('headline', 'Headline', default: 'Discover New Places'),
            Field::text('description', 'Description'),
            Field::link('searchUrl', 'Search URL', default: '/tours'),
            Field::string('searchPlaceholder', 'Search Placeholder', default: 'Where do you want to go?'),
            Field::string('datePlaceholder', 'Date Placeholder', default: 'Add dates'),
        ];
    }
}
