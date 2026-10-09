<?php

// Compares out/old and out/new: byte equality per format, pixel difference of the
// PNGs, and writes a side-by-side review sheet (old | new | diff) to parity-sheet.png.

$combos = require __DIR__.'/combos.php';
$old = __DIR__.'/out/old';
$new = __DIR__.'/out/new';

$rows = [];
$sheet = new Imagick;

foreach (array_keys($combos) as $name) {
    $same = [];
    foreach (['svg', 'png', 'eps'] as $format) {
        $same[$format] = file_get_contents("$old/$name.$format") === file_get_contents("$new/$name.$format");
    }

    $a = new Imagick("$old/$name.png");
    $b = new Imagick("$new/$name.png");
    $a->setOption('fuzz', '2%');
    [$diffImage, $differentPixels] = $a->compareImages($b, Imagick::METRIC_ABSOLUTEERRORMETRIC);
    $total = $a->getImageWidth() * $a->getImageHeight();

    $rows[] = sprintf(
        '| %-20s | %-3s | %-3s | %-3s | %6d px (%.3f %%) |',
        $name,
        $same['svg'] ? 'yes' : 'no',
        $same['png'] ? 'yes' : 'no',
        $same['eps'] ? 'yes' : 'no',
        $differentPixels,
        100 * $differentPixels / $total,
    );

    $row = new Imagick;
    foreach ([$a, $b, $diffImage] as $image) {
        $image->setImageFormat('png');
        $image->thumbnailImage(200, 200, true);
        $row->addImage($image);
    }
    $row->resetIterator();
    // One row per combo, in the same order as the printed table (no fonts in the container for labels).
    $sheet->addImage($row->appendImages(false));
}

$sheet->resetIterator();
$out = $sheet->appendImages(true);
$out->setImageFormat('png');
$out->writeImage(__DIR__.'/parity-sheet.png');

echo "| combo                | svg | png | eps | differing pixels (2% fuzz) |\n";
echo "|----------------------|-----|-----|-----|----------------------------|\n";
echo implode("\n", $rows)."\n";
echo "\nReview sheet (old | new | diff): spikes/qr-renderer/parity-sheet.png\n";
