<?php

namespace Tests\Feature\Domain;

use App\Models\Plan;
use App\Models\QrCode;
use App\Models\User;
use App\Services\Subscriptions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SubscribesUsers;
use Tests\TestCase;

class SubscriptionsTest extends TestCase
{
    use RefreshDatabase, SubscribesUsers;

    private Subscriptions $subscriptions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriptions = new Subscriptions;
    }

    public function test_users_without_a_subscription_are_on_the_free_plan(): void
    {
        $free = $this->freePlan();

        $this->assertTrue($this->subscriptions->currentPlan(User::factory()->create())->is($free));
    }

    public function test_free_plan_falls_back_to_config_when_not_seeded(): void
    {
        $plan = $this->subscriptions->currentPlan(User::factory()->create());

        $this->assertTrue($plan->is_free);
        $this->assertSame(3, $plan->qr_limit);
        $this->assertSame(100, $plan->scan_limit);
    }

    public function test_an_active_subscription_maps_to_its_plan(): void
    {
        $this->freePlan();
        $business = Plan::factory()->create(['qr_limit' => 100]);
        $user = User::factory()->create();
        $this->subscribe($user, $business);

        $this->assertTrue($this->subscriptions->currentPlan($user)->is($business));
        $this->assertTrue($this->subscriptions->isOnPaidPlan($user));
    }

    public function test_a_cancelled_subscription_keeps_its_plan_until_the_grace_period_ends(): void
    {
        $free = $this->freePlan();
        $plan = Plan::factory()->create();

        $graced = User::factory()->create();
        $this->subscribe($graced, $plan, 'active', now()->addDays(5)->toDateTimeString());
        $this->assertTrue($this->subscriptions->currentPlan($graced)->is($plan));

        $ended = User::factory()->create();
        $this->subscribe($ended, $plan, 'canceled', now()->subDay()->toDateTimeString());
        $this->assertTrue($this->subscriptions->currentPlan($ended)->is($free));
    }

    public function test_quota_counts_the_users_own_qr_codes(): void
    {
        $this->freePlan();
        $user = User::factory()->create();
        QrCode::factory()->count(2)->for($user)->create();
        QrCode::factory()->count(5)->create();

        $this->assertSame(1, $this->subscriptions->remainingQrCodes($user));
        $this->assertTrue($this->subscriptions->canCreateQrCode($user));

        QrCode::factory()->for($user)->create();
        $this->assertFalse($this->subscriptions->canCreateQrCode($user));
    }

    public function test_scan_limit_follows_the_owners_current_plan(): void
    {
        $this->freePlan();
        $qrCode = QrCode::factory()->create(['scan_count' => 100]);

        $this->assertTrue($this->subscriptions->hasReachedScanLimit($qrCode));

        $this->subscribe($qrCode->user, Plan::factory()->create(['scan_limit' => 5000]));
        $this->assertFalse($this->subscriptions->hasReachedScanLimit($qrCode->fresh()));
    }

    public function test_only_active_paid_plans_with_a_price_are_purchasable(): void
    {
        $free = $this->freePlan();
        $paid = Plan::factory()->create();
        $inactive = Plan::factory()->inactive()->create();
        $noPrice = Plan::factory()->create(['stripe_price_id' => null]);

        $this->assertSame([$paid->id], Plan::query()->purchasable()->pluck('id')->all());
        $this->assertFalse($free->isPurchasable());
        $this->assertFalse($inactive->isPurchasable());
        $this->assertFalse($noPrice->isPurchasable());
    }
}
