<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class PricingTable extends Block
{
    public string $name = 'pricingTable';

    public string $label = 'Pricing Table';

    public function fields(): array
    {
        return [
            Field::string('badge', 'Badge Text', default: 'Pricing & Plans'),
            Field::string('headline', 'Headline', default: 'Affordable Pricing Plans'),
            Field::text('subtitle', 'Subtitle', default: 'Flexible, transparent pricing to support your team\'s productivity and growth at every stage.'),
            Field::string('monthlyLabel', 'Monthly Switch Label', default: 'Billed Monthly'),
            Field::string('yearlyLabel', 'Yearly Switch Label', default: 'Billed yearly'),
            Field::list('plans', 'Plan', [
                Field::string('name', 'Plan Name', default: 'Plan'),
                Field::string('description', 'Description', default: 'Plan description.'),
                Field::image('icon', 'Icon Image', default: '/placeholder-image.png'),
                Field::string('monthlyPrice', 'Monthly Price', default: '$10'),
                Field::string('yearlyPrice', 'Yearly Price', default: '$100'),
                Field::string('period', 'Period', default: '/ member / month'),
                Field::string('ctaLabel', 'CTA Label', default: 'Get Started'),
                Field::link('ctaUrl', 'CTA URL', default: '#'),
                Field::boolean('featured', 'Featured (highlight)'),
                Field::list('features', 'Feature', [
                    Field::string('text', 'Feature Text', default: 'Feature'),
                ], count: 4),
            ], count: 3),
            Field::boolean('startupEnabled', 'Show Startup Program', default: true),
            Field::string('startupText', 'Startup Text', default: 'We just launched our startup program - '),
            Field::string('startupHighlight', 'Startup Highlight', default: 'Get 50% off'),
            Field::string('startupButtonLabel', 'Startup Button', default: 'Apply Now'),
            Field::link('startupButtonUrl', 'Startup Button URL', default: '#'),
            Field::list('badges', 'Info Badge', [
                Field::icon('icon', 'Icon'),
                Field::string('text', 'Badge Text', default: 'Badge'),
            ], count: 3),
        ];
    }
}
