<?php

use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('assigns the default subscription plan and creates usage counter on user registration', function () {
    $plan = SubscriptionPlan::create([
        'name' => 'Default Free Plan',
        'price' => 0.00,
        'max_emails' => 500,
        'max_contacts' => 200,
        'max_campaigns' => 5,
        'max_groups' => 2,
    ]);

    Setting::create([
        'site_title' => 'Test Site',
        'default_subscription_plan_id' => $plan->id,
    ]);

    post(route('register'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect('/app/campaigns');

    $user = User::where('email', 'john@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->activeSubscription)->not->toBeNull()
        ->and($user->activeSubscription->subscription_plan_id)->toBe($plan->id)
        ->and($user->activeSubscription->status)->toBe('active')
        ->and($user->activeSubscription->plan->name)->toBe('Default Free Plan')
        ->and($user->usageCounter)->not->toBeNull()
        ->and($user->usageCounter->emails_sent_this_cycle)->toBe(0)
        ->and($user->usageCounter->contacts_count)->toBe(0);
});

it('allows reassigning user subscription plan', function () {
    $plan1 = SubscriptionPlan::create([
        'name' => 'Basic Plan',
        'price' => 10.00,
        'max_emails' => 1000,
        'max_contacts' => 500,
        'max_campaigns' => 10,
        'max_groups' => 5,
    ]);

    $plan2 = SubscriptionPlan::create([
        'name' => 'Pro Plan',
        'price' => 30.00,
        'max_emails' => 5000,
        'max_contacts' => 2500,
        'max_campaigns' => 50,
        'max_groups' => 20,
    ]);

    $user = User::factory()->create();

    $user->assignDefaultSubscription($plan1);

    expect($user->activeSubscription->subscription_plan_id)->toBe($plan1->id);

    $user->assignDefaultSubscription($plan2);

    $user->refresh();

    expect($user->activeSubscription->subscription_plan_id)->toBe($plan2->id)
        ->and($user->subscriptions()->count())->toBe(2)
        ->and($user->subscriptions()->where('status', 'cancelled')->count())->toBe(1);
});
