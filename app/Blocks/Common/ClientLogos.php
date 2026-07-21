<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class ClientLogos extends Block
{
    public string $name = 'clientLogos';

    public string $label = 'Client Logos Marquee';

    public function fields(): array
    {
        return [
            Field::string('caption', 'Caption', default: 'Backed by the best'),
            Field::number('speed', 'Animation Speed (seconds)', default: 30),
            Field::list('logos', 'Logo', [
                Field::image('image', 'Logo Image', default: '/placeholder-image.png'),
                Field::string('alt', 'Alt Text'),
            ], count: 5),
        ];
    }
}
