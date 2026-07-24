<?php

use App\Models\Admin;
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
    expect($ids)->toContain('nav-taxonomies');
    // Empty query is a launcher: only Navigation, capped per group.
    expect(collect($groups)->pluck('group')->all())->toBe(['Navigation']);
});

it('finds create actions when searching', function () {
    $response = getJson(route('admin.search', ['q' => 'new taxonomy']))->assertSuccessful();

    $ids = collect($response->json('groups'))
        ->flatMap(fn ($g) => collect($g['items'])->pluck('id'))
        ->all();

    expect($ids)->toContain('act-new-taxonomy');
});

it('searches live taxonomies', function () {
    $taxonomy = Taxonomy::create([
        'title' => 'Unique Himalaya Tags',
        'slug' => 'himalaya-tags',
    ]);

    $response = getJson(route('admin.search', ['q' => 'Himalaya']))->assertSuccessful();

    $items = collect($response->json('groups'))->flatMap(fn ($g) => $g['items']);
    $ids = $items->pluck('id')->all();

    expect($ids)->toContain('tax-'.$taxonomy->id);
});

it('returns no dynamic content for empty query even when content exists', function () {
    Taxonomy::create([
        'title' => 'About Us Tags',
        'slug' => 'about-us-tags',
    ]);

    $response = getJson(route('admin.search'))->assertSuccessful();

    $groups = collect($response->json('groups'))->pluck('group')->all();
    expect($groups)->not->toContain('Categories');
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
        Taxonomy::create([
            'title' => "Travel Guide Part {$i}",
            'slug' => "travel-guide-part-{$i}",
        ]);
    }

    $response = getJson(route('admin.search', ['q' => 'Travel Guide']))->assertSuccessful();

    $taxGroup = collect($response->json('groups'))->firstWhere('group', 'Categories');
    expect($taxGroup)->not->toBeNull();
    expect(count($taxGroup['items']))->toBeLessThanOrEqual(7);
});
