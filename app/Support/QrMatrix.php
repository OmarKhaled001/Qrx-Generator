<?php

namespace App\Support;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;

/**
 * Raw QR modules for illustrations that animate parts of a code (the landing hero):
 * one SVG path for all dark modules plus a few "particles" that can fly apart.
 */
final class QrMatrix
{
    /**
     * @return array{size: int, path: string, particles: list<array{x: int, y: int, dx: float, dy: float}>}
     */
    public static function for(string $content, int $particles = 18): array
    {
        $matrix = Encoder::encode($content, ErrorCorrectionLevel::L())->getMatrix();
        $size = $matrix->getWidth();
        $path = '';
        $dark = [];

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($matrix->get($x, $y) === 1) {
                    $path .= "M{$x} {$y}h1v1h-1z";
                    $dark[] = [$x, $y];
                }
            }
        }

        // Evenly spaced picks; each drifts outward from the centre, further the further out it starts.
        $center = ($size - 1) / 2;
        $step = max(1, intdiv(count($dark), $particles));
        $picked = [];

        foreach (array_slice(array_values(array_filter($dark, fn ($module, $index) => $index % $step === 0, ARRAY_FILTER_USE_BOTH)), 0, $particles) as [$x, $y]) {
            $picked[] = ['x' => $x, 'y' => $y, 'dx' => round(($x - $center) * 1.6, 1), 'dy' => round(($y - $center) * 1.6, 1)];
        }

        return ['size' => $size, 'path' => $path, 'particles' => $picked];
    }
}
