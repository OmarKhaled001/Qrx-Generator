<?php

namespace Tests\Concerns;

use App\Models\Plan;
use App\Models\User;
use App\Services\Subscriptions;
use Illuminate\Support\Str;

trait SubscribesUsers
{
    /**
     * Store an active Stripe subscription to $plan the way Cashier's webhooks would.
     */
    protected function subscribe(User $user, Plan $plan, string $status = 'active', ?string $endsAt = null): void
    {
        $subscription = $user->subscriptions()->create([
            'type' => Subscriptions::TYPE,
            'stripe_id' => 'sub_'.Str::random(14),
            'stripe_status' => $status,
            'stripe_price' => $plan->stripe_price_id,
            'quantity' => 1,
            'ends_at' => $endsAt,
        ]);

        $subscription->items()->create([
            'stripe_id' => 'si_'.Str::random(14),
            'stripe_product' => 'prod_'.$plan->slug,
            'stripe_price' => $plan->stripe_price_id,
            'quantity' => 1,
        ]);
    }

    protected function freePlan(): Plan
    {
        return Plan::query()->where('is_free', true)->first() ?? Plan::factory()->free()->create();
    }
}
