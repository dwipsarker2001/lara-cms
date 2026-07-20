# Subscription Management Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace flat limit columns on `users` table with a `subscriptions` table, migrate existing data, and update all limit checks.

**Architecture:** A `subscriptions` table links users to subscription_plans. A `Subscription` model with `belongsTo: user, plan`. The `User` model gets a `hasOne subscription` relationship and a `planLimit()` helper. All controllers that check limits read from the subscription instead of user columns.

**Tech Stack:** Laravel 13, MySQL, Eloquent

## Global Constraints

- Follow existing code conventions (constructors, type hints, etc.)
- Run `vendor/bin/pint --format agent` after all PHP changes
- Run `php artisan test --compact` before claiming completion

---

### Task 1: Create Subscriptions Migration + Model

**Files:**
- Create: `database/migrations/2026_07_19_000001_create_subscriptions_table.php`
- Create: `app/Models/Subscription.php`
- Modify: `app/Models/SubscriptionPlan.php` (add `hasMany subscriptions`)
- Modify: `app/Models/User.php` (add `hasOne subscription`, add `planLimit()`, remove `max_*` from fillable)

- [ ] **Step 1: Create migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->integer('emails_used')->default(0);
            $table->integer('contacts_used')->default(0);
            $table->integer('campaigns_used')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // Migrate existing users: create a subscription for each
        $activePlan = DB::table('subscription_plans')->where('active_on_register', true)->first();
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            DB::table('subscriptions')->insert([
                'user_id' => $user->id,
                'plan_id' => $activePlan?->id ?? DB::table('subscription_plans')->first()?->id ?? 1,
                'emails_used' => 0,
                'contacts_used' => 0,
                'campaigns_used' => 0,
                'starts_at' => $user->created_at,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['max_emails', 'max_contacts', 'max_campaigns', 'max_groups']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('max_emails')->nullable();
            $table->integer('max_contacts')->nullable();
            $table->integer('max_campaigns')->nullable();
            $table->integer('max_groups')->nullable();
        });

        Schema::dropIfExists('subscriptions');
    }
};
```

- [ ] **Step 2: Create Subscription model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'plan_id', 'emails_used', 'contacts_used',
        'campaigns_used', 'starts_at', 'expires_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}
```

- [ ] **Step 3: Update SubscriptionPlan model**

Add to `app/Models/SubscriptionPlan.php`:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

// Inside class:
public function subscriptions(): HasMany
{
    return $this->hasMany(Subscription::class, 'plan_id');
}
```

- [ ] **Step 4: Update User model**

Add to `app/Models/User.php`:

```php
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasOne;

// Inside class, add:
public function subscription(): HasOne
{
    return $this->hasOne(Subscription::class);
}

public function planLimit(string $field): int
{
    return $this->subscription?->plan?->{$field} ?? 0;
}
```

Remove `'max_emails', 'max_contacts', 'max_campaigns', 'max_groups'` from the `#[Fillable]` attribute.

- [ ] **Step 5: Run migration**

```bash
php artisan migrate
```

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat: add subscriptions table, migrate existing users"
```

---

### Task 2: Update RegisterController

**Files:**
- Modify: `app/Http/Controllers/Auth/RegisterController.php`

- [ ] **Step 1: Replace limit-copy logic with subscription creation**

Replace the active package block:

```php
$activePackage = \App\Models\SubscriptionPlan::where('active_on_register', true)->first();

$user = User::create([...]);

if ($activePackage) {
    $user->subscription()->create([
        'plan_id' => $activePackage->id,
        'starts_at' => now(),
        'status' => 'active',
    ]);
} else {
    $fallback = \App\Models\SubscriptionPlan::first();
    if ($fallback) {
        $user->subscription()->create([
            'plan_id' => $fallback->id,
            'starts_at' => now(),
            'status' => 'active',
        ]);
    }
}
```

Remove the old `$userData['max_*'] = ...` lines.

- [ ] **Step 2: Run pint**

```bash
vendor/bin/pint --format agent
```

- [ ] **Step 3: Commit**

```bash
git add -A
git commit -m "feat: create subscription on user registration"
```

---

### Task 3: Update CampaignController Limits

**Files:**
- Modify: `app/Http/Controllers/Marketing/CampaignController.php`

- [ ] **Step 1: Update `remCampaigns()`**

```php
private function remCampaigns(): int
{
    $max = auth()->user()->planLimit('max_campaigns');
    $count = Campaign::where('user_id', auth()->id())->count();
    return max($max - $count, 0);
}
```

- [ ] **Step 2: Update `remEmails()`**

```php
private function remEmails(): int
{
    $max = auth()->user()->planLimit('max_emails');
    $used = auth()->user()->subscription->emails_used ?? 0;
    return max($max - $used, 0);
}
```

- [ ] **Step 3: Run pint + commit**

---

### Task 4: Update ContactController + GroupController Limits

**Files:**
- Modify: `app/Http/Controllers/Marketing/ContactController.php`
- Modify: `app/Http/Controllers/Marketing/GroupController.php`

- [ ] **Step 1: Update ContactController**

Find `$rem_groups = 0;` and replace with computed values:

```php
$max = auth()->user()->planLimit('max_groups');
$count = \App\Models\Marketing\Group::where('user_id', auth()->id())->count();
$rem_groups = max($max - $count, 0);
```

Similarly for `$rem_contacts` if used.

- [ ] **Step 2: Update DashboardController**

Replace hardcoded `$rem_emails = 0; $rem_campaigns = 0; $rem_groups = 0;` with:

```php
$rem_emails = auth()->user()->planLimit('max_emails');
$rem_campaigns = auth()->user()->planLimit('max_campaigns');
$rem_groups = auth()->user()->planLimit('max_groups');
```

- [ ] **Step 3: Run pint + commit**

---

### Task 5: Admin User Edit — Subscription Management

**Files:**
- Modify: `app/Http/Controllers/Admin/UserController.php`
- Create: `resources/views/admin/users/edit.blade.php` (if missing)
- Modify: `routes/admin.php`

- [ ] **Step 1: Add subscription update route**

In `routes/admin.php`, inside the admin group:

```php
Route::put('users/{user}/subscription', [\App\Http\Controllers\Admin\UserController::class, 'updateSubscription'])
    ->name('users.subscription.update');
```

- [ ] **Step 2: Add `updateSubscription` method to UserController**

```php
public function updateSubscription(Request $request, User $user)
{
    $data = $request->validate([
        'plan_id' => 'required|exists:subscription_plans,id',
        'status' => 'required|in:active,expired,cancelled',
        'expires_at' => 'nullable|date',
    ]);

    $subscription = $user->subscription;
    if ($subscription) {
        $subscription->update($data);
    } else {
        $user->subscription()->create(array_merge($data, ['starts_at' => now()]));
    }

    return redirect()->back()->with('success', 'Subscription updated successfully.');
}
```

- [ ] **Step 3: Add subscription fields to user edit view**

Show current plan, remaining slots, plan selector dropdown, status, expiry.

- [ ] **Step 4: Run pint + commit**

---

### Task 6: Verify

- [ ] **Step 1: Run tests**

```bash
php artisan test --compact
```

- [ ] **Step 2: Manual smoke test**

Register a new user, verify they get a subscription with the active_on_register plan. Check `/admin/users/{id}` shows subscription info.

- [ ] **Step 3: Run pint**

```bash
vendor/bin/pint --format agent
```
