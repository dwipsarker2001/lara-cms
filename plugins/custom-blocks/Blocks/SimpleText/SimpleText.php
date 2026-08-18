<?php

namespace Plugins\CustomBlocks\Blocks\SimpleText;

use App\Blocks\Block;
use App\Blocks\Field;

class SimpleText extends Block
{
    public string $name = 'simpleText';

    public string $label = 'Simple Text';

    public function fields(): array
    {
        return [
            Field::string('heading', 'Heading', default: 'Welcome'),
            Field::richText('body', 'Body', default: '<p>Your content here.</p>'),
        ];
    }
}
