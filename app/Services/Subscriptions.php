<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\QrCode;
use App\Models\User;

/**
 * The only place that answers "which plan is this user on, and what may they do?".
 * A valid Cashier subscription (incl. grace period) maps to its plan by Stripe
 * price; everyone else is on the free plan.
 */
final class Subscriptions
{
    public const TYPE = 'default';

    public function currentPlan(User $user): Plan
    {
        $subscription = $user->subscription(self::TYPE);

        if ($subscription && $subscription->valid()) {
            $plan = Plan::query()->where('stripe_price_id', $subscription->stripe_price)->first();

            if ($plan) {
                return $plan;
            }
        }

        return Plan::free();
    }

    public function isOnPaidPlan(User $user): bool
    {
        return ! $this->currentPlan($user)->is_free;
    }

    public function remainingQrCodes(User $user): int
    {
        return max(0, $this->currentPlan($user)->qr_limit - $user->qrCodes()->count());
    }

    public function canCreateQrCode(User $user): bool
    {
        return $this->remainingQrCodes($user) > 0;
    }

    public function hasReachedScanLimit(QrCode $qrCode): bool
    {
        return $qrCode->hasReachedScanLimit($this->currentPlan($qrCode->user));
    }
}
