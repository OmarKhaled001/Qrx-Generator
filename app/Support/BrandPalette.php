<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * Reads colour palettes from resources/css/tokens.css so the Filament admin
 * uses exactly the same brand colours as the app: tokens.css stays the single
 * source of truth.
 */
final class BrandPalette
{
    /**
     * @return array<int, string> shade (50…950) => oklch(...) string, as Filament expects
     */
    public static function get(string $name): array
    {
        static $cache = [];

        if (isset($cache[$name])) {
            return $cache[$name];
        }

        preg_match_all(
            '/--color-'.preg_quote($name, '/').'-(\d{2,3}):\s*(oklch\([^)]*\))\s*;/',
            File::get(resource_path('css/tokens.css')),
            $matches,
            PREG_SET_ORDER,
        );

        $palette = [];
        foreach ($matches as [, $shade, $value]) {
            $palette[(int) $shade] = $value;
        }
        ksort($palette);

        return $cache[$name] = $palette;
    }
}
