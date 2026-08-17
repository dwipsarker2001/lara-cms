<?php

use App\Blocks\Field;
use App\Models\Admin;
use App\Models\Collection;
use App\Models\Form;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('creates a form field definition via Field::form() and Field::formField()', function () {
    $field = Field::form('mapFullName', 'Full Name Form Key', formFieldKey: 'formId', default: 'full_name');

    expect($field)->toBeArray();
    expect($field['type'])->toBe('form');
    expect($field['name'])->toBe('mapFullName');
    expect($field['label'])->toBe('Full Name Form Key');
    expect($field['formFieldKey'])->toBe('formId');
    expect($field['defaultValue'])->toBe('full_name');

    $aliasField = Field::formField('mapEmail', 'Email Form Key', formFieldKey: 'formId', default: 'email');
    expect($aliasField['type'])->toBe('form');
    expect($aliasField['name'])->toBe('mapEmail');
    expect($aliasField['formFieldKey'])->toBe('formId');
});

it('passes available forms to entry editor and renders form key picker template', function () {
    $form = Form::factory()->create([
        'title' => 'Booking Form',
        'fields' => [
            ['_key' => '1', 'type' => 'text', 'label' => 'Full Name', 'name' => 'customer_name'],
            ['_key' => '2', 'type' => 'email', 'label' => 'Customer Email', 'name' => 'customer_email'],
        ],
    ]);

    $collection = Collection::create(['name' => 'Pages', 'slug' => 'pages']);
    $entry = $collection->entries()->create([
        'slug' => 'checkout',
        'data' => ['title' => 'Checkout Page'],
        'sections' => [],
    ]);

    $response = get(route('admin.collections.entries.editor', [$collection, $entry]));
    $response->assertSuccessful();
    $response->assertSee('window.editorForms', false);
    $response->assertSee('Booking Form', false);
    $response->assertSee('customer_name', false);
    $response->assertSee('field.type === \'form\'', false);
});
