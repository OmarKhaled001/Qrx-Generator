# Qrx-Generator — Redesign & Modernization Plan

_Revision 2, 2026-10-09. Strategy, schema and the MVP roadmap are approved; work happens on `redesign/v2`. **M0 is complete** (see §5). Revision 1 (the A-vs-B comparison) is in git history at `ce4ddf1`._

## 0. Status

| Done | Commit | Result |
|---|---|---|
| Security fixes (ownership policies, Livewire 2.12.7, URL/size/format checks, demo rate limit) | `26cc548` | On `master` (local, not pushed) |
| Migrations as single source of truth; all 62 tests green; `migrate:fresh --seed` clean on MySQL 8.4 strict | `0484b97` | On `master` |
| Audit + plan moved to `docs/` | `ce4ddf1` | On `master` |
| **M0:** behaviour inventory (87/87 tests), QR renderer spike (parity proven), test Dockerfile | M0 commit | On `redesign/v2`: [behaviour-inventory.md](behaviour-inventory.md), [spike results](../spikes/qr-renderer/README.md) |

**Target stack** (verified 2026-10-09 from php.net and Packagist): **PHP 8.5.11** · Laravel 13.35 · Livewire 4.4 · Filament 5.10 · Cashier 16.8 · Tailwind 4.3 · Vite 8.3. There is no version ceiling.

**Required host PHP extensions:** `imagick` (PNG/EPS QR output), `gd`, `intl`, `pdo_mysql`, `bcmath`.

---

## 1. Decisions

### Approved

| # | Decision | Consequence in this plan |
|---|---|---|
| 1 | **Strategy B**: fresh Laravel 13 project on `redesign/v2`, in this repository. **MVP first**, everything else in later phases | §5 roadmap |
| 2a | **One `qr_codes` table**: `type` column + JSON `payload` validated per type (+ JSON `design`) | Replaces `qrx_codes`, `styles` and the 7 `feature_*` tables (§3.1) |
| 2b | **`scans` table** for analytics | Scans are *recorded* from the MVP; charts come in a later phase |
| 2c | **Single `users` table with `is_admin`**, enforced in Filament's `canAccessPanel()` | The `admins` table and second guard are not carried over |
| 3a | **Fortify** for authentication | Headless; our own Livewire/Blade views |
| 3b | **Cashier for billing; no custom transactions logic** | See below |
| 5 | `fix/schema-and-tests` merged; docs in `docs/` | Done |

**On Cashier replacing the transactions table:** I see no strong reason against it, so it's adopted.
- Cashier already stores what the app needs locally (`subscriptions` and `subscription_items`) and keeps them current through Stripe webhooks.
- The old `transactions` table was written *before* payment, so it was never accurate.
- Invoices and receipts come from Stripe on demand (`$user->invoices()`) and through the Stripe Billing Portal.
- **The one thing lost** is local revenue totals for the admin dashboard widget. In the MVP, the admin links to the Stripe Dashboard for revenue. If a local revenue report is wanted later, it's a small webhook listener that stores `invoice.paid` events (phase L6).

### Still open: your three decisions arrived as template placeholders

Nothing in the MVP's first steps (M0–M2) depends on them, so they can be decided at the first review point. Here is what each option costs:

