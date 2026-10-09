<?php

namespace Tests\Feature\Qr;

use App\Models\Plan;
use App\Models\QrCode;
use App\Models\Scan;
use App\Services\ScanStats;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SubscribesUsers;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase, SubscribesUsers;

    private QrCode $qrCode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->freePlan();
        $this->travelTo(now()->setDate(2026, 10, 9)->setTime(12, 0));
        $this->qrCode = QrCode::factory()->create();

        $scan = fn (string $at, string $device, ?string $country, string $visitor) => Scan::factory()->for($this->qrCode)->create([
            'scanned_at' => $at, 'device' => $device, 'country' => $country, 'visitor_hash' => hash('sha256', $visitor),
        ]);
        $scan('2026-10-09 09:00', 'mobile', 'EG', 'a');
        $scan('2026-10-09 10:00', 'mobile', 'EG', 'a');
        $scan('2026-10-09 11:00', 'desktop', 'SA', 'b');
        $scan('2026-10-07 11:00', 'mobile', null, 'a');
        $scan('2026-08-01 11:00', 'mobile', 'EG', 'c'); // outside 30 days
    }

    public function test_stats_are_zero_filled_and_scoped_to_30_days(): void
    {
        $stats = app(ScanStats::class)->forQrCode($this->qrCode);

        $this->assertCount(30, $stats['daily']);
        $this->assertSame(3, $stats['daily']['2026-10-09']);
        $this->assertSame(1, $stats['daily']['2026-10-07']);
        $this->assertSame(0, $stats['daily']['2026-10-08']);
        $this->assertSame(4, $stats['total']);
        $this->assertSame(3, $stats['unique']); // a + b on the 9th, a on the 7th
        $this->assertSame(['mobile' => 3, 'desktop' => 1], $stats['devices']);
        $this->assertSame(['Egypt' => 2, 'Saudi Arabia' => 1], $stats['countries']);
    }

    public function test_country_names_follow_the_language(): void
    {
        app()->setLocale('ar');

        $this->assertArrayHasKey('مصر', app(ScanStats::class)->forQrCode($this->qrCode)['countries']);
    }

    public function test_paid_plans_see_the_breakdown_and_free_plans_an_upgrade_card(): void
    {
        $this->actingAs($this->qrCode->user)->get('/qr/'.$this->qrCode->id)
            ->assertSee(__('See when, where and on which devices this code is scanned.'))
            ->assertDontSee(__('Unique visitors'));

        $this->subscribe($this->qrCode->user, Plan::factory()->create());

        $this->get('/qr/'.$this->qrCode->id)
            ->assertSee([__('Unique visitors'), 'Egypt', __('Scans per day, last 30 days')]);
    }

    public function test_the_dashboard_shows_a_30_day_chart(): void
    {
        $this->assertSame(3, app(ScanStats::class)->forUser($this->qrCode->user)['2026-10-09']);

        $this->actingAs($this->qrCode->user)->get('/dashboard')->assertSee(__('Scans per day, last 30 days'));
    }
}
