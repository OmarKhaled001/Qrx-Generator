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
