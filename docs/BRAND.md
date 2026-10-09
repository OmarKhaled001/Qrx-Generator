# Qrx brand

## The mark

The icon is a QR finder pattern (rounded ring + centre module) that doubles as the letter **Q**: two modules step out of the bottom-right corner as its tail. It sits on a rounded tile with a teal-to-violet diagonal gradient. The wordmark `qrx` is drawn with the same 4.5-unit rounded stroke as the ring, so mark and word read as one family.

- The product name is written **Qrx** in running text. The wordmark is always lowercase.
- The logo is never mirrored, including in Arabic/RTL layouts. Latin letters and the Q's tail keep their direction.

## Files

All brand files live in [`public/brand/`](../public/brand). Browser-default icons sit in `public/`.

| File | Use |
|---|---|
| `brand/qrx-logo.svg` | Full logo on light backgrounds (wordmark `#121825`) |
| `brand/qrx-logo-dark.svg` | Full logo on dark backgrounds (wordmark white) |
| `brand/qrx-logo-mono.svg` | One-colour logo (black; recolour freely) for print, stamps, embossing |
| `brand/qrx-icon.svg` | Icon only, on the gradient tile |
| `brand/qrx-mark.svg` | Glyph only, one colour, no tile (watermarks, small monochrome UI) |
| `brand/qrx-logo.png`, `brand/qrx-logo-dark.png` | 464×192 raster logo for email and places without SVG |
| `brand/qrx-icon-{16,32,48,192,512}.png` | Raster icon sizes (manifest, ICO source) |
| `brand/qrx-icon-maskable-512.png` | Android adaptive icon (full bleed, glyph inside the 80% safe zone) |
| `brand/og-image-en.png`, `brand/og-image-ar.png` | 1200×630 social share cards, chosen by the page language |
| `favicon.svg`, `favicon.ico` (16/32/48) | Browser tab icons |
| `apple-touch-icon.png` | 180×180, full-bleed tile (iOS rounds the corners) |
| `site.webmanifest` | Install metadata and icons |

## Where it is used

- **App:** `<x-ui.logo />` ([resources/views/components/ui/logo.blade.php](../resources/views/components/ui/logo.blade.php)) inlines the same drawing. The wordmark takes `currentColor`, so it follows light and dark mode. `<x-ui.logo mark />` renders the icon only.
- **Head tags:** [partials/head.blade.php](../resources/views/partials/head.blade.php) carries the favicons, apple-touch-icon, manifest, theme colour and Open Graph/Twitter tags (`og:image` and `og:locale` follow the language).
- **Admin:** `brandLogo` and `darkModeBrandLogo` in [AdminPanelProvider](../app/Providers/Filament/AdminPanelProvider.php).
- **Error pages:** [components/error-page.blade.php](../resources/views/components/error-page.blade.php).

## Colours

The design tokens in [resources/css/tokens.css](../resources/css/tokens.css) are the source of truth (OKLCH). Hex values are given for tools that need them.

| Role | Token | Hex |
|---|---|---|
| Gradient start | `brand-500` | `#00a5ab` |
| Primary (buttons, links, theme colour) | `brand-600` | `#00828b` |
| Primary hover / ink on light | `brand-700` | `#006a74` |
| Gradient end / accent | `accent-600` | `#7543e9` |
| Ink (text, light-mode wordmark) | `neutral-900` | `#121825` |
| Page background (light) | `neutral-50` | `#f9fafb` |
| Page background (dark) | `neutral-950` | `#050912` |

The gradient always runs from the top-left (`brand-500`) to the bottom-right (`accent-600`). The glyph on the tile is always white.

## Typography

- **English:** Plus Jakarta Sans (variable).
- **Arabic:** Cairo (variable).
- Both are self-hosted through `@fontsource-variable`. The font switches with `html[lang]`; see [DECISIONS.md](DECISIONS.md) R1.

## Rules

- **Clear space:** keep at least the height of the centre module (⅙ of the icon height) free on every side.
- **Minimum size:** icon 16 px; full logo 80 px wide (wordmark 24 px tall). Below that, use the icon only.
- **Don't:**
  - recolour the gradient or the glyph;
  - add outlines or shadows to the mark (the share card's soft shadow is the only exception);
  - stretch the logo;
  - set the wordmark in a font;
  - place the light logo on dark backgrounds.

## Regenerating the PNGs

The SVGs are the masters. After editing one, rebuild the front end so the OG cards use current fonts and CSS. With the app serving `APP_URL`, run the Playwright image (its version must match `playwright-core` in package.json):

```sh
npm run build
docker run --rm --network host --user "$(id -u):$(id -g)" -e HOME=/tmp -v "$PWD":/app -w /app \
  mcr.microsoft.com/playwright:v1.64.0-noble node scripts/brand-assets.mjs
```

Then rebuild `favicon.ico` from the 16/32/48 PNGs. Any PHP with Imagick works, for example the `qrx-php85` test image:

```sh
docker run --rm --user "$(id -u):$(id -g)" -v "$PWD":/app -w /app qrx-php85 php -r '$ico = new Imagick(); foreach ([16, 32, 48] as $s) { $i = new Imagick("public/brand/qrx-icon-$s.png"); $i->setImageFormat("ico"); $ico->addImage($i); } $ico->writeImages("public/favicon.ico", true);'
```

The OG card source is the local-only route `/dev/og-image/{en|ar}` ([resources/views/dev/og-image.blade.php](../resources/views/dev/og-image.blade.php)).
