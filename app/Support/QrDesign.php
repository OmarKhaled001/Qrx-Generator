<?php

namespace App\Support;

/**
 * The visual design of a QR code, stored as JSON on qr_codes.design:
 * {style, eye, color, bg, gradient: {from, to}|null, margin}.
 */
final class QrDesign
{
    public const STYLES = ['square', 'dot', 'round'];

    public const EYES = ['square', 'circle'];

    private const HEX = 'regex:/^#[0-9a-fA-F]{6}$/';

    /**
     * @return array{style: string, eye: string, color: string, bg: string, gradient: null, margin: int}
     */
    public static function defaults(): array
    {
        return [
            'style' => 'square',
            'eye' => 'square',
            'color' => '#0f172a',
            'bg' => '#ffffff',
            'gradient' => null,
            'margin' => 1,
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(string $prefix = 'design.'): array
    {
        return [
            $prefix.'style' => ['required', 'in:'.implode(',', self::STYLES)],
            $prefix.'eye' => ['required', 'in:'.implode(',', self::EYES)],
            $prefix.'color' => ['required', self::HEX],
            $prefix.'bg' => ['required', self::HEX],
            $prefix.'gradient' => ['nullable', 'array'],
            $prefix.'gradient.from' => ["required_with:{$prefix}gradient", self::HEX],
            $prefix.'gradient.to' => ["required_with:{$prefix}gradient", self::HEX],
            $prefix.'margin' => ['required', 'integer', 'between:0,'.QrOptions::MAX_MARGIN],
        ];
    }

    /**
     * Fill in defaults, drop unknown keys, clamp the margin and lowercase colours.
     *
     * @param  array<string, mixed>  $design
     * @return array{style: string, eye: string, color: string, bg: string, gradient: array{from: string, to: string}|null, margin: int}
     */
    public static function normalize(array $design): array
    {
        $design = array_merge(self::defaults(), array_intersect_key($design, self::defaults()));

        $defaults = self::defaults();
        $gradient = $design['gradient'];
        $gradient = is_array($gradient) && self::isHex($gradient['from'] ?? null) && self::isHex($gradient['to'] ?? null)
            ? ['from' => strtolower($gradient['from']), 'to' => strtolower($gradient['to'])]
            : null;

        return [
            'style' => in_array($design['style'], self::STYLES, true) ? $design['style'] : 'square',
            'eye' => in_array($design['eye'], self::EYES, true) ? $design['eye'] : 'square',
            // Invalid colours (e.g. half-typed in the builder) fall back to the defaults.
            'color' => self::isHex($design['color']) ? strtolower($design['color']) : $defaults['color'],
            'bg' => self::isHex($design['bg']) ? strtolower($design['bg']) : $defaults['bg'],
            'gradient' => $gradient,
            'margin' => QrOptions::margin($design['margin']),
        ];
    }

    public static function isHex(mixed $value): bool
    {
        return is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1;
    }

    /**
     * WCAG contrast ratio between two hex colours (1 to 21).
     */
    public static function contrastRatio(string $first, string $second): float
    {
        $luminance = function (string $hex): float {
            $channels = array_map(
                fn (string $pair) => hexdec($pair) / 255,
                str_split(ltrim($hex, '#'), 2),
            );
            $linear = array_map(fn (float $c) => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4, $channels);

            return 0.2126 * $linear[0] + 0.7152 * $linear[1] + 0.0722 * $linear[2];
        };

        [$light, $dark] = [max($luminance($first), $luminance($second)), min($luminance($first), $luminance($second))];

        return round(($light + 0.05) / ($dark + 0.05), 2);
    }

    /**
     * The weakest contrast between the code colour(s) and the background.
     *
     * @param  array<string, mixed>  $design
     */
    public static function weakestContrast(array $design): float
    {
        $design = self::normalize($design);
        $foregrounds = $design['gradient'] ? array_values($design['gradient']) : [$design['color']];

        return min(array_map(fn (string $color) => self::contrastRatio($color, $design['bg']), $foregrounds));
    }
}
