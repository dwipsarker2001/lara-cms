<?php

namespace App\Blocks\Global;

use App\Blocks\Block;
use App\Blocks\Field;

class Footer extends Block
{
    public string $name = 'footer';

    public string $label = 'Footer';

    public bool $global = true;

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::string('ctaHeading', 'CTA Heading', default: 'Start your 7-day free trial'),
            Field::text('ctaDescription', 'CTA Description', default: 'Start your free trial now to experience seamless project management without any commitment!'),
            Field::string('ctaPlaceholder', 'CTA Placeholder', default: 'Enter your email'),
            Field::string('ctaButtonLabel', 'CTA Button Label', default: 'Get Started'),
            Field::string('rating', 'Rating', default: '4.9 rating'),
            Field::string('ratingLabel', 'Rating Label', default: 'Based on 300k Users'),
            Field::image('logo', 'Logo', default: '/placeholder-image.png'),
            Field::string('brandName', 'Brand Name', default: 'Lara CMS'),
            Field::number('logoHeight', 'Logo Height', default: 40),
            Field::string('tagline', 'Tagline', default: 'Simplifying Projects and Achieving Goals.'),
            Field::string('email', 'Email', default: 'hello@laracms.com'),
            Field::list('linkColumns', 'Link Columns', [
                Field::string('heading', 'Heading', default: 'Home'),
                Field::list('links', 'Links', [
                    Field::string('label', 'Label', default: 'Features'),
                    Field::link('href', 'Href'),
                ], count: 6),
            ], count: 3),
            Field::string('socialHeading', 'Social Heading', default: 'Social'),
            Field::list('social', 'Social', [
                Field::icon('icon', 'Icon'),
                Field::string('label', 'Label', default: 'LinkedIn'),
                Field::link('url', 'URL'),
            ], count: 3),
            Field::string('copyright', 'Copyright', default: 'c 2026 Lara CMS. All rights reserved.'),
            Field::string('systemsStatus', 'Systems Status', default: 'All Systems Operational'),
            Field::string('privacyLabel', 'Privacy Label', default: 'Privacy Policy'),
            Field::link('privacyLink', 'Privacy Link'),
        ];
    }
}
