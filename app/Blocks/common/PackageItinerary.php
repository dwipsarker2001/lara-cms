<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class PackageItinerary extends Block
{
    public string $name = 'packageItinerary';

    public string $label = 'Package Itinerary';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Tour Itinerary'),
            Field::list('sections', 'Section', [
                Field::string('location', 'Location'),
                Field::string('departure', 'Departure Time'),
                Field::list('days', 'Day', [
                    Field::string('dayLabel', 'Day Label', default: 'Day 1'),
                    Field::string('title', 'Title', default: 'Morning exploration'),
                    Field::richText('body', 'Body'),
                ]),
            ]),
        ];
    }
}
