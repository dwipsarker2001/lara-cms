<?php

namespace App\Blocks\Home;

use App\Blocks\Block;
use App\Blocks\Field;

class TravelDeals extends Block
{
    public string $name = 'travelDeals';

    public string $label = 'Travel Deals';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Travel Deals'),
            Field::text('description', 'Description'),
            Field::group('button', 'Button', [
                Field::string('label', 'Label'),
                Field::link('link', 'Link'),
            ]),
            Field::list('cards', 'Cards', [
                Field::image('image', 'Image'),
                Field::string('badge', 'Badge', default: 'Popular'),
                Field::string('title', 'Title'),
                Field::text('description', 'Description'),
                Field::string('priceLabel', 'Price Label', default: 'Per Person'),
                Field::number('price', 'Price', default: 299),
                Field::number('originalPrice', 'Original Price', default: 499),
                Field::string('buttonLabel', 'Button Label', default: 'Book Now'),
                Field::list('features', 'Features', [
                    Field::icon('icon', 'Icon'),
                    Field::string('text', 'Text', default: 'Included'),
                    Field::richText('tooltip', 'Tooltip'),
                ]),
            ], count: 3),
        ];
    }
}