| Decision | Option 1 | Option 2 | Needed by |
|---|---|---|---|
| **Free tier** | **No free tier:** nothing extra; users without a subscription see plans | **Limited free tier** (e.g. N QR codes, M scans/month): a "free" `Plan` row with no Stripe price, quota checks in the `Subscriptions` service, a downgrade path when a paid plan ends. **+1.5–2 days** if in the MVP, or a later phase (L5) | M2.3 (quota rules) |
| **Location & MP3** | **Drop them:** nothing extra | **Keep them:** Location is a small payload type (~0.5 day). MP3 needs file upload, storage, an audio player on the scan page and upload limits (~2 days). Planned as a later phase (L7), not MVP | L7 only |
| **Brand** | **hossam-x-studios is the theme author:** remove all references and propose a new palette (the plan's default: a deep teal-blue direction, §4) | **It is the client's brand:** keep it, take its colours, logo and fonts as the primitive palette, and design around it. You'd need to send the brand assets | M3.1 (tokens) |

---

## 2. MVP scope

**MVP = a working, end-to-end v2:** register/login → subscribe with Stripe → create and manage QR codes → a printed QR scans correctly → admins manage users, plans and QR codes. When the MVP is done, `redesign/v2` replaces the old app on `master`, including deleting the Bootstrap themes and the 128 MB of assets.

| Area | In the MVP | Later phase |
|---|---|---|
| Auth | Register, login, logout, forgot/reset password, email verification, profile (name, email, password, delete account) | Google/Facebook login (L1) |
| QR codes | All 7 current types (URL, text, phone, SMS, email, Wi-Fi, vCard), each with per-type validation. One create/edit builder with live preview and design options (colour, gradient, style, eye, margin). List with status filter and search. Detail page with copy short URL, download PNG/SVG/EPS, pause/resume, delete | Location / MP3 (L7, if kept) |
| Scan page `/x/{code}` | All 7 types, plus friendly paused / expired / owner-lapsed / not-found states. Scan recorded in `scans` | — |
| Analytics | Recording only (counter + `scans` rows) | Charts, overview dashboard, per-QR stats (L2) |
| Billing | Pricing page, Stripe Checkout, webhooks (Cashier), quota and expiry enforced from the subscription, Billing Portal link | Free tier (L5, if chosen); local revenue report (L6) |
| Admin (Filament 5) | Users (incl. `is_admin`), Plans, QR codes. Default Filament look with our primary colour | Contacts, dashboard widgets, full token theme (L3) |
| Public site | Minimal landing: hero, short feature list, pricing, footer | Live demo, features section, contact form (L4) |
| Design system | Tokens (light mode), fonts, 3 layouts, the ~15 components the MVP pages use, Heroicons | Dark mode, `/dev/ui` gallery, remaining components, Filament theme (L3) |
| i18n / RTL | **RTL-ready from day one:** every string in `__()` → `lang/en.json`; `dir` from the locale; logical properties only (CI lint); RTL pseudo-locale smoke test | Arabic translation + locale switcher (L8) |

**Features the old app has that are absent from `master` between MVP cutover and their later phase:**
- social login;
- the landing-page live demo;
- the contact form;
- the dashboard charts;
- the admin Contacts resource and widgets.

Nothing is live, so this is acceptable. Each returns in the phase named above.

---

## 3. Architecture (unchanged in substance from revision 1, updated for the decisions)

### 3.1 Data model

| Table | Columns (key ones) | Notes |
|---|---|---|
| `users` | name, email, password, `is_admin` (bool), phone/country/city/area (nullable), provider/provider_id (nullable, for L1), locale, Cashier columns | `User implements FilamentUser`; `canAccessPanel()` returns `$this->is_admin` |
| `plans` | title, stripe_product_id, stripe_price_id, price, qr_limit, scan_limit, is_active, sort_order, features (JSON) | Maps a Stripe price to quotas |
| `qr_codes` | user_id, name, `type` (enum), `payload` (JSON), `design` (JSON), `code` (unique short code), `status` (enum), `scan_count`, image_path | One model; `payload` cast to a per-type value object |
| `scans` | qr_code_id, scanned_at, device (mobile/tablet/desktop), referrer_host, country (nullable), visitor_hash (nullable) | **No raw IP** (your rule): country from a trusted proxy header (e.g. `CF-IPCountry`); `visitor_hash` = HMAC-SHA256(IP + user agent) with a daily-rotating key. Obvious bots skipped |
| Cashier | `subscriptions`, `subscription_items` | Source of truth for "is subscribed / which plan / until when" |
| `contacts` | — | Comes back with the contact form in L4 |

- **Enums:** `QrType` (7 cases, extensible) and `QrStatus` (active / paused / expired).
- **Per-type payloads:** each type is one class with its validation rules, the string encoded in the QR, and the Blade partial for the scan page. Adding a type means one class, one form partial and one scan partial.

### 3.2 Code layout

```
app/Enums, app/Models, app/Policies/QrCodePolicy
app/Actions/      CreateQrCode, UpdateQrCode, DeleteQrCode, ToggleQrStatus, RecordScan
app/Services/     QrRenderer (bacon/bacon-qr-code 3.x), Subscriptions (plan, quota, expiry)
app/Support/      QrOptions (ported), QrPayloads/* (one per type)
app/Livewire/     Dashboard/*  (thin; call Actions)
app/Filament/     admin panel only
```

Livewire components and controllers stay thin. Only `QrRenderer` touches the QR library, and only `Subscriptions` answers "what may this user do?".

### 3.3 QR rendering

`simplesoftwareio/simple-qrcode` is unmaintained (last release 2021) and pinned to bacon 2.x, so `QrRenderer` calls **`bacon/bacon-qr-code` 3.1.1** directly. The **M0 spike** proves visual parity for every style × eye × gradient combination, rendered side by side. The fallback is `endroid/qr-code` 6.x.

### 3.4 Billing flow (Cashier)

1. **Pricing → checkout:** the pricing page leads to `CheckoutController`, which calls `$user->newSubscription('default', $plan->stripe_price_id)->checkout(...)`.
2. **Webhooks:** Cashier's `/stripe/webhook` keeps `subscriptions` in sync.
3. **Gating:** `Subscriptions::currentPlan($user)` reads `$user->subscription('default')` (valid / on grace period) and maps the Stripe price to a `Plan`. QR creation checks `qr_limit`, and the scan page checks owner validity and `scan_limit`.
4. **Billing page:** shows the current plan and renewal date, with a "Manage billing" button (`$user->redirectToBillingPortal()`).

### 3.5 i18n / RTL (from the first UI commit)

- **Strings:** every user-facing string goes through `__()` with keys in `lang/en.json`. A CI test fails on hard-coded text in views.
- **Direction:** `<html lang dir>` is set in the single layout from the locale.
- **Logical properties only:** `ms-/me-/ps-/pe-/start-/end-/text-start`, enforced by a CI lint that rejects `ml-/mr-/pl-/pr-/left-/right-`. Directional icons use the `rtl:` variant.
- **Fonts:** IBM Plex Sans + IBM Plex Sans Arabic, self-hosted.
- **Smoke test:** an RTL pseudo-locale run (a fake `xx-rtl` locale that wraps strings) catches layout breaks before Arabic translations exist.

---

## 4. Design tokens (Tailwind 4)

The design from revision 1 is unchanged; what changes is how much of it the MVP builds. Tokens live in **`resources/css/tokens.css`** (an `@theme` block) and are imported by the app CSS and, in L3, by the Filament theme.

- **Primitives:** brand / neutral / success / warning / danger / info, 50–950, in OKLCH. The brand palette depends on decision 4 (§1).
- **Semantic tokens:** `bg`, `surface`, `text`, `text-muted`, `border`, `primary` (+hover / on-primary), `success`/`warning`/`danger` (the **QR status mapping**), `focus`.
  - **MVP:** light values only, but every component uses semantic tokens, so dark mode (L3) is a token-only change.
- **Typography:** IBM Plex Sans / IBM Plex Sans Arabic. Scale xs 12 → 5xl 48; weights 400 / 500 / 600.
- **Shape and motion:** radius 6 / 10 / 16 / full; two shadows; 120 / 200 ms motion, off under `prefers-reduced-motion`.
- **Spacing and breakpoints:** Tailwind defaults; mobile-first (the scan page is designed at 360 px).
- **Icons:** Heroicons only (`blade-ui-kit/blade-heroicons`), the same set Filament uses.
- **MVP components:** `button`, `input`, `textarea`, `select`, `checkbox`, `color-picker`, `field` (label / hint / error with `aria-describedby`), `card`, `badge`, `alert`, `modal`, `dropdown`, `tabs`, `table`, `pagination`, `empty-state`, `toast`.
- **Filament in the MVP:** `->colors(['primary' => <brand-600 hex>])` in the `PanelProvider`. The full shared token theme comes in L3.

---

## 5. Roadmap

Estimates are working days for one developer familiar with Laravel, **±30 %**. Every step is a small PR into `redesign/v2`, and CI must be green: PHP 8.5 + MySQL 8.4 with imagick / gd / intl, PHPUnit, Pint, Larastan, the logical-property lint and the hard-coded-string check. Nothing is live, so **rollback = revert the PR**; `master` keeps the old app until M9.

### MVP

| Phase | Steps | Proven by | Days |
|---|---|---|---|
| **M0 Prep** ✅ done (on `redesign/v2`, while it still holds the old app) | Behaviour inventory (every MVP flow → existing test, or a new characterization test against the old app) · QR renderer spike on bacon 3 (visual parity) | `docs/behaviour-inventory.md`; parity image grid + snapshot tests | 1–2 |
| **M1 Skeleton** | Laravel 13 / PHP 8.5 skeleton on `redesign/v2`; Livewire 4, Filament 5, Fortify, Cashier 16, Tailwind 4, Vite 8; CI; port the MVP-relevant tests, marked skipped | CI green; tests listed as skipped, 0 failing | 1–2 |
| **M2 Domain** | 2.1 Migrations, enums, models, factories, seeders · 2.2 7 payload types + `QrRenderer` + `QrOptions` · 2.3 Actions, `QrCodePolicy`, `Subscriptions` service · 2.4 `RecordScan` | `migrate:fresh --seed`; un-skipped ownership, creation, URL-scheme and size-clamp tests; unit tests per payload type | 4–6 |
| **M3 UI foundations** | 3.1 Tokens (light), fonts, Heroicons, app / guest / public layouts · 3.2 MVP components · 3.3 i18n + RTL infrastructure and CI checks | Component render tests; RTL pseudo-locale snapshot; lint in CI | 3–4 |
| **M4 Auth** | Fortify views: login, register, forgot/reset, verify email, profile (+ delete account) | Ported Breeze-era auth and profile tests | 1.5–2 |
| **M5 QR management** | 5.1 List (filter, search, pagination) · 5.2 Create/edit builder with live preview · 5.3 Detail: copy URL, download PNG/SVG/EPS, pause/resume, delete | Ported authorization and download tests; Livewire tests per screen | 5–7 |
| **M6 Public** | 6.1 Scan page: 7 types + 4 states · 6.2 Minimal landing + pricing · 6.3 Error pages | Ported scan-page tests (incl. stored-URL safety); a11y check ≥ 95 on the scan page | 2–3 |
| **M7 Billing** | Checkout, webhook wiring, quota/expiry gating, billing page + portal | Ported checkout test (stubbed Stripe); webhook tests with signed payloads; one manual Stripe test-mode run | 1.5–2.5 |
| **M8 Admin** | Filament 5 panel, `canAccessPanel` via `is_admin`; Users, Plans, QR codes resources; `app:make-admin` command for real environments | Ported admin tests + Filament resource tests | 1.5–2.5 |
| **M9 MVP cutover** | Browser smoke tests of the end-to-end journey (LTR + RTL pseudo-locale) · delete old code, Bootstrap themes and 128 MB of assets · README and deploy docs · merge to `master` | 0 skipped MVP tests; smoke suite green; `grep` finds no `assets-dashboard`/`assets-frontend` | 1–2 |
| | | **MVP total** | **22–33** |

### Later phases (each independent and shippable on its own after M9)

| Phase | Scope | Days |
|---|---|---|
| **L1** Social login | Google + Facebook via Socialite; buttons on login/register | 1 |
| **L2** Analytics | Overview dashboard (usage, scans over time, top QRs), per-QR scan chart | 2–3 |
| **L3** Design completion | Dark mode + toggle, `/dev/ui` component gallery, Filament theme sharing `tokens.css` | 2–3 |
| **L4** Marketing site | Live QR demo (rate-limited), features section, contact form + admin Contacts resource | 2–3 |
| **L5** Free tier (*only if chosen*) | Free plan, quota rules, downgrade path | 1.5–2 |
| **L6** Admin reporting (*optional*) | Store `invoice.paid` webhooks; revenue and signups widgets | 1–2 |
| **L7** Location / MP3 (*only if kept*) | Location payload; MP3 upload, storage, player, limits | 2.5 |
| **L8** Arabic | Translation file (translator-supplied), locale switcher, RTL visual QA pass | 1–2 + translation |
| | **Later total** | **9–14**, plus 1.5–4.5 if L5 / L7 are chosen |

**Review points with you:**
- after **M0** (parity spike + inventory, and your three open decisions);
- after **M3** (you see the design foundations before any feature page is built);
- after **M5.2** (the builder, the core of the product);
- at **M9** (the end-to-end MVP).

### About cost

These per-phase estimates come out **higher than revision 1's lump estimate** (29–38 days for everything). The full total is now **31–47 days** plus optional phases, because the breakdown exposed work the lump figure underweighted: per-type payloads, quota and expiry logic, CI checks, and cutover. Revision 1's figures for Strategy A were estimated the same way and are likely understated by a similar margin.

Two levers if the MVP needs to be smaller:
- ship the MVP with **URL, text and Wi-Fi only** (−1.5 days; the other 4 types move into L-phases);
- drop **EPS** download (−0.5 day).

---

## 6. Removing the Bootstrap themes and static assets

This happens in **M9**: `public/assets-dashboard` (115 MB) and `public/assets-frontend` (13 MB) are never copied into v2, and they are deleted from `master` at cutover. That includes the CDN tags, jQuery and its plugins, the theme customizer and the 7 icon sets.

Verification: `grep` finds no references, the browser smoke run shows no missing static files, and Vite's manifest is the only asset source.

Git history is not rewritten. A `git filter-repo` purge of the old blobs is possible later, as a separate, explicitly approved step.

---

## 7. Risks

| Risk | Mitigation |
|---|---|
| QR output looks different after the library switch | M0 parity spike before anything depends on it; snapshot tests |
| PNG/EPS need `imagick` on the host | Listed requirement; CI runs with it; SVG works without it |
| Features absent from `master` between M9 and their L-phase (§2) | Accepted, since nothing is live; each has a named phase |
| Stripe webhooks need test-mode setup | Stubbed-client tests in CI + one manual Stripe CLI run in M7 |
| The three open decisions block M2.3 / M3.1 if still open | Asked at the M0 review; defaults are the "Option 1" column in §1 if you prefer not to decide yet |
| Estimates ±30 % | Small PRs give visible progress; scope levers listed in §5 |

---

## 8. Needed from you

1. **Approve the MVP roadmap** (§5) so I can create `redesign/v2` and start M0.
2. **Free tier, Location/MP3, brand:** your answers came through as template placeholders. They can wait until the M0 review.
