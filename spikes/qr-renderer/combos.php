<?php

// Option combinations rendered by both the old (simple-qrcode / bacon 2) and the
// new (bacon 3 direct) renderer. Keys become file names.

$url = 'https://qrx.example/x/qx-ab12cd34';
$solid = ['color' => '#1f2937', 'bg' => '#ffffff', 'gradient' => null];
$gradient = ['color' => '#1f2937', 'bg' => '#fffbeb', 'gradient' => ['#2563eb', '#db2777']];

$combos = [];

foreach (['square', 'dot', 'round'] as $style) {
    foreach (['square', 'circle'] as $eye) {
        foreach (['solid' => $solid, 'gradient' => $gradient] as $fillName => $fill) {
            $combos["{$style}-{$eye}-{$fillName}"] = [
                'content' => $url, 'style' => $style, 'eye' => $eye, 'margin' => 1, 'ec' => 'H',
            ] + $fill;
        }
    }
}

$combos['margin-0'] = ['content' => $url, 'style' => 'square', 'eye' => 'square', 'margin' => 0, 'ec' => 'H'] + $solid;
$combos['margin-4'] = ['content' => $url, 'style' => 'square', 'eye' => 'square', 'margin' => 4, 'ec' => 'H'] + $solid;
// The dashboard preview renders without an explicit error-correction level (bacon default).
$combos['preview-default-ec'] = ['content' => $url, 'style' => 'round', 'eye' => 'circle', 'margin' => 1, 'ec' => null] + $solid;
$combos['arabic-text'] = ['content' => 'مرحبا بالعالم — Qrx', 'style' => 'square', 'eye' => 'square', 'margin' => 1, 'ec' => 'H'] + $solid;

return $combos;
