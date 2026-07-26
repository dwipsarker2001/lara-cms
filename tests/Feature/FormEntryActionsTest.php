<?php

use App\Models\Admin;
use App\Models\Form;
use App\Models\FormEntry;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('allows editing a form submission entry', function () {
    $form = Form::factory()->create([
        'fields' => [
            ['_key' => 'k1', 'type' => 'text', 'label' => 'Name', 'name' => 'name'],
            ['_key' => 'k2', 'type' => 'email', 'label' => 'Email', 'name' => 'email'],
        ],
    ]);

    $entry = FormEntry::create([
        'form_id' => $form->id,
        'data' => ['name' => 'John Doe', 'email' => 'john@example.com'],
    ]);

    put(route('admin.forms.entries.update', [$form, $entry]), [
        'data' => [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ],
    ])->assertRedirect(route('admin.forms.entries', $form));

    $entry->refresh();
    expect($entry->data['name'])->toBe('Jane Doe');
    expect($entry->data['email'])->toBe('jane@example.com');
});

it('allows deleting a form submission entry', function () {
    $form = Form::factory()->create();
    $entry = FormEntry::create([
        'form_id' => $form->id,
        'data' => ['message' => 'Test submission'],
    ]);

    delete(route('admin.forms.entries.destroy', [$form, $entry]))
        ->assertRedirect(route('admin.forms.entries', $form));

    expect(FormEntry::find($entry->id))->toBeNull();
});
