<?php

use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Support\BlogSidebarData;

test('blog list resolves featured_image from entry data', function () {
    $collection = Collection::create(['name' => 'Blog', 'slug' => 'blog']);
    CollectionEntry::create([
        'collection_id' => $collection->id,
        'slug' => 'test-featured-image-post',
        'data' => [
            'title' => 'Post with Featured Image',
            'featured_image' => 'https://example.com/featured.jpg',
        ],
        'published' => true,
    ]);

    $view = $this->view('blocks.blog-list', [
        'data' => ['postCollection' => 'blog'],
    ]);

    $view->assertSee('https://example.com/featured.jpg');
});

test('blog list handles empty thumbnail when entry has no image', function () {
    $collection = Collection::create(['name' => 'Blog', 'slug' => 'blog']);
    CollectionEntry::create([
        'collection_id' => $collection->id,
        'slug' => 'test-no-image-post',
        'data' => [
            'title' => 'Post without Image',
        ],
        'published' => true,
    ]);

    $recent = BlogSidebarData::getRecentPosts(1, 'blog');
    expect($recent[0]['image'])->toBeNull();

    $view = $this->view('blocks.blog-list', [
        'data' => ['postCollection' => 'blog'],
    ]);

    $view->assertSee('Post without Image');
});
