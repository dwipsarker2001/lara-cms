<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class ClientTestimonials extends Block
{
    public string $name = 'clientTestimonials';

    public string $label = 'Client Testimonials';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'What our clients say'),
            Field::text('description', 'Description'),
            Field::number('animationSpeed', 'Animation Speed', default: 60),
            Field::list('testimonials', 'Testimonials', [
                Field::image('avatar', 'Avatar'),
                Field::string('name', 'Name'),
                Field::string('role', 'Role', default: 'Happy Traveler'),
                Field::number('rating', 'Rating', default: 5),
                Field::string('handle', 'Handle'),
                Field::string('mentionLabel', 'Mention Label'),
                Field::text('quote', 'Quote'),
                Field::link('twitterUrl', 'Twitter URL'),
            ], count: 6),
        ];
    }
}
