<?php

use App\Models\Admin;
use App\Models\Collection;
use App\Models\CollectionEntry;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');

    $this->collection = Collection::create([
        'name' => 'Blog Posts',
        'slug' => 'blog',
        'enable_seo' => true,
    ]);

    $this->entry = CollectionEntry::create([
        'collection_id' => $this->collection->id,
        'slug' => 'my-first-post',
        'published' => true,
        'data' => ['title' => 'My First Post'],
        'meta' => [],
    ]);
});

it('saves custom SEO metadata on collection entry update and renders it in public HTML', function () {
    put(route('admin.collections.entries.update', [$this->collection, $this->entry]), [
        'slug' => 'my-first-post',
        'published' => true,
        'meta' => [
            'metaTitle' => 'Custom Post Title for SEO',
            'metaDescription' => 'Special meta description for search engines.',
            'canonicalUrl' => 'https://example.com/blog/my-first-post',
            'socialImage' => '/uploads/post-og.jpg',
        ],
    ])->assertRedirect();

    $this->entry->refresh();
    expect($this->entry->meta['metaTitle'])->toBe('Custom Post Title for SEO');
    expect($this->entry->meta['metaDescription'])->toBe('Special meta description for search engines.');

    get('/blog/my-first-post')
        ->assertStatus(200)
        ->assertSee('<title>Custom Post Title for SEO</title>', false)
        ->assertSee('<meta name="description" content="Special meta description for search engines.">', false)
        ->assertSee('<link rel="canonical" href="https://example.com/blog/my-first-post">', false)
        ->assertSee('<meta property="og:image" content="/uploads/post-og.jpg">', false);
});

it('safely renders collection entry edit and create pages with complex strings, single quotes, newlines and json schema without syntax errors', function () {
    $complexEntry = CollectionEntry::create([
        'collection_id' => $this->collection->id,
        'slug' => 'complex-travel-post',
        'published' => true,
        'data' => [
            'title' => "World's Best \"Secret\" Destinations & Tips",
        ],
        'meta' => [
            'metaTitle' => "World's Best \"Secret\" Destinations",
            'metaDescription' => "Here's an in-depth review of traveler's favorites:\n1. Island's hidden bay\n2. \"Paradise\" resort",
            'schema' => "{\n  \"@context\": \"https://schema.org\",\n  \"@type\": \"BlogPosting\",\n  \"headline\": \"World's Best\"\n}",
            'ogTitle' => "World's Best",
            'xCardDescription' => "It's awesome!",
        ],
    ]);

    get(route('admin.collections.entries.edit', [$this->collection, $complexEntry]))
        ->assertStatus(200)
        ->assertSee('x-data="{', false);

    get(route('admin.collections.entries.create', $this->collection))
        ->assertStatus(200)
        ->assertSee('x-data="{', false);
});
