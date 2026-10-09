# Decisions log

Judgement calls made during the autonomous v2 build, one line of reasoning each. Newest at the bottom of each phase. **Review these.**

## Given by the owner (final)

- All five proposed behaviour changes from the M0 review are approved (email verification required; 404 for unknown/paused codes; only active plans purchasable; preview at error-correction H; round-style EPS fix).
- Limited free tier: yes. Location QR: keep. MP3 QR: drop. Brand: own product; hossam-x-studios references removed; new palette and typography. Arabic + RTL included.
- Nothing is pushed; everything stays local.

## M1 — Skeleton

- **Fresh Laravel 13.35 skeleton replaces the old app on `redesign/v2`** (old app stays on `master` until M9). The old code and its 128 MB of theme assets therefore leave the branch now, not at M9.
- **Skeleton's `AGENTS.md`/`CLAUDE.md` not kept:** generic "install PHP" agent boilerplate, nothing project-specific.
- **Fortify two-factor auth and passkeys disabled** (and their migrations removed): not in the approved MVP; both can be switched on later in `config/fortify.php`.
- **Email verification enabled in Fortify:** approved behaviour change #1.
- **Tests are ported per phase instead of copied in and skipped:** the old tests reference classes and tables that don't exist in v2, so skipped copies would never run unchanged. `docs/behaviour-inventory.md` maps every old test to its v2 counterpart, and the M9 gate is "every inventory row has a passing v2 test".
- **Tests run in Docker (`bin/test`, PHP 8.5.11 + imagick + gd + intl)** so PNG/EPS paths are always exercised; local PHP 8.4 lacks imagick.
- **Larastan at level 5** in CI: catches real type errors without forcing generics annotations everywhere.
- **Free tier (L5) and Location QR (L7) are built inside the MVP** (M2/M5/M7) instead of later phases: both are now decided, and adding them later would rework the quota logic and the type system just built.

## M2 — Domain

