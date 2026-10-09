<?php

namespace Tests\Feature\Admin;

use App\Enums\QrStatus;
use App\Filament\Resources\Plans\Pages\CreatePlan;
use App\Filament\Resources\QrCodes\Pages\EditQrCode;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\Plan;
use App\Models\QrCode;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_only_administrators_can_open_the_panel(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
        $this->actingAs($this->admin)->get('/admin')->assertOk()->assertSee(__('Paying subscribers'));
    }

    public function test_admin_lists_users_qr_codes_and_plans(): void
    {
        $customer = User::factory()->create(['name' => 'Customer One']);
        QrCode::factory()->for($customer)->create(['name' => 'Customer QR']);
        Plan::factory()->free()->create();

        $this->actingAs($this->admin);
        $this->get('/admin/users')->assertOk()->assertSee('Customer One');
        $this->get('/admin/qr-codes')->assertOk()->assertSee(['Customer QR', 'Customer One']);
        $this->get('/admin/plans')->assertOk()->assertSee('Free');
        $this->get("/admin/users/{$customer->id}/edit")->assertOk();
    }

    public function test_admin_can_grant_admin_access_but_not_remove_their_own(): void
    {
        $customer = User::factory()->create();
        $this->actingAs($this->admin);

        Livewire::test(EditUser::class, ['record' => $customer->getRouteKey()])
            ->fillForm(['is_admin' => true])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertTrue($customer->fresh()->is_admin);

        Livewire::test(EditUser::class, ['record' => $this->admin->getRouteKey()])
            ->assertFormFieldIsDisabled('is_admin')
            ->call('save');
        $this->assertTrue($this->admin->fresh()->is_admin);
    }

    public function test_admin_can_create_a_paid_plan_with_price_in_dollars(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreatePlan::class)
            ->fillForm([
                'name' => 'Pro',
                'slug' => 'pro',
                'is_free' => false,
                'is_active' => true,
                'stripe_price_id' => 'price_pro_123',
                'price_cents' => '19.50',
                'currency' => 'usd',
                'interval' => 'month',
                'qr_limit' => 60,
                'scan_limit' => 20000,
                'features' => ['60 dynamic QR codes'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $plan = Plan::query()->where('slug', 'pro')->sole();
        $this->assertSame(1950, $plan->price_cents);
        $this->assertSame(['60 dynamic QR codes'], $plan->features);
        $this->assertTrue($plan->isPurchasable());
    }

    public function test_admin_can_pause_any_customers_qr_code(): void
    {
        $qrCode = QrCode::factory()->create();
        $this->actingAs($this->admin);

        Livewire::test(EditQrCode::class, ['record' => $qrCode->getRouteKey()])
            ->fillForm(['status' => QrStatus::Paused->value])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(QrStatus::Paused, $qrCode->fresh()->status);
        $this->assertSame($qrCode->payload, $qrCode->fresh()->payload);
    }

    public function test_make_admin_command_promotes_or_creates_accounts(): void
    {
        $customer = User::factory()->create(['email' => 'owner@example.com']);

        $this->artisan('app:make-admin', ['email' => 'owner@example.com'])->assertSuccessful();
        $this->assertTrue($customer->fresh()->is_admin);

        $this->artisan('app:make-admin', ['email' => 'new@example.com', '--name' => 'New Admin', '--password' => 'secret-pass-1'])->assertSuccessful();
        $new = User::query()->where('email', 'new@example.com')->sole();
        $this->assertTrue($new->is_admin);
        $this->assertTrue($new->hasVerifiedEmail());
    }
}
