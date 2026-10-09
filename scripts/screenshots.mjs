// Captures the main pages in English and Arabic at desktop and mobile widths into docs/screenshots.
// Run inside the Playwright image against the seeded local app (see README "Screenshots").
//   ONLY=landing,scan  limit to some pages      THEME=dark  dark mode instead of light
import { mkdir } from 'node:fs/promises';
import { chromium } from 'playwright-core';

const appUrl = process.env.APP_URL ?? 'http://localhost:8010';
const out = new URL('../docs/screenshots/', import.meta.url);
const only = process.env.ONLY?.split(',');
const theme = process.env.THEME ?? 'light';
const password = 'password';

const shots = [
    { name: 'landing', path: '/', full: true },
    { name: 'login', path: '/login' },
    { name: 'dashboard', path: '/dashboard', user: 'demo@qrx.test', full: true },
    { name: 'qr-list', path: '/qr', user: 'demo@qrx.test', full: true },
    { name: 'builder', path: '/qr/create', user: 'demo@qrx.test', full: true },
    { name: 'qr-detail', path: '/qr/6', user: 'demo@qrx.test', full: true },
    { name: 'billing', path: '/billing', user: 'demo@qrx.test', full: true },
    { name: 'profile', path: '/profile', user: 'demo@qrx.test', full: true },
    { name: 'scan', path: '/x/kJKkSF6n', full: true },
    { name: 'admin', path: '/admin', user: 'admin@qrx.test', full: true },
].filter((shot) => !only || only.includes(shot.name));

const viewports = {
    desktop: { width: 1440, height: 900 },
    mobile: { width: 390, height: 844, isMobile: true, hasTouch: true },
};

async function setLocale(page, locale) {
    await page.evaluate(async (locale) => {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        await fetch(`/locale/${locale}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': token } });
    }, locale);
}

async function settle(page) {
    await page.waitForLoadState('networkidle');
    await page.evaluate(() => document.fonts.ready);
    // Walk the page so scroll-driven and lazy content reaches its final state, then return to the top.
    await page.evaluate(async () => {
        for (let y = 0; y < document.body.scrollHeight; y += window.innerHeight / 2) {
            window.scrollTo(0, y);
            await new Promise((resolve) => setTimeout(resolve, 60));
        }
        window.scrollTo(0, 0);
    });
    await page.waitForTimeout(400);
}

await mkdir(out, { recursive: true });
const browser = await chromium.launch();

// Arabic first so the accounts end on English, their seeded preference.
for (const locale of ['ar', 'en']) {
    for (const [device, viewport] of Object.entries(viewports)) {
        const { width, height, ...options } = viewport;
        const context = await browser.newContext({ viewport: { width, height }, ...options, locale, colorScheme: theme });
        await context.addInitScript((theme) => localStorage.setItem('qrx-theme', theme), theme);
        const page = await context.newPage();
        let signedIn = null;

        for (const shot of shots) {
            if (shot.user && shot.user !== signedIn) {
                await context.clearCookies();
                const login = shot.path.startsWith('/admin') ? '/admin/login' : '/login';
                await page.goto(appUrl + login);
                await page.fill('input[type="email"]', shot.user);
                await page.fill('input[type="password"]', password);
                await Promise.all([page.waitForURL((url) => !url.pathname.endsWith('/login')), page.press('input[type="password"]', 'Enter')]);
                signedIn = shot.user;
            } else if (!shot.user && signedIn) {
                await context.clearCookies();
                signedIn = null;
            }

            await page.goto(appUrl + (shot.path.startsWith('/x/') ? '/' : shot.path));
            await setLocale(page, locale);
            await page.goto(appUrl + shot.path);
            await settle(page);

            // A page wider than the viewport scrolls sideways on phones: report it loudly.
            const overflow = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
            if (overflow > 0) {
                console.warn(`OVERFLOW ${shot.name} ${locale} ${device}: ${overflow}px wider than the viewport`);
            }

            const file = `${shot.name}-${locale}-${device}${theme === 'dark' ? '-dark' : ''}.png`;
            await page.screenshot({ path: new URL(file, out).pathname, fullPage: shot.full ?? false });
            console.log(`docs/screenshots/${file}`);
        }

        await context.close();
    }
}

await browser.close();
