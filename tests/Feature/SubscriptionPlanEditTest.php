<?php

use App\Models\Admin;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('can access subscription plan edit page', function () {
    $plan = SubscriptionPlan::create([
        'name' => 'Pro Plan',
        'price' => 29.99,
        'max_emails' => 1000,
        'max_contacts' => 500,
        'max_campaigns' => 10,
        'max_groups' => 5,
    ]);

    get(route('admin.subscription-plans.edit', $plan))
        ->assertStatus(200)
        ->assertSee('Pro Plan');
});

it('can update a subscription plan', function () {
    $plan = SubscriptionPlan::create([
        'name' => 'Pro Plan',
        'price' => 29.99,
        'max_emails' => 1000,
        'max_contacts' => 500,
        'max_campaigns' => 10,
        'max_groups' => 5,
    ]);

    Pest\Laravel\put(route('admin.subscription-plans.update', $plan), [
        'name' => 'Updated Plan Name',
        'price' => 49.99,
        'max_emails' => 2000,
        'max_contacts' => 1000,
        'max_campaigns' => 20,
        'max_groups' => 10,
    ])
        ->assertRedirect(route('admin.subscription-plans.index'))
        ->assertSessionHas('success', 'Plan updated successfully.');

    $plan->refresh();
    expect($plan->name)->toBe('Updated Plan Name')
        ->and((float) $plan->price)->toBe(49.99)
        ->and($plan->max_emails)->toBe(2000);
});



