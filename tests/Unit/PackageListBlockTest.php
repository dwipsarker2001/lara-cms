<?php

use App\Blocks\CardSlot;
use App\Blocks\ListBlock;
use App\Models\Collection;
use App\Models\Taxonomy;
use App\Models\Term;
use Plugins\TravelTheme\Blocks\PackageList\PackageList;

test('CardSlot creates correct instances for each type', function () {
    $text = CardSlot::text('title', 'Card Title');
    expect($text->key)->toBe('title')
        ->and($text->label)->toBe('Card Title')
        ->and($text->type)->toBe('text');

    $image = CardSlot::image('image', 'Thumbnail');
    expect($image->type)->toBe('image');

    $price = CardSlot::price('price', 'Price');
    expect($price->type)->toBe('price');

    $url = CardSlot::url('link', 'URL');
    expect($url->type)->toBe('url');
});

test('PackageList extends ListBlock', function () {
    $block = new PackageList;
    expect($block)->toBeInstanceOf(ListBlock::class);
});

test('PackageList resolvedFields includes listCollection and one select per card slot', function () {
    $block = new PackageList;
    $fields = $block->resolvedFields();
    $names = array_column($fields, 'name');

    expect($names)->toContain('listCollection');
    expect($names)->toContain('map_title');
    expect($names)->toContain('map_image');
    expect($names)->toContain('map_price');
    expect($names)->toContain('map_originalPrice');
    expect($names)->toContain('map_excerpt');
    expect($names)->toContain('map_destination');
    expect($names)->toContain('map_category');
    expect($names)->toContain('map_duration');
    expect($names)->toContain('map_badge');
    expect($names)->toContain('destinationTaxonomy');
    expect($names)->toContain('categoryTaxonomy');
    expect($names)->toContain('packagesPerPage');
    expect($names)->toContain('priceMax');
});

test('PackageList resolveCard maps collection entry data using manual slot assignments', function () {
    $collection = Collection::create([
        'name' => 'Custom Packages',
        'slug' => 'custom-packages',
        'fields' => [
            ['title' => 'Custom Name', 'template' => 'custom_name'],
            ['title' => 'Cover Photo', 'template' => 'cover_photo'],
            ['title' => 'Ticket Cost', 'template' => 'ticket_cost'],
            ['title' => 'Full Summary', 'template' => 'full_summary'],
        ],
    ]);

    $entry = $collection->entries()->create([
        'slug' => 'sylhet-expedition',
        'published' => true,
        'data' => [
            'custom_name' => 'Sylhet Wild Expedition',
            'cover_photo' => 'https://example.com/photo.jpg',
            'ticket_cost' => 14500,
            'full_summary' => 'Explore deep forests and hidden rivers.',
        ],
        'sections' => [],
    ]);

    $block = new PackageList;
    $card = $block->resolveCard($entry, [
        'map_title' => 'custom_name',
        'map_image' => 'cover_photo',
        'map_price' => 'ticket_cost',
        'map_excerpt' => 'full_summary',
    ]);

    expect($card->title)->toBe('Sylhet Wild Expedition')
        ->and($card->image)->toBe('https://example.com/photo.jpg')
        ->and($card->price)->toBe(14500)
        ->and($card->excerpt)->toBe('Explore deep forests and hidden rivers.')
        ->and($card->_link)->toBe($entry->route())
        ->and($card->_slug)->toBe('sylhet-expedition');
});

test('PackageList resolveCard falls back to entry title for unmapped title slot', function () {
    $collection = Collection::create([
        'name' => 'Tours',
        'slug' => 'tours',
        'fields' => [],
    ]);

    $entry = $collection->entries()->create([
        'slug' => 'my-tour',
        'published' => true,
        'data' => ['title' => 'My Tour Title'],
        'sections' => [],
    ]);

    $block = new PackageList;
    $card = $block->resolveCard($entry, []); // no mappings

    expect($card->title)->toBe('My Tour Title');
});

test('ListBlock collectionFieldOptions returns tagged options per collection', function () {
    Collection::create([
        'name' => 'Packages',
        'slug' => 'packages',
        'enable_seo' => true,
        'fields' => [
            ['title' => 'Cover Image', 'template' => 'cover_image'],
            ['title' => 'Ticket Price', 'template' => 'ticket_price'],
        ],
    ]);

    $opts = ListBlock::collectionFieldOptions();

    $values = array_column($opts, 'value');
    expect($values)->toContain('')         // Not Mapped option
        ->and($values)->toContain('cover_image')
        ->and($values)->toContain('ticket_price');

    $coverOpt = collect($opts)->firstWhere('value', 'cover_image');
    expect($coverOpt['collection'])->toBe('packages')
        ->and($coverOpt['collection_name'])->toBe('Packages');

    $notMapped = collect($opts)->firstWhere('value', '');
    expect($notMapped['collection'])->toBe('');
});

test('PackageList block renders with taxonomy settings', function () {
    $destTax = Taxonomy::create(['title' => 'Destinations', 'slug' => 'destinations']);
    Term::create(['taxonomy_id' => $destTax->id, 'title' => 'Sylhet', 'slug' => 'sylhet']);

    $block = new PackageList;
    $html = $block->render(data: [
        'listCollection' => '',
        'destinationTaxonomy' => 'destinations',
        'categoryTaxonomy' => '',
        'packagesPerPage' => 6,
        'priceMax' => 500000,
    ]);

    expect($html)->toBeString()->toContain('Sylhet');
});
