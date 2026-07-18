<?php

use App\Models\Admin;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->admin = Admin::factory()->create([
        'name' => 'Dwip Sarker',
        'email' => 'dwip@example.com',
    ]);
    actingAs($this->admin, 'admin');
});

it('greets the authenticated admin by their real name', function () {
    get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Hello, Dwip Sarker', false)
        ->assertDontSee('Samantha Walker', false);
});

it('redirects guests away from the dashboard', function () {
    auth('admin')->logout();

    get(route('admin.dashboard'))
        ->assertRedirect();
});
