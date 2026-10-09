<?php

namespace App\Services;

use App\Models\QrCode;
use App\Support\QrDesign;
use App\Support\QrOptions;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Eye\EyeInterface;
use BaconQrCode\Renderer\Eye\SimpleCircleEye;
use BaconQrCode\Renderer\Eye\SquareEye;
use BaconQrCode\Renderer\Image\EpsImageBackEnd;
use BaconQrCode\Renderer\Image\ImageBackEndInterface;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Module\DotsModule;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Module\RoundnessModule;
use BaconQrCode\Renderer\Module\SquareModule;
use BaconQrCode\Renderer\RendererStyle\EyeFill;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\Gradient;
use BaconQrCode\Renderer\RendererStyle\GradientType;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use InvalidArgumentException;

/**
 * Turns a QR code (or any content + design) into SVG, PNG or EPS using
 * bacon/bacon-qr-code 3 directly. Option mapping matches the old
 * simple-qrcode output (see spikes/qr-renderer). Always error-correction H.
 */
final class QrRenderer
{
    private const MODULE_SIZE = 0.5;

    public function forQrCode(QrCode $qrCode, string $format = 'svg', int $size = 300): string
    {
        return $this->render($qrCode->shortUrl(), $qrCode->design, $format, $size);
    }

    /**
     * SVG as a data URI, for <img src>. Inline SVGs would clash: bacon uses a
     * fixed gradient element id.
     *
     * @param  array<string, mixed>  $design
     */
    public function svgDataUri(string $content, array $design, int $size = 300): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode($this->render($content, $design, 'svg', $size));
    }

    /**
     * @param  array<string, mixed>  $design
     */
    public function render(string $content, array $design, string $format = 'svg', int $size = 300): string
    {
        $design = QrDesign::normalize($design);
        $module = $this->module($design['style']);

        $style = new RendererStyle(
            QrOptions::size($size, QrOptions::MAX_DOWNLOAD_SIZE),
            $design['margin'],
            $module,
            $this->eye($design['eye']),
            $this->fill($design),
        );

        $writer = new Writer(new ImageRenderer($style, $this->backEnd($format)));

        return $writer->writeString($content, 'UTF-8', ErrorCorrectionLevel::H());
    }

    private function module(string $style): ModuleInterface
    {
        return match ($style) {
            'dot' => new DotsModule(self::MODULE_SIZE),
            'round' => new RoundnessModule(self::MODULE_SIZE),
            default => SquareModule::instance(),
        };
    }

    private function eye(string $eye): EyeInterface
    {
        return $eye === 'circle' ? SimpleCircleEye::instance() : SquareEye::instance();
    }

    /**
     * @param  array{color: string, bg: string, gradient: array{from: string, to: string}|null}  $design
     */
    private function fill(array $design): Fill
    {
        $background = $this->rgb($design['bg']);
        $inherit = EyeFill::inherit();

        if ($design['gradient'] !== null) {
            $gradient = new Gradient($this->rgb($design['gradient']['from']), $this->rgb($design['gradient']['to']), GradientType::DIAGONAL());

            return Fill::withForegroundGradient($background, $gradient, $inherit, $inherit, $inherit);
        }

        return Fill::withForegroundColor($background, $this->rgb($design['color']), $inherit, $inherit, $inherit);
    }

    private function backEnd(string $format): ImageBackEndInterface
    {
        return match ($format) {
            'svg' => new SvgImageBackEnd,
            'png' => new ImagickImageBackEnd('png'),
            'eps' => new EpsImageBackEnd,
            default => throw new InvalidArgumentException("Unsupported format [$format]."),
        };
    }

    private function rgb(string $hex): Rgb
    {
        if (! preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex, $m)) {
            throw new InvalidArgumentException("Invalid colour [$hex].");
        }

        return new Rgb((int) hexdec($m[1]), (int) hexdec($m[2]), (int) hexdec($m[3]));
    }
}
