<?php

use App\Models\Layout;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('lists layouts', function () {
    $layouts = Layout::factory()->count(3)->create();

    get(route('admin.layouts.index'))
        ->assertSuccessful()
        ->assertSee($layouts[0]->name);
});

it('creates a layout', function () {
    post(route('admin.layouts.store'), [
        'name' => 'My Layout',
        'collection' => 'page',
    ])->assertRedirect(route('admin.layouts.index'));

    expect(Layout::where('name', 'My Layout')->exists())->toBeTrue();
});

it('validates layout creation', function () {
    post(route('admin.layouts.store'), [
        'name' => '',
        'collection' => '',
    ])->assertSessionHasErrors(['name', 'collection']);
});

it('validates collection must be a valid type', function () {
    post(route('admin.layouts.store'), [
        'name' => 'Test',
        'collection' => 'invalid',
    ])->assertSessionHasErrors(['collection']);

    post(route('admin.layouts.store'), [
        'name' => 'Test',
        'collection' => 'page',
    ])->assertValid('collection');

    post(route('admin.layouts.store'), [
        'name' => 'Test',
        'collection' => 'blog',
    ])->assertValid('collection');

    post(route('admin.layouts.store'), [
        'name' => 'Test',
        'collection' => 'package',
    ])->assertValid('collection');
});

it('updates a layout', function () {
    $layout = Layout::factory()->create();

    patch(route('admin.layouts.update', $layout), [
        'name' => 'Updated Name',
        'collection' => 'blog',
    ])->assertRedirect(route('admin.layouts.index'));

    expect($layout->fresh()->name)->toBe('Updated Name');
    expect($layout->fresh()->collection)->toBe('blog');
});

it('deletes a layout', function () {
    $layout = Layout::factory()->create();

    delete(route('admin.layouts.destroy', $layout))
        ->assertRedirect(route('admin.layouts.index'));

    expect(Layout::find($layout->id))->toBeNull();
});

it('loads the layout editor', function () {
    $layout = Layout::factory()->create();

    get(route('admin.layouts.editor', $layout))
        ->assertSuccessful()
        ->assertSee('Sections');
});

it('updates layout sections', function () {
    $layout = Layout::factory()->create();

    $sections = [
        ['_key' => 'key-1', 'name' => 'PageBanner', 'data' => ['title' => 'Hello'], 'enabled' => true],
    ];

    patch(route('admin.layouts.update-sections', $layout), [
        'sections' => $sections,
    ])->assertSuccessful()->assertJson(['message' => 'Sections saved.']);

    expect($layout->fresh()->sections)->toBe($sections);
});
