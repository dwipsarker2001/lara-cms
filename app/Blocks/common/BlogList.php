<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class BlogList extends Block
{
    public string $name = 'blogList';

    public string $label = 'Blog List';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::select('layout', 'Posts Layout', [
                ['value' => 'grid', 'label' => 'Grid (Box)'],
                ['value' => 'list', 'label' => 'List'],
            ], default: 'grid'),
            Field::number('postsPerPage', 'Posts Per Page', default: 6),
        ];
    }
}
