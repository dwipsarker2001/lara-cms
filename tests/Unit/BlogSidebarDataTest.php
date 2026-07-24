<?php

use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Models\Taxonomy;
use App\Support\BlogSidebarData;

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
