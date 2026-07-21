<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class FeatureImageCards extends Block
{
    public string $name = 'featureImageCards';

    public string $label = 'Feature Image Cards';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline'),
            Field::text('description', 'Description'),
            Field::list('cards', 'Cards', [
                Field::image('image', 'Image', default: '/placeholder-image.png'),
                Field::string('title', 'Title'),
                Field::text('description', 'Description'),
            ]),
        ];
    }
}
