<?php

namespace App\Blocks\custom;

use App\Blocks\Block;
use App\Blocks\Field;

class LatestBlog extends Block
{
    public string $name = 'latestBlog';

    public string $label = 'Latest Blog';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Latest Travel Blog'),
            Field::text('description', 'Description', default: 'Stories, tips and inspiration to help you plan your next trip.'),
        ];
    }
}
