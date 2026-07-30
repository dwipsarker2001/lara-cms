<?php

use App\Models\Admin;
use App\Models\Form;
use App\Models\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('automatically creates a notification on form entry submission', function () {
    $form = Form::factory()->create([
        'title' => 'Contact Inquiry',
        'icon' => 'fa-solid fa-comments',
    ]);

    post(route('forms.public-submit', $form), [
        'full_name' => 'Dwip Sarker',
        'email' => 'dwip@example.com',
        'message' => 'Hello there',
    ]);

    $notification = Notification::where('title', 'New Entry: Contact Inquiry')->first();
    expect($notification)->not->toBeNull();
    expect($notification->sub)->toContain('Dwip Sarker');
    expect($notification->sub)->toContain('dwip@example.com');
});

it('renders dashboard with dynamic notifications database content', function () {
    Notification::create([
        'title' => 'Custom SLA Alert',
        'sub' => 'Severity 1 issue flagged',
        'icon' => 'triangle-exclamation',
        'tone' => 'text-red-500',
    ]);

    get(route('admin.dashboard'))
        ->assertStatus(200)
        ->assertSee('Custom SLA Alert')
        ->assertSee('Severity 1 issue flagged');
});
