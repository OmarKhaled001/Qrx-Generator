<?php

namespace Tests\Feature\Billing;

use App\Models\Plan;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\FakesStripe;
use Tests\Concerns\SubscribesUsers;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use FakesStripe, RefreshDatabase, SubscribesUsers;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);
        $this->user = User::factory()->create();
    }

    public function test_billing_page_shows_the_current_plan_and_all_plans(): void
    {
        $this->actingAs($this->user)->get('/billing')
            ->assertOk()
            ->assertSee([__('Plan & billing'), __('Current plan'), 'Starter', 'Business', 'Agency']);
    }

    public function test_checkout_redirects_to_stripe_for_a_purchasable_plan(): void
    {
        $this->fakeStripe([
            '/v1/customers' => ['id' => 'cus_test', 'object' => 'customer'],
            '/v1/checkout/sessions' => ['id' => 'cs_test', 'object' => 'checkout.session', 'url' => 'https://checkout.stripe.com/c/pay/cs_test'],
        ]);
        $plan = Plan::query()->where('slug', 'business')->sole();

        $this->actingAs($this->user)->get('/billing/checkout/business')
            ->assertRedirect('https://checkout.stripe.com/c/pay/cs_test');

        $this->assertSame(['POST /v1/customers', 'POST /v1/checkout/sessions'], $this->stripeCalls);
        $this->assertSame($plan->stripe_price_id, $this->stripeParams['/v1/checkout/sessions']['line_items'][0]['price']);
        $this->assertStringContainsString('/billing?checkout=success', $this->stripeParams['/v1/checkout/sessions']['success_url']);
        $this->assertSame('cus_test', $this->user->fresh()->stripe_id);
    }

    public function test_free_inactive_and_unknown_plans_cannot_be_bought(): void
    {
        Plan::query()->where('slug', 'agency')->update(['is_active' => false]);
        $this->actingAs($this->user);

        $this->get('/billing/checkout/free')->assertNotFound();
        $this->get('/billing/checkout/agency')->assertNotFound();
        $this->get('/billing/checkout/nope')->assertNotFound();
    }

    public function test_subscribers_swap_plans_instead_of_buying_twice(): void
    {
        $starter = Plan::query()->where('slug', 'starter')->sole();
        $this->user->forceFill(['stripe_id' => 'cus_test'])->save();
        $this->subscribe($this->user, $starter);
        $subscriptionId = $this->user->subscription()->stripe_id;

        $this->fakeStripe([
            "/v1/subscriptions/{$subscriptionId}" => [
                'id' => $subscriptionId, 'object' => 'subscription', 'status' => 'active', 'customer' => 'cus_test',
                'items' => ['object' => 'list', 'data' => [['id' => 'si_new', 'object' => 'subscription_item', 'quantity' => 1,
                    'price' => ['id' => config('qrx.stripe_prices.business'), 'object' => 'price', 'product' => 'prod_business']]]],
            ],
        ]);

        $this->actingAs($this->user)->get('/billing/checkout/business')->assertRedirect('/billing');

        $this->assertContains("POST /v1/subscriptions/{$subscriptionId}", $this->stripeCalls);
        $this->assertSame(config('qrx.stripe_prices.business'), $this->user->subscription()->fresh()->stripe_price);
    }

    public function test_billing_portal_needs_a_billing_account(): void
    {
        $this->actingAs($this->user)->get('/billing/portal')->assertRedirect('/billing')->assertSessionHas('toast');

        $this->fakeStripe(['/v1/billing_portal/sessions' => ['id' => 'bps_test', 'object' => 'billing_portal.session', 'url' => 'https://billing.stripe.com/p/session/test']]);
        $this->user->forceFill(['stripe_id' => 'cus_test'])->save();

        $this->actingAs($this->user)->get('/billing/portal')->assertRedirect('https://billing.stripe.com/p/session/test');
    }

    public function test_guests_and_unverified_users_are_redirected(): void
    {
        $this->get('/billing')->assertRedirect('/login');
        $this->get('/billing/checkout/business')->assertRedirect('/login');
        $this->actingAs(User::factory()->unverified()->create())->get('/billing')->assertRedirect('/email/verify');
    }
}
