<?php

// Renders every combo with the prototype v2 renderer (bacon 3, no simple-qrcode).
// Run with the spike's own vendor: php spikes/qr-renderer/render-new.php

require __DIR__.'/vendor/autoload.php';

use Spike\QrRenderer;

$combos = require __DIR__.'/combos.php';
$out = __DIR__.'/out/new';
@mkdir($out, 0777, true);

$renderer = new QrRenderer;

foreach ($combos as $name => $c) {
    foreach (['svg', 'png', 'eps'] as $format) {
        file_put_contents("$out/$name.$format", $renderer->render($c['content'], $c, $format, 300, $c['ec']));
    }
}

echo 'new: rendered '.count($combos).' combos x 3 formats on PHP '.PHP_VERSION."\n";
