<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class FeatureGradientCards extends Block
{
    public string $name = 'featureGradientCards';

    public string $label = 'Feature Gradient Cards';

    public function fields(): array
    {
        return [
            Field::string('badge', 'Badge Text', default: 'Powerful Features'),
            Field::string('headline', 'Headline', default: 'Work Smarter with Powerful Features'),
            Field::text('description', 'Description', default: 'Effortlessly manage tasks, collaborate with teams, and meet deadlines with precision and clarity.'),
            Field::image('image', 'Illustration Image', default: '/placeholder-image.png'),
            Field::list('cards', 'Card', [
                Field::string('text', 'Card Text', default: 'Feature description.'),
            ], count: 4),
        ];
    }
}
