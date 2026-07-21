<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class FeaturesCombined extends Block
{
    public string $name = 'featuresCombined';

    public string $label = 'Features Combined';

    public function fields(): array
    {
        return [
            Field::string('badge', 'Badge Text', default: 'Task Management'),
            Field::string('headline', 'Headline', default: 'All Your Tasks, Organized Effortlessly'),
            Field::list('features', 'Feature', [
                Field::string('title', 'Title', default: 'Feature'),
                Field::string('description', 'Description', default: 'Feature description.'),
            ], count: 4),
            Field::image('image', 'Illustration Image', default: '/placeholder-image.png'),
            Field::string('secondaryBadge', 'Secondary Badge', default: 'Powerful Features'),
            Field::string('secondaryHeadline', 'Secondary Headline', default: 'Work Smarter with Powerful Features'),
            Field::text('description', 'Description', default: 'Effortlessly manage tasks, collaborate with teams, and meet deadlines with precision and clarity.'),
            Field::list('cards', 'Card', [
                Field::string('text', 'Card Text', default: 'Feature description.'),
            ], count: 4),
        ];
    }
}
