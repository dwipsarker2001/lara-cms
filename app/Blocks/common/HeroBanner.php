<?php

namespace App\Blocks\common;

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
            Field::string('headline', 'Headline', default: 'Discover New Places, Create Lasting Memories'),
            Field::text('description', 'Description', default: 'Handpicked stays, seamless booking, and local experiences — everything you need to plan your next adventure with confidence.'),
            Field::link('searchUrl', 'Search URL', default: '/tours'),
            Field::string('searchPlaceholder', 'Search Placeholder', default: 'Where do you want to go?'),
            Field::string('datePlaceholder', 'Date Placeholder', default: 'Add dates'),
        ];
    }
}
