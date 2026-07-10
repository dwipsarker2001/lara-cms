<?php

namespace App\Blocks\Global;

use App\Blocks\Block;
use App\Blocks\Field;

class SiteTopBar extends Block
{
    public string $name = 'siteTopBar';

    public string $label = 'Site Top Bar';

    public bool $global = true;

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::boolean('topBarEnabled', 'Top Bar Enabled', default: true),
            Field::string('topBarEmail', 'Top Bar Email', default: 'hello@example.com'),
            Field::string('promoText', 'Promo Text', default: 'Get 10% off your first booking!'),
            Field::string('promoLinkText', 'Promo Link Text'),
            Field::link('promoLinkUrl', 'Promo Link URL'),
            Field::list('topBarSocial', 'Top Bar Social', [
                Field::icon('icon', 'Icon'),
                Field::string('platform', 'Platform', default: 'facebook'),
                Field::link('url', 'URL'),
            ], count: 3),
        ];
    }
}
