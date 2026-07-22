<?php

namespace App\Blocks\common;

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
            Field::text('subtitle', 'Subtitle', default: 'Experience seamless travel planning with trusted experts, personalized packages, and worldwide destinations.'),

            Field::image('image', 'Image'),
            Field::list('features', 'Features', [
                Field::string('number', 'Number', default: '01'),
                Field::string('title', 'Title', default: 'Expert Travel Planning'),
                Field::text('description', 'Description', default: 'Our travel specialists create customized itineraries tailored to your preferences and budget.'),
            ], count: 4),
        ];
    }
}
