<?php

use App\Blocks\Block;
use App\Blocks\BlockRegistry;
use App\Blocks\Common\HeroSimple;
use App\Blocks\Field;

test('Block::render returns a string for a block with a view', function () {
    $block = new HeroSimple;

    // HeroSimple has a $background=false override plus background is auto-prepended for non-global
    // blocks, but the headline-only default data is enough to render the existing heroSimple view.
    $html = $block->render(data: ['headline' => 'Test headline', 'subtitle' => '', 'ctaLabel' => '', 'ctaUrl' => '', 'dashboardImage' => '', 'rating' => '', 'ratingLabel' => '', 'badge' => '']);

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

    expect($block->render())->toBe('');
});

test('Block::resolvedFields prepends background for non-global blocks', function () {
    $block = new \App\Blocks\Common\TravelDeals;

    $names = array_column($block->resolvedFields(), 'name');

    expect($names)->toContain('background');
    expect($names)->toContain('headline');
});

test('Block::toArray exposes name, label, global and fields', function () {
    $block = new HeroSimple;

    $array = $block->toArray();

    expect($array)
        ->toHaveKeys(['name', 'label', 'global', 'fields'])
        ->and($array['name'])->toBe('heroSimple')
        ->and($array['global'])->toBeFalse();
});

test('BlockRegistry discovers concrete block subclasses', function () {
    $registry = app(BlockRegistry::class);
    $all = $registry->all();

    expect($all)->toHaveKey('heroSimple')
        ->and($all['heroSimple'])->toBeInstanceOf(HeroSimple::class);
});

test('Field::background returns a structured object field with image, color, opacity', function () {
    $bg = Field::background();

    expect($bg['type'])->toBe('object');
    expect(array_column($bg['fields'], 'name'))->toBe(['image', 'color', 'opacity']);
});
