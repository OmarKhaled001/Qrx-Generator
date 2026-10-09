<?php

namespace Tests\Feature\Qr;

use App\Models\Plan;
use App\Models\QrCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\SubscribesUsers;
use Tests\TestCase;

class QrDownloadTest extends TestCase
{
    use RefreshDatabase, SubscribesUsers;

    private QrCode $qrCode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->freePlan();
        $this->qrCode = QrCode::factory()->create(['name' => 'Spring Flyer']);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function formats(): array
    {
        return [
            'png' => ['png', 'image/png', "\x89PNG"],
            'svg' => ['svg', 'image/svg+xml', '<?xml'],
            'eps' => ['eps', 'application/postscript', '%!PS-Adobe'],
        ];
    }

    #[DataProvider('formats')]
    public function test_paid_users_download_every_format(string $format, string $contentType, string $magic): void
    {
        $this->subscribe($this->qrCode->user, Plan::factory()->create());

        $response = $this->actingAs($this->qrCode->user)->get("/qr/{$this->qrCode->id}/download?format={$format}&size=500")
            ->assertOk()
            ->assertHeader('Content-Type', $contentType)
            ->assertDownload("spring-flyer.{$format}");

        $this->assertStringStartsWith($magic, $response->getContent());
    }

    public function test_free_users_get_png_and_svg_but_not_eps(): void
    {
        $this->actingAs($this->qrCode->user);

        $this->get("/qr/{$this->qrCode->id}/download?format=png")->assertOk();
        $this->get("/qr/{$this->qrCode->id}/download?format=svg")->assertOk();
        $this->get("/qr/{$this->qrCode->id}/download?format=eps")->assertForbidden();
    }

    public function test_size_is_clamped_and_formats_are_allowlisted(): void
    {
        $this->actingAs($this->qrCode->user);

        $svg = $this->get("/qr/{$this->qrCode->id}/download?format=svg&size=100000")->assertOk()->getContent();
        $this->assertStringContainsString('width="2000"', $svg);

        $png = $this->get("/qr/{$this->qrCode->id}/download?format=png&size=10")->getContent();
        $this->assertSame(100, getimagesizefromstring($png)[0]);

        $this->get("/qr/{$this->qrCode->id}/download?format=php")->assertSessionHasErrors('format');
    }

    public function test_other_users_and_guests_cannot_download(): void
    {
        $this->actingAs(User::factory()->create())->get("/qr/{$this->qrCode->id}/download?format=png")->assertForbidden();

        auth()->logout();
        $this->get("/qr/{$this->qrCode->id}/download?format=png")->assertRedirect('/login');
    }
}
