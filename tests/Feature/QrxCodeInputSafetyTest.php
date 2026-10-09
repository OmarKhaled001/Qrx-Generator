<?php

namespace Tests\Feature;

use App\Http\Livewire\QrDemo;
use App\Http\Livewire\QrxCreate;
use App\Models\QrxCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Concerns\CreatesQrxCodes;
use Tests\TestCase;

class QrxCodeInputSafetyTest extends TestCase
{
    use CreatesQrxCodes, RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        RateLimiter::clear('qr-demo:127.0.0.1');

        $this->owner = User::factory()->create();
    }

    /**
     * @dataProvider unsafeUrls
     */
    public function test_creating_a_url_qr_rejects_non_http_schemes(string $url): void
    {
        $this->actingAs($this->owner);

        Livewire::test(QrxCreate::class)
            ->call('url')
            ->set('url', $url)
            ->call('generat')
            ->assertHasErrors(['url']);

        $this->assertSame(0, QrxCode::count());
    }

    public static function unsafeUrls(): array
    {
        return [
            'javascript' => ['javascript:alert(document.cookie)'],
            'mixed-case javascript' => ['JaVaScRiPt:alert(1)'],
            'data uri' => ['data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg=='],
            'vbscript' => ['vbscript:msgbox(1)'],
            'file' => ['file:///etc/passwd'],
            'no scheme' => ['example.com'],
        ];
    }

    public function test_creating_a_url_qr_accepts_https(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(QrxCreate::class)
            ->call('url')
            ->set('url', 'https://example.com/landing?ref=qr')
            ->call('generat')
            ->assertHasNoErrors(['url']);
    }

    public function test_scan_page_does_not_link_stored_javascript_urls(): void
    {
        $this->subscribe($this->owner);
        $qrxCode = $this->createQrxCode($this->owner, 'url', ['url' => 'javascript:alert(document.cookie)']);

        $this->get(route('qr.show', $qrxCode->code))
            ->assertOk()
            ->assertDontSee('href="javascript:', false);
    }

    public function test_scan_page_links_https_urls(): void
    {
        $this->subscribe($this->owner);
        $qrxCode = $this->createQrxCode($this->owner, 'url', ['url' => 'https://example.com/?a=1&b=2']);

        $this->get(route('qr.show', $qrxCode->code))
            ->assertOk()
            ->assertSee('href="https://example.com/?a=1&amp;b=2"', false);
    }

    public function test_scan_page_escapes_stored_text(): void
    {
        $this->subscribe($this->owner);
        $qrxCode = $this->createQrxCode($this->owner, 'text', ['text' => '"><script>alert(1)</script>']);

        $this->get(route('qr.show', $qrxCode->code))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_download_rejects_formats_outside_the_allowlist(): void
    {
        $qrxCode = $this->createQrxCode($this->owner);

        $this->actingAs($this->owner)
            ->get(route('dashboard.qr.download', ['id' => $qrxCode->id, 'type' => 'php', 'size' => 300]))
            ->assertSessionHasErrors('type');
    }

    public function test_download_clamps_the_requested_size(): void
    {
        $qrxCode = $this->createQrxCode($this->owner);

        $response = $this->actingAs($this->owner)
            ->get(route('dashboard.qr.download', ['id' => $qrxCode->id, 'type' => 'svg', 'size' => 100000]))
            ->assertOk();

        $this->assertStringContainsString('width="2000"', $response->streamedContent());
    }

    public function test_demo_preview_clamps_the_size(): void
    {
        Livewire::test(QrDemo::class)
            ->set('size', 100000)
            ->assertSee('width="500"', false)
            ->assertDontSee('width="100000"', false);
    }

    public function test_demo_is_rate_limited_per_ip(): void
    {
        for ($i = 0; $i < 120; $i++) {
            RateLimiter::hit('qr-demo:127.0.0.1', 60);
        }

        Livewire::test(QrDemo::class)
            ->set('text', 'hello')
            ->assertStatus(429);
    }
}
