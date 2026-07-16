<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageBooking extends Block
{
    public string $name = 'packageBooking';

    public string $label = 'Package Booking';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::string('discountBadge', 'Badge'),
            Field::number('originalPrice', 'Original Price'),
            Field::number('salePrice', 'Sale Price'),
            Field::list('guarantees', 'Guarantee', [
                Field::string('text', 'Text', default: 'Luxury Experience Guaranteed.'),
            ]),
            Field::string('bookLabel', 'Book Button Label', default: 'Book Now'),
            Field::string('whatsappPhone', 'WhatsApp Number'),
            Field::string('bonusNote', 'Bonus Note'),
        ];
    }
}
