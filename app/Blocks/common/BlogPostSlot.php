<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class BlogPostSlot extends Block
{
    public string $name = 'blogPostSlot';

    public string $label = 'Blog Post Slot';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::string('blogListHref', 'Blog List Href', default: '/blog'),
        ];
    }
}
