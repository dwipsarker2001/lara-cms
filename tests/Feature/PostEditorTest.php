<?php

use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('loads the editor page', function () {
    $post = Post::factory()->create();

    get(route('admin.posts.editor', $post))
        ->assertSuccessful()
        ->assertSee($post->title)
        ->assertSee('Components')
        ->assertSee('Sections');
});

it('updates post sections', function () {
    $post = Post::factory()->create();

    $sections = [
        ['_key' => 'key-1', 'name' => 'PageBanner', 'data' => ['title' => 'Hello', 'subtitle' => 'World'], 'enabled' => true],
        ['_key' => 'key-2', 'name' => 'HeroBanner', 'data' => ['headline' => 'Welcome'], 'enabled' => true],
    ];

    patch(route('admin.posts.update-sections', $post), [
        'sections' => $sections,
    ])->assertSuccessful()->assertJson(['message' => 'Sections saved.']);

    $post->refresh();
    expect($post->sections)->toBe($sections);
});

it('validates sections structure', function () {
    $post = Post::factory()->create();

    patch(route('admin.posts.update-sections', $post), [
        'sections' => 'not-an-array',
    ])->assertSessionHasErrors('sections');
});

it('casts sections as array', function () {
    $post = Post::factory()->create(['sections' => [
        ['_key' => 'k1', 'name' => 'Test', 'data' => ['foo' => 'bar'], 'enabled' => true],
    ]]);

    expect($post->sections)->toBeArray();
    expect($post->sections[0]['name'])->toBe('Test');
    expect($post->sections[0]['data']['foo'])->toBe('bar');
});

it('rejects unauthenticated requests', function () {
    $post = Post::factory()->create();
    $this->app['auth']->logout();

    $response = $this->get(route('admin.posts.editor', $post));

    $response->assertRedirect();
});
