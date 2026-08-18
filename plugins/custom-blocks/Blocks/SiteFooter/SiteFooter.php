<?php

namespace Plugins\CustomBlocks\Blocks\SiteFooter;

use App\Blocks\Block;
use App\Blocks\Field;

class SiteFooter extends Block
{
    public string $name = 'siteFooter';

    public string $label = 'Site Footer';

    public bool $global = true;

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::image('bannerImage', 'Banner Image'),
            Field::image('logo', 'Logo'),
            Field::number('logoHeight', 'Logo Height', default: 40),
            Field::string('brandName', 'Brand Name', default: 'E CMS'),
            Field::text('description', 'Description', default: 'Discover extraordinary travel experiences curated by experts. From hidden gems to iconic landmarks, every journey is crafted to inspire and transform.'),
            Field::string('email', 'Email', default: 'hello@dwipsarker.com'),
            Field::string('phone', 'Phone', default: '+880 1771 868 382'),
            Field::list('linkColumns', 'Link Columns', [
                Field::string('heading', 'Heading', default: 'Quick Links'),
                Field::list('links', 'Links', [
                    Field::string('label', 'Label', default: 'Contact Us'),
                    Field::link('href', 'Href'),
                ], count: 3),
            ], count: 3),
            Field::string('socialHeading', 'Social Heading', default: 'Connect with us'),
            Field::list('social', 'Social', [
                Field::icon('icon', 'Icon'),
                Field::string('label', 'Label', default: 'Facebook'),
                Field::link('url', 'URL'),
            ]),
            Field::string('copyrightBrand', 'Copyright Brand', default: 'E CMS'),
            Field::string('copyright', 'Copyright', default: '© 2026 E CMS. All rights reserved.'),
            Field::list('legalLinks', 'Legal Links', [
                Field::string('label', 'Label', default: 'Privacy Policy'),
                Field::link('href', 'Href'),
            ]),
            Field::string('languageCurrency', 'Language / Currency', default: 'EN / USD'),
        ];
    }
}
