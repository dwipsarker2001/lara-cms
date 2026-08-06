<?php

use App\Models\Admin;
use App\Models\Collection;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('renders data-collection-id attribute on admin collections index page', function () {
    $col1 = Collection::create(['name' => 'First', 'slug' => 'first', 'position' => 1]);
    $col2 = Collection::create(['name' => 'Second', 'slug' => 'second', 'position' => 2]);

    get(route('admin.collections.index'))
        ->assertStatus(200)
        ->assertSee('data-collection-id="'.$col1->id.'"', false)
        ->assertSee('data-collection-id="'.$col2->id.'"', false);
});

it('saves reordered collection positions to the database', function () {
    $col1 = Collection::create(['name' => 'Alpha', 'slug' => 'alpha', 'position' => 1]);
    $col2 = Collection::create(['name' => 'Beta', 'slug' => 'beta', 'position' => 2]);

    patch(route('admin.collections.reorder'), [
        'collection_ids' => [$col2->id, $col1->id],
    ])->assertStatus(204);

    expect($col2->fresh()->position)->toBe(0);
    expect($col1->fresh()->position)->toBe(1);
});
