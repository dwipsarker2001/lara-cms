# Subscription Management System

## Overview

Replace the current flat limit columns on `users` table with a proper `subscriptions` table that tracks which plan a user is on, usage, status, and dates. Keeps the system ready for future payment integration while admins can manually manage subscriptions now.

## Tables

### `subscriptions`

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id | bigint FK -> users | |
| plan_id | bigint FK -> subscription_plans | |
| emails_used | int, default 0 | incremented on send |
| contacts_used | int, default 0 | incremented on contact create |
| campaigns_used | int, default 0 | incremented on campaign create |
| starts_at | timestamp | when subscription began |
| expires_at | timestamp, nullable | null = never expires |
| status | enum: active, expired, cancelled, past_due | |
| created_at | timestamp | |
| updated_at | timestamp | |

The `max_emails`, `max_contacts`, `max_campaigns`, `max_groups` columns will be **removed** from the `users` table. All limit checks read from `user->subscription->plan`.

## Models

### `App\Models\Subscription`

```php
belongsTo: user, plan
casts: status => SubscriptionStatus enum
```

### `App\Models\SubscriptionPlan` (add)

```php
hasMany: subscriptions
```

### `User` (modify)

```php
hasOne: subscription
// remove: max_emails, max_contacts, max_campaigns, max_groups from fillable/casts
```

Add a helper accessor on User for quick limit checks:

```php
public function planLimit(string $field): int
{
    return $this->subscription?->plan?->{$field} ?? 0;
}
```

## Flow

### Registration

`RegisterController@store`:
1. Finds `active_on_register` plan (existing logic)
2. Creates a `Subscription` record: user_id, plan_id, status=active, starts_at=now
3. No longer sets `max_*` on `users`

### Campaign Limit Check

`CampaignController::remCampaigns()`:
```php
$max = auth()->user()->planLimit('max_campaigns');
$used = Campaign::where('user_id', auth()->id())->count();
return max($max - $used, 0);
```

### Email Limit Check

`CampaignController::sendcampaign()`:
```php
$max = auth()->user()->planLimit('max_emails');
$used = auth()->user()->subscription->emails_used;
// block if $used >= $max
```

When emails are sent (in `SendEmailJob` or stats callback), increment `subscription.emails_used`.

### Admin UI

- **User edit page** (`admin/administrators` style): Show current plan, remaining slots per metric, a dropdown to change plan, and a "Cancel Subscription" action.
- Located under **Advance > Users** in the admin sidebar — or a new **Subscriptions** section if volume grows.

### Future Payment Integration

Plug into webhook:
```php
// Stripe webhook example
$subscription->update([
    'plan_id' => $newPlanId,
    'status' => 'active',
    'expires_at' => $expiry,
]);
```

## Files Changed

| File | Change |
|------|--------|
| `database/migrations/xxxx_create_subscriptions_table.php` | New |
| `app/Models/Subscription.php` | New |
| `app/Models/SubscriptionPlan.php` | Add `hasMany subscriptions` |
| `app/Models/User.php` | Add `hasOne subscription`, remove `max_*` from fillable/casts |
| `app/Http/Controllers/Auth/RegisterController.php` | Create subscription on register |
| `app/Http/Controllers/Marketing/CampaignController.php` | Read limits from subscription |
| `app/Http/Controllers/Marketing/ContactController.php` | Read limits from subscription |
| `app/Http/Controllers/Marketing/GroupController.php` | Read limits from subscription |
| `app/Http/Controllers/Admin/UserController.php` | Add subscription management to user edit |
| `resources/views/admin/users/edit.blade.php` | Show subscription info + plan changer |
| `routes/admin.php` | Add subscription update route |

## Migration

A migration will:
1. Create `subscriptions` table
2. Migrate existing users: create a subscription for each user using their current `max_*` values
3. Drop `max_emails`, `max_contacts`, `max_campaigns`, `max_groups` from `users`
