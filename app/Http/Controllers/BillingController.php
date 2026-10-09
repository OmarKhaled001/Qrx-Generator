<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\Subscriptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Cashier\Checkout;

/**
 * Plans, Stripe Checkout and the Stripe Billing Portal. Cashier and its
 * webhooks keep the local subscriptions table in sync; there is no custom
 * transactions table (approved).
 */
class BillingController extends Controller
{
    public function show(Request $request, Subscriptions $subscriptions): View
    {
        $user = $request->user();

        return view('billing', [
            'plan' => $subscriptions->currentPlan($user),
            'subscription' => $user->subscription(Subscriptions::TYPE),
            'plans' => Plan::query()->active()->get(),
            'qrCount' => $user->qrCodes()->count(),
        ]);
    }

    public function checkout(Request $request, Plan $plan): Checkout|RedirectResponse
    {
        abort_unless($plan->isPurchasable(), 404);

        $user = $request->user();
        $subscription = $user->subscription(Subscriptions::TYPE);

        // Already subscribed: change plan in place instead of starting a second subscription.
        if ($subscription?->valid()) {
            $subscription->swap($plan->stripe_price_id);

            return redirect()->route('billing')->with('toast', [
                'message' => __('You are now on the :plan plan.', ['plan' => __($plan->name)]),
                'type' => 'success',
            ]);
        }

        return $user->newSubscription(Subscriptions::TYPE, $plan->stripe_price_id)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('billing', ['checkout' => 'success']),
                'cancel_url' => route('billing'),
                'locale' => app()->getLocale() === 'ar' ? 'auto' : app()->getLocale(),
            ]);
    }

    public function portal(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasStripeId()) {
            return redirect()->route('billing')->with('toast', [
                'message' => __('You do not have a billing account yet.'),
                'type' => 'warning',
            ]);
        }

        return $user->redirectToBillingPortal(route('billing'));
    }
}
