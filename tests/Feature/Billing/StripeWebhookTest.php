<?php

namespace Tests\Feature\Billing;

use App\Models\User;
use App\Services\Subscriptions;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Stripe → Cashier webhook → subscriptions table → our plan mapping, end to end.
 * (No webhook secret is set in tests, so Cashier skips signature verification.)
 */
class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function subscriptionEvent(string $type, string $status, string $price, ?int $endsAt = null): array
    {
        return [
            'id' => 'evt_test',
            'type' => $type,
            'data' => ['object' => [
                'id' => 'sub_123',
                'object' => 'subscription',
                'customer' => 'cus_webhook',
                'status' => $status,
                'cancel_at_period_end' => $endsAt !== null,
                'cancel_at' => $endsAt,
                'current_period_end' => $endsAt,
                'metadata' => ['type' => Subscriptions::TYPE],
                'items' => ['data' => [[
                    'id' => 'si_123',
                    'quantity' => 1,
                    'price' => ['id' => $price, 'product' => 'prod_business'],
                ]]],
            ]],
        ];
    }

    public function test_a_paid_subscription_from_stripe_unlocks_the_plan(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create();
        $user->forceFill(['stripe_id' => 'cus_webhook'])->save();
        $price = config('qrx.stripe_prices.business');

        $this->postJson('/stripe/webhook', $this->subscriptionEvent('customer.subscription.created', 'active', $price))->assertOk();

        $plan = app(Subscriptions::class)->currentPlan($user->fresh());
        $this->assertSame('business', $plan->slug);
        $this->assertSame(100, $plan->qr_limit);
    }

    public function test_a_deleted_subscription_moves_the_user_back_to_free(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create();
        $user->forceFill(['stripe_id' => 'cus_webhook'])->save();
        $price = config('qrx.stripe_prices.business');

        $this->postJson('/stripe/webhook', $this->subscriptionEvent('customer.subscription.created', 'active', $price))->assertOk();
        $this->postJson('/stripe/webhook', $this->subscriptionEvent('customer.subscription.deleted', 'canceled', $price))->assertOk();

        $this->assertTrue(app(Subscriptions::class)->currentPlan($user->fresh())->is_free);
    }

    public function test_the_webhook_accepts_events_and_is_exempt_from_csrf(): void
    {
        $this->postJson('/stripe/webhook', ['id' => 'evt_x', 'type' => 'unknown.event', 'data' => ['object' => ['id' => 'x']]])->assertOk();

        // Laravel skips CSRF checks while running tests, so assert the exclusion itself.
        $this->assertContains('stripe/*', app(PreventRequestForgery::class)->getExcludedPaths());
    }
}
