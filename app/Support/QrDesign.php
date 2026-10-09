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

        $gradient = $design['gradient'];
        $gradient = is_array($gradient) && filled($gradient['from'] ?? null) && filled($gradient['to'] ?? null)
            ? ['from' => strtolower($gradient['from']), 'to' => strtolower($gradient['to'])]
            : null;

        return [
            'style' => in_array($design['style'], self::STYLES, true) ? $design['style'] : 'square',
            'eye' => in_array($design['eye'], self::EYES, true) ? $design['eye'] : 'square',
            'color' => strtolower((string) $design['color']),
            'bg' => strtolower((string) $design['bg']),
            'gradient' => $gradient,
            'margin' => QrOptions::margin($design['margin']),
        ];
    }
}
