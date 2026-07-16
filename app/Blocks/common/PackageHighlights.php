<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageHighlights extends Block
{
    public string $name = 'packageHighlights';

    public string $label = 'Package Highlights';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Highlights of the Tour'),
            Field::list('items', 'Highlight', [
                Field::text('text', 'Text', default: 'Scenic viewpoints and memorable photo stops'),
            ]),
        ];
    }
}
