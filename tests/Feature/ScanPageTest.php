<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesQrxCodes;
use Tests\TestCase;

/**
 * Characterization of the public scan page (/x/{code}) before the v2 rewrite.
 */
class ScanPageTest extends TestCase
{
    use CreatesQrxCodes, RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
    }

    /**
     * @dataProvider typesAndVisibleContent
     */
    public function test_each_type_renders_its_content(string $type, string $visible): void
    {
        $this->subscribe($this->owner);
        $qrxCode = $this->createQrxCode($this->owner, $type);

        $this->get(route('qr.show', $qrxCode->code))
            ->assertOk()
            ->assertSee($visible);
    }

    public static function typesAndVisibleContent(): array
    {
        return [
            'text' => ['text', 'hello'],
            'url' => ['url', 'https://example.com'],
            'phone' => ['phone', '+201000000000'],
            'sms' => ['message', 'Hi there'],
            'email' => ['email', 'hello@example.com'],
            'vcard' => ['vcard', 'Jane Doe'],
            'wifi' => ['wifi', 'Office'],
        ];
    }

    public function test_a_scan_increments_the_counter(): void
    {
        $this->subscribe($this->owner);
        $qrxCode = $this->createQrxCode($this->owner);

        $this->get(route('qr.show', $qrxCode->code))->assertOk();
        $this->get(route('qr.show', $qrxCode->code))->assertOk();

        $this->assertSame(2, (int) $qrxCode->fresh()->scan_count);
    }

    public function test_a_paused_qr_shows_not_found_and_is_not_counted(): void
    {
        $this->subscribe($this->owner);
        $qrxCode = $this->createQrxCode($this->owner);
        $qrxCode->forceFill(['status' => 'pause'])->save();

        $this->get(route('qr.show', $qrxCode->code))
            ->assertOk()
            ->assertSee('Page not found');

        $this->assertSame(0, (int) $qrxCode->fresh()->scan_count);
    }

    public function test_reaching_the_plan_scan_limit_expires_the_qr(): void
    {
        $this->subscribe($this->owner, scanCount: 2);
        $qrxCode = $this->createQrxCode($this->owner);
        $qrxCode->forceFill(['scan_count' => 2])->save();

        $this->get(route('qr.show', $qrxCode->code))
            ->assertOk()
            ->assertSee('Page not found');

        $this->assertSame('exp', $qrxCode->fresh()->status);
    }

    public function test_an_unknown_code_shows_not_found(): void
    {
        $this->get(route('qr.show', 'qx-000000'))
            ->assertOk()
            ->assertSee('Page not found');
    }
}
