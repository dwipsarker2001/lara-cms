<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class FeaturesTwoCol extends Block
{
    public string $name = 'featuresTwoCol';

    public string $label = 'Features Two Column';

    public function fields(): array
    {
        return [
            Field::string('badge', 'Badge Text', default: 'Task Management'),
            Field::string('headline', 'Headline', default: 'All Your Tasks, Organized Effortlessly'),
            Field::image('image', 'Illustration Image', default: '/placeholder-image.png'),
            Field::list('features', 'Feature', [
                Field::string('title', 'Title', default: 'Feature'),
                Field::string('description', 'Description', default: 'Feature description.'),
            ], count: 4),
        ];
    }
}
