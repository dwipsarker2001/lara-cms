<?php

use App\Models\Form;
use App\Models\FormEntry;
use App\Models\User;
use Illuminate\Support\ViewErrorBag;

it('submits a public form entry and saves to database', function () {
    $form = Form::factory()->create([
        'title' => 'Custom Booking Form',
        'submit_text' => 'Book Now',
        'success_message' => 'Your booking has been received!',
        'fields' => [
            ['_key' => '1', 'type' => 'text', 'label' => 'Full Name', 'name' => 'full_name', 'required' => true],
            ['_key' => '2', 'type' => 'email', 'label' => 'Email', 'name' => 'email', 'required' => true],
        ],
    ]);

    $response = $this->post(route('forms.public-submit', $form), [
        'full_name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Your booking has been received!');

    expect(FormEntry::where('form_id', $form->id)->count())->toBe(1);

    $entry = FormEntry::where('form_id', $form->id)->first();
    expect($entry->data['full_name'])->toBe('Jane Doe');
    expect($entry->data['email'])->toBe('jane@example.com');
});

it('submits standard built-in checkout form and shows submission in admin entries table', function () {
    $admin = User::factory()->create();

    $response = $this->post(route('forms.public-submit-default'), [
        'full_name' => 'Alex Smith',
        'email' => 'alex@example.com',
        'phone' => '+15550001111',
        'travel_date' => '2026-09-01',
        'preferred_time' => '10:00',
        'adults' => 2,
        'children' => 1,
        'additional_message' => 'Window seat please',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $form1 = Form::find(1);
    expect($form1)->not->toBeNull();

    $entry = FormEntry::where('form_id', 1)->first();
    expect($entry)->not->toBeNull();
    expect($entry->data['full_name'])->toBe('Alex Smith');
    expect($entry->data['email'])->toBe('alex@example.com');

    $adminResponse = $this->actingAs($admin, 'admin')->get(route('admin.forms.entries', $form1));
    $adminResponse->assertStatus(200);
    $adminResponse->assertSee('Alex Smith');
    $adminResponse->assertSee('alex@example.com');
    $adminResponse->assertSee('Window seat please');
});

it('renders dynamic form fields from selected form in checkout-form and submits correctly with custom error message', function () {
    $form = Form::factory()->create([
        'title' => 'Custom Tour Form',
        'submit_text' => 'Book Tour Now',
        'fields' => [
            ['_key' => '1', 'type' => 'text', 'label' => 'Full Name', 'name' => 'full_name', 'error_message' => 'Name is required', 'required' => true],
            ['_key' => '2', 'type' => 'email', 'label' => 'Email', 'name' => 'email', 'error_message' => 'Email is required', 'required' => true],
            ['_key' => '3', 'type' => 'text', 'label' => 'Custom Notes', 'name' => 'custom_notes', 'required' => false],
        ],
    ]);

    $view = $this->view('blocks.checkout-form', [
        'data' => [
            'formId' => $form->id,
            'formTitle' => 'Traveler Details',
        ],
        'errors' => new ViewErrorBag,
    ]);

    $view->assertSee('name="full_name"', false);
    $view->assertSee('name="email"', false);
    $view->assertSee('name="custom_notes"', false);
    $view->assertSee('name="adults"', false);
    $view->assertSee('name="children"', false);
    $view->assertSee(route('forms.public-submit', $form));
    $view->assertSee('Book Tour Now');

    // Test submission validation with custom error message
    $failResponse = $this->post(route('forms.public-submit', $form), []);
    $failResponse->assertSessionHasErrors([
        'full_name' => 'Name is required',
        'email' => 'Email is required',
    ]);

    // Test successful submission
    $successResponse = $this->post(route('forms.public-submit', $form), [
        'full_name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'custom_notes' => 'Vegetarian meal please',
    ]);

    $successResponse->assertRedirect();
    $entry = FormEntry::where('form_id', $form->id)->first();
    expect($entry)->not->toBeNull();
    expect($entry->data['full_name'])->toBe('Jane Doe');
    expect($entry->data['email'])->toBe('jane@example.com');
    expect($entry->data['custom_notes'])->toBe('Vegetarian meal please');
});
