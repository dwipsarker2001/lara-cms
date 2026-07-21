<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class HeroSimple extends Block
{
    public string $name = 'heroSimple';

    public string $label = 'Hero Simple';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::string('badge', 'Badge Text', default: '200K+ Projects Managed Daily'),
            Field::string('headline', 'Headline', default: 'Task Management Made Simple and Powerful'),
            Field::string('subtitle', 'Subtitle', default: 'Boost productivity with seamless task and team management.'),
            Field::string('ctaLabel', 'CTA Label', default: 'Join Waitlist'),
            Field::link('ctaUrl', 'CTA URL', default: '#'),
            Field::image('dashboardImage', 'Dashboard Image', default: '/placeholder-image.png'),
            Field::string('rating', 'Rating Text', default: '4.9 rating'),
            Field::string('ratingLabel', 'Rating Label', default: 'Based on 300k Users'),
        ];
    }
}
