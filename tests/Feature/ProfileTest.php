<?php

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => 'password',
        'avatar' => null,
    ]);
    actingAs($this->admin, 'admin');
});

it('shows the manage profile page for authenticated users', function () {
    get(route('admin.profile.edit'))
        ->assertSuccessful()
        ->assertSee('Manage Profile')
        ->assertSee('Name')
        ->assertSee('Email Address')
        ->assertSee('Avatar')
        ->assertSee('Change Password')
        ->assertSee('Save')
        ->assertSee('admin@example.com', false);
});

it('redirects guests away from the profile page', function () {
    auth('admin')->logout();

    get(route('admin.profile.edit'))
        ->assertRedirect();
});

it('updates name email and avatar', function () {
    put(route('admin.profile.update'), [
        'name' => 'New Name',
        'email' => 'new@example.com',
        'avatar' => '/storage/avatars/me.png',
    ])
        ->assertRedirect()
        ->assertSessionHas('success', 'Saved');

    $this->admin->refresh();

    expect($this->admin->name)->toBe('New Name')
        ->and($this->admin->email)->toBe('new@example.com')
        ->and($this->admin->avatar)->toBe('/storage/avatars/me.png');
});

it('can clear the avatar', function () {
    $this->admin->update(['avatar' => '/storage/avatars/old.png']);

    put(route('admin.profile.update'), [
        'name' => $this->admin->name,
        'email' => $this->admin->email,
        'avatar' => '',
    ])->assertRedirect();

    expect($this->admin->fresh()->avatar)->toBeNull();
});

it('updates password when provided and confirmed', function () {
    put(route('admin.profile.update'), [
        'name' => $this->admin->name,
        'email' => $this->admin->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])
        ->assertRedirect()
        ->assertSessionHas('success', 'Saved');

    expect(Hash::check('new-password', $this->admin->fresh()->password))->toBeTrue();
});

it('rejects mismatched password confirmation', function () {
    put(route('admin.profile.update'), [
        'name' => $this->admin->name,
        'email' => $this->admin->email,
        'password' => 'new-password',
        'password_confirmation' => 'different-password',
    ])->assertSessionHasErrors('password');

    expect(Hash::check('password', $this->admin->fresh()->password))->toBeTrue();
});

it('requires a unique email', function () {
    Admin::factory()->create(['email' => 'taken@example.com']);

    put(route('admin.profile.update'), [
        'name' => $this->admin->name,
        'email' => 'taken@example.com',
    ])->assertSessionHasErrors('email');
});

it('requires name and email', function () {
    put(route('admin.profile.update'), [
        'name' => '',
        'email' => '',
    ])->assertSessionHasErrors(['name', 'email']);
});
