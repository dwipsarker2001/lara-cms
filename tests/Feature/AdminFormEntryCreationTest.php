<?php

use App\Models\Admin;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('can manually create a form entry and it generates a notification', function () {
    $form = Form::factory()->create([
        'title' => 'Admin Bookings',
        'icon' => 'fa-solid fa-calendar',
    ]);

    post(route('admin.forms.entries.store', $form), [
        'data' => [
            'full_name' => 'Dwip Sarker',
            'email' => 'dwip@example.com',
            'phone' => '123456',
        ],
    ])->assertRedirect(route('admin.forms.entries', $form));

    $entry = FormEntry::where('form_id', $form->id)->first();
    expect($entry)->not->toBeNull();
    expect($entry->data['full_name'])->toBe('Dwip Sarker');

    $notification = Notification::where('title', 'New Entry: Admin Bookings')->first();
    expect($notification)->not->toBeNull();
    expect($notification->sub)->toContain('Dwip Sarker');
    expect($notification->sub)->toContain('dwip@example.com');
});

it('can view the manual create form entry page', function () {
    $form = Form::factory()->create([
        'title' => 'Admin Bookings',
        'icon' => 'fa-solid fa-calendar',
    ]);

    $this->get(route('admin.forms.entries.create', $form))
        ->assertStatus(200)
        ->assertSee('Add Admin Bookings');
});
