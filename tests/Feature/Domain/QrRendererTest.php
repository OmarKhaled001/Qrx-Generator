<?php

namespace Tests\Feature\Domain;

use App\Models\QrCode;
use App\Services\QrRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class QrRendererTest extends TestCase
{
    use RefreshDatabase;

    private QrRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new QrRenderer;
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function formats(): array
    {
        return [
            'svg' => ['svg', '<svg'],
            'png' => ['png', "\x89PNG"],
            'eps' => ['eps', '%!PS-Adobe'],
        ];
    }

    #[DataProvider('formats')]
    public function test_it_renders_every_format(string $format, string $magic): void
    {
        $output = $this->renderer->render('https://qrx.test/x/abc', [], $format, 300);

        $this->assertStringContainsString($magic, substr($output, 0, 200));
    }

    public function test_it_renders_every_style_eye_and_gradient(): void
    {
        foreach (['square', 'dot', 'round'] as $style) {
            foreach (['square', 'circle'] as $eye) {
                foreach ([null, ['from' => '#0e7490', 'to' => '#7c3aed']] as $gradient) {
                    $svg = $this->renderer->render('content', compact('style', 'eye', 'gradient'), 'svg', 200);
                    $this->assertStringStartsWith('<?xml', $svg);
                }
            }
        }
    }

    public function test_png_has_the_requested_size(): void
    {
        $png = $this->renderer->render('content', [], 'png', 400);

        [$width, $height] = getimagesizefromstring($png);
        $this->assertSame([400, 400], [$width, $height]);
    }

    public function test_size_is_clamped_to_the_download_maximum(): void
    {
        $svg = $this->renderer->render('content', [], 'svg', 100000);

        $this->assertStringContainsString('width="2000"', $svg);
    }

    public function test_a_qr_code_encodes_its_short_url(): void
    {
        $qrCode = QrCode::factory()->create();

        $this->assertSame(
            $this->renderer->render($qrCode->shortUrl(), $qrCode->design),
            $this->renderer->forQrCode($qrCode),
        );
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $this->renderer->svgDataUri('x', []));
    }

    public function test_unknown_formats_are_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->renderer->render('content', [], 'gif');
    }
}
