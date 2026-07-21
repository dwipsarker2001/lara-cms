<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class ClientTestimonials extends Block
{
    public string $name = 'clientTestimonials';

    public string $label = 'Testimonials';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'What Our Clients Say'),
            Field::text('description', 'Description', default: 'Hear from our satisfied customers.'),
            Field::number('animationSpeed', 'Animation Speed (seconds)', default: 20),
            Field::list('testimonials', 'Testimonials', [
                Field::image('avatar', 'Avatar', default: '/placeholder-image.png'),
                Field::string('name', 'Name', default: 'Jane Doe'),
                Field::string('role', 'Role', default: 'CEO, Company'),
                Field::number('rating', 'Rating', default: 5),
                Field::string('handle', 'Handle', default: '@janedoe'),
                Field::string('mentionLabel', 'Mention Label', default: '@Company'),
                Field::text('quote', 'Quote', default: 'Amazing product!'),
                Field::link('twitterUrl', 'Twitter URL', default: '#'),
            ], count: 4),
        ];
    }
}
