<?php

use App\Models\Admin;
use App\Models\Post;
use App\Models\Taxonomy;
use App\Models\Term;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('updates a post without writing a non-existent term_ids column', function () {
    $post = Post::factory()->create();
    $taxonomy = Taxonomy::create(['title' => 'Category', 'slug' => 'category']);
    $term = Term::create(['title' => 'India', 'slug' => 'india', 'taxonomy_id' => $taxonomy->id]);

    patch(route('admin.posts.update', $post), [
        'title' => 'Updated Title',
        'slug' => $post->slug,
        'tags' => 'Another, India',
        'term_ids' => (string) $term->id,
        'published' => true,
    ])->assertRedirect();

    $post->refresh();
    expect($post->title)->toBe('Updated Title');
    expect($post->tags)->toBe(['Another', 'India']);
    expect($post->terms->pluck('id')->toArray())->toBe([$term->id]);
});

it('creates a post without writing a non-existent term_ids column', function () {
    $taxonomy = Taxonomy::create(['title' => 'Category', 'slug' => 'category']);
    $term = Term::create(['title' => 'India', 'slug' => 'india', 'taxonomy_id' => $taxonomy->id]);

    post(route('admin.posts.store'), [
        'title' => 'New Post',
        'slug' => 'new-post',
        'tags' => 'Foo, Bar',
        'term_ids' => (string) $term->id,
        'published' => true,
    ])->assertRedirect();

    $post = Post::where('slug', 'new-post')->firstOrFail();
    expect($post->tags)->toBe(['Foo', 'Bar']);
    expect($post->terms->pluck('id')->toArray())->toBe([$term->id]);
});
