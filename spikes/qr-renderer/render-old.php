<?php

// Renders every combo the way the current app does (simple-qrcode on bacon 2).
// Run from the project root's vendor: php spikes/qr-renderer/render-old.php

require __DIR__.'/../../vendor/autoload.php';

use SimpleSoftwareIO\QrCode\Generator;

$combos = require __DIR__.'/combos.php';
$out = __DIR__.'/out/old';
@mkdir($out, 0777, true);

$rgb = fn (string $hex) => sscanf(ltrim($hex, '#'), '%02x%02x%02x');

foreach ($combos as $name => $c) {
    foreach (['svg', 'png', 'eps'] as $format) {
        $qr = (new Generator)->format($format)->size(300)->encoding('UTF-8')
            ->style($c['style'])->margin($c['margin'])->eye($c['eye']);

        if ($c['ec'] !== null) {
            $qr->errorCorrection($c['ec']);
        }

        [$br, $bg, $bb] = $rgb($c['bg']);
        $qr->backgroundColor($br, $bg, $bb);

        if ($c['gradient']) {
            [$fr, $fg, $fb] = $rgb($c['gradient'][0]);
            [$tr, $tg, $tb] = $rgb($c['gradient'][1]);
            $qr->gradient($fr, $fg, $fb, $tr, $tg, $tb, 'diagonal');
        } else {
            [$r, $g, $b] = $rgb($c['color']);
            $qr->color($r, $g, $b);
        }

        file_put_contents("$out/$name.$format", (string) $qr->generate($c['content']));
    }
}

echo 'old: rendered '.count($combos)." combos x 3 formats\n";
