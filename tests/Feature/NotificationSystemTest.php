<?php

use App\Models\Admin;
use App\Models\Form;
use App\Models\Notification;
use App\Support\NotificationCenter;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('creates notifications via NotificationCenter builder and helper methods', function () {
    $n1 = NotificationCenter::make('Test Notice')
        ->sub('Sub details')
        ->icon('star')
        ->type('primary')
        ->url('https://example.com')
        ->send();

    expect($n1->title)->toBe('Test Notice');
    expect($n1->type)->toBe('primary');
    expect($n1->url)->toBe('https://example.com');
    expect($n1->isRead())->toBeFalse();

    $n2 = NotificationCenter::success('Success Title', 'Success Sub');
    expect($n2->type)->toBe('success');
    expect($n2->icon)->toBe('check-circle');

    $n3 = NotificationCenter::warning('Warning Title', 'Warning Sub');
    expect($n3->type)->toBe('warning');
    expect($n3->icon)->toBe('triangle-exclamation');

    $n4 = NotificationCenter::error('Error Title', 'Error Sub');
    expect($n4->type)->toBe('error');
    expect($n4->icon)->toBe('x-circle');
});

it('handles read and unread scopes and helper methods on Notification model', function () {
    $unreadNotification = NotificationCenter::info('Unread Notice');
    $readNotification = NotificationCenter::info('Read Notice');
    $readNotification->markAsRead();

    expect($unreadNotification->isRead())->toBeFalse();
    expect($readNotification->isRead())->toBeTrue();

    expect(Notification::unread()->pluck('id'))->toContain($unreadNotification->id);
    expect(Notification::unread()->pluck('id'))->not->toContain($readNotification->id);
    expect(Notification::read()->pluck('id'))->toContain($readNotification->id);
});

it('lists notifications via API with pagination and unread filters', function () {
    NotificationCenter::info('First');
    $n2 = NotificationCenter::info('Second');
    $n2->markAsRead();

    $response = getJson(route('api.notifications.index'));
    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('unread_count', 1);

    $unreadResponse = getJson(route('api.notifications.index', ['unread' => 1]));
    $unreadResponse->assertStatus(200);
    expect(count($unreadResponse->json('data')))->toBe(1);
});

it('stores notification via API endpoint', function () {
    $response = postJson(route('api.notifications.store'), [
        'title' => 'API Created Notification',
        'sub' => 'Created via POST /api/notifications',
        'type' => 'success',
        'url' => '/admin/dashboard',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.title', 'API Created Notification');

    expect(Notification::where('title', 'API Created Notification')->exists())->toBeTrue();
});

it('marks single notification as read via API', function () {
    $notification = NotificationCenter::info('Mark Me Read');

    $response = patchJson(route('api.notifications.mark-as-read', $notification));
    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    expect($notification->fresh()->isRead())->toBeTrue();
});

it('marks all notifications as read via API', function () {
    NotificationCenter::info('Notice 1');
    NotificationCenter::info('Notice 2');

    $response = postJson(route('api.notifications.mark-all-read'));
    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('unread_count', 0);

    expect(Notification::unread()->count())->toBe(0);
});

it('triggers NotificationCenter events on Administrator CRUD operations', function () {
    // Store
    post(route('admin.administrators.store'), [
        'name' => 'John Manager',
        'email' => 'john.manager@example.com',
        'password' => 'password123',
        'is_active' => 1,
    ]);

    $createdNotification = Notification::where('title', 'New Admin Created: John Manager')->first();
    expect($createdNotification)->not->toBeNull();
    expect($createdNotification->type)->toBe('success');

    $newAdmin = Admin::where('email', 'john.manager@example.com')->first();

    // Update
    put(route('admin.administrators.update', $newAdmin), [
        'name' => 'John Manager Updated',
        'email' => 'john.manager@example.com',
        'is_active' => 1,
    ]);

    $updatedNotification = Notification::where('title', 'Admin Updated: John Manager Updated')->first();
    expect($updatedNotification)->not->toBeNull();
    expect($updatedNotification->type)->toBe('info');

    // Delete
    delete(route('admin.administrators.destroy', $newAdmin));

    $deletedNotification = Notification::where('title', 'Admin Deleted: John Manager Updated')->first();
    expect($deletedNotification)->not->toBeNull();
    expect($deletedNotification->type)->toBe('warning');
});

it('triggers NotificationCenter on form entry submission', function () {
    $form = Form::factory()->create([
        'title' => 'Support Request',
    ]);

    post(route('forms.public-submit', $form), [
        'full_name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'message' => 'Need help',
    ]);

    $notification = Notification::where('title', 'New Entry: Support Request')->first();
    expect($notification)->not->toBeNull();
    expect($notification->type)->toBe('info');
    expect($notification->url)->toBe(route('admin.forms.entries', $form->id));
});

it('triggers NotificationCenter on bookings plugin route', function () {
    post('/bookings', [
        'customer_name' => 'Alice Smith',
        'tour_title' => 'Desert Safari',
    ]);

    $notification = Notification::where('sub', 'Alice Smith booked Desert Safari')->first();
    expect($notification)->not->toBeNull();
    expect($notification->type)->toBe('success');
    expect($notification->url)->toBe(route('admin.bookings.index'));
});
