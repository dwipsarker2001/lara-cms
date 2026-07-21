<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class Sectors extends Block
{
    public string $name = 'sectors';

    public string $label = 'Sectors (Tabbed)';

    public function fields(): array
    {
        return [
            Field::string('badgeText', 'Badge Text', default: 'Industry Insights'),
            Field::string('headline', 'Headline', default: 'How Prismo will help you in different sectors'),
            Field::string('ctaText', 'CTA Text', default: 'Start 7-day free trial'),
            Field::link('ctaUrl', 'CTA URL', default: '/signup'),
            Field::list('footerFeatures', 'Footer Features', [
                Field::string('text', 'Text', default: 'Feature'),
            ], count: 4),
            Field::list('sectors', 'Sectors', [
                Field::string('tabName', 'Tab Name', default: 'Category'),
                Field::icon('icon', 'Icon', default: 'fa-solid fa-fire'),
                Field::string('iconColor', 'Icon Color', default: '#F87171'),
                Field::string('panelTitle', 'Panel Title', default: 'New Sector Insights'),
                Field::string('stat1Headline', 'Stat 1 Headline', default: '50% Improvement'),
                Field::text('stat1Description', 'Stat 1 Description', default: 'A short description of how this sector benefits from this stat.'),
                Field::string('stat2Headline', 'Stat 2 Headline', default: '3x Faster Results'),
                Field::text('stat2Description', 'Stat 2 Description', default: 'A second supporting stat with a short narrative explanation.'),
            ], count: 5),
        ];
    }
}
