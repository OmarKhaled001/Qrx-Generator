# Qrx-Generator — Redesign & Modernization Plan (Phase 2)

_Written 2026-10-09 against `fix/schema-and-tests` @ `0484b97`. **Proposal only. Nothing below is implemented until you approve it.**_

## 0. Where we are

| Done | Commit | Result |
|---|---|---|
| Security fixes (ownership policies, Livewire 2.12.7, URL/size/format checks, demo rate limit) | `26cc548`, merged to `master` | Locally merged, not pushed |
| Migrations as single source of truth + all tests green | `0484b97` on `fix/schema-and-tests` | Awaiting your review |

**Test suite today:** 62 tests, all passing on PHP 8.3 + MySQL 8.4 (strict mode) with `imagick` and `gd`. `php artisan migrate:fresh --seed` runs cleanly on the same stack. On a machine without imagick, the 5 tests that render PNGs are skipped (via `#[RequiresPhpExtension('imagick')]`), not failed.

**Schema approach chosen: edit migrations in place, not squash.**
- *Why not squash (`schema:dump`)?* A dump is a database-specific SQL file. It would replace readable migrations with SQL right before Strategy B (recommended below) redesigns the schema anyway, and it gives the SQLite test runs a separate code path.
- *Why editing is safe now:* nothing is deployed, so no environment has run the old versions.
- *Once a real environment exists:* every change becomes a new migration.

**Target versions** (verified 2026-10-09 from php.net and Packagist):

| | Latest stable | Requires |
|---|---|---|
| PHP | **8.5.11** (2026-09-24) | — |
| Laravel | 13.35.0 | PHP ≥ 8.3 |
| Livewire | 4.4.7 | PHP ≥ 8.1, Laravel 10–13 |
| Filament | 5.10.1 | PHP ≥ 8.2, Laravel ≥ 11.28, Livewire ≥ 4.4.7 |
| Cashier (Stripe) | 16.8.0 | Laravel 10–13 |
| Tailwind CSS / Vite / laravel-vite-plugin | 4.3.3 / 8.3.4 / 3.2.0 | — |

**No version ceiling:** every target package accepts PHP 8.5. Required PHP extensions on the host: `imagick` (PNG/EPS QR output), `gd` (avatars), `intl` (Filament, locale-aware formatting), `pdo_mysql`, `bcmath`.

---

## 1. Strategy comparison

The current code by layer (lines):

| Layer | Lines | Fate in **A** (incremental) | Fate in **B** (fresh project) |
|---|---|---|---|
| Blade views (2 Bootstrap themes) | 3,187 | Rewritten (redesign) | Rewritten (redesign) |
| Filament v2 resources/widgets | 1,608 | Upgraded v2→v3 (near-rewrite), →v4, →v5 | Written once for v5 |
| Livewire 2 components | 803 | Upgraded v2→v3→v4, then refactored | Rewritten as Livewire 4 + services |
| Controllers (non-auth) | 409 | Upgraded, then refactored | Rewritten thin, over services |
| Models | 603 | Kept, cleaned | Ported, cleaned |
| Migrations | 748 | Kept | Ported (or redesigned, see §3.2) |
| Tests | 1,206 (15 files) | Kept; run at every hop | **Ported first**; they become the acceptance spec |
| Static theme assets | 128 MB | Deleted at the end | Never copied |

### A) Incremental upgrade

Path: Laravel 10 → 11 → 12 → 13, with Livewire 2 → 3 and Filament 2 → 3 **together** (Filament 3 requires Livewire 3), then Filament 3 → 4 → 5 and Livewire 3 → 4, then the redesign.

- **Effort:** ~6–9 days of upgrade work before any redesign, plus ~18–25 days of redesign. **Total ≈ 24–34 days.**
- **Risk:** medium–high.
  - The Livewire 3 + Filament 3 hop is one large, untestable-in-between step: 11 resources, 3 widgets and 3 components all change API at once.
  - Each later hop repeats this on code that the redesign will throw away anyway.
  - The Laravel 11 "slim skeleton" structure (no `Kernel.php`, `bootstrap/app.php` routing) is optional in an upgrade, so the codebase keeps its old structure unless it is migrated manually.
- **Carried over:** everything, including legacy structure, naming (`generat`, `subscripe`…), the dual auth guard, 7 feature tables and the copy-pasted QR rendering, until each is refactored.
- **Rewritten:** all views, all Filament resources (during the v3 hop), all Livewire components.
- **How tests prove nothing was lost:** the 62 tests run after every hop. This works well, but most of the effort goes into keeping tests green on code that is about to be deleted.

