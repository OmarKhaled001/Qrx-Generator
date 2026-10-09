# M0 spike: QR rendering on bacon/bacon-qr-code 3

**Question:** can v2 drop `simplesoftwareio/simple-qrcode` (unmaintained since 2021, pinned to bacon 2) and call `bacon/bacon-qr-code` 3 directly, with no visible change to users' QR codes?

**Answer: yes.** [`src/QrRenderer.php`](src/QrRenderer.php) is the prototype that becomes `App\Services\QrRenderer` in M2.

## Method

- [`combos.php`](combos.php) defines 16 option sets:
  - 3 module styles × 2 eye shapes × solid/gradient fill;
  - margin 0 and 4;
  - the bacon-default error-correction level that the dashboard preview uses;
  - an Arabic UTF-8 text.
- [`render-old.php`](render-old.php) renders each set exactly as today's app does: simple-qrcode on bacon 2.0.8, PHP 8.3.
- [`render-new.php`](render-new.php) renders each set with the prototype: bacon 3.1.1 directly, PHP **8.5.11**, imagick 3.8.1.
- [`compare.php`](compare.php) compares bytes and pixels and writes [`parity-sheet.png`](parity-sheet.png): one row per set in the order below; columns are old, new, and an ImageMagick diff where red would mark differences.
- Every new output was then decoded with `zbarimg` (a real QR decoder) and checked against the encoded content.

## Results

| Check | Result |
|---|---|
| PNG, all 16 sets | **Byte-identical** old vs new |
| SVG, all 16 sets | Bytes differ, **rasterised pixel-identical** (0 px). Bacon 3 rotates the eye shape into each corner, so the 2nd/3rd eye paths start at a different vertex; the geometry is the same |
| EPS, square/dot styles (10 sets) | Rasterised pixel-identical |
| EPS, **round** style (6 sets) | **Different, and bacon 3 is the correct one.** Bacon 2 leaves white holes throughout round modules in EPS; bacon 3 matches the SVG reference. See [`eps-round-fix.png`](eps-round-fix.png) (old \| new \| SVG reference). Anyone downloading round-style EPS today gets a damaged image |
| Decodes correctly (`zbarimg`) | **48 / 48** new outputs (16 sets × PNG/SVG/EPS), Arabic text included |

Row order in the sheet: square-square-solid, square-square-gradient, square-circle-solid, square-circle-gradient, dot-square-solid, dot-square-gradient, dot-circle-solid, dot-circle-gradient, round-square-solid, round-square-gradient, round-circle-solid, round-circle-gradient, margin-0, margin-4, preview-default-ec, arabic-text.

## Findings that change the plan

1. **Hosting needs `imagick`.** Bacon 3's GD renderer (`GDLibRenderer`) rejects gradients, so styled PNGs require imagick. imagick 3.8.1 builds and works on PHP 8.5.11.
2. **Ghostscript is only needed for tests that rasterise EPS.** Generating EPS doesn't need it.
3. **Option mapping to keep in M2:** module size 0.5 for dot/round, diagonal gradient, no eye style → eye follows the module, UTF-8 encoding, error correction H for stored/downloaded codes.
4. **Preview error correction:** today's preview uses bacon's default (L) while the saved code uses H, so the preview can look less dense than the file you download. v2 previews at H so the preview matches what you get.

## Reproduce

```sh
# from the project root (old app vendor installed)
docker build --build-arg PHP_VERSION=8.3 -t qrx-php83 docker/php-test
docker build --build-arg PHP_VERSION=8.5 -t qrx-php85 docker/php-test
docker run --name old -v "$PWD":/app -w /app qrx-php83 php spikes/qr-renderer/render-old.php
(cd spikes/qr-renderer && composer install)
docker run --name new -v "$PWD":/app -w /app qrx-php85 sh -c 'php spikes/qr-renderer/render-new.php && php spikes/qr-renderer/compare.php'
docker rm old new
```
