<?php

use App\Blocks\Field;
use App\Models\Admin;
use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Models\Taxonomy;
use App\Models\Term;
use Plugins\CustomBlocks\Blocks\DestinationsGrid\DestinationsGrid;
use Plugins\CustomBlocks\Blocks\TravelDeals\TravelDeals;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
});

test('Field::taxonomies and Field::collection return proper schema definitions', function () {
    $collectionField = Field::collection('package_id', 'Select Package', collection: 'packages');
    expect($collectionField['type'])->toBe('collection')
        ->and($collectionField['name'])->toBe('package_id')
        ->and($collectionField['collection'])->toBe('packages');

    $taxonomyField = Field::taxonomies('term_id', 'Select Destination', taxonomyId: 'destinations');
    expect($taxonomyField['type'])->toBe('taxonomies')
        ->and($taxonomyField['name'])->toBe('term_id')
        ->and($taxonomyField['taxonomy_id'])->toBe('destinations');
});

test('TravelDeals block resolves data from referenced collection entry sources', function () {
    $collection = Collection::create([
        'name' => 'Packages',
        'slug' => 'packages',
        'fields' => [
            ['title' => 'Price', 'template' => 'price', 'type' => 'number'],
            ['title' => 'Original Price', 'template' => 'original_price', 'type' => 'number'],
            ['title' => 'Featured Image', 'template' => 'featured_image', 'type' => 'image'],
            ['title' => 'Description', 'template' => 'description', 'type' => 'text'],
        ],
    ]);

    $entry = CollectionEntry::create([
        'collection_id' => $collection->id,
        'slug' => 'maldives-luxury-tour',
        'published' => true,
        'data' => [
            'title' => 'Maldives Luxury Tour',
            'price' => '৳999',
            'original_price' => '৳1499',
            'featured_image' => '/uploads/maldives.jpg',
            'description' => 'Unforgettable tropical paradise holiday experience',
            'badge' => 'Exclusive',
        ],
    ]);

    $block = new TravelDeals;
    $html = $block->render(
        data: [
            '_sources' => [
                'cards.0.title' => 'entry:'.$entry->id.':title',
                'cards.0.image' => 'entry:'.$entry->id.':featured_image',
                'cards.0.price' => 'entry:'.$entry->id.':price',
                'cards.0.originalPrice' => 'entry:'.$entry->id.':original_price',
                'cards.0.description' => 'entry:'.$entry->id.':description',
            ],
            'headline' => 'Special Deals',
            'cards' => [
                [
                    'package_id' => (string) $entry->id,
                    'title' => '',
                    'image' => '/placeholder-image.png',
                    'price' => '',
                    'originalPrice' => '',
                ],
            ],
        ],
        _key: 'deal-1'
    );

    expect($html)->toContain('Maldives Luxury Tour')
        ->and($html)->toContain('/uploads/maldives.jpg')
        ->and($html)->toContain('৳999')
        ->and($html)->toContain('৳1499')
        ->and($html)->toContain('Unforgettable tropical paradise');
});

test('DestinationsGrid block resolves data from referenced taxonomy term sources', function () {
    $taxonomy = Taxonomy::create([
        'title' => 'Destinations',
        'slug' => 'destinations',
        'fields' => [
            ['title' => 'Image', 'template' => 'image', 'type' => 'image'],
        ],
    ]);

    $term = Term::create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Santorini, Greece',
        'slug' => 'santorini-greece',
        'data' => [
            'image' => '/uploads/santorini.jpg',
        ],
    ]);

    $block = new DestinationsGrid;
    $html = $block->render(
        data: [
            '_sources' => [
                'places.0.name' => 'term:'.$term->id.':title',
                'places.0.image' => 'term:'.$term->id.':image',
                'places.0.link' => 'term:'.$term->id.':route',
            ],
            'headline' => 'Featured Destinations',
            'places' => [
                [
                    'term_id' => (string) $term->id,
                    'name' => '',
                    'image' => '/placeholder-image.png',
                    'slug' => '',
                ],
            ],
        ],
        _key: 'dest-1'
    );

    expect($html)->toContain('Santorini, Greece')
        ->and($html)->toContain('/uploads/santorini.jpg')
        ->and($html)->toContain('/destinations/santorini-greece');
});

test('Taxonomy and Term route generation works with custom route_pattern', function () {
    $taxonomy = Taxonomy::create([
        'title' => 'Destinations',
        'slug' => 'destinations',
        'route_pattern' => 'https://travel.eapply.site/packages?destination={slug}',
    ]);

    $term = Term::create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Bangladesh',
        'slug' => 'bangladesh',
    ]);

    expect($term->route())->toBe('https://travel.eapply.site/packages?destination=bangladesh');

    $block = new DestinationsGrid;
    $html = $block->render(
        data: [
            'headline' => 'Explore Bangladesh',
            'places' => [
                [
                    'term_id' => (string) $term->id,
                    'name' => '',
                ],
            ],
        ],
        _key: 'dest-bd'
    );

    expect($html)->toContain('https://travel.eapply.site/packages?destination=bangladesh');
});

test('DestinationsGrid correctly resolves input source bindings with term:id:key format', function () {
    $taxonomy = Taxonomy::create([
        'title' => 'Destinations',
        'slug' => 'destinations',
        'route_pattern' => '/packages?destination={slug}',
    ]);

    $term = Term::create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Cox\'s Bazar',
        'slug' => 'coxs-bazar',
        'data' => [
            'image' => '/uploads/coxs-bazar.jpg',
        ],
    ]);

    $block = new DestinationsGrid;
    $html = $block->render(
        data: [
            '_sources' => [
                'places.0.name' => 'term:'.$term->id.':title',
                'places.0.image' => 'term:'.$term->id.':image',
                'places.0.link' => 'term:'.$term->id.':route',
            ],
            'headline' => 'Explore',
            'places' => [
                [
                    'name' => 'Old Static Name',
                    'image' => '/placeholder-image.png',
                    'link' => '#',
                ],
            ],
        ],
        _key: 'dest-binding'
    );

    expect($html)->toContain('Cox&#039;s Bazar')
        ->and($html)->toContain('/uploads/coxs-bazar.jpg')
        ->and($html)->toContain('/packages?destination=coxs-bazar');
});
