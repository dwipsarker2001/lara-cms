<?php

namespace App\Blocks\global;

use App\Blocks\Block;
use App\Blocks\Field;

class SiteNavbar extends Block
{
    public string $name = 'siteNavbar';

    public string $label = 'Site Navbar';

    public bool $global = true;

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::image('logo', 'Logo'),
            Field::number('logoHeight', 'Logo Height', default: 40),
            Field::list('nav', 'Navigation', [
                Field::string('label', 'Label', default: 'Home'),
                Field::link('href', 'Href', default: '/'),
                Field::list('dropdown', 'Dropdown', [
                    Field::string('label', 'Label', default: 'Item'),
                    Field::link('href', 'Href'),
                ]),
            ], count: 4),
            Field::string('contactLabel', 'Contact Label', default: 'Chat with us'),
            Field::string('contactNumber', 'Contact Number', default: '+8801771868382'),
            Field::link('contactLink', 'Contact Link', default: 'https://wa.me/8801771868382'),
            Field::image('contactIcon', 'Contact Icon'),
        ];
    }
}
