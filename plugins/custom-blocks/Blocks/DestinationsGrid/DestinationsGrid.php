<?php

namespace Plugins\CustomBlocks\Blocks\DestinationsGrid;

use App\Blocks\Block;
use App\Blocks\Field;

class DestinationsGrid extends Block
{
    public string $name = 'destinationsGrid';

    public string $label = 'Destinations Grid';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'All Destinations'),
            Field::text('description', 'Description', default: 'Explore our handpicked destinations for your next adventure'),
            Field::list('places', 'Places', [
                Field::taxonomies('term_id', 'Select Destination', taxonomyId: 'destinations', routePattern: '/packages?destination={slug}'),
                Field::image('image', 'Image', default: '/placeholder-image.png'),
                Field::string('name', 'Name', default: 'Destination Name'),
                Field::string('slug', 'Slug'),
                Field::string('link', 'Custom URL Override'),
            ], count: 7),
        ];
    }
}
