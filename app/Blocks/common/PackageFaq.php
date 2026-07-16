<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageFaq extends Block
{
    public string $name = 'packageFaq';

    public string $label = 'Package FAQ';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Frequently Asked Questions'),
            Field::list('items', 'Question', [
                Field::string('question', 'Question'),
                Field::richText('answer', 'Answer'),
            ]),
        ];
    }
}
