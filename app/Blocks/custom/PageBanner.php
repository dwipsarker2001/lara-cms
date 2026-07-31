<?php

namespace App\Blocks\custom;

use App\Blocks\Block;
use App\Blocks\Field;

class PageBanner extends Block
{
    public string $name = 'pageBanner';

    public string $label = 'Page Banner';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::string('title', 'Title', default: 'Page Title', source: 'title'),
            Field::image('backgroundImage', 'Background Image', default: '/placeholder-image.png'),
        ];
    }
}
