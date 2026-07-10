<?php

namespace App\Blocks\Home;

use App\Blocks\Block;
use App\Blocks\Field;

class FeatureImageCards extends Block
{
    public string $name = 'featureImageCards';

    public string $label = 'Feature Image Cards';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Our Premium Travel Services'),
            Field::text('description', 'Description'),
            Field::list('cards', 'Cards', [
                Field::image('image', 'Image'),
                Field::string('title', 'Title', default: 'Hotel & Resort Booking'),
                Field::text('description', 'Description'),
            ], count: 4),
        ];
    }
}
