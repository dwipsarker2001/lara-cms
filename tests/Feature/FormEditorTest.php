<?php

use App\Models\Admin;
use App\Models\Form;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('loads the form editor page', function () {
    $form = Form::factory()->create(['title' => 'Contact Form']);

    get(route('admin.forms.editor', $form))
        ->assertSuccessful()
        ->assertSee('Form Editor')
        ->assertSee('Build Form')
        ->assertSee('Build your form structure here and then generate ui for that form.')
        ->assertSee('No fields yet')
        ->assertSee('open-field-picker', false)
        ->assertSee('fieldPicker', false)
        ->assertSee('Text')
        ->assertSee('Email');
});

it('renders existing fields on the editor page', function () {
    $form = Form::factory()->create([
        'fields' => [
            [
                '_key' => 'key-1',
                'type' => 'text',
                'label' => 'Full Name',
                'name' => 'full_name',
                'placeholder' => '',
                'required' => false,
            ],
        ],
    ]);

    get(route('admin.forms.editor', $form))
        ->assertSuccessful()
        ->assertSee('Full Name', false)
        ->assertSee('key-1', false);
});

it('updates form fields', function () {
    $form = Form::factory()->create();

    $fields = [
        [
            '_key' => 'key-1',
            'type' => 'email',
            'label' => 'Email',
            'name' => 'email',
            'placeholder' => 'you@example.com',
            'required' => true,
        ],
        [
            '_key' => 'key-2',
            'type' => 'textarea',
            'label' => 'Message',
            'name' => 'message',
            'placeholder' => 'Write something…',
            'required' => false,
        ],
    ];

    patch(route('admin.forms.update-fields', $form), [
        'fields' => $fields,
    ])->assertSuccessful()->assertJson(['message' => 'Form fields saved.']);

    $form->refresh();
    expect($form->fields)->toBe($fields);
});

it('validates fields structure', function () {
    $form = Form::factory()->create();

    patch(route('admin.forms.update-fields', $form), [
        'fields' => 'not-an-array',
    ])->assertSessionHasErrors('fields');
});

it('casts fields as array', function () {
    $form = Form::factory()->create([
        'fields' => [
            ['_key' => 'k1', 'type' => 'text', 'label' => 'Name', 'name' => 'name'],
        ],
    ]);

    expect($form->fields)->toBeArray();
    expect($form->fields[0]['type'])->toBe('text');
    expect($form->fields[0]['label'])->toBe('Name');
});

it('rejects unauthenticated requests', function () {
    $form = Form::factory()->create();
    $this->app['auth']->logout();

    get(route('admin.forms.editor', $form))->assertRedirect();
});
