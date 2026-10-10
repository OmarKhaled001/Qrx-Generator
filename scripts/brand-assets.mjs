// Rasterises the brand SVGs in public/brand into the PNG icon set and the Open Graph cards.
// Run inside the Playwright image (see docs/BRAND.md); the app must be serving APP_URL for the OG cards.
import { readFile, writeFile } from 'node:fs/promises';
import { chromium } from 'playwright-core';

const root = new URL('../public/', import.meta.url);
const brand = new URL('brand/', root);
const appUrl = process.env.APP_URL ?? 'http://localhost:8010';

const icon = await readFile(new URL('qrx-icon.svg', brand), 'utf8');
const logo = await readFile(new URL('qrx-logo.svg', brand), 'utf8');
const logoDark = await readFile(new URL('qrx-logo-dark.svg', brand), 'utf8');

// Full-bleed tile: iOS and Android apply their own corner mask.
const fullBleed = icon.replace('rx="12" fill="url(#qrx-tile)"', 'fill="url(#qrx-tile)"');
// Maskable: shrink the glyph into the 80% safe zone.
const maskable = fullBleed
    .replace('<rect x="9.75"', '<g transform="translate(24 24) scale(0.75) translate(-24.75 -24.75)"><rect x="9.75"')
    .replace('</g>\n</svg>', '</g></g>\n</svg>');

const targets = [
    ...[16, 32, 48, 192, 512].map((size) => ({ svg: icon, file: `brand/qrx-icon-${size}.png`, width: size, height: size })),
    { svg: maskable, file: 'brand/qrx-icon-maskable-512.png', width: 512, height: 512 },
    { svg: fullBleed, file: 'apple-touch-icon.png', width: 180, height: 180 },
    { svg: logo, file: 'brand/qrx-logo.png', width: 464, height: 192 },
    { svg: logoDark, file: 'brand/qrx-logo-dark.png', width: 464, height: 192 },
];

const browser = await chromium.launch();
const page = await browser.newPage();

for (const { svg, file, width, height } of targets) {
    await page.setViewportSize({ width, height });
    await page.setContent(`<style>html,body{margin:0}svg{display:block;width:${width}px;height:${height}px}</style>${svg}`);
    await writeFile(new URL(file, root), await page.screenshot({ omitBackground: true }));
    console.log(`public/${file}`);
}

await page.setViewportSize({ width: 1200, height: 630 });
for (const locale of ['en', 'ar']) {
    await page.goto(`${appUrl}/dev/og-image/${locale}`, { waitUntil: 'networkidle' });
    await page.evaluate(() => document.fonts.ready);
    await writeFile(new URL(`brand/og-image-${locale}.png`, root), await page.screenshot());
    console.log(`public/brand/og-image-${locale}.png`);
}

await browser.close();
