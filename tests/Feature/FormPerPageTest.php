<?php

use App\Models\Admin;
use App\Models\Form;
use App\Models\FormEntry;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('can update total row per page setting for a form', function () {
    $form = Form::factory()->create([
        'title' => 'Contact Us',
        'submit_text' => 'Submit',
        'success_message' => 'Thank you!',
        'per_page' => 15,
    ]);

    put(route('admin.forms.update', $form), [
        'title' => 'Contact Us Updated',
        'submit_text' => 'Submit',
        'success_message' => 'Thank you!',
        'per_page' => 25,
    ])->assertRedirect(route('admin.forms.index'));

    expect($form->refresh()->per_page)->toBe(25);
});

it('uses custom per_page setting on entries pagination', function () {
    $form = Form::factory()->create([
        'title' => 'Contact Us',
        'submit_text' => 'Submit',
        'success_message' => 'Thank you!',
        'per_page' => 5,
    ]);

    FormEntry::factory()->count(10)->create(['form_id' => $form->id]);

    $response = get(route('admin.forms.entries', $form))->assertOk();

    $entries = $response->viewData('entries');
    expect($entries->perPage())->toBe(5);
    expect($entries->count())->toBe(5);
});
