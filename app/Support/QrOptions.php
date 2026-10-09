<?php

namespace App\Support;

/**
 * Server-side limits for user-supplied QR rendering options.
 */
final class QrOptions
{
    public const MIN_SIZE = 100;
    // Largest preview the Livewire builders render (the UI offers up to 500).
    public const MAX_PREVIEW_SIZE = 500;
    // Largest file the download endpoint will generate.
    public const MAX_DOWNLOAD_SIZE = 2000;
    public const MAX_MARGIN = 10;

    // Allowed download formats => Content-Type.
    public const DOWNLOAD_FORMATS = [
        'png' => 'image/png',
        'svg' => 'image/svg+xml',
        'eps' => 'application/postscript',
    ];

    public static function size(mixed $size, int $max = self::MAX_PREVIEW_SIZE): int
    {
        return max(self::MIN_SIZE, min($max, (int) $size));
    }

    public static function margin(mixed $margin): int
    {
        return max(0, min(self::MAX_MARGIN, (int) $margin));
    }
}
