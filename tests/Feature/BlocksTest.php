<?php

use App\Blocks\Block;
use App\Blocks\BlockRegistry;
use App\Blocks\custom\HeroBanner;
use App\Blocks\custom\TravelDeals;
use App\Blocks\Field;

test('Block::render returns a string for a block with a view', function () {
    $block = new HeroBanner;

    $html = $block->render(data: ['headline' => 'Test headline', 'backgroundImage' => '', 'badge' => '', 'description' => '', 'searchUrl' => '', 'searchPlaceholder' => '', 'datePlaceholder' => '']);

    expect($html)
        ->toBeString()
        ->toContain('Test headline');
});

test('Block::render returns empty string when the view does not exist', function () {
    // Build an anonymous Block subclass whose kebab-name has no matching view.
    $block = new class extends Block
    {
        public string $name = 'definitelyMissingView';

        public string $label = 'Missing';

        public function view(): string
        {
            return 'blocks.does-not-exist';
        }

        public function fields(): array
        {
            return [];
        }
    };

    expect($block->render(data: []))->toBe('');
});

test('Block::resolvedFields prepends background for non-global blocks', function () {
    $block = new TravelDeals;

    $names = array_column($block->resolvedFields(), 'name');

    expect($names)->toContain('background');
    expect($names)->toContain('headline');
});

test('Block::toArray exposes name, label, global and fields', function () {
    $block = new HeroBanner;

    $array = $block->toArray();

    expect($array)
        ->toHaveKeys(['name', 'label', 'global', 'fields'])
        ->and($array['name'])->toBe('heroBanner')
        ->and($array['global'])->toBeFalse();
});

test('BlockRegistry discovers concrete block subclasses', function () {
    $registry = app(BlockRegistry::class);
    $all = $registry->all();

    expect($all)->toHaveKey('heroBanner')
        ->and($all['heroBanner'])->toBeInstanceOf(HeroBanner::class);
});

test('Field::background returns a structured object field with image, color, opacity', function () {
    $bg = Field::background();

    expect($bg['type'])->toBe('object');
    expect(array_column($bg['fields'], 'name'))->toBe(['image', 'color', 'opacity']);
});

test('Field::map returns a map field array', function () {
    $field = Field::map('mapImage', 'Map Image', default: 'https://maps.example.com');

    expect($field['type'])->toBe('map');
    expect($field['name'])->toBe('mapImage');
    expect($field['label'])->toBe('Map Image');
    expect($field['defaultValue'])->toBe('https://maps.example.com');
});

test('Block::parseMapValue resolves iframe tags, share links, embed URLs and image assets', function () {
    $iframeTag = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12..." width="600" height="450"></iframe>';
    $shareLink = 'https://maps.app.goo.gl/YZTtmrA8dA1ok9Hz9';
    $embedUrl = 'https://www.google.com/maps/embed?pb=!1m18!...';
    $imagePath = '/uploads/map-sylhet.jpg';

    $parsedIframe = Block::parseMapValue($iframeTag);
    expect($parsedIframe['type'])->toBe('iframe');
    expect($parsedIframe['url'])->toBe('https://www.google.com/maps/embed?pb=!1m18!1m12...');

    $parsedShare = Block::parseMapValue($shareLink);
    expect($parsedShare['type'])->toBe('iframe');
    expect($parsedShare['url'])->toContain('https://maps.google.com/maps?q=https%3A%2F%2Fmaps.app.goo.gl');

    $parsedEmbed = Block::parseMapValue($embedUrl);
    expect($parsedEmbed['type'])->toBe('iframe');
    expect($parsedEmbed['url'])->toBe($embedUrl);

    $parsedImage = Block::parseMapValue($imagePath);
    expect($parsedImage['type'])->toBe('image');
    expect($parsedImage['url'])->toBe($imagePath);
});
