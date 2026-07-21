<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class TestimonialsGrid extends Block
{
    public string $name = 'testimonialsGrid';

    public string $label = 'Testimonials Grid';

    public function fields(): array
    {
        return [
            Field::string('badge', 'Badge Text', default: 'Testimonials'),
            Field::string('headline', 'Headline', default: 'What Our Users Are Saying About Us'),
            Field::text('subtitle', 'Subtitle', default: ''),
            Field::list('testimonials', 'Testimonial', [
                Field::image('avatar', 'Avatar', default: '/placeholder-image.png'),
                Field::string('name', 'Name', default: 'Name'),
                Field::string('role', 'Role', default: 'Role'),
                Field::text('quote', 'Quote', default: 'Great product!'),
                Field::link('twitterUrl', 'Twitter URL'),
            ], count: 4),
            Field::list('statCards', 'Stat Card', [
                Field::string('value', 'Value', default: '10X'),
                Field::string('label', 'Label', default: 'Growth'),
            ], count: 4),
        ];
    }
}
