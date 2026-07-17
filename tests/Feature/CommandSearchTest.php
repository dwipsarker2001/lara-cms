<?php

use App\Models\Admin;
use App\Models\Layout;
use App\Models\Page;
use App\Models\Post;
use App\Models\Taxonomy;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('requires authentication', function () {
    auth('admin')->logout();

    getJson(route('admin.search'))->assertRedirect();
});

it('returns navigation commands when query is empty', function () {
    $response = getJson(route('admin.search'))->assertSuccessful();

    $groups = $response->json('groups');
    expect($groups)->not->toBeEmpty();
    expect(collect($groups)->pluck('group')->all())->toContain('Navigation');

    $ids = collect($groups)->flatMap(fn ($g) => collect($g['items'])->pluck('id'))->all();
    expect($ids)->toContain('nav-dashboard');
    expect($ids)->toContain('nav-pages');
    // Empty query is a launcher: only Navigation, capped per group.
    expect(collect($groups)->pluck('group')->all())->toBe(['Navigation']);
});

it('finds create actions when searching', function () {
    $response = getJson(route('admin.search', ['q' => 'new page']))->assertSuccessful();

    $ids = collect($response->json('groups'))
        ->flatMap(fn ($g) => collect($g['items'])->pluck('id'))
        ->all();

    expect($ids)->toContain('act-new-page');
});

it('searches live pages posts layouts and taxonomies', function () {
    $page = Page::create([
        'title' => 'Unique Himalaya Trek Page',
        'slug' => 'himalaya-trek-page',
        'published' => true,
        'position' => 0,
    ]);

    $post = Post::factory()->create([
        'title' => 'Unique Himalaya Trek Post',
        'slug' => 'himalaya-trek-post',
    ]);

    $layout = Layout::factory()->create([
        'name' => 'Unique Himalaya Layout',
    ]);

    $taxonomy = Taxonomy::create([
        'title' => 'Unique Himalaya Tags',
        'slug' => 'himalaya-tags',
    ]);

    $response = getJson(route('admin.search', ['q' => 'Himalaya']))->assertSuccessful();

    $items = collect($response->json('groups'))->flatMap(fn ($g) => $g['items']);
    $ids = $items->pluck('id')->all();

    expect($ids)->toContain('page-'.$page->id);
    expect($ids)->toContain('post-'.$post->id);
    expect($ids)->toContain('layout-'.$layout->id);
    expect($ids)->toContain('tax-'.$taxonomy->id);

    $pageItem = $items->firstWhere('id', 'page-'.$page->id);
    expect($pageItem['href'])->toBe(route('admin.pages.editor', $page));
    expect($pageItem['subtitle'])->toBe('/himalaya-trek-page');
});

it('returns no dynamic content for empty query even when content exists', function () {
    Page::create([
        'title' => 'About Us',
        'slug' => 'about',
        'published' => true,
        'position' => 0,
    ]);

    $response = getJson(route('admin.search'))->assertSuccessful();

    $groups = collect($response->json('groups'))->pluck('group')->all();
    expect($groups)->not->toContain('Pages');
});

it('returns navigation matches when typing a nav keyword', function () {
    $response = getJson(route('admin.search', ['q' => 'assets']))->assertSuccessful();

    $ids = collect($response->json('groups'))
        ->flatMap(fn ($g) => collect($g['items'])->pluck('id'))
        ->all();

    expect($ids)->toContain('nav-assets');
});

it('caps each group to the per-group limit', function () {
    foreach (range(1, 12) as $i) {
        Page::create([
            'title' => "Travel Guide Part {$i}",
            'slug' => "travel-guide-{$i}",
            'published' => true,
            'position' => $i,
        ]);
    }

    $response = getJson(route('admin.search', ['q' => 'Travel Guide']))->assertSuccessful();

    $pagesGroup = collect($response->json('groups'))->firstWhere('group', 'Pages');
    expect($pagesGroup)->not->toBeNull();
    expect(count($pagesGroup['items']))->toBeLessThanOrEqual(7);
});
