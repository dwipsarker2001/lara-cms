<?php

namespace App\Blocks\Home;

use App\Blocks\Block;
use App\Blocks\Field;

class WhyChooseUs extends Block
{
    public string $name = 'whyChooseUs';

    public string $label = 'Why Choose Us';

    public function fields(): array
    {
        return [
            Field::string('heading', 'Heading', default: 'Why Choose Us'),
            Field::text('subtitle', 'Subtitle'),
            Field::image('image', 'Image'),
            Field::list('features', 'Features', [
                Field::string('number', 'Number', default: '01'),
                Field::string('title', 'Title'),
                Field::text('description', 'Description'),
            ], count: 4),
        ];
    }
}
