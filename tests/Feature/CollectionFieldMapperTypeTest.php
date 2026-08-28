<?php

use App\Blocks\Field;
use App\Models\Admin;
use App\Models\Collection;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('creates a collection field definition via Field::collectionField() and Field::collectionKey()', function () {
    $field = Field::collectionField('map_title', 'Package Title', collectionFieldKey: 'collection', default: 'title');

    expect($field)->toBeArray();
    expect($field['type'])->toBe('collectionField');
    expect($field['name'])->toBe('map_title');
    expect($field['label'])->toBe('Package Title');
    expect($field['collectionFieldKey'])->toBe('collection');
    expect($field['defaultValue'])->toBe('title');

    $aliasField = Field::collectionKey('map_price', 'Price', collectionFieldKey: 'collection', default: 'price');
    expect($aliasField['type'])->toBe('collectionField');
    expect($aliasField['name'])->toBe('map_price');
    expect($aliasField['collectionFieldKey'])->toBe('collection');
});

it('renders collection field mapping template in entry editor', function () {
    $collection = Collection::create(['name' => 'Pages', 'slug' => 'pages']);
    $entry = $collection->entries()->create([
        'slug' => 'checkout',
        'data' => ['title' => 'Checkout Page'],
        'sections' => [],
    ]);

    $response = get(route('admin.collections.entries.editor', [$collection, $entry]));
    $response->assertSuccessful();
    $response->assertSee('window.editorAllCollections', false);
    $response->assertSee('field.type === \'collectionField\'', false);
});
