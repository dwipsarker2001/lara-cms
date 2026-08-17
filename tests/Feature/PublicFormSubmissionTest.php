<?php

use App\Models\Admin;
use App\Models\Collection;
use App\Models\Form;
use App\Models\FormEntry;
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
    $admin = Admin::factory()->create();

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

it('renders mapped form fields from selected form in checkout-form and submits correctly', function () {
    $form = Form::factory()->create([
        'title' => 'Custom Tour Form',
        'submit_text' => 'Book Tour Now',
        'fields' => [
            ['_key' => '1', 'type' => 'text', 'label' => 'Full Name', 'name' => 'customer_name', 'required' => true],
            ['_key' => '2', 'type' => 'email', 'label' => 'Email', 'name' => 'customer_email', 'required' => true],
            ['_key' => '3', 'type' => 'text', 'label' => 'Custom Notes', 'name' => 'custom_notes', 'required' => false],
            ['_key' => '4', 'type' => 'number', 'label' => 'Adult Guests', 'name' => 'adults_count', 'required' => false],
        ],
    ]);

    $view = $this->view('blocks.custom.checkout-form', [
        'data' => [
            'formId' => $form->id,
            'formTitle' => 'Traveler Details',
            'mapFullName' => 'customer_name',
            'mapEmail' => 'customer_email',
            'mapMessage' => 'custom_notes',
            'mapAdults' => 'adults_count',
        ],
        'errors' => new ViewErrorBag,
    ]);

    $view->assertSee('name="customer_name"', false);
    $view->assertSee('name="customer_email"', false);
    $view->assertSee('name="custom_notes"', false);
    $view->assertSee('name="adults_count"', false);
    $view->assertSee(route('forms.public-submit', $form));
    $view->assertSee('Book Tour Now');

    // Test successful submission
    $successResponse = $this->post(route('forms.public-submit', $form), [
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'custom_notes' => 'Vegetarian meal please',
        'adults_count' => 3,
    ]);

    $successResponse->assertRedirect();
    $entry = FormEntry::where('form_id', $form->id)->first();
    expect($entry)->not->toBeNull();
    expect($entry->data['customer_name'])->toBe('Jane Doe');
    expect($entry->data['customer_email'])->toBe('jane@example.com');
    expect($entry->data['custom_notes'])->toBe('Vegetarian meal please');
    expect($entry->data['adults_count'])->toBe(3);
});

it('shows error alert and disables booking confirm button when no package is selected in checkout-form', function () {
    $view = $this->view('blocks.custom.checkout-form', [
        'data' => [
            'formTitle' => 'Traveler Details',
            'buttonText' => 'Confirm Booking',
        ],
        'errors' => new ViewErrorBag,
    ]);

    $view->assertSee('No Package Selected');
    $view->assertSee('Please select a tour package first to see pricing and confirm your booking.');
    $view->assertSee('Browse Packages');
    $view->assertSee('disabled', false);
    $view->assertSee('Confirm Booking');
});

it('renders error alert and disables booking on public checkout page when no package_id query is passed', function () {
    $pages = Collection::create(['name' => 'Pages', 'slug' => 'pages']);
    $checkoutPage = $pages->entries()->create([
        'slug' => 'checkout',
        'published' => true,
        'data' => ['title' => 'Checkout'],
        'sections' => [
            [
                '_key' => 's1',
                'name' => 'checkoutForm',
                'data' => [
                    'buttonText' => 'Confirm Booking',
                ],
            ],
        ],
    ]);

    $response = $this->get('/checkout');
    $response->assertStatus(200);
    $response->assertSee('No Package Selected');
    $response->assertSee('Please select a tour package first to see pricing and confirm your booking.');
    $response->assertSee('disabled', false);
});
