<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create([
        'name' => 'Primary Admin',
        'email' => 'primary@example.com',
        'password' => 'password',
    ]);
    actingAs($this->admin, 'admin');
});

it('lists normal users', function () {
    $user = User::factory()->create(['name' => 'Normal User', 'email' => 'normal@example.com']);

    get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Users')
        ->assertSee('Normal User')
        ->assertSee('normal@example.com');
});

it('shows the create user form', function () {
    get(route('admin.users.create'))
        ->assertSuccessful()
        ->assertSee('Add User')
        ->assertSee('Create User');
});

it('stores a new normal user', function () {
    post(route('admin.users.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'avatar' => 'https://example.com/avatar.png',
    ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User created.');

    $user = User::query()->where('email', 'john@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('John Doe')
        ->and($user->avatar)->toBe('https://example.com/avatar.png')
        ->and(Hash::check('password123', $user->password))->toBeTrue();
});

it('shows the edit user form', function () {
    $user = User::factory()->create(['name' => 'Editable User']);

    get(route('admin.users.edit', $user))
        ->assertSuccessful()
        ->assertSee('Edit User')
        ->assertSee('Editable User');
});

it('updates a normal user', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'olduser@example.com',
    ]);

    put(route('admin.users.update', $user), [
        'name' => 'Updated User Name',
        'email' => 'updateduser@example.com',
        'avatar' => 'https://example.com/newavatar.png',
        'password' => 'newpassword123',
    ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User updated.');

    $user->refresh();

    expect($user->name)->toBe('Updated User Name')
        ->and($user->email)->toBe('updateduser@example.com')
        ->and($user->avatar)->toBe('https://example.com/newavatar.png')
        ->and(Hash::check('newpassword123', $user->password))->toBeTrue();
});

it('deletes a normal user', function () {
    $user = User::factory()->create();

    delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User deleted.');

    expect(User::query()->whereKey($user->id)->exists())->toBeFalse();
});
