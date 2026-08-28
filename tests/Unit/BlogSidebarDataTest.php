<?php

use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Models\Taxonomy;
use Plugins\TravelTheme\Support\BlogSidebarData;

it('returns recent posts array from BlogSidebarData', function () {
    $posts = BlogSidebarData::getRecentPosts();
    expect($posts)->toBeArray();
    expect(count($posts))->toBeGreaterThan(0);
});

it('returns categories array from BlogSidebarData', function () {
    $categories = BlogSidebarData::getCategories();
    expect($categories)->toBeArray();
    expect(count($categories))->toBeGreaterThan(0);
});

it('returns tags array from BlogSidebarData', function () {
    $tags = BlogSidebarData::getTags();
    expect($tags)->toBeArray();
    expect(count($tags))->toBeGreaterThan(0);
});

it('fetches live entries when published blog posts exist', function () {
    $collection = Collection::create(['name' => 'Blog Posts', 'slug' => 'posts']);
    CollectionEntry::create([
        'collection_id' => $collection->id,
        'slug' => 'test-dynamic-post',
        'data' => [
            'title' => 'Test Dynamic Post',
            'category' => 'Adventure',
            'tags' => ['Adventure', 'Nature'],
        ],
        'published' => true,
        'sections' => [],
    ]);

    $recent = BlogSidebarData::getRecentPosts(1);
    expect($recent[0]['title'])->toBe('Test Dynamic Post');
});

it('fetches categories from taxonomy models created in admin taxonomies', function () {
    Taxonomy::create([
        'title' => 'Custom Category',
        'slug' => 'custom-category',
    ]);

    $categories = BlogSidebarData::getCategories();
    expect(collect($categories)->pluck('name')->contains('Custom Category'))->toBeTrue();
});

it('accurately counts categories defined in entry data and entry sections', function () {
    $collection = Collection::create(['name' => 'Blogs', 'slug' => 'blogs']);

    // Entry 1: category in data
    CollectionEntry::create([
        'collection_id' => $collection->id,
        'slug' => 'post-1',
        'data' => [
            'title' => 'Post 1',
            'category' => 'Adventure',
        ],
        'published' => true,
        'sections' => [],
    ]);

    // Entry 2: category in sections (e.g. blogDetails block)
    CollectionEntry::create([
        'collection_id' => $collection->id,
        'slug' => 'post-2',
        'data' => [],
        'published' => true,
        'sections' => [
            [
                'name' => 'blogDetails',
                'enabled' => true,
                'data' => [
                    'title' => 'Trekking the Hills',
                    'category' => 'Adventure',
                    'author' => 'Rafi Hasan',
                ],
            ],
        ],
    ]);

    // Entry 3: different category in data
    CollectionEntry::create([
        'collection_id' => $collection->id,
        'slug' => 'post-3',
        'data' => [
            'title' => 'Post 3',
            'category' => 'Heritage',
        ],
        'published' => true,
        'sections' => [],
    ]);

    $categories = BlogSidebarData::getCategories('blogs');
    $adventure = collect($categories)->firstWhere('name', 'Adventure');
    $heritage = collect($categories)->firstWhere('name', 'Heritage');

    expect($adventure)->not->toBeNull();
    expect($adventure['count'])->toBe(2);
    expect($heritage)->not->toBeNull();
    expect($heritage['count'])->toBe(1);
});

it('extracts categories and tags from CollectionEntry and generic objects', function () {
    $obj = (object) [
        'data' => [
            'category' => 'Travel',
            'tags' => ['Europe', 'Adventure'],
        ],
        'sections' => [],
        'meta' => [],
    ];

    $categories = BlogSidebarData::extractEntryCategories($obj);
    $tags = BlogSidebarData::extractEntryTags($obj);

    expect($categories)->toContain('Travel');
    expect($tags)->toContain('Europe', 'Adventure');
});
