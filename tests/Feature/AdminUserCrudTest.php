<?php

use App\Models\Admin;
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

it('lists administrators', function () {
    $other = Admin::factory()->create(['name' => 'Other Admin']);

    get(route('admin.administrators.index'))
        ->assertSuccessful()
        ->assertSee('Administrators')
        ->assertSee('Primary Admin')
        ->assertSee('Other Admin')
        ->assertSee($other->email);
});

it('redirects guests away from the administrators index', function () {
    auth('admin')->logout();

    get(route('admin.administrators.index'))
        ->assertRedirect();
});

it('shows the create administrator form', function () {
    get(route('admin.administrators.create'))
        ->assertSuccessful()
        ->assertSee('Add Administrator')
        ->assertSee('Create Administrator');
});

it('stores a new administrator', function () {
    post(route('admin.administrators.store'), [
        'name' => 'New Admin',
        'email' => 'new-admin@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'avatar' => 'https://example.com/avatar.png',
    ])
        ->assertRedirect(route('admin.administrators.index'))
        ->assertSessionHas('success', 'Administrator created.');

    $created = Admin::query()->where('email', 'new-admin@example.com')->first();

    expect($created)->not->toBeNull()
        ->and($created->name)->toBe('New Admin')
        ->and($created->avatar)->toBe('https://example.com/avatar.png')
        ->and(Hash::check('password123', $created->password))->toBeTrue();
});

it('validates required fields when storing', function () {
    post(route('admin.administrators.store'), [])
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

it('shows the edit administrator form', function () {
    $other = Admin::factory()->create(['name' => 'Editable Admin']);

    get(route('admin.administrators.edit', $other))
        ->assertSuccessful()
        ->assertSee('Edit Administrator')
        ->assertSee('Editable Admin');
});

it('updates an administrator', function () {
    $other = Admin::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    put(route('admin.administrators.update', $other), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'avatar' => 'https://example.com/new.png',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ])
        ->assertRedirect(route('admin.administrators.index'))
        ->assertSessionHas('success', 'Administrator updated.');

    $other->refresh();

    expect($other->name)->toBe('Updated Name')
        ->and($other->email)->toBe('updated@example.com')
        ->and($other->avatar)->toBe('https://example.com/new.png')
        ->and(Hash::check('newpassword', $other->password))->toBeTrue();
});

it('prevents an administrator from deleting themselves', function () {
    delete(route('admin.administrators.destroy', $this->admin))
        ->assertRedirect(route('admin.administrators.index'))
        ->assertSessionHas('error', 'You cannot delete yourself.');

    expect(Admin::query()->whereKey($this->admin->id)->exists())->toBeTrue();
});

it('deletes another administrator', function () {
    $other = Admin::factory()->create();

    delete(route('admin.administrators.destroy', $other))
        ->assertRedirect(route('admin.administrators.index'))
        ->assertSessionHas('success', 'Administrator deleted.');

    expect(Admin::query()->whereKey($other->id)->exists())->toBeFalse();
});
