<?php

use App\Models\Admin;
use App\Models\Collection;
use App\Models\Form;
use App\Models\FormEntry;
use Illuminate\Support\ViewErrorBag;
use Plugins\Bookings\Blocks\CheckoutForm\CheckoutForm;
use Plugins\Bookings\Models\Booking;

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

it('submits checkout-form booking and saves to database', function () {
    $response = $this->post(route('bookings.submit'), [
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'customer_phone' => '+15551234567',
        'travel_date' => '2026-10-15',
        'preferred_time' => '14:00',
        'adults' => 3,
        'children' => 1,
        'notes' => 'Vegetarian meal please',
        'tour_title' => 'Paris Special Tour',
        'amount' => 899.00,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $booking = Booking::where('customer_email', 'jane@example.com')->first();
    expect($booking)->not->toBeNull();
    expect($booking->customer_name)->toBe('Jane Doe');
    expect($booking->adults)->toBe(3);
    expect($booking->children)->toBe(1);
    expect($booking->notes)->toBe('Vegetarian meal please');
});

it('shows error alert and disables booking confirm button when no package is selected in checkout-form', function () {
    $view = $this->view((new CheckoutForm)->view(), [
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
