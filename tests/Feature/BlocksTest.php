<?php

use App\Blocks\Block;
use App\Blocks\BlockRegistry;
use App\Blocks\custom\HeroBanner;
use App\Blocks\custom\TravelDeals;
use App\Blocks\Field;
use App\Models\Admin;
use App\Models\Collection;
use App\Support\BlockPreview;
use App\Support\Sections;

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

test('Block::resolvedFields prepends background and devices for non-global blocks', function () {
    $block = new TravelDeals;

    $names = array_column($block->resolvedFields(), 'name');

    expect($names)->toContain('devices');
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

test('Sections::withGlobals places header globals at top and footer globals at bottom', function () {
    $pages = Collection::create(['name' => 'Pages', 'slug' => 'pages']);
    $pages->entries()->create([
        'slug' => 'home',
        'published' => true,
        'sections' => [
            ['_key' => '1', 'name' => 'siteNavbar', 'enabled' => true, 'data' => ['brandName' => 'Home Nav']],
            ['_key' => '2', 'name' => 'heroBanner', 'enabled' => true, 'data' => ['headline' => 'Home Hero']],
            ['_key' => '3', 'name' => 'siteFooter', 'enabled' => true, 'data' => ['copyright' => 'Home Footer']],
        ],
    ]);

    $sections = [
        ['_key' => '10', 'name' => 'simpleText', 'enabled' => true, 'data' => ['text' => 'Page Text']],
    ];

    $resolved = Sections::withGlobals($sections);
    $names = array_column($resolved, 'name');

    expect($names)->toBe(['siteNavbar', 'simpleText', 'siteFooter']);
});

test('updating global block from about page synchronizes home and all other pages', function () {
    $admin = Admin::factory()->create();
    $pages = Collection::create(['name' => 'Pages', 'slug' => 'pages']);

    $home = $pages->entries()->create([
        'slug' => 'home',
        'title' => 'Home',
        'published' => true,
        'sections' => [
            ['_key' => '1', 'name' => 'siteNavbar', 'enabled' => true, 'data' => ['brandName' => 'Original Brand']],
            ['_key' => '2', 'name' => 'heroBanner', 'enabled' => true, 'data' => ['headline' => 'Home Hero']],
        ],
    ]);

    $contact = $pages->entries()->create([
        'slug' => 'contact',
        'title' => 'Contact',
        'published' => true,
        'sections' => [
            ['_key' => '10', 'name' => 'siteNavbar', 'enabled' => true, 'data' => ['brandName' => 'Original Brand']],
            ['_key' => '11', 'name' => 'simpleText', 'enabled' => true, 'data' => ['text' => 'Contact Form']],
        ],
    ]);

    $about = $pages->entries()->create([
        'slug' => 'about',
        'title' => 'About',
        'published' => true,
        'sections' => [
            ['_key' => '20', 'name' => 'siteNavbar', 'enabled' => true, 'data' => ['brandName' => 'Updated Global Brand']],
            ['_key' => '21', 'name' => 'simpleText', 'enabled' => true, 'data' => ['text' => 'About Us']],
        ],
    ]);

    $response = $this->actingAs($admin, 'admin')->patchJson(
        route('admin.collections.entries.update-sections', [$pages, $about]),
        ['sections' => $about->sections]
    );

    $response->assertOk();

    // Home entry sections should be updated
    $home->refresh();
    $homeNav = collect($home->sections)->firstWhere('name', 'siteNavbar');
    expect($homeNav['data']['brandName'])->toBe('Updated Global Brand');

    // Contact entry sections should also be updated
    $contact->refresh();
    $contactNav = collect($contact->sections)->firstWhere('name', 'siteNavbar');
    expect($contactNav['data']['brandName'])->toBe('Updated Global Brand');
});

test('editor loads latest global block data even if entry had older snapshot', function () {
    $admin = Admin::factory()->create();
    $pages = Collection::create(['name' => 'Pages', 'slug' => 'pages']);

    $home = $pages->entries()->create([
        'slug' => 'home',
        'title' => 'Home',
        'published' => true,
        'sections' => [
            ['_key' => '1', 'name' => 'siteNavbar', 'enabled' => true, 'data' => ['brandName' => 'Master Brand']],
        ],
    ]);

    $about = $pages->entries()->create([
        'slug' => 'about',
        'title' => 'About',
        'published' => true,
        'sections' => [
            ['_key' => '20', 'name' => 'siteNavbar', 'enabled' => true, 'data' => ['brandName' => 'Stale Brand']],
        ],
    ]);

    $response = $this->actingAs($admin, 'admin')->get(
        route('admin.collections.entries.editor', [$pages, $about])
    );

    $response->assertOk();
    $response->assertViewHas('syncedSections', function ($synced) {
        $nav = collect($synced)->firstWhere('name', 'siteNavbar');

        return $nav && $nav['data']['brandName'] === 'Master Brand';
    });
});

test('Field::devices returns a devices field definition with default true for all devices', function () {
    $devices = Field::devices();

    expect($devices['type'])->toBe('devices')
        ->and($devices['name'])->toBe('devices')
        ->and($devices['label'])->toBe('Screen Visibility')
        ->and($devices['defaultValue'])->toBe([
            'laptop' => true,
            'tablet' => true,
            'mobile' => true,
        ]);
});

test('BlockPreview::getDeviceVisibilityClasses generates accurate responsive Tailwind classes', function () {
    // All active or unconfigured/empty -> empty string (shows on all screens)
    expect(BlockPreview::getDeviceVisibilityClasses(['laptop' => true, 'tablet' => true, 'mobile' => true]))->toBe('');
    expect(BlockPreview::getDeviceVisibilityClasses(null))->toBe('');
    expect(BlockPreview::getDeviceVisibilityClasses(''))->toBe('');
    expect(BlockPreview::getDeviceVisibilityClasses([]))->toBe('');
    expect(BlockPreview::getDeviceVisibilityClasses(['laptop' => false, 'tablet' => false, 'mobile' => false]))->toBe('');

    // Single device visible
    expect(BlockPreview::getDeviceVisibilityClasses(['laptop' => true, 'tablet' => false, 'mobile' => false]))->toBe('hidden lg:block');
    expect(BlockPreview::getDeviceVisibilityClasses(['laptop' => false, 'tablet' => true, 'mobile' => false]))->toBe('hidden md:block lg:hidden');
    expect(BlockPreview::getDeviceVisibilityClasses(['laptop' => false, 'tablet' => false, 'mobile' => true]))->toBe('block md:hidden');

    // Two devices visible (one hidden)
    expect(BlockPreview::getDeviceVisibilityClasses(['laptop' => false, 'tablet' => true, 'mobile' => true]))->toBe('lg:hidden');
    expect(BlockPreview::getDeviceVisibilityClasses(['laptop' => true, 'tablet' => false, 'mobile' => true]))->toBe('block md:hidden lg:block');
    expect(BlockPreview::getDeviceVisibilityClasses(['laptop' => true, 'tablet' => true, 'mobile' => false]))->toBe('hidden md:block');
});

test('BlockPreview::render applies device visibility classes to the section wrapper', function () {
    $sections = [
        [
            '_key' => 's1',
            'name' => 'simpleText',
            'enabled' => true,
            'data' => [
                'text' => 'Mobile only section',
                'devices' => ['laptop' => false, 'tablet' => false, 'mobile' => true],
            ],
        ],
    ];

    $html = BlockPreview::render($sections, withGlobals: false);

    expect($html)->toContain('data-section-index="0"')
        ->and($html)->toContain('class="block md:hidden"');
});

test('BlockPreview::render ensures legacy blocks without devices setting show on all screens', function () {
    $sections = [
        [
            '_key' => 's2',
            'name' => 'simpleText',
            'enabled' => true,
            'data' => [
                'text' => 'Legacy section without devices data',
            ],
        ],
    ];

    $html = BlockPreview::render($sections, withGlobals: false);

    expect($html)->toContain('<div data-section-index="0">')
        ->and($html)->not->toContain('class="hidden"')
        ->and($html)->not->toContain('lg:hidden');
});
