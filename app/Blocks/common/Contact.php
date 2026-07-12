<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class Contact extends Block
{
    public string $name = 'contact';

    public string $label = 'Contact';

    public function fields(): array
    {
        return [
            Field::string('heading', 'Heading', default: 'Contact Us'),
            Field::text('subheading', 'Subheading'),
            Field::string('mapEmbedUrl', 'Map Embed URL'),
            Field::string('emailTitle', 'Email Title', default: 'Email'),
            Field::string('emailDescription', 'Email Description'),
            Field::string('emailValue', 'Email Value'),
            Field::string('phoneTitle', 'Phone Title', default: 'Phone'),
            Field::string('phoneDescription', 'Phone Description'),
            Field::string('phoneValue', 'Phone Value'),
            Field::string('officeTitle', 'Office Title', default: 'Office'),
            Field::string('officeDescription', 'Office Description'),
            Field::string('officeValue', 'Office Value'),
        ];
    }
}