### B) Fresh Laravel 13 project, porting the domain ✅ **Recommended**

Path: new Laravel 13 skeleton on PHP 8.5 with Livewire 4, Filament 5 and Tailwind 4 from day one. Port the migrations, models, policies, business rules and tests, then build the UI directly in Tailwind.

- **Effort:** ~3 days skeleton, CI and spikes · ~5–7 days domain + test port · ~18–25 days UI (same as A) · ~3 days cutover. **Total ≈ 29–38 days.**
  - The totals overlap with A. The difference is in *what the days buy*: in B no day is spent upgrading code that is later deleted.
- **Risk:** medium, and concentrated in one place: forgetting a behaviour the old app had. That risk is handled explicitly (next paragraph). There are no big-bang framework hops.
- **Carried over:**
  - the 62 tests, as behaviour specs;
  - the migrations (or a deliberately redesigned schema, §3.2);
  - models, `QrxCodePolicy`, the `QrOptions` limits;
  - the plan/quota rules, the public `/x/{code}` contract, the Stripe/Cashier integration and the Socialite flow.
- **Rewritten:** UI, Livewire components, Filament resources, controllers (thin, over services) and QR rendering (one service instead of ~14 copies).
- **How tests prove nothing was lost:**
  1. **Behaviour inventory first.** Before porting, list every route and user flow (§2 of `PROJECT_AUDIT.md` + this plan's §7) and map each one to a test in the current suite. Flows without a test get a *characterization test written against the old app first*, on `master`, so it captures today's behaviour.
  2. **Port the suite before the features.** Tests are copied into the new project at the start and marked `skip` until their feature lands. "Done" for any step means its tests are un-skipped and green.
  3. **The cutover gate** is: zero skipped tests, plus a browser smoke test of the critical journeys (register → subscribe → create QR → scan → edit → download), plus an RTL (Arabic) smoke run.

**Why B:** nothing is live, there is no data to migrate, and ~80 % of the code (views, Filament, Livewire) is rewritten in *both* strategies. B also lets us fix the structural problems (dual guards, 7 feature tables, scattered subscription logic) once, instead of upgrading them three times. B is done in the **same repository** on branch `redesign/v2`, so git history is kept.

**When A would be better:** if the app were live with data and users, or if the UI were staying. Neither is true.

---

## 2. Assumptions for the open questions you left to my judgement

Each assumption is cheap to reverse **before** the step that depends on it. Please correct any that are wrong.

| # | Question | Assumption | Step that depends on it |
|---|---|---|---|
| Q5 | Brand | Keep the name **Qrx** and design a new palette and logo treatment (§4). The `hossam-x-studios.com` preview URL is replaced by a neutral sample and the footer credit is removed | P4 |
| Q6 | Dark mode | Tokens are dark-mode-ready from day one, and light **and** dark ship together (with tokens it costs little). A toggle plus `prefers-color-scheme` default | P4 |
| Q7/8 | Admin | Filament 5 for admins only. The 7 per-type "Feature" resources disappear; the admin edits a QR's content inside one QR resource | P8 |
| Q9 | Billing | Plan length and renewal come from **Stripe** (subscription status via Cashier), not "created_at + 1 year". No free tier. When an owner's subscription lapses, their QR codes show a friendly "temporarily unavailable" page (not a 500) | P3, P7 |
| Q10 | QR types | Launch with the 7 working types. **Location** and **MP3** are dropped from v2; the new schema makes adding a type a small, isolated change | P3 |
| Q11 | Scan analytics | Each scan is recorded (timestamp, device class, referrer host; obvious bots excluded) so the dashboard can show scans over time. No IP or personal data stored. Unique-visitor counting is deferred | P3, P5 |
| Q12 | API / mobile | No consumers. `tymon/jwt-auth`, the unrouted `Api` controllers and Sanctum are **not** carried over | P2 |
| Q13 | Social login | Google + Facebook (already configured). Buttons are added to login/register | P4 |
| Q14 | URLs | Typos in route names and URLs are fixed (nothing to redirect). **`/x/{code}` stays exactly as is** | P3 |
| Q15 | Assets in git | The 128 MB of theme assets is deleted in a normal commit (no history rewrite) | P9 |
| — | Auth tables | **One `users` table with an `is_admin` flag** replaces the separate `admins` table + second guard. The dual-guard setup caused two of today's bugs (wrong guard in confirm-password, Filament 403) | P3 |
| — | Auth scaffolding | **Laravel Fortify** (headless) + our own Livewire views, for full control over design and RTL. The Laravel starter kits bring their own UI (Flux) that we would fight | P4 |
| — | Test framework | Keep **PHPUnit** so the 62 tests port without rewriting | P2 |

---

## 3. Target architecture

### 3.1 Code structure

```
app/
  Enums/            QrType, QrStatus, …                 (replace magic strings)
  Models/           User, QrCode, QrScan, Plan, Contact, (+ Cashier models)
  Policies/         QrCodePolicy                         (ported from QrxCodePolicy)
  Actions/          CreateQrCode, UpdateQrCode, DeleteQrCode, RecordScan, ToggleQrStatus
  Services/         QrRenderer (one place that turns a QrCode into SVG/PNG/EPS)
                    Subscriptions (current plan, quota, "can create?", grace period)
  Support/          QrOptions (ported), QrPayload/* (one validated value object per type)
  Livewire/         Dashboard/*, Public/QrDemo               (thin: call Actions)
  Filament/         Admin panel only
  Http/Controllers/ PublicScanController (/x/{code}), Download, Stripe webhooks
lang/               en.json (ar.json later)
resources/css/      tokens.css, app.css, filament/admin/theme.css
resources/views/    components/ui/*, layouts/{app,guest,public}.blade.php, …
```

Rules: Livewire components and controllers stay thin and call **Actions**. Only `QrRenderer` touches the QR library. Only `Subscriptions` answers "what plan is this user on, and what may they do?". This removes ~14 copies of QR rendering and 5 copies of the plan lookup.

### 3.2 Schema (redesign while there is no data; your decision)

| Today | Proposed | Why |
|---|---|---|
| `qrx_codes` + `styles` + 7 `feature_*` tables | `qr_codes` with `type` (enum), `payload` (JSON, validated per type), `design` (JSON: colours, gradient, eye, style, margin) | One model, no type `switch` statements, no orphan rows when the type changes (audit M2), one Filament resource |
| `users` + `admins` (two guards) | `users.is_admin` | One guard (see §2) |
| `scan_count` column only | `qr_codes.scan_count` (cached counter, incremented atomically) + `qr_scans` rows | Analytics (Q11), and fixes the non-atomic increment (M7) |
| `code = 'qx-' + rand(6 digits)` | Random 8-char base62, unique, generated with retry | Not guessable (M6). Route stays `/x/{code}` |
| `transactions` written before payment | Written from Stripe webhooks (`checkout.session.completed`, `invoice.paid`) | Revenue figures become real |
| `plans` | Unchanged columns + `sort_order`, `features` (JSON, for the pricing page) | Pricing copy comes from the DB |

**Alternative if you'd rather not change the schema:** port today's tables 1:1. Everything else in this plan still works; the per-type code just stays more verbose.

### 3.3 QR rendering (needs a spike in P1)

`simplesoftwareio/simple-qrcode` is unmaintained (last release 2021) and pins `bacon/bacon-qr-code` 2.x. Plan: call **`bacon/bacon-qr-code` 3.1.1** directly from `QrRenderer`. simple-qrcode was a thin wrapper over it, so module styles, eye shapes, gradients, margin and SVG/PNG/EPS output are all available in the library itself.

The P1 spike must prove *visual parity* with today's output: every style × eye × gradient combination, rendered side by side.

Fallback: `endroid/qr-code` 6.x (PHP ≥ 8.4, also built on bacon 3). It's more batteries-included but has weaker gradient support.

### 3.4 Multi-language and RTL from day one

- **Translatable strings:** every user-facing string goes through `__()`, with keys in `lang/en.json`. `ar.json` is added later without touching templates. A test fails the build if a Blade view contains hard-coded user-facing text outside `__()` (checked by a simple scanner test).
- **Locale and `dir`:**
  - `SetLocale` middleware takes the locale from the user preference, falling back to `Accept-Language` and then `en`.
  - `<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">` lives in the single layout.
- **Logical properties only:**
  - Use Tailwind's logical utilities (`ms-*`/`me-*`, `ps-*`/`pe-*`, `start-*`/`end-*`, `text-start`/`text-end`, `rounded-s-*`/`rounded-e-*`, `border-s`/`border-e`).
  - Physical `ml-/mr-/pl-/pr-/left-/right-` are banned, enforced by a lint step in CI.
  - Directional icons (arrows, chevrons) use the `rtl:` variant to mirror.
- **Fonts:** **IBM Plex Sans** + **IBM Plex Sans Arabic**, a matched pair designed together, self-hosted via `@fontsource` (no Google Fonts CDN).
- **Formatting:** dates and numbers go through Laravel's locale-aware helpers (`Number::format`, Carbon `translatedFormat`).
- **Filament:** Filament renders RTL automatically for RTL locales. It's included in the RTL smoke test.

---

## 4. Design-token system (Tailwind 4, shared by dashboard, public site and Filament)

**Single source of truth:** `resources/css/tokens.css`, a Tailwind 4 `@theme` block. Both `resources/css/app.css` (public site and client dashboard) and `resources/css/filament/admin/theme.css` (Filament custom theme) `@import` it.

**Two layers of tokens:**

1. **Primitive palette** (never used directly in components): `--color-brand-50 … 950`, `--color-neutral-50 … 950`, `--color-success-*`, `--color-warning-*`, `--color-danger-*`, `--color-info-*`. Defined in OKLCH so the steps are perceptually even. **Proposed brand: a deep teal-blue** (`brand-600 ≈ oklch(0.52 0.12 230)`). It reads as "tech/utility", works in light and dark, and is distinct from Filament's default amber and from typical "QR generator" purple. To be confirmed with a visual board in P4.
2. **Semantic tokens** (what components use), switched for dark mode under `.dark`:

| Token | Light | Dark | Used for |
|---|---|---|---|
| `--color-bg` / `--color-surface` / `--color-surface-raised` | neutral-50 / white / white | neutral-950 / 900 / 800 | Page, cards, popovers |
| `--color-text` / `--color-text-muted` | neutral-900 / 600 | neutral-50 / 400 | Body, secondary |
| `--color-border` | neutral-200 | neutral-800 | Dividers, inputs |
| `--color-primary` / `--color-primary-hover` / `--color-on-primary` | brand-600 / 700 / white | brand-400 / 300 / neutral-950 | Main actions, links |
| `--color-success` / `--color-warning` / `--color-danger` | 600 shades | 400 shades | **QR status: active / paused / expired** (one mapping, used everywhere) |
| `--color-focus` | brand-500 | brand-400 | Focus rings (visible, 2 px, offset) |

**Typography:**
- Font: IBM Plex Sans / IBM Plex Sans Arabic.
- Scale: `xs 12 · sm 14 · base 16 · lg 18 · xl 20 · 2xl 24 · 3xl 30 · 4xl 36 · 5xl 48`, line-height 1.5 for body and 1.2 for headings.
- Weights: 400 / 500 / 600 only.
- Arabic gets +1 step of line-height via `:lang(ar)`.

**Spacing and shape:**
- Spacing: Tailwind's default 4 px base (no custom scale). Layout rhythm uses 4 / 6 / 8 / 12 / 16.
- Radius: `--radius-sm 6px`, `--radius 10px` (cards, inputs, buttons), `--radius-lg 16px` (modals, hero cards), `--radius-full`.
- Shadows: two only, `--shadow-sm` (cards) and `--shadow-lg` (popovers, modals).
- Motion: `--duration-fast 120ms`, `--duration 200ms`, one easing. Everything is disabled under `prefers-reduced-motion`.
- Breakpoints: Tailwind defaults (`sm 640 · md 768 · lg 1024 · xl 1280 · 2xl 1536`). Mobile-first; the public scan page is designed at 360 px first.

**Filament:**
- The custom admin theme imports `tokens.css` and maps Filament's `primary`/`gray`/`success`/`warning`/`danger` palettes to our primitives, so the admin panel matches the product.
- **Spike item (P1):** confirm the cleanest way in Filament 5 to feed panel colours from the same token file. The fallback is a small `config/brand.php` that both the CSS build and the `PanelProvider` read, so there is still only one source.

**Components:** anonymous Blade components in `resources/views/components/ui/`, each documented on an internal `/dev/ui` page (local env only) that renders every variant in light, dark, LTR and RTL:

`button` (primary / secondary / ghost / danger; sm / md / lg; loading state) · `input` · `textarea` · `select` · `checkbox` · `radio-group` · `color-picker` · `field` (label + hint + error, wired with `aria-describedby`) · `card` · `stat` · `badge` (status mapping) · `alert` · `modal` · `dropdown` · `tabs` · `table` · `pagination` · `empty-state` · `avatar` · `icon` · `toast` · `skeleton`.

**One icon set: Heroicons** (outline 24 for UI, solid 20 for small badges), via `blade-ui-kit/blade-heroicons`. Filament already uses Heroicons, so admin and dashboard share one visual language. The 7 icon sets in use today (Flaticon, Font Awesome, Remix, MDI, Phosphor, Bootstrap Icons, Heroicons) go away.

---

## 5. Client dashboard (custom Livewire 4 + Tailwind)

**One layout,** `layouts/app.blade.php`:
- Sidebar (collapsible; becomes a drawer below `lg`), top bar (search, locale switcher, theme toggle, user menu), content slot, toasts.
- Navigation is defined once in a config array, so menu items and active states aren't duplicated.

**Pages:**

| Page | Notes |
|---|---|
| Overview | Plan usage (QRs used / quota, days left, from Stripe), scans over the last 30 days, top QR codes. Empty state with "Create your first QR" |
| QR codes | One page with a status filter (all / active / paused / expired), search and a grid/list toggle. Replaces today's 4 near-identical pages |
| Create / edit QR | One Livewire builder used for both: type picker → content form (validated per type) → design panel (colours, gradient, style, eye, margin) → live preview → save. Preview is debounced; **no `wire:poll`** |
| QR detail | Preview, short URL with copy, download (PNG/SVG/EPS, size), scans chart, pause/resume, delete with confirmation |
| Billing | Current plan, upgrade (Stripe Checkout), manage payment and invoices (Stripe Billing Portal) |
| Profile & settings | Name, email, password, language, theme, delete account |
| Auth | Login, register (+ Google/Facebook), forgot/reset password, verify email, using `layouts/guest.blade.php` |

**Public side,** `layouts/public.blade.php`:
- **Landing:** hero, live QR demo (rate-limited as today), features, pricing from the DB, contact form with throttling, footer.
- **Scan page `/x/{code}`:** the most-seen page in the product. Mobile-first, fast (no JS framework, minimal CSS), one template per type, accessible. Friendly pages for paused, expired and owner-lapsed states.
- **Error pages:** 404 / 403 / 429 / 500, in the same style.

**Accessibility baseline (each page must pass before it counts as done):**
- Keyboard-only flow works.
- Visible focus.
- Labels on every input, with errors announced.
- Contrast ≥ 4.5:1 (checked on tokens, light and dark).
- No disabled context menu.
- Lighthouse a11y ≥ 95 on the landing and scan pages.

---

## 6. Removing the Bootstrap themes and 128 MB of static assets

- In B, the new project never copies `public/assets-dashboard` (115 MB) or `public/assets-frontend` (13 MB).
- **At cutover (P9):** delete both directories and every view that references them, in one commit.
- **Verification:**
  - `grep -r "assets-dashboard\|assets-frontend" resources app routes config` returns nothing;
  - the browser smoke tests show no 404s for static files;
  - Vite's manifest is the only asset source.
- **Also removed:**
  - CDN `<script>`/`<link>` tags (Font Awesome, the 7× clipboard.js copies);
  - jQuery and its plugins;
  - the theme customizer;
  - `changer.js`/`styleswitch.js`.
- **Clipboard:** a 10-line Alpine `x-on:click="navigator.clipboard.writeText(...)"`.
- **Git history:** not rewritten (§2, Q15), so clones still contain the old blobs. If clone size matters later, a one-off `git filter-repo` can remove them. That's destructive and needs your separate approval.

---

## 7. Phased roadmap

Every step is a small PR into `redesign/v2`. "Done" means the step's tests are green in CI (GitHub Actions: PHP 8.5 + MySQL 8.4 with imagick, gd and intl) plus Pint and Larastan. Nothing is live, so **rollback = revert the PR**. `master` keeps the old app working until cutover.

| Phase | Step | Proven by |
|---|---|---|
| **P0** ✅ | Security fixes; schema matches code; 62 tests green | Done (`26cc548`, `0484b97`) |
| **P1 Prep** (on `master`) | 1.1 Behaviour inventory (every route/flow → test) and characterization tests for any gaps | Inventory table in `docs/behaviour-inventory.md`; new tests green on the old app |
| | 1.2 Spike: `QrRenderer` on bacon 3 with visual parity | Side-by-side image grid + snapshot tests |
| | 1.3 Spike: shared tokens → Filament 5 theme | Throwaway branch screenshot; mechanism written into §4 |
| **P2 Skeleton** | 2.1 Fresh Laravel 13 / PHP 8.5 skeleton on `redesign/v2`; Livewire 4, Filament 5, Tailwind 4, Vite 8, Pint, Larastan, CI | CI green on an empty app |
| | 2.2 Port the test suite, all marked skipped with a reason | CI lists N skipped, 0 failed |
| **P3 Domain** | 3.1 Migrations (new schema §3.2 or 1:1 port), enums, models, factories, seeders | `migrate:fresh --seed` in CI; model tests |
| | 3.2 `QrRenderer`, `QrOptions`, per-type payload validation | Unit tests incl. the URL-scheme and size-clamp cases |
| | 3.3 Actions + `QrCodePolicy` + `Subscriptions` service (Cashier-based quota and grace logic) | Ported ownership + creation tests un-skipped |
| | 3.4 Public `/x/{code}` controller + scan recording | Ported scan tests + new analytics tests |
| **P4 Foundations** | 4.1 Tokens, fonts, light/dark, base layouts, `/dev/ui` component gallery | Component tests; a11y check on the gallery in 4 modes |
| | 4.2 i18n + RTL infrastructure (`SetLocale`, `lang/en.json`, logical-property lint, hard-coded-string test) | RTL snapshot of layouts; lint in CI |
| | 4.3 Auth with Fortify (+ Google/Facebook) | Ported auth + social-login tests |
| **P5 Dashboard** | 5.1 QR list · 5.2 Create/edit builder · 5.3 QR detail + download · 5.4 Overview · 5.5 Profile/settings | Each un-skips its ported tests and adds Livewire tests; browser smoke per page |
| **P6 Public site** | 6.1 Scan page (all types + states) · 6.2 Landing + demo + pricing + contact · 6.3 Error pages | Ported scan/demo tests; Lighthouse a11y ≥ 95 |
| **P7 Billing** | 7.1 Checkout (Cashier 16) · 7.2 Webhooks → transactions and status · 7.3 Billing portal | Ported checkout test (stubbed Stripe); webhook tests with signed payloads; manual Stripe test-mode run |
| **P8 Admin** | 8.1 Filament 5 panel + theme · 8.2 Users, Plans, QR codes, Contacts, Transactions resources · 8.3 Dashboard widgets | Ported admin tests + Filament resource tests |
| **P9 Cutover** | 9.1 Gate: 0 skipped tests, browser smoke of the critical journeys (LTR + RTL), a11y checks · 9.2 Delete old code and the 128 MB of assets · 9.3 README, deploy docs, `.env.example` · 9.4 Merge `redesign/v2` → `master` | All of the left; final review with you |

**Order and parallelism:**
- P3 must precede P5–P8.
- P4 can overlap with P3.
- P5, P6 and P7 are independent of each other once P4 is done.

**Your review points:** after P1 (spikes and inventory), after P4 (design system: you see the gallery before any page is built), after P5.2 (the builder, the core of the product), and at P9.

---

## 8. Risks

| Risk | Impact | Mitigation |
|---|---|---|
| QR output differs visually after switching library | Users see different QR styling | P1.2 parity spike before anything depends on it; snapshot tests |
| PNG/EPS output depends on `imagick` on the host | Downloads fail on hosts without it | Listed as a hosting requirement (§0); CI runs with imagick; SVG works without it |
| Token sharing with Filament 5 is awkward | Admin drifts from the product's look | P1.3 spike; `config/brand.php` fallback keeps one source |
| Schema redesign (§3.2) is a bigger change than a 1:1 port | More porting work in P3 | Optional; decide now. Both paths keep the same tests |
| Stripe webhooks need test-mode setup | Billing untested end-to-end | Stubbed-client tests in CI + one manual Stripe CLI run in P7 |
| Estimates are rough (one developer, ±30 %) | Timeline slips | Small PRs make progress visible weekly; scope cuts are possible per phase (e.g. analytics charts) |
| Arabic typography and RTL edge cases only show up with real translations | Layout bugs later | RTL smoke tests from P4 using pseudo-translated strings |

---

## 9. Decisions I need from you to start

1. **Strategy:** approve **B** (fresh Laravel 13 project on `redesign/v2`)?
2. **Schema:** redesign per §3.2 (`qr_codes` + JSON payload/design, `qr_scans`, single `users` table), or a 1:1 port?
3. **§2 assumptions:** anything to change, especially billing (Stripe-driven plan length, no free tier), dropping Location/MP3, and one `users` table?
4. **Brand:** OK to propose a new palette (deep teal-blue direction) and drop the hossam-x-studios references?
5. **`fix/schema-and-tests`:** merge into `master` before P1?
