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

    $view = $this->view('blocks.custom.blog-list', [
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

    $view = $this->view('blocks.custom.blog-list', [
        'data' => ['postCollection' => 'blog'],
    ]);

    $view->assertSee('Post without Image');
});

test('blog list resolves map_title slot mapping to slug when configured', function () {
    $collection = Collection::create(['name' => 'Blog', 'slug' => 'blog']);
    CollectionEntry::create([
        'collection_id' => $collection->id,
        'slug' => 'custom-slug-title-test',
        'data' => [
            'title' => 'Original Post Title',
        ],
        'published' => true,
    ]);

    $view = $this->view('blocks.custom.blog-list', [
        'data' => [
            'postCollection' => 'blog',
            'map_title' => 'slug',
        ],
    ]);

    $view->assertSee('custom-slug-title-test');
});

test('blog list resolves map_author slot mapping to created_by when configured', function () {
    $collection = Collection::create(['name' => 'Blog', 'slug' => 'blog']);
    CollectionEntry::create([
        'collection_id' => $collection->id,
        'slug' => 'created-by-test',
        'data' => [
            'title' => 'Post with Created By',
            'created_by' => 'John Doe',
        ],
        'published' => true,
    ]);

    $view = $this->view('blocks.custom.blog-list', [
        'data' => [
            'postCollection' => 'blog',
            'map_author' => 'created_by',
        ],
    ]);

    $view->assertSee('John Doe');
});

test('public collection entry route /blogs/first-blog renders successfully', function () {
    $collection = Collection::create(['name' => 'Blogs', 'slug' => 'blogs']);
    $entry = CollectionEntry::create([
        'collection_id' => $collection->id,
        'slug' => 'first-blog',
        'data' => [
            'title' => 'First Blog Post',
            'content' => 'Hello World Content',
        ],
        'published' => true,
        'sections' => [],
    ]);

    $response = $this->get('/blogs/first-blog');

    $response->assertOk();
    $response->assertViewIs('public.page');
    $response->assertViewHas('page', function ($page) use ($entry) {
        return $page->id === $entry->id;
    });
});
