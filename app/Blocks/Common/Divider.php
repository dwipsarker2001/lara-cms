<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class Divider extends Block
{
    public string $name = 'divider';

    public string $label = 'Section Divider';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::image('dividerImage', 'Divider Image', default: 'https://framerusercontent.com/images/iDrlOYszhHrSmrgaFKo5G5kRV8.svg?width=1240&height=10'),
        ];
    }
}
