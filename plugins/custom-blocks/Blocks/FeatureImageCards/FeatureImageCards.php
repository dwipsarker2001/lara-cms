<?php

namespace Plugins\CustomBlocks\Blocks\FeatureImageCards;

use App\Blocks\Block;
use App\Blocks\Field;

class FeatureImageCards extends Block
{
    public string $name = 'featureImageCards';

    public string $label = 'Feature Image Cards';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Our Premium Travel Services'),
            Field::text('description', 'Description', default: 'Comprehensive travel solutions tailored to make every journey seamless, memorable, and hassle-free.'),
            Field::list('cards', 'Cards', [
                Field::image('image', 'Image'),
                Field::string('title', 'Title', default: 'Hotel & Resort Booking'),
                Field::text('description', 'Description', default: 'Handpicked accommodations worldwide at the best rates for every budget.'),
            ], count: 4),
        ];
    }
}
