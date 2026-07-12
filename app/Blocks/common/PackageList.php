<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageList extends Block
{
    public string $name = 'packageList';

    public string $label = 'Package List';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::number('packagesPerPage', 'Packages Per Page', default: 5),
            Field::number('priceMax', 'Max Price', default: 500000),
        ];
    }
}
