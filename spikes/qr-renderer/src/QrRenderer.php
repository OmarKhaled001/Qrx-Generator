<?php

namespace Spike;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Eye\EyeInterface;
use BaconQrCode\Renderer\Eye\ModuleEye;
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
 * Prototype of the v2 QrRenderer: bacon/bacon-qr-code 3 used directly, with the
 * same option mapping simple-qrcode applied (module size 0.5, diagonal gradients).
 *
 * @phpstan-type Design array{style: string, eye: ?string, margin: int, color: string, bg: string, gradient: ?array{0: string, 1: string}}
 */
final class QrRenderer
{
    private const MODULE_SIZE = 0.5;

    /**
     * @param  Design  $design
     */
    public function render(string $content, array $design, string $format = 'svg', int $size = 300, ?string $errorCorrection = 'H'): string
    {
        $style = new RendererStyle(
            $size,
            $design['margin'],
            $this->module($design['style']),
            $this->eye($design['eye'], $design['style']),
            $this->fill($design),
        );

        $writer = new Writer(new ImageRenderer($style, $this->backEnd($format)));

        return $writer->writeString($content, 'UTF-8', $this->errorCorrection($errorCorrection));
    }

    private function module(string $style): ModuleInterface
    {
        return match ($style) {
            'square' => SquareModule::instance(),
            'dot' => new DotsModule(self::MODULE_SIZE),
            'round' => new RoundnessModule(self::MODULE_SIZE),
            default => throw new InvalidArgumentException("Unknown module style [$style]."),
        };
    }

    private function eye(?string $eye, string $style): EyeInterface
    {
        return match ($eye) {
            'square' => SquareEye::instance(),
            'circle' => SimpleCircleEye::instance(),
            null => new ModuleEye($this->module($style)),
            default => throw new InvalidArgumentException("Unknown eye style [$eye]."),
        };
    }

    /**
     * @param  Design  $design
     */
    private function fill(array $design): Fill
    {
        $background = $this->rgb($design['bg']);
        $inherit = EyeFill::inherit();

        if ($design['gradient'] !== null) {
            $gradient = new Gradient($this->rgb($design['gradient'][0]), $this->rgb($design['gradient'][1]), GradientType::DIAGONAL());

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

    private function errorCorrection(?string $level): ?ErrorCorrectionLevel
    {
        return match ($level === null ? null : strtoupper($level)) {
            null => null,
            'L' => ErrorCorrectionLevel::L(),
            'M' => ErrorCorrectionLevel::M(),
            'Q' => ErrorCorrectionLevel::Q(),
            'H' => ErrorCorrectionLevel::H(),
            default => throw new InvalidArgumentException("Unknown error correction level [$level]."),
        };
    }

    private function rgb(string $hex): Rgb
    {
        if (! preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex, $m)) {
            throw new InvalidArgumentException("Invalid colour [$hex].");
        }

        return new Rgb(hexdec($m[1]), hexdec($m[2]), hexdec($m[3]));
    }
}
