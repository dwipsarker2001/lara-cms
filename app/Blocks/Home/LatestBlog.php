<?php

namespace App\Blocks\Home;

use App\Blocks\Block;
use App\Blocks\Field;

class LatestBlog extends Block
{
    public string $name = 'latestBlog';

    public string $label = 'Latest Blog';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Latest Travel Blog'),
            Field::text('description', 'Description'),
        ];
    }
}
