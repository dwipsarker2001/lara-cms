<?php

use App\Models\Admin;
use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Models\Setting;
use App\Models\Taxonomy;
use App\Models\Term;

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
    // 1. Create a target collection and entry
    $targetCollection = Collection::create(['name' => 'Target', 'slug' => 'target', 'position' => 1]);
    $targetEntry = $targetCollection->entries()->create([
        'slug' => 'target-entry',
        'data' => ['title' => 'Target Title'],
        'published' => true,
        'sections' => [
            ['_key' => 's1', 'name' => 'HeroBanner', 'data' => ['headline' => 'Target Headline'], 'enabled' => true],
        ],
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

    // 4. Verify created entry has copied sections
    $newEntry = CollectionEntry::where('collection_id', $sourceCollection->id)->firstOrFail();
    expect($newEntry->sections)->toBe($targetEntry->sections);
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

    $entry = $collection->entries()->create([
        'slug' => 'hello-page',
        'data' => ['title' => 'Hello Page'],
        'published' => true,
        'sections' => [],
        'position' => 1,
    ]);

    expect($entry->route())->toBe('/hello-page');

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

    // Create an entry with custom SEO metadata
    $entry = $collection->entries()->create([
        'slug' => 'sample-article',
        'data' => ['title' => 'Sample Article Title'],
        'published' => true,
        'sections' => [],
        'position' => 1,
        'meta' => [
            'metaTitle' => 'SEO Optimized Title Override',
            'metaDescription' => 'This is a custom SEO optimized description.',
            'canonicalUrl' => 'https://customsite.com/sample-article',
            'indexing' => 'No',
            'linkFollowing' => 'No',
        ],
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

it('can create and edit collection entries using taxonomies custom field type', function () {
    $taxonomy = Taxonomy::create(['title' => 'Travel Tags', 'slug' => 'travel-tags']);
    $term1 = Term::create(['taxonomy_id' => $taxonomy->id, 'title' => 'Adventure', 'slug' => 'adventure']);
    $term2 = Term::create(['taxonomy_id' => $taxonomy->id, 'title' => 'Heritage', 'slug' => 'heritage']);

    $collection = Collection::create([
        'name' => 'Tour Packages',
        'slug' => 'tours',
        'position' => 6,
        'fields' => [
            [
                'title' => 'Tags Selection',
                'description' => 'Select tags for the package',
                'type' => 'taxonomies',
                'template' => 'tags_selection',
                'taxonomy_id' => (string) $taxonomy->id,
            ],
        ],
    ]);

    // Create entry selecting multiple terms (IDs)
    post(route('admin.collections.entries.store', $collection), [
        'data' => [
            'title' => 'Adventure Package',
            'tags_selection' => [(string) $term1->id, (string) $term2->id],
        ],
        'slug' => 'adventure-package',
        'published' => '1',
    ])->assertRedirect();

    $entry = CollectionEntry::where('slug', 'adventure-package')->firstOrFail();
    expect($entry->data['tags_selection'])->toBeArray();
    expect($entry->data['tags_selection'])->toContain((string) $term1->id);
    expect($entry->data['tags_selection'])->toContain((string) $term2->id);

    // Get the edit page to verify tags load correctly
    get(route('admin.collections.entries.edit', [$collection, $entry]))
        ->assertSuccessful()
        ->assertSee('Adventure')
        ->assertSee('Heritage');
});

it('can create and edit collection entries using tags custom field type', function () {
    $collection = Collection::create([
        'name' => 'News Articles',
        'slug' => 'news-articles',
        'position' => 7,
        'fields' => [
            [
                'title' => 'Article Tags',
                'description' => 'Enter tags for this article',
                'type' => 'tags',
                'template' => 'article_tags',
            ],
        ],
    ]);

    // Create entry with tags
    post(route('admin.collections.entries.store', $collection), [
        'data' => [
            'title' => 'Breaking News',
            'article_tags' => ['Tech', 'Laravel', 'AI'],
        ],
        'slug' => 'breaking-news',
        'published' => '1',
    ])->assertRedirect();

    $entry = CollectionEntry::where('slug', 'breaking-news')->firstOrFail();
    expect($entry->data['article_tags'])->toBeArray();
    expect($entry->data['article_tags'])->toContain('Tech');
    expect($entry->data['article_tags'])->toContain('Laravel');
    expect($entry->data['article_tags'])->toContain('AI');

    // Get the edit page to verify tags load correctly
    get(route('admin.collections.entries.edit', [$collection, $entry]))
        ->assertSuccessful()
        ->assertSee('Tech')
        ->assertSee('Laravel')
        ->assertSee('AI');
});

it('supports default_entry_id for collection field type and preselects it', function () {
    $targetCollection = Collection::create(['name' => 'Authors', 'slug' => 'authors', 'position' => 1]);
    $targetEntry = $targetCollection->entries()->create([
        'slug' => 'john-doe',
        'data' => ['title' => 'John Doe'],
        'published' => true,
        'position' => 1,
    ]);

    $sourceCollection = Collection::create([
        'name' => 'Posts',
        'slug' => 'posts',
        'position' => 2,
        'fields' => [
            [
                'title' => 'Author',
                'description' => 'Select an author',
                'type' => 'collection',
                'template' => 'author_id',
                'collection_id' => (string) $targetCollection->id,
                'default_entry_id' => (string) $targetEntry->id,
            ],
        ],
    ]);

    get(route('admin.collections.edit', $sourceCollection))
        ->assertSuccessful()
        ->assertSee('Default Entry')
        ->assertSee('John Doe');

    get(route('admin.collections.entries.create', $sourceCollection))
        ->assertSuccessful()
        ->assertSee('John Doe');
});

it('filters out collection type custom fields from editor collectionFields list', function () {
    $targetCollection = Collection::create(['name' => 'Target', 'slug' => 'target', 'position' => 1]);
    $sourceCollection = Collection::create([
        'name' => 'Source Collection',
        'slug' => 'source-col',
        'position' => 2,
        'fields' => [
            [
                'title' => 'Cover Photo',
                'type' => 'image',
                'template' => 'cover_photo',
            ],
            [
                'title' => 'Related Tour',
                'type' => 'collection',
                'template' => 'related_tour',
                'collection_id' => (string) $targetCollection->id,
            ],
        ],
    ]);

    $entry = $sourceCollection->entries()->create([
        'slug' => 'test-entry',
        'data' => [
            'title' => 'Test Entry Title',
            'cover_photo' => 'https://example.com/photo.jpg',
            'related_tour' => '123',
        ],
        'published' => true,
        'sections' => [],
        'position' => 1,
    ]);

    get(route('admin.collections.entries.editor', [$sourceCollection, $entry]))
        ->assertSuccessful()
        ->assertSee('"key":"cover_photo"', false)
        ->assertDontSee('"key":"related_tour"', false);
});

it('shows all collection fields when enable_seo is false and only specific collection fields when enable_seo is true', function () {
    $packagesCol = Collection::create([
        'name' => 'Packages',
        'slug' => 'packages',
        'enable_seo' => true,
        'position' => 1,
        'fields' => [
            ['title' => 'Package Image', 'type' => 'image', 'template' => 'package_image'],
        ],
    ]);

    $layoutCol = Collection::create([
        'name' => 'Layouts',
        'slug' => 'layouts',
        'enable_seo' => false,
        'position' => 2,
        'fields' => [
            ['title' => 'Layout Banner', 'type' => 'image', 'template' => 'layout_banner'],
        ],
    ]);

    $packageEntry = $packagesCol->entries()->create([
        'slug' => 'bali-tour',
        'data' => ['title' => 'Bali Tour', 'package_image' => 'https://example.com/bali.jpg'],
        'published' => true,
        'sections' => [],
    ]);

    $layoutEntry = $layoutCol->entries()->create([
        'slug' => 'hero-layout',
        'data' => ['title' => 'Hero Layout', 'layout_banner' => 'https://example.com/banner.jpg'],
        'published' => true,
        'sections' => [],
    ]);

    // 1. Specific Collection (enable_seo === true): shows ONLY package_image, NOT layout_banner
    get(route('admin.collections.entries.editor', [$packagesCol, $packageEntry]))
        ->assertSuccessful()
        ->assertSee('package_image')
        ->assertDontSee('layout_banner');

    // 2. Layout Collection (enable_seo === false): shows ALL collections fields (package_image AND layout_banner)
    get(route('admin.collections.entries.editor', [$layoutCol, $layoutEntry]))
        ->assertSuccessful()
        ->assertSee('package_image')
        ->assertSee('layout_banner');
});

it('always includes title field in grouped collection fields for input binding', function () {
    $layoutCol = Collection::create([
        'name' => 'Layouts',
        'slug' => 'layouts',
        'enable_seo' => false,
        'position' => 1,
        'fields' => [
            ['title' => 'Layout Banner', 'type' => 'image', 'template' => 'layout_banner'],
        ],
    ]);

    $servicesCol = Collection::create([
        'name' => 'Services',
        'slug' => 'services',
        'enable_seo' => true,
        'position' => 2,
        'fields' => [
            ['title' => 'Service Icon', 'type' => 'image', 'template' => 'service_icon'],
        ],
    ]);

    $layoutEntry = $layoutCol->entries()->create([
        'slug' => 'main-layout',
        'data' => ['title' => 'Main Layout'],
        'published' => true,
        'sections' => [],
    ]);

    $response = get(route('admin.collections.entries.editor', [$layoutCol, $layoutEntry]));
    $response->assertSuccessful();

    // Verify Title key exists under both groups in the JSON data passed to window.editorGroupedCollectionFields
    $response->assertSee('"key":"title"', false);
    $response->assertSee('"label":"Title"', false);
});

it('can create collection with location field and store entry with country state city data', function () {
    $collection = Collection::create([
        'name' => 'Tours',
        'slug' => 'tours',
        'position' => 1,
        'fields' => [
            [
                'title' => 'Tour Location',
                'description' => 'Select destination location',
                'type' => 'location',
                'template' => 'tour_location',
            ],
        ],
    ]);

    // Check entry create view renders the location picker setup
    get(route('admin.collections.entries.create', $collection))
        ->assertSuccessful()
        ->assertSee('locationField')
        ->assertSee('data[tour_location][country]', false);

    // Store entry with structured location data
    post(route('admin.collections.entries.store', $collection), [
        'data' => [
            'title' => 'Paris Romance Trip',
            'tour_location' => [
                'country' => 'France',
                'country_code' => 'FR',
                'state' => 'Île-de-France',
                'state_code' => 'IDF',
                'city' => 'Paris',
                'formatted' => 'Paris, Île-de-France, France',
            ],
        ],
        'slug' => 'paris-romance-trip',
        'published' => '1',
    ])->assertRedirect();

    $entry = CollectionEntry::where('slug', 'paris-romance-trip')->firstOrFail();
    expect($entry->data['tour_location']['country'])->toBe('France');
    expect($entry->data['tour_location']['country_code'])->toBe('FR');
    expect($entry->data['tour_location']['city'])->toBe('Paris');
    expect($entry->data['tour_location']['formatted'])->toBe('Paris, Île-de-France, France');

    // Check entry edit view renders the saved location values
    get(route('admin.collections.entries.edit', [$collection, $entry]))
        ->assertSuccessful()
        ->assertSee('locationField')
        ->assertSee('Paris, Île-de-France, France');
});

it('supports custom sub-field checkboxes (country, state, city) on location custom fields', function () {
    $fields = [
        [
            'title' => 'City Only Location',
            'type' => 'location',
            'template' => 'city_location',
            'enable_country' => false,
            'enable_state' => false,
            'enable_city' => true,
        ],
    ];

    post(route('admin.collections.store'), [
        'name' => 'City Tours',
        'icon' => 'fa-map',
        'fields' => json_encode($fields),
        'enable_seo' => '0',
    ])->assertRedirect();

    $collection = Collection::where('slug', 'city-tours')->firstOrFail();
    expect($collection->fields[0]['enable_country'])->toBeFalse();
    expect($collection->fields[0]['enable_city'])->toBeTrue();

    // Verify create view hides country and state hidden inputs and renders config
    get(route('admin.collections.entries.create', $collection))
        ->assertSuccessful()
        ->assertSee('enable_country: false', false)
        ->assertSee('enable_city: true', false)
        ->assertSee('data[city_location][city]', false)
        ->assertDontSee('data[city_location][country]', false)
        ->assertDontSee('data[city_location][state]', false);
});
