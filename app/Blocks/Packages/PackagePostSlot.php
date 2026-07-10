<?php

namespace App\Blocks\Packages;

use App\Blocks\Block;
use App\Blocks\Field;

class PackagePostSlot extends Block
{
    public string $name = 'packagePostSlot';

    public string $label = 'Package Post Slot';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::string('packageListHref', 'Package List Href', default: '/packages'),
        ];
    }
}
