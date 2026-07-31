<?php

namespace App\Blocks\custom;

use App\Blocks\Block;
use App\Blocks\Field;

class BlogSection extends Block
{
    public string $name = 'blogSection';

    public string $label = 'Blog Section';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::image('topImage', 'Top Image', default: ''),
            Field::string('blogListLabel', 'Blog Link Label', default: 'Blog'),
            Field::string('blogListHref', 'Blog List URL', default: '/blog'),
            Field::number('recentCount', 'Recent Posts Count', default: 4),
            Field::richText('body', 'Body', default: '<p>Write your content here...</p>'),
        ];
    }
}
