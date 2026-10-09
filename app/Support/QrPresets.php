<?php

namespace App\Support;

/**
 * Ready-made looks for the builder, the landing demo and the style gallery.
 * Every preset keeps contrast high enough to scan (see QrPresetsTest).
 */
final class QrPresets
{
    /**
     * @return array<string, array{label: string, style: string, eye: string, color: string, bg: string, gradient: array{from: string, to: string}|null}>
     */
    public static function all(): array
    {
        return [
            'classic' => ['label' => __('Classic'), 'style' => 'square', 'eye' => 'square', 'color' => '#0f172a', 'bg' => '#ffffff', 'gradient' => null],
            'ocean' => ['label' => __('Ocean'), 'style' => 'round', 'eye' => 'circle', 'color' => '#0e7490', 'bg' => '#ffffff', 'gradient' => ['from' => '#0e7490', 'to' => '#7c3aed']],
            'violet' => ['label' => __('Violet dots'), 'style' => 'dot', 'eye' => 'circle', 'color' => '#6d28d9', 'bg' => '#ffffff', 'gradient' => null],
            'forest' => ['label' => __('Forest'), 'style' => 'round', 'eye' => 'square', 'color' => '#166534', 'bg' => '#f0fdf4', 'gradient' => null],
            'sunset' => ['label' => __('Sunset'), 'style' => 'dot', 'eye' => 'circle', 'color' => '#c2410c', 'bg' => '#fff7ed', 'gradient' => ['from' => '#c2410c', 'to' => '#be185d']],
            'midnight' => ['label' => __('Midnight'), 'style' => 'square', 'eye' => 'circle', 'color' => '#1e1b4b', 'bg' => '#eef2ff', 'gradient' => null],
        ];
    }

    /**
     * @return array{label: string, style: string, eye: string, color: string, bg: string, gradient: array{from: string, to: string}|null}|null
     */
    public static function find(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    /**
     * The preset as a QrDesign array (no label).
     *
     * @return array<string, mixed>
     */
    public static function design(string $key): array
    {
        $preset = self::find($key) ?? self::all()['classic'];

        return QrDesign::normalize(array_diff_key($preset, ['label' => true]));
    }
}
