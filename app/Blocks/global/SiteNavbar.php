<?php

namespace App\Blocks\Global;

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
            Field::image('logo', 'Logo', default: '/placeholder-image.png'),
            Field::number('logoHeight', 'Logo Height', default: 32),
            Field::string('brandName', 'Brand Name', default: 'Lara CMS'),
            Field::list('nav', 'Navigation', [
                Field::string('label', 'Label', default: 'Home'),
                Field::link('href', 'Link', default: '/'),
            ], count: 5),
            Field::string('cta1Label', 'CTA 1 Label', default: 'Join Waitlist'),
            Field::link('cta1Link', 'CTA 1 Link', default: '/waitlist'),
            Field::string('cta2Label', 'CTA 2 Label', default: 'Contact Us'),
            Field::link('cta2Link', 'CTA 2 Link', default: '/#contact'),
        ];
    }
}
