<?php

use App\Models\Admin;
use App\Models\Form;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('renders icon selector on form create page', function () {
    \Pest\Laravel\get(route('admin.forms.create'))
        ->assertStatus(200)
        ->assertSee('window.FA_ICONS =', false)
        ->assertSee('Choose icon');
});

it('creates a form with a selected icon', function () {
    post(route('admin.forms.store'), [
        'title' => 'Feedback Form',
        'icon' => 'fa-solid fa-comments',
        'description' => 'Give us feedback',
        'submit_text' => 'Submit',
        'success_message' => 'Thanks!',
    ])->assertRedirect(route('admin.forms.index'));

    $form = Form::where('title', 'Feedback Form')->first();
    expect($form)->not->toBeNull();
    expect($form->icon)->toBe('fa-solid fa-comments');
});

it('updates a form icon', function () {
    $form = Form::factory()->create([
        'title' => 'Contact Us',
        'icon' => 'fa-solid fa-envelope',
    ]);

    put(route('admin.forms.update', $form), [
        'title' => 'Contact Us Updated',
        'icon' => 'fa-solid fa-paper-plane',
        'description' => $form->description,
        'submit_text' => $form->submit_text,
        'success_message' => $form->success_message,
    ])->assertRedirect(route('admin.forms.index'));

    $form->refresh();
    expect($form->icon)->toBe('fa-solid fa-paper-plane');
});
