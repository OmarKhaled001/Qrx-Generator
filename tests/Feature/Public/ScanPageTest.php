<?php

namespace Tests\Feature\Public;

use App\Enums\QrType;
use App\Models\Plan;
use App\Models\QrCode;
use App\Models\Scan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\SubscribesUsers;
use Tests\TestCase;

class ScanPageTest extends TestCase
{
    use RefreshDatabase, SubscribesUsers;

    private const PHONE = ['HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) Mobile/15E148'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->freePlan();
    }

    private function scan(QrCode $qrCode)
    {
        return $this->withServerVariables(self::PHONE)->get('/x/'.$qrCode->code);
    }

    /**
     * @return array<string, array{0: QrType, 1: string}>
     */
    public static function typesAndContent(): array
    {
        return [
            'text' => [QrType::Text, 'Welcome to our shop!'],
            'phone' => [QrType::Phone, '+201000000000'],
            'sms' => [QrType::Sms, 'Hi there'],
            'email' => [QrType::Email, 'hello@example.com'],
            'vcard' => [QrType::Vcard, 'Jane Doe'],
            'wifi' => [QrType::Wifi, 'Office'],
            'location' => [QrType::Location, 'Cairo Tower'],
        ];
    }

    #[DataProvider('typesAndContent')]
    public function test_each_type_shows_its_content(QrType $type, string $visible): void
    {
        $qrCode = QrCode::factory()->ofType($type)->create();

        $this->scan($qrCode)->assertOk()->assertSee($visible)->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_website_codes_redirect_to_the_site_and_count_the_scan(): void
    {
        $qrCode = QrCode::factory()->ofType(QrType::Url)->create();

        $this->scan($qrCode)->assertRedirect('https://example.com/menu');

        $this->assertSame(1, $qrCode->fresh()->scan_count);
        $this->assertSame(1, Scan::count());
    }

    public function test_link_previews_see_the_content_but_are_not_counted(): void
    {
        $qrCode = QrCode::factory()->ofType(QrType::Text)->create();

        $this->withServerVariables(['HTTP_USER_AGENT' => 'WhatsApp/2.23'])->get('/x/'.$qrCode->code)->assertOk();

        $this->assertSame(0, $qrCode->fresh()->scan_count);
    }

    public function test_paused_codes_are_unavailable_and_not_counted(): void
    {
        $qrCode = QrCode::factory()->ofType(QrType::Text)->paused()->create();

        $this->scan($qrCode)->assertNotFound()->assertSee(__('This code is temporarily unavailable'));

        $this->assertSame(0, $qrCode->fresh()->scan_count);
    }

    public function test_codes_over_their_plan_limit_are_unavailable(): void
    {
        $qrCode = QrCode::factory()->ofType(QrType::Text)->create(['scan_count' => 100]);

        $this->scan($qrCode)->assertNotFound()->assertSee(__('This code is temporarily unavailable'));
        $this->assertSame(100, $qrCode->fresh()->scan_count);

        // Upgrading brings it back without touching the QR code.
        $this->subscribe($qrCode->user, Plan::factory()->create(['scan_limit' => 5000]));
        $this->scan($qrCode->fresh())->assertOk();
    }

    public function test_unknown_codes_return_404(): void
    {
        $this->get('/x/doesnotexist')->assertNotFound()->assertSee(__('We could not find this code'));
    }

    public function test_stored_unsafe_urls_are_never_linked_and_text_is_escaped(): void
    {
        $unsafe = QrCode::factory()->create(['type' => QrType::Url, 'payload' => ['url' => 'javascript:alert(document.cookie)']]);
        $this->scan($unsafe)->assertOk()->assertDontSee('href="javascript:', false)->assertSee(__('This link cannot be opened safely.'));

        $text = QrCode::factory()->create(['type' => QrType::Text, 'payload' => ['text' => '"><script>alert(1)</script>']]);
        $this->scan($text)->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_contact_cards_can_be_saved_as_vcf(): void
    {
        $qrCode = QrCode::factory()->ofType(QrType::Vcard)->create();

        $response = $this->get('/x/'.$qrCode->code.'/contact.vcf')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/vcard; charset=utf-8')
            ->assertDownload('jane-doe.vcf');

        $vcf = $response->getContent();
        $this->assertStringStartsWith("BEGIN:VCARD\r\nVERSION:3.0\r\nFN:Jane Doe", $vcf);
        $this->assertStringContainsString('ADR;TYPE=WORK:;;Cairo\, Egypt', $vcf);
        $this->assertStringEndsWith("END:VCARD\r\n", $vcf);

        $this->get('/x/'.QrCode::factory()->ofType(QrType::Text)->create()->code.'/contact.vcf')->assertNotFound();
    }

    public function test_the_scan_page_renders_in_arabic(): void
    {
        $qrCode = QrCode::factory()->ofType(QrType::Phone)->create();

        $this->withSession(['locale' => 'ar'])->withServerVariables(self::PHONE)->get('/x/'.$qrCode->code)
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('اتصل الآن');
    }
}
