<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class FeatureGridRow extends Block
{
    public string $name = 'featureGridRow';

    public string $label = 'Feature Row (2x2 Grid)';

    public function fields(): array
    {
        return [
            Field::string('badge', 'Badge Text', default: 'Task Management'),
            Field::string('headline', 'Headline', default: 'All Your Tasks, Organized Effortlessly'),
            Field::text('description', 'Description', default: 'Effortlessly manage tasks, collaborate with teams, and meet deadlines with precision and clarity.'),
            Field::image('image', 'Mockup Image', default: '/placeholder-image.png'),
            Field::list('features', 'Features', [
                Field::string('title', 'Title', default: 'Feature Title'),
                Field::text('description', 'Description', default: 'Feature description text goes here.'),
                Field::string('icon', 'Icon (Lucide)', default: 'Star'),
                Field::string('iconColor', 'Icon Color', default: '#8B5CF6'),
            ], count: 2),
        ];
    }
}
