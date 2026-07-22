<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'avatar', 'max_emails', 'max_contacts', 'max_campaigns', 'max_groups'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->latestOfMany();
    }

    public function usageCounter(): HasOne
    {
        return $this->hasOne(UsageCounter::class);
    }

    public function assignDefaultSubscription(?SubscriptionPlan $plan = null): ?Subscription
    {
        if (! $plan) {
            $setting = Setting::first();
            if ($setting && $setting->default_subscription_plan_id) {
                $plan = SubscriptionPlan::find($setting->default_subscription_plan_id);
            }
            if (! $plan) {
                $plan = SubscriptionPlan::first();
            }
        }

        if (! $plan) {
            return null;
        }

        // Deactivate existing active subscriptions
        $this->subscriptions()->where('status', 'active')->update([
            'status' => 'cancelled',
            'ends_at' => now(),
        ]);

        $subscription = $this->subscriptions()->create([
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => null,
        ]);

        // Sync plan limits to user model
        $this->update([
            'max_emails' => $plan->max_emails,
            'max_contacts' => $plan->max_contacts,
            'max_campaigns' => $plan->max_campaigns,
            'max_groups' => $plan->max_groups,
        ]);

        // Ensure usage counter exists
        $this->usageCounter()->firstOrCreate([], [
            'emails_sent_this_cycle' => 0,
            'contacts_count' => 0,
            'campaigns_count' => 0,
            'groups_count' => 0,
            'cycle_started_at' => now(),
        ]);

        return $subscription;
    }

    public function currentPlan(): ?SubscriptionPlan
    {
        return $this->activeSubscription?->plan;
    }
}
