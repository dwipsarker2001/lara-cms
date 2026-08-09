<?php

use App\Models\Taxonomy;

test('taxonomy stores custom fields builder configuration', function () {
    $fields = [
        ['title' => 'Featured Image', 'type' => 'image', 'template' => 'image'],
        ['title' => 'Badge Color', 'type' => 'color', 'template' => 'color'],
    ];

    $taxonomy = Taxonomy::create([
        'title' => 'Custom Categories',
        'slug' => 'custom-categories',
        'fields' => $fields,
    ]);

    expect($taxonomy->fields)->toBeArray()
        ->toHaveCount(2)
        ->and($taxonomy->fields[0]['type'])->toBe('image');
});

test('term stores data for taxonomy custom input fields', function () {
    $taxonomy = Taxonomy::create([
        'title' => 'Destinations',
        'slug' => 'destinations',
        'fields' => [
            ['title' => 'Photo', 'type' => 'image', 'template' => 'photo'],
        ],
    ]);

    $term = $taxonomy->terms()->create([
        'title' => 'Cox\'s Bazar',
        'slug' => 'coxs-bazar',
        'data' => [
            'photo' => 'https://example.com/coxs-bazar.jpg',
        ],
    ]);

    expect($term->data)->toBeArray()
        ->and($term->data['photo'])->toBe('https://example.com/coxs-bazar.jpg');
});
