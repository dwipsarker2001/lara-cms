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
