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
