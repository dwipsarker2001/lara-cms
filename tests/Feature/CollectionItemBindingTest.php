<?php

use App\Blocks\BlockRegistry;
use App\Models\Admin;
use App\Models\Collection;
use App\Support\PluginLoader;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    app(PluginLoader::class)->boot();
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('resolves bound collection entry fields dynamically via entry:id:key sources and live-updates on price change', function () {
    $packagesCol = Collection::create(['name' => 'Packages', 'slug' => 'packages', 'enable_seo' => true]);
    $entry = $packagesCol->entries()->create([
        'slug' => 'paris-getaway',
        'published' => true,
        'data' => [
            'title' => 'Paris Luxury 5-Day Getaway',
            'price' => '25000',
            'original_price' => '30000',
            'image' => '/storage/paris.jpg',
            'description' => 'Experience the magic of Paris.',
            'badge' => 'Exclusive Deal',
        ],
    ]);

    $block = app(BlockRegistry::class)->get('travelDeals');
    expect($block)->not->toBeNull();

    $blockData = [
        'headline' => 'Special Deals',
        '_sources' => [
            'cards.0.title' => 'entry:'.$entry->id.':title',
            'cards.0.price' => 'entry:'.$entry->id.':price',
            'cards.0.originalPrice' => 'entry:'.$entry->id.':original_price',
            'cards.0.image' => 'entry:'.$entry->id.':image',
            'cards.0.buttonLink' => 'entry:'.$entry->id.':link',
        ],
        'cards' => [
            [
                'image' => '/placeholder-image.png',
                'title' => 'Placeholder',
                'price' => '0',
                'originalPrice' => '0',
                'buttonLabel' => 'Book Now',
                'buttonLink' => '#',
            ],
            [
                'image' => '/custom-card.jpg',
                'title' => 'Manual Tour Card',
                'price' => '15000',
                'originalPrice' => '18000',
                'buttonLabel' => 'Explore',
                'buttonLink' => '/tours/manual',
            ],
        ],
    ];

    $html = $block->render($blockData);

    expect($html)->toContain('Paris Luxury 5-Day Getaway');
    expect($html)->toContain('25,000');
    expect($html)->toContain('30,000');
    expect($html)->toContain('/storage/paris.jpg');
    expect($html)->toContain('/packages/paris-getaway');
    expect($html)->toContain('Manual Tour Card');
    expect($html)->toContain('15,000');

    // Update the collection entry's price in DB
    $entry->update([
        'data' => array_merge($entry->data, [
            'price' => '19999',
            'title' => 'Paris Luxury 5-Day FLASH SALE',
        ]),
    ]);

    $updatedHtml = $block->render($blockData);

    expect($updatedHtml)->toContain('Paris Luxury 5-Day FLASH SALE');
    expect($updatedHtml)->toContain('19,999');
});

it('renders collection and entry picker in bind input popover in entry editor', function () {
    $collection = Collection::create(['name' => 'Pages', 'slug' => 'pages']);
    $entry = $collection->entries()->create([
        'slug' => 'home',
        'data' => ['title' => 'Home Page'],
        'sections' => [],
    ]);

    $response = get(route('admin.collections.entries.editor', [$collection, $entry]));
    $response->assertSuccessful();
    $response->assertSee('getEntrySourceFields', false);
    $response->assertSee('entryPickerOpen', false);
});
