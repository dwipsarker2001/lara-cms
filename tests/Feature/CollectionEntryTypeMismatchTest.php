<?php

use App\Models\Admin;
use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\Term;

test('collection_id cast to integer on collection entry model', function () {
    $entry = new CollectionEntry(['collection_id' => '42']);
    expect($entry->collection_id)->toBeInt()->toBe(42);
});

test('form_id cast to integer on form entry model', function () {
    $entry = new FormEntry(['form_id' => '108']);
    expect($entry->form_id)->toBeInt()->toBe(108);
});

test('taxonomy_id cast to integer on term model', function () {
    $term = new Term(['taxonomy_id' => '99']);
    expect($term->taxonomy_id)->toBeInt()->toBe(99);
});

test('collection entry controller actions allow access when collection_id matches as string or int', function () {
    $admin = Admin::factory()->create();

    $collection = Collection::create([
        'name' => 'Products',
        'slug' => 'products',
    ]);

    $entry = CollectionEntry::create([
        'collection_id' => (string) $collection->id,
        'slug' => 'product-1',
        'data' => ['title' => 'Product 1'],
        'published' => true,
    ]);

    // Test edit action
    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.collections.entries.edit', [$collection, $entry]));
    $response->assertStatus(200);

    // Test editor action
    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.collections.entries.editor', [$collection, $entry]));
    $response->assertStatus(200);

    // Test update action
    $response = $this->actingAs($admin, 'admin')
        ->put(route('admin.collections.entries.update', [$collection, $entry]), [
            'slug' => 'product-1-updated',
            'data' => ['title' => 'Product 1 Updated'],
            'published' => true,
        ]);
    $response->assertRedirect(route('admin.collections.entries.index', $collection));

    // Test updateSections action
    $response = $this->actingAs($admin, 'admin')
        ->patchJson(route('admin.collections.entries.update-sections', [$collection, $entry]), [
            'sections' => [],
        ]);
    $response->assertStatus(200);

    // Test destroy action
    $response = $this->actingAs($admin, 'admin')
        ->delete(route('admin.collections.entries.destroy', [$collection, $entry]));
    $response->assertRedirect(route('admin.collections.entries.index', $collection));
});

test('collection entry controller aborts 404 when collection_id does not match collection id', function () {
    $admin = Admin::factory()->create();

    $collection1 = Collection::create(['name' => 'Col 1', 'slug' => 'col-1']);
    $collection2 = Collection::create(['name' => 'Col 2', 'slug' => 'col-2']);

    $entry = CollectionEntry::create([
        'collection_id' => $collection1->id,
        'slug' => 'entry-1',
        'data' => ['title' => 'Entry 1'],
        'published' => true,
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.collections.entries.edit', [$collection2, $entry]))
        ->assertStatus(404);
});

test('form controller actions allow access when form_id matches as string or int', function () {
    $admin = Admin::factory()->create();

    $form = Form::create([
        'title' => 'Contact Form',
    ]);

    $entry = FormEntry::create([
        'form_id' => (string) $form->id,
        'data' => ['name' => 'Jane Doe'],
        'ip_address' => '127.0.0.1',
        'status' => 0,
    ]);

    // Test entryJson
    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.forms.entries.json', [$form, $entry]));
    $response->assertStatus(200);

    // Test updateEntry
    $response = $this->actingAs($admin, 'admin')
        ->put(route('admin.forms.entries.update', [$form, $entry]), [
            'data' => ['name' => 'Jane Doe Updated'],
        ]);
    $response->assertRedirect(route('admin.forms.entries', $form));

    // Test destroyEntry
    $response = $this->actingAs($admin, 'admin')
        ->delete(route('admin.forms.entries.destroy', [$form, $entry]));
    $response->assertRedirect(route('admin.forms.entries', $form));
});
