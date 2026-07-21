<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class AboutMetrics extends Block
{
    public string $name = 'aboutMetrics';

    public string $label = 'About Metrics';

    public function fields(): array
    {
        return [
            Field::string('badge', 'Badge', default: 'Why Choose Us'),
            Field::string('headline', 'Headline', default: 'Travel with Confidence'),
            Field::text('description', 'Description', default: 'Experience the difference with our award-winning service and support.'),
            Field::string('ctaLabel', 'CTA Label', default: 'Get Started'),
            Field::link('ctaUrl', 'CTA URL', default: '#'),
            Field::string('ratingText', 'Rating Text', default: '4.9 Average user rating'),
            Field::image('image', 'Image', default: '/placeholder-image.png'),
            Field::list('metrics', 'Metrics', [
                Field::string('value', 'Value', default: '99%'),
                Field::string('label', 'Label', default: 'Satisfaction'),
            ], count: 4),
        ];
    }
}
