<?php

namespace Tests\Feature\Domain;

use App\Actions\Qr\RecordScan;
use App\Models\QrCode;
use App\Models\Scan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RecordScanTest extends TestCase
{
    use RefreshDatabase;

    private const IPHONE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148';

    private function request(string $ip = '203.0.113.7', string $userAgent = self::IPHONE, array $headers = []): Request
    {
        $server = ['REMOTE_ADDR' => $ip, 'HTTP_USER_AGENT' => $userAgent];
        foreach ($headers as $name => $value) {
            $server['HTTP_'.strtoupper(str_replace('-', '_', $name))] = $value;
        }

        return Request::create('/x/code', 'GET', server: $server);
    }

    public function test_a_scan_increments_the_counter_and_stores_a_row(): void
    {
        $qrCode = QrCode::factory()->create();

        $this->assertTrue(app(RecordScan::class)->handle($qrCode, $this->request(headers: ['Referer' => 'https://news.example.org/a', 'CF-IPCountry' => 'eg'])));

        $fresh = $qrCode->fresh();
        $this->assertSame(1, $fresh->scan_count);
        $this->assertNotNull($fresh->last_scanned_at);

        $scan = Scan::sole();
        $this->assertSame('mobile', $scan->device);
        $this->assertSame('news.example.org', $scan->referrer_host);
        $this->assertSame('EG', $scan->country);
    }

    public function test_no_raw_ip_address_is_stored_anywhere(): void
    {
        $ip = '203.0.113.7';
        app(RecordScan::class)->handle(QrCode::factory()->create(), $this->request($ip));

        $this->assertNotContains('ip', Schema::getColumnListing('scans'));
        $row = json_encode(Scan::sole()->getAttributes());
        $this->assertStringNotContainsString($ip, $row);
        $this->assertStringNotContainsString('203.0.113', $row);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', Scan::sole()->visitor_hash);
    }

    public function test_visitor_hash_is_stable_within_a_day_and_changes_the_next_day(): void
    {
        $qrCode = QrCode::factory()->create();
        $record = app(RecordScan::class);

        $this->travelTo(now()->setTime(9, 0));
        $record->handle($qrCode, $this->request());
        $record->handle($qrCode, $this->request());
        $this->travel(1)->days();
        $record->handle($qrCode, $this->request());

        [$first, $second, $nextDay] = Scan::query()->orderBy('id')->pluck('visitor_hash')->all();
        $this->assertSame($first, $second);
        $this->assertNotSame($first, $nextDay);
    }

    public function test_bots_and_link_previews_are_not_counted(): void
    {
        $qrCode = QrCode::factory()->create();

        foreach (['WhatsApp/2.23', 'facebookexternalhit/1.1', 'Googlebot/2.1', 'curl/8.0', ''] as $userAgent) {
            $this->assertFalse(app(RecordScan::class)->handle($qrCode, $this->request(userAgent: $userAgent)));
        }

        $this->assertSame(0, $qrCode->fresh()->scan_count);
        $this->assertSame(0, Scan::count());
    }

    public function test_device_and_country_detection(): void
    {
        $qrCode = QrCode::factory()->create();
        $record = app(RecordScan::class);

        $record->handle($qrCode, $this->request(userAgent: 'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X)', headers: ['CF-IPCountry' => 'XX']));
        $record->handle($qrCode, $this->request(userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/130'));

        $this->assertSame(['tablet', 'desktop'], Scan::query()->orderBy('id')->pluck('device')->all());
        $this->assertNull(Scan::query()->orderBy('id')->first()->country);
    }
}
