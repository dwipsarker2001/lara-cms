<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class Comparison extends Block
{
    public string $name = 'comparison';

    public string $label = 'Comparison (Prismo)';

    public function fields(): array
    {
        return [
            Field::string('badgeText', 'Badge', default: 'Comparison'),
            Field::string('headline', 'Headline', default: 'What Sets Prismo Apart'),
            Field::text('description', 'Description', default: 'Discover how Prismo outperforms other platforms with superior features, better performance, and unmatched ease of use.'),
            Field::string('otherTitle', 'Other Platforms Title', default: 'OTHER PLATFORMS'),
            Field::list('otherItems', 'Other Items', [
                Field::string('text', 'Feature Text'),
            ], count: 8),
            Field::image('prismoIcon', 'Prismo Icon', default: '/placeholder-image.png'),
            Field::list('prismoItems', 'Prismo Items', [
                Field::string('text', 'Feature Text'),
            ], count: 8),
            Field::string('ctaText', 'CTA Text', default: 'Start 7-day free trial'),
            Field::link('ctaUrl', 'CTA URL', default: '#'),
        ];
    }
}
