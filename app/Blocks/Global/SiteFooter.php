<?php

namespace App\Blocks\Global;

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
            Field::text('description', 'Description'),
            Field::string('email', 'Email'),
            Field::string('phone', 'Phone'),
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
            Field::string('copyright', 'Copyright'),
            Field::list('legalLinks', 'Legal Links', [
                Field::string('label', 'Label', default: 'Privacy Policy'),
                Field::link('href', 'Href'),
            ]),
            Field::string('languageCurrency', 'Language / Currency', default: 'EN / USD'),
        ];
    }
}
