<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class ProductOverview extends Block
{
    public string $name = 'productOverview';

    public string $label = 'Product Overview';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::string('badge', 'Badge Text', default: 'Product Overview'),
            Field::string('headline', 'Headline', default: 'Simplify Task Management with Powerful Features'),
            Field::text('description', 'Description', default: 'Discover features designed to simplify workflows, boost productivity, and improve team collaboration seamlessly.'),
            Field::string('ctaLabel', 'CTA Button Label', default: 'Get Started'),
            Field::image('dashboardImage', 'Dashboard Image', default: '/placeholder-image.png'),
            Field::list('features', 'Feature Card', [
                Field::icon('icon', 'Icon'),
                Field::string('title', 'Title', default: 'Feature'),
                Field::string('description', 'Description', default: 'Feature description.'),
            ], count: 4),
            Field::string('otherFeaturesHeading', 'Other Features Heading', default: 'Other Interesting Features'),
            Field::list('otherFeatures', 'Other Feature', [
                Field::string('text', 'Text', default: 'Feature'),
            ], count: 8),
        ];
    }
}
