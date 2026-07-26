<?php

use App\Models\Admin;
use App\Models\Collection;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('renders window.FA_ICONS script on collection create page', function () {
    get(route('admin.collections.create'))
        ->assertStatus(200)
        ->assertSee('window.FA_ICONS =', false);
});

it('creates a collection with a selected icon', function () {
    post(route('admin.collections.store'), [
        'name' => 'Portfolio',
        'icon' => 'fa-solid fa-briefcase',
        'enable_seo' => 1,
    ])->assertRedirect(route('admin.collections.entries.index', Collection::where('name', 'Portfolio')->first()));

    $collection = Collection::where('name', 'Portfolio')->first();
    expect($collection)->not->toBeNull();
    expect($collection->icon)->toBe('fa-solid fa-briefcase');
});
