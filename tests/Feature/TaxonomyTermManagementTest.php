<?php

use App\Models\Admin;
use App\Models\Taxonomy;
use App\Models\Term;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('can view taxonomies index page', function () {
    $taxonomy = Taxonomy::create(['title' => 'Categories', 'slug' => 'categories']);
    Term::create(['taxonomy_id' => $taxonomy->id, 'title' => 'News', 'slug' => 'news']);

    $response = get(route('admin.taxonomies.index'));
    $response->assertStatus(200);
    $response->assertSee('Taxonomies');
    $response->assertSee('Categories');
    $response->assertSee('News');
});

it('can view taxonomies create page', function () {
    $response = get(route('admin.taxonomies.create'));
    $response->assertStatus(200);
});

it('can create a new taxonomy group', function () {
    $response = post(route('admin.taxonomies.store'), [
        'title' => 'Product Brands',
        'slug' => 'product-brands',
        'description' => 'Brand list for catalog',
    ]);

    $taxonomy = Taxonomy::where('slug', 'product-brands')->first();
    expect($taxonomy)->not->toBeNull();
    expect($taxonomy->title)->toBe('Product Brands');

    $response->assertRedirect(route('admin.taxonomies.show', $taxonomy));
});

it('automatically generates a slug if omitted when creating a taxonomy group', function () {
    $response = post(route('admin.taxonomies.store'), [
        'title' => 'Travel Destinations',
    ]);

    $taxonomy = Taxonomy::where('title', 'Travel Destinations')->first();
    expect($taxonomy)->not->toBeNull();
    expect($taxonomy->slug)->toBe('travel-destinations');

    $response->assertRedirect(route('admin.taxonomies.show', $taxonomy));
});

it('can view taxonomy detail page', function () {
    $taxonomy = Taxonomy::create(['title' => 'Tags', 'slug' => 'tags']);
    $term = Term::create(['taxonomy_id' => $taxonomy->id, 'title' => 'Trending', 'slug' => 'trending']);

    $response = get(route('admin.taxonomies.show', $taxonomy));
    $response->assertStatus(200);
    $response->assertSee('Tags');
    $response->assertSee('Trending');
});

it('can create terms inside a taxonomy', function () {
    $taxonomy = Taxonomy::create(['title' => 'Genres', 'slug' => 'genres']);

    $response = post(route('admin.taxonomies.terms.store', $taxonomy), [
        'title' => 'Sci-Fi',
        'slug' => 'sci-fi',
    ]);

    $term = Term::where('taxonomy_id', $taxonomy->id)->where('slug', 'sci-fi')->first();
    expect($term)->not->toBeNull();
    expect($term->title)->toBe('Sci-Fi');

    $response->assertRedirect(route('admin.taxonomies.show', $taxonomy));
});

it('can update terms inside a taxonomy', function () {
    $taxonomy = Taxonomy::create(['title' => 'Regions', 'slug' => 'regions']);
    $term = Term::create(['taxonomy_id' => $taxonomy->id, 'title' => 'Asia', 'slug' => 'asia']);

    $response = put(route('admin.taxonomies.terms.update', [$taxonomy, $term]), [
        'title' => 'Asia Pacific',
        'slug' => 'asia-pacific',
    ]);

    expect($term->fresh()->title)->toBe('Asia Pacific');
    expect($term->fresh()->slug)->toBe('asia-pacific');
    $response->assertRedirect(route('admin.taxonomies.show', $taxonomy));
});

it('can delete terms inside a taxonomy', function () {
    $taxonomy = Taxonomy::create(['title' => 'Authors', 'slug' => 'authors']);
    $term = Term::create(['taxonomy_id' => $taxonomy->id, 'title' => 'John Doe', 'slug' => 'john-doe']);

    $response = delete(route('admin.taxonomies.terms.destroy', [$taxonomy, $term]));

    expect(Term::find($term->id))->toBeNull();
    $response->assertRedirect(route('admin.taxonomies.show', $taxonomy));
});

it('can reorder terms inside a taxonomy', function () {
    $taxonomy = Taxonomy::create(['title' => 'Priorities', 'slug' => 'priorities']);
    $t1 = Term::create(['taxonomy_id' => $taxonomy->id, 'title' => 'Low', 'position' => 0]);
    $t2 = Term::create(['taxonomy_id' => $taxonomy->id, 'title' => 'High', 'position' => 1]);

    $response = patch(route('admin.taxonomies.terms.reorder', $taxonomy), [
        'term_ids' => [$t2->id, $t1->id],
    ]);

    $response->assertJson(['success' => true]);
    expect($t2->fresh()->position)->toBe(0);
    expect($t1->fresh()->position)->toBe(1);
});

it('displays dynamic taxonomies list in sidebar menu', function () {
    Taxonomy::create(['title' => 'Custom Categories', 'slug' => 'custom-categories']);
    Taxonomy::create(['title' => 'Custom Tags', 'slug' => 'custom-tags']);

    $response = get(route('admin.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Taxonomies');
    $response->assertSee('Custom Categories');
    $response->assertSee('Custom Tags');
});