- **"Expired" is derived, not stored:** a QR is over its limit when `scan_count >= owner's plan scan_limit`. Upgrading brings every QR back at once, with no status rows to repair. Stored status is only active/paused.
- **Scan limit stays lifetime-per-QR** (as in the old app), not per month.
- **Bots and link previews are not counted** (WhatsApp/Telegram/Slack previews, crawlers, curl). The old app counted every page load, which inflated numbers.
- **Short codes: 8 random characters without look-alikes** (no 0/O/1/l/I), ~10^13 combinations, uniqueness-checked with retry.
- **Country comes from a trusted proxy header** (`CF-IPCountry` by default, configurable via `QRX_COUNTRY_HEADER`); without a CDN it stays empty. No GeoIP database is bundled.
- **visitor_hash** = HMAC-SHA256(IP + user agent) keyed with HMAC(app key, date): unique visitors per day, unlinkable across days, no raw IP stored.
- **No stored QR image files:** QR images are rendered on demand from the design (fast, a few ms). Removes the old app's file/DB drift.
- **Error correction is always H** (approved change #4); size clamped to 100–2000 px.
- **vCard gains organization, job title and website**; Wi-Fi gains "open network" and "hidden network". Both are standard fields the old forms lacked.
- **Free plan: 3 QR codes, 100 scans per QR.** Paid plans seeded as Starter $9 (25 QRs / 5k scans), Business $29 (100 / 50k), Agency $79 (500 / 500k), monthly. Prices are display values; the real charge is whatever the Stripe price says.
- **Stripe price IDs live in `config/qrx.php`** (env placeholders), not `env()` in the seeder, so they survive `config:cache`.
- **QR creation locks the user row** so two simultaneous creates can't exceed the quota.

## M3 — UI foundations

- **Brand palette:** deep teal primary (OKLCH hue ~200) with a violet accent for gradients and the logo, cool slate neutrals. Distinct from Filament's amber and from the "purple QR app" cliché; works in light and dark.
- **Typography:** IBM Plex Sans + IBM Plex Sans Arabic (a matched pair), self-hosted via @fontsource (no Google/Bunny CDN). Arabic gets 1.7 line-height.
- **Dark mode ships now** (tokens make it a token-only change), defaulting to the OS setting with a toggle; this pulls part of L3 forward.
- **Arabic strings are added phase by phase** (`lang/ar.json`, enforced by TranslationCoverageTest) instead of all at L8, so no phase leaves untranslated UI behind. L8 then covers validation/auth messages, Filament and RTL QA.
- **Three CI guardrails as PHPUnit tests:** no physical left/right utilities, no untranslated view text, every string has an Arabic translation with matching placeholders.
- **Eloquent strict-ish mode outside production:** lazy-loading (N+1) and silently discarded attributes throw. Not "missing attributes", because the in-memory free-plan fallback intentionally has unset columns.
- **Component gallery at `/dev/ui`** exists from M3 (local/testing only) and is extended in L3.
- **Skeleton `welcome.blade.php` removed;** `/` shows a minimal translated hero until the M6 landing page.

## M4 — Auth

- **Registration keeps the old fields** (phone, country, city, area) as required, per the inventory; on the profile they are optional so social-login accounts (L1) are not blocked.
- **Email verification is required** for the dashboard (approved change #1); registration lands on "Check your inbox".
- **Deleting an account cancels any Stripe subscription immediately** (`cancelNow`) before the user and their QR codes are deleted, so nobody keeps being billed for a deleted account.
- **Login is throttled to 5 attempts per minute per email + IP** (Fortify default kept).
- **New users get the language they signed up in** (`users.locale`).
- **Arabic plurals** use Laravel's range syntax with Arabic's zero/one/two/few/many forms rather than English singular/plural.

## M5 — QR management

- **One builder for create and edit** (type → content → design, sticky live preview). Switching type resets only the content fields; the short code never changes on edit.
- **Editing is allowed even at the plan limit** (the old app blocked it, audit M3); only creating is capped.
- **At the limit, the builder shows an upgrade prompt** and the save button is disabled; the server enforces it too.
- **EPS download is a paid-plan feature** (matches the plan feature lists); PNG and SVG for everyone. Download sizes: 500 / 1000 / 2000 px (clamped 100–2000 server-side).
- **Contrast warning** when the code/background contrast is below 3:1 (WCAG luminance), since low-contrast codes often fail to scan. It warns but does not block.
- **Invalid colours fall back to defaults when rendering**, so a half-typed hex in the live preview never errors; validation still rejects them on save.
- **The Wi-Fi password is never shown on the dashboard detail page** (it is shown on the public scan page, which is its purpose).
- **Delete uses the browser's native confirm dialog** (`wire:confirm`), translated; accessible and dependency-free.
- **"Limit reached" is a list filter**, derived from the owner's plan rather than stored.

## M6 — Public

- **Website QR codes redirect straight to the site** (302) after counting the scan. The old app showed an intermediate page; a redirect is what people expect from a QR code. Only http/https targets redirect; anything else is shown as text, never linked.
- **Paused and over-limit codes show the same "temporarily unavailable" page** (HTTP 404, approved change #2), so scanners never learn about the owner's plan. Unknown codes get "we could not find this code" (404).
- **Contact cards offer "Save contact"** as a vCard 3.0 download (`/x/{code}/contact.vcf`), which phones import natively; the old app only displayed the fields.
- **Wi-Fi scan page:** shows the network and a reveal/copy password field with instructions. Web pages cannot join a network, so the old "Connect" link (which never worked in browsers) is gone.
- **Location scan page:** "Open in Google Maps" / "Open in Apple Maps" links; no embedded map (avoids loading a third-party map for every scan).
- **Scan pages send `Cache-Control: no-store`** (every scan reaches the server and is counted) and `X-Robots-Tag: noindex`.
- **`/x/{code}` is rate-limited to 300 requests/minute per IP**, generous because venues share one IP behind NAT.
- **Error pages use a standalone layout** (no session, forms or Livewire) so the 500 page cannot itself fail; a `Route::fallback` routes unknown URLs through the web middleware so 404s are localised.
- **Landing page (MVP):** hero, the 8 types, pricing from the database (active plans only). L4 adds the live demo and contact form.

## M7 — Billing

- **Cashier only, no custom transactions table** (approved). Invoices and payment methods live in Stripe's Billing Portal ("Manage billing").
- **Existing subscribers switch plans with `swap()`** (prorated by Stripe) instead of a second Checkout, so nobody ends up with two subscriptions.
- **New limits apply when Stripe's webhook arrives** (usually seconds); the success page says so instead of granting limits on the redirect, which could be faked.
- **Monthly prices** ($9 / $29 / $79) as display values; real amounts come from the Stripe prices configured in `.env` (`STRIPE_PRICE_*`), placeholders until you create them.
- **Promotion codes are allowed** in Checkout (`allowPromotionCodes`), as in the old app.
- **Stripe webhooks are CSRF-exempt** (`stripe/*`), as Cashier requires; signature verification is on as soon as `STRIPE_WEBHOOK_SECRET` is set.
- **Checkout URL uses the plan slug** (`/billing/checkout/business`); free, inactive and unknown plans return 404.

## M8 — Admin

- **Panel access = `users.is_admin`** (approved); the separate admins table and guard are gone. `User` defaults `is_admin` to false in memory too, so a fresh model is never a "null" admin.
- **`is_admin` stays non-fillable;** the Filament user pages set it with `forceFill`. Admins cannot remove their own admin flag or delete their own account.
- **Admins do not create or edit QR content.** They can rename, pause/resume or delete any QR code; content belongs to the owner's builder. (The old admin had 7 per-type resources.)
- **Plan price is edited in dollars and stored in cents;** a plan needs a Stripe price ID unless it is the free plan.
- **Admin panel uses the brand teal, IBM Plex Sans, dark mode, and follows the user's language** (SetLocale added to the panel middleware; Filament ships Arabic).
- **`php artisan app:make-admin email@…`** creates or promotes an administrator on real servers (the demo seeder only runs outside production).
- **Admin stats widget** (users, paying subscribers, QR codes, scans in 30 days) is in M8 rather than L6, because it is small and useful from day one; L6 adds revenue reporting.

## M9 — MVP cutover

- **"Browser smoke test" = an HTTP + Livewire end-to-end journey test** (register → verify → create → scan → change → download, plus the RTL pass over every dashboard page). A real browser pass is done once at the end of the run (final report).
- **JSON key order is not asserted:** MySQL normalises JSON object key order, so tests compare payloads by keys and values. Nothing in the app depends on payload key order.
- **MVP merged into `master` locally** with a merge commit; nothing is pushed (origin is public). Later phases continue on `redesign/v2` and are merged again at the end.

## L1 — Social login

- **Google and Facebook buttons appear only when their client ID is configured;** unconfigured providers return 404.
- **A social sign-in with the email of an existing account links to that account** instead of failing or duplicating it. Google/Facebook verify emails, so this is safe; the email is lower-cased first.
- **New social accounts are created verified** and without a password; such users can set one via "Forgot password", and delete their account by typing their email (no password to confirm with).
- **No email from the provider → clear error,** no account (Facebook accounts can withhold the email).
- **Found during L1:** `tests/Unit` was an empty directory that git does not track, so after switching branches PHPUnit refused to start. It now holds real unit tests (vCard escaping, contrast, clamping).

## L2 — Analytics

- **Per-QR breakdown (daily chart, unique visitors, devices, top countries) is a paid-plan feature,** matching the seeded plan feature lists; free users keep totals and see an upgrade card. The dashboard's 30-day chart is for everyone.
- **Charts are server-rendered SVG** with a screen-reader table, no JavaScript chart library. Time runs left to right in both languages (a mirrored time axis confuses more than it helps).
- **"Unique visitors" = distinct daily visitor hashes, summed over days,** because the hash key rotates daily (privacy by design); the same person on two days counts twice.
- **Country names come from ICU (`intl`)** in the user's language, e.g. "مصر" for EG in Arabic.

## L3 — Design completion

- **`tokens.css` is the single source for the admin too:** `App\Support\BrandPalette` reads the OKLCH brand and neutral palettes from it for Filament's `colors()`, and the Filament theme imports the same file. Change a token once; app and admin follow.
- **Admin fonts are self-hosted** (Filament's default would load Bunny Fonts from a CDN).
- **Dark mode already shipped in M3,** so L3 covered the admin theme and the gallery (now including the chart).

## L4 — Marketing site

- **Landing demo needs no account and stores nothing;** previews and SVG download only (SVG works without imagick). Over the per-IP limit it shows a friendly notice instead of erroring.
- **Contact form:** validation, a hidden honeypot field (bots get a fake "sent"), and 3 messages per 10 minutes per IP. Messages go to an admin inbox ("Messages", unread badge, mark as read, reply by email) rather than email, so nothing depends on a mail setup.

## L6 — Admin reporting

- **Revenue comes from Stripe's `invoice.paid` webhook,** recorded by a listener on Cashier's `WebhookReceived` event into a `payments` table (idempotent per invoice; zero-amount invoices skipped). It reflects money actually paid, unlike the old app's pre-payment "transactions".
- **Admin dashboard:** "Revenue (30 days)" stat plus a 6-month revenue/sign-ups chart. Amounts are shown in USD (the configured Cashier currency).
- **Add `invoice.paid` to the Stripe webhook events** (README lists it).

## L8 — Arabic

- **UI strings were translated phase by phase** (360+ in `lang/ar.json`, enforced by TranslationCoverageTest), so L8 added Laravel's own message files: `lang/ar/{validation,auth,passwords,pagination}.php`, with Arabic attribute names.
- **Arabic copy is Modern Standard Arabic,** written in-house rather than by a professional translator. Worth a review by a native copywriter before launch, especially marketing headlines.
- **"QR" stays in Latin script** in Arabic text (standard usage); brand, file formats (PNG/SVG/EPS) and Stripe likewise.
- **Digits:** Laravel's `Number` helpers follow the locale, so Arabic pages show Arabic-Indic digits in counts; phone numbers, URLs, codes and emails are forced left-to-right with `dir="ltr"`.
- **The admin panel uses Filament's built-in Arabic** and switches to RTL from the user's language.

## Final run

- **Real-browser visual check not done:** this machine has no Chrome, and installing one was out of scope for an unattended run. Every page is covered by HTTP/Livewire tests (incl. the RTL pass over all dashboard pages), but nobody has looked at the pages yet. Please click through the list in the final report.
- **Local server runs on port 8010,** because port 8000 is used by another app on this machine; it runs inside the PHP 8.5 Docker image so PNG/EPS downloads (imagick) work. `.env` `APP_URL` was set to `http://localhost:8010`.

# Round 2 — UI and brand upgrade (branch `ui/brand-upgrade`)

## R1 — Typography

- **Arabic: Cairo (variable, 200–1000), self-hosted** via `@fontsource-variable/cairo` (as requested).
- **English: Plus Jakarta Sans (variable), self-hosted.** It shares Cairo's open, geometric-humanist shapes, generous x-height and round terminals, so mixed Arabic/Latin lines (emails, URLs, plan names) look like one family. Inter was the alternative; it reads colder and narrower next to Cairo.
- **Fonts switch by locale:** `html:lang(ar)` swaps the `--font-sans` token to put Cairo first; app and Filament admin both follow. IBM Plex (round 1) is removed.

## R2 — Logo and brand assets

- **Mark = QR finder pattern that is also a "Q"** (ring + centre module, two modules as the tail). One idea, readable at 16 px; a full 3-finder QR glyph turned to mush below 32 px.
- **Wordmark is drawn, not typeset** (same 4.5-unit round stroke as the ring), so the logo never depends on a font being loaded and can't drift from the mark.
- **Logo is never mirrored in RTL**; only layout around it is. Inline `<x-ui.logo>` uses `currentColor` for the wordmark so one component serves light and dark.
- **Two OG cards (en, ar)** picked by page language rather than one bilingual card; each is readable at feed-thumbnail size.
- **Rasterising with Playwright's Docker image + `playwright-core`** (no browser download on `npm install`). Chrome isn't installed on the host; the image is pinned to the same version as the package.
- **`APP_NAME` default changed to `Qrx`** (the local `.env` said `Laravel`, which leaked into titles and the admin brand name).

## R3 — Design-system polish

- **Bug found by the screenshots: Alpine never loaded on pages without a Livewire component** (dashboard, login, billing, profile, scan pages), so the mobile menu was stuck open and dropdowns, theme toggle and copy buttons were dead there. Layouts now load `@livewireStyles`/`@livewireScripts` themselves; `ScriptsTest` checks every layout loads it exactly once.
- **Elevation is tinted and layered** (`--shadow-tint` swaps in dark mode) plus a 1px top highlight in dark mode, where shadows disappear. Primary buttons get a teal glow on hover; everything presses down slightly (`active:scale`). `prefers-reduced-motion` still zeroes all transitions.
- **Time-series charts run in the reading direction**: mirrored in Arabic (newest on the left), client chart and admin Chart.js alike. Replaces the earlier "always left to right" call; Arabic readers scan right to left, and the axis labels now follow.
- **Prices use one format in both languages ("$29")** via `App\Support\Money`. ICU's Arabic `US$ 29` was reordered by the bidi algorithm into "$US 29".
- **Style presets** (`App\Support\QrPresets`) shared by the builder, the landing demo and (R4) the style gallery; a test keeps every preset at ≥ 4.5:1 contrast so they scan reliably.
- **Plan name shown under a "Current plan" label, never composed into a sentence** (`:plan plan`): Arabic adjective agreement made composed phrases ungrammatical.
- **Auth pages are split-screen on large screens**; the brand panel states what the product does (no testimonials or numbers).
- **Screen-reader tables wrap in an `sr-only` div**: a `<table class="sr-only">` keeps its row heights and was stretching pages by ~250px.

## R4 — Landing sections

- **Sections:**
  - hero
  - how it works (3 steps)
  - use cases by industry (tabs)
  - the 8 types
  - style gallery
  - live demo (mini builder)
  - analytics preview
  - pricing with a comparison table
  - FAQ
  - contact
  - final CTA

  Each section is a partial in `resources/views/landing/`.
- **No testimonials, customer logos, user counts or statistics.** Omitted entirely rather than shipped as hidden placeholders: nothing to forget to remove before launch. A test fails if the page says "testimonial", "Trusted by", "customers" or "happy users".
- **The analytics preview uses visibly labelled sample data** ("Example data" badge + "Illustration with sample data, not real results." caption): it shows what the product does, it doesn't claim results.
- **The style gallery shows real, scannable codes** rendered from the shared presets (cached for a day; rendering is the slow part); "Try this style" loads the preset into the live demo via a Livewire event.
- **The comparison table is computed from plan data and capabilities** (limits, EPS/analytics on paid plans, "Priority support" from the plan's feature list), so it can't drift from what the app enforces.
- **FAQ answers describe actual behaviour** (downgrade keeps links; codes past the free scan limit show a notice; no IPs stored). Native `<details>` for keyboard and screen-reader support without JavaScript.
- **Free plan price shown as "$0 / month"** instead of the word "Free" next to the plan named "Free".
- **Contrast fixes found while reviewing:** brand-600 nudged from L 0.556 to 0.545 (white on primary buttons was 4.43:1, now 4.6:1); a deeper gradient (`brand-700 → accent-700`, ≥ 6.3:1) for surfaces with white text (CTA band, auth panel, "Most popular").
- **Bug found by the mobile screenshots:** the comparison table's `sr-only` labels are absolutely positioned and escaped their `overflow-x-auto` scroller, making every page 600px wide on phones. The scroller is now `relative`; `scripts/screenshots.mjs` warns about any page wider than the viewport.

## R5 — Hero illustration and scroll animation

- **GSAP + ScrollTrigger** (as suggested). A lighter CSS scroll-timeline version can't scrub a pinned, multi-stage story in all browsers yet (Firefox and Safari lack `animation-timeline`). The 45 KB (gzip) chunk is code-split and fetched only on the landing page and only when the visitor allows motion.
- **The server renders the finished frame**; the timeline animates *from* the first frame. Visitors with reduced motion or without JavaScript see the complete picture, and GSAP is never downloaded for them. With motion allowed, the scene stays hidden until the first frame is drawn (a CSS fallback reveals it after 2.5s if the script fails), so the finished frame never flashes first.
- **Large screens:** the hero pins for 110% of the viewport height while the scroll scrubs the story:
  1. the phone swings in;
  2. the viewfinder locks on;
  3. a scan line sweeps and the printed code pings;
  4. the code breaks into particles;
  5. the screen builds the content.

  **Small screens:** no pinning (scroll-jacking on a phone is hostile). A shorter version without particles plays once when the scene comes into view.
- **Only transforms and opacity are animated.** Measured in headless Chromium while scrubbing: 60 fps average, p95 frame 16.7 ms, no long tasks; layout shift 0.0065 (good is < 0.1).
- **RTL:** the drawing contains no text and is mirrored as a whole with `rtl:-scale-x-100`, so the phone comes from the reading direction's end and the on-screen UI reads right to left. The check mark is flipped back because it isn't directional (a test fails if `<text>` ever appears inside).
- **The printed code is a real QR code for the site** (built from the raw matrix by `App\Support\QrMatrix` so individual modules can animate).
