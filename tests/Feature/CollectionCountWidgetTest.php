<?php

use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Widgets\CollectionCountWidget;

it('correctly queries collections and their entries counts', function () {
    // Clear entries first
    CollectionEntry::query()->delete();
    Collection::query()->delete();

    // Create collections
    $pages = Collection::create(['name' => 'Pages', 'slug' => 'pages']);
    $blog = Collection::create(['name' => 'Blog Posts', 'slug' => 'blog']);

    // Create entries for Pages (2 total, 1 published)
    $pages->entries()->create(['slug' => 'home', 'data' => [], 'published' => true, 'sections' => []]);
    $pages->entries()->create(['slug' => 'about', 'data' => [], 'published' => false, 'sections' => []]);

    // Create entries for Blog Posts (1 total, 1 published)
    $blog->entries()->create(['slug' => 'post-1', 'data' => [], 'published' => true, 'sections' => []]);

    $widget = new CollectionCountWidget;
    $view = $widget->render();

    $collections = $view->getData()['collections'];

    expect($collections)->toHaveCount(2);

    $pagesData = $collections->firstWhere('slug', 'pages');
    expect($pagesData['count'])->toBe('2');
    expect($pagesData['published'])->toBe(1);
    expect($pagesData['delta'])->toBe('1 / 2 published');
    expect($pagesData['up'])->toBeFalse();

    $blogData = $collections->firstWhere('slug', 'blog');
    expect($blogData['count'])->toBe('1');
    expect($blogData['published'])->toBe(1);
    expect($blogData['delta'])->toBe('All published');
    expect($blogData['up'])->toBeTrue();
});
