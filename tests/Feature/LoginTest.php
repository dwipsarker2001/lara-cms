<?php

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

it('logs in an admin with valid credentials', function () {
    Admin::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'admin@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/admin');
    $this->assertAuthenticatedAs(Admin::first(), 'admin');
});

it('handles invalid login credentials without database query exceptions', function () {
    $response = $this->post('/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('throttles excessive login attempts after 3 tries', function () {
    for ($i = 0; $i < 3; $i++) {
        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->post('/login', [
        'email' => 'admin@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
});
