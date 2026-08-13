<?php

use App\Blocks\Block;
use App\Blocks\Field;

it('includes source key in scalar field definitions when specified', function () {
    $field = Field::string('title', 'Tour Title', default: 'Default Title', source: 'title');

    expect($field)->toHaveKey('source', 'title');
    expect($field['name'])->toBe('title');
});

it('prioritizes entry custom field data over block default or inline data when source is set', function () {
    $fields = [
        Field::string('title', 'Tour Title', default: 'Default Title', source: 'title'),
        Field::string('rating', 'Rating', default: '4.5', source: 'rating'),
        Field::string('location', 'Location', default: 'Default Location'), // no source
    ];

    $page = (object) [
        'data' => [
            'title' => 'Custom Entry Title',
            'rating' => '4.9',
            'location' => 'Custom Entry Location',
        ],
    ];

    $blockData = [
        'title' => 'Inline Block Title',
        'rating' => '4.0',
        'location' => 'Inline Block Location',
    ];

    $merged = Block::mergeSourceData($blockData, $fields, $page);

    expect($merged['title'])->toBe('Custom Entry Title');
    expect($merged['rating'])->toBe('4.9');
    expect($merged['location'])->toBe('Inline Block Location'); // kept inline because field has no source
});

it('falls back to block data when source entry value is missing or empty', function () {
    $fields = [
        Field::string('title', 'Tour Title', default: 'Default Title', source: 'title'),
    ];

    $page = (object) [
        'data' => [
            'title' => '', // empty value
        ],
    ];

    $blockData = [
        'title' => 'Fallback Block Title',
    ];

    $merged = Block::mergeSourceData($blockData, $fields, $page);

    expect($merged['title'])->toBe('Fallback Block Title');
});

it('prioritizes section-level dynamic _sources over static PHP field sources', function () {
    $fields = [
        Field::string('title', 'Tour Title', default: 'Default Title', source: 'title'),
        Field::string('heading', 'Heading', default: 'Default Heading'), // no static source
    ];

    $page = (object) [
        'data' => [
            'title' => 'Static Title From Entry',
            'custom_heading' => 'Dynamic Heading From Entry',
        ],
    ];

    $blockData = [
        'title' => 'Inline Title',
        'heading' => 'Inline Heading',
        '_sources' => [
            'heading' => 'custom_heading', // Dynamic visual link
        ],
    ];

    $merged = Block::mergeSourceData($blockData, $fields, $page, $blockData['_sources']);

    expect($merged['title'])->toBe('Static Title From Entry'); // static source fallback
    expect($merged['heading'])->toBe('Dynamic Heading From Entry'); // dynamic visual binding
});

it('allows unlinking a field using __none__ in section-level _sources', function () {
    $fields = [
        Field::string('title', 'Tour Title', default: 'Default Title', source: 'title'),
    ];

    $page = (object) [
        'data' => [
            'title' => 'Entry Title Should Be Ignored',
        ],
    ];

    $blockData = [
        'title' => 'Manual User Typed Title',
        '_sources' => [
            'title' => '__none__', // Unlinked visually by user
        ],
    ];

    $merged = Block::mergeSourceData($blockData, $fields, $page, $blockData['_sources']);

    expect($merged['title'])->toBe('Manual User Typed Title');
});

it('recursively merges source data for nested list fields like galleryImages', function () {
    $fields = [
        Field::list('galleryImages', 'Gallery Images', [
            Field::image('image', 'Image'),
        ], count: 2),
    ];

    $page = (object) [
        'data' => [
            'featured_image' => 'https://example.com/entry-featured-image.jpg',
            'cover_photo' => 'https://example.com/entry-cover.jpg',
        ],
    ];

    $blockData = [
        'galleryImages' => [
            ['image' => 'https://example.com/default-1.jpg'],
            ['image' => 'https://example.com/default-2.jpg'],
        ],
        '_sources' => [
            'galleryImages.0.image' => 'featured_image',
            'galleryImages.1.image' => 'cover_photo',
        ],
    ];

    $merged = Block::mergeSourceData($blockData, $fields, $page, $blockData['_sources']);

    expect($merged['galleryImages'][0]['image'])->toBe('https://example.com/entry-featured-image.jpg');
    expect($merged['galleryImages'][1]['image'])->toBe('https://example.com/entry-cover.jpg');
});

it('formats structured location data to a string when bound to a string field', function () {
    $fields = [
        Field::string('location_text', 'Location', default: 'Default Location', source: 'address'),
    ];

    $page = (object) [
        'data' => [
            'address' => [
                'country' => 'United States',
                'country_code' => 'US',
                'state' => 'California',
                'state_code' => 'CA',
                'city' => 'Los Angeles',
                'formatted' => 'Los Angeles, California, United States',
            ],
        ],
    ];

    $blockData = [
        'location_text' => 'Inline Location',
    ];

    $merged = Block::mergeSourceData($blockData, $fields, $page);

    expect($merged['location_text'])->toBe('Los Angeles, California, United States');
});
