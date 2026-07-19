<?php

use App\Models\Admin;
use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Models\Page;
use App\Models\Setting;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('can view collection create page with collections list', function () {
    $c = Collection::create(['name' => 'Existing Collection', 'slug' => 'existing', 'position' => 1]);
    get(route('admin.collections.create'))
        ->assertSuccessful()
        ->assertSee('Existing Collection');
});

it('can create collection with fields data including collection type', function () {
    $target = Collection::create(['name' => 'Target Collection', 'slug' => 'target', 'position' => 1]);

    $fields = [
        [
            'title' => 'Related Entry',
            'description' => 'Select a related entry',
            'type' => 'collection',
            'template' => 'related_entry',
            'collection_id' => (string) $target->id,
        ],
    ];

    post(route('admin.collections.store'), [
        'name' => 'New Collection',
        'icon' => 'fa-book',
        'fields' => json_encode($fields),
        'enable_seo' => '1',
    ])->assertRedirect();

    $collection = Collection::where('slug', 'new-collection')->firstOrFail();
    expect($collection->fields)->toBeArray();
    expect($collection->fields[0]['type'])->toBe('collection');
    expect($collection->fields[0]['collection_id'])->toBe((string) $target->id);
    expect($collection->enable_seo)->toBeTrue();
});

it('copies sections from selected collection relation during entry creation', function () {
    // 1. Create a target collection, page, and entry
    $targetCollection = Collection::create(['name' => 'Target', 'slug' => 'target', 'position' => 1]);
    $targetPage = Page::create([
        'title' => 'Target Page',
        'slug' => 'target-page',
        'published' => true,
        'sections' => [
            ['_key' => 's1', 'name' => 'HeroBanner', 'data' => ['headline' => 'Target Headline'], 'enabled' => true],
        ],
    ]);
    $targetEntry = $targetCollection->entries()->create([
        'data' => ['title' => 'Target Title'],
        'page_id' => $targetPage->id,
        'sections' => $targetPage->sections,
        'position' => 1,
    ]);

    // 2. Create a source collection referencing the target
    $sourceCollection = Collection::create([
        'name' => 'Source',
        'slug' => 'source',
        'position' => 2,
        'fields' => [
            [
                'title' => 'Related Entry',
                'description' => '',
                'type' => 'collection',
                'template' => 'related_entry',
                'collection_id' => (string) $targetCollection->id,
            ],
        ],
    ]);

    // 3. Create entry selecting the relation
    post(route('admin.collections.entries.store', $sourceCollection), [
        'data' => [
            'title' => 'New Source Title',
            'related_entry' => (string) $targetEntry->id,
        ],
        'slug' => 'new-source-entry',
        'published' => '1',
    ])->assertRedirect();

    // 4. Verify created entry page has copied sections
    $newEntry = CollectionEntry::where('collection_id', $sourceCollection->id)->firstOrFail();
    $newPage = $newEntry->page;
    expect($newPage->sections)->toBe($targetPage->sections);
    expect($newEntry->sections)->toBe($targetPage->sections);
});

it('hides SEO tabs if enable_seo is false', function () {
    $c = Collection::create([
        'name' => 'No SEO Collection',
        'slug' => 'no-seo',
        'position' => 3,
        'enable_seo' => false,
    ]);

    get(route('admin.collections.entries.create', $c))
        ->assertSuccessful()
        ->assertDontSee('SEO pro')
        ->assertDontSee('Meta Title');
});

it('routes pages collection entries to the root slug', function () {
    $collection = Collection::create([
        'name' => 'Pages',
        'slug' => 'pages',
        'position' => 4,
    ]);

    $page = Page::create([
        'title' => 'Hello Page',
        'slug' => 'hello-page',
        'published' => true,
        'sections' => [],
    ]);

    $entry = $collection->entries()->create([
        'data' => ['title' => 'Hello Page'],
        'page_id' => $page->id,
        'sections' => [],
        'position' => 1,
    ]);

    expect($page->route())->toBe('/hello-page');

    get('/hello-page')->assertSuccessful();
    get('/pages/hello-page')->assertNotFound();
});

it('renders SEO tags dynamically on the public layout', function () {
    $collection = Collection::create([
        'name' => 'News',
        'slug' => 'news',
        'position' => 5,
        'enable_seo' => true,
    ]);

    // Create a page with custom SEO metadata
    $page = Page::create([
        'title' => 'Sample Article Title',
        'slug' => 'sample-article',
        'published' => true,
        'sections' => [],
        'meta' => [
            'metaTitle' => 'SEO Optimized Title Override',
            'metaDescription' => 'This is a custom SEO optimized description.',
            'canonicalUrl' => 'https://customsite.com/sample-article',
            'indexing' => 'No',
            'linkFollowing' => 'No',
        ],
    ]);

    $collection->entries()->create([
        'data' => ['title' => 'Sample Article Title'],
        'page_id' => $page->id,
        'sections' => [],
        'position' => 1,
    ]);

    // Set site name default in settings table
    $settings = Setting::firstOrCreate(['id' => 1]);
    $settings->update([
        'seo' => [
            'siteName' => 'My CMS Site Name',
            'namePosition' => 'After',
            'separator' => '|',
        ],
    ]);

    get('/news/sample-article')
        ->assertSuccessful()
        ->assertSee('<title>SEO Optimized Title Override | My CMS Site Name</title>', false)
        ->assertSee('<meta name="description" content="This is a custom SEO optimized description.">', false)
        ->assertSee('<link rel="canonical" href="https://customsite.com/sample-article">', false)
        ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
});
