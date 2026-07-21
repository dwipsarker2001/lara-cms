<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class FeatureListRow extends Block
{
    public string $name = 'featureListRow';

    public string $label = 'Feature Row (Checklist)';

    public function fields(): array
    {
        return [
            Field::string('badge', 'Badge Text', default: 'Powerful Features'),
            Field::string('headline', 'Headline', default: 'Work Smarter with Powerful Features'),
            Field::text('description', 'Description', default: 'Effortlessly manage tasks, collaborate with teams, and meet deadlines with precision and clarity.'),
            Field::image('image', 'Mockup Image', default: '/placeholder-image.png'),
            Field::list('features', 'Features', [
                Field::string('title', 'Title', default: 'Feature Title'),
                Field::text('description', 'Description', default: 'Feature description text goes here.'),
            ], count: 2),
        ];
    }
}
