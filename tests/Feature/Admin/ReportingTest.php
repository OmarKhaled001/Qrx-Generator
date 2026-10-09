<?php

namespace Tests\Feature\Admin;

use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Models\Payment;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportingTest extends TestCase
{
    use RefreshDatabase;

    private function invoicePaid(string $id, int $amount, string $customer = 'cus_report'): array
    {
        return [
            'id' => 'evt_'.$id,
            'type' => 'invoice.paid',
            'data' => ['object' => [
                'id' => $id, 'object' => 'invoice', 'customer' => $customer, 'amount_paid' => $amount, 'currency' => 'usd',
                'status_transitions' => ['paid_at' => now()->subDay()->timestamp],
            ]],
        ];
    }

    public function test_paid_invoices_are_recorded_once(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['stripe_id' => 'cus_report'])->save();

        $this->postJson('/stripe/webhook', $this->invoicePaid('in_1', 2900))->assertOk();
        $this->postJson('/stripe/webhook', $this->invoicePaid('in_1', 2900))->assertOk(); // Stripe retry

        $payment = Payment::sole();
        $this->assertSame(2900, $payment->amount_cents);
        $this->assertSame($user->id, $payment->user_id);
    }

    public function test_zero_amount_and_other_events_are_ignored(): void
    {
        $this->postJson('/stripe/webhook', $this->invoicePaid('in_free', 0))->assertOk();
        $this->postJson('/stripe/webhook', ['id' => 'evt_x', 'type' => 'invoice.created', 'data' => ['object' => ['id' => 'in_2', 'amount_paid' => 900]]])->assertOk();

        $this->assertSame(0, Payment::count());
    }

    public function test_admin_dashboard_shows_revenue(): void
    {
        Payment::query()->create(['stripe_invoice_id' => 'in_9', 'amount_cents' => 7900, 'currency' => 'usd', 'paid_at' => now()]);
        $this->actingAs(User::factory()->admin()->create());
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(StatsOverview::class)->assertSee(['Revenue (30 days)', '$79']);
        Livewire::test(RevenueChart::class)->assertOk();
    }
}
