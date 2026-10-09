# Qrx-Generator — Project Audit (Phase 1, read-only)

_Audited 2026-10-09 on `master` @ `d91406b`. Nothing was installed, upgraded or modified; this file is the only change._

> **Verification limits.** `vendor/` and `node_modules/` are not present, so `php artisan route:list`, `php artisan about` and the test suite could **not** be run without installing dependencies (a write operation). Versions come from `composer.lock` / `package-lock.json`; advisories from `composer audit --locked` and `npm audit --package-lock-only`; "latest" from `composer outdated --locked` / `npm view`. Findings marked **(inferred)** are deduced from reading code + migrations and should be confirmed against the real database.

---

## 1. Executive summary

- **What it is:** a SaaS QR-code generator. Users subscribe to a Stripe plan (Cashier), create dynamic QR codes of 7 types (text, URL, SMS, email, vCard, phone, Wi-Fi), style them (colour/gradient/eye/margin), and each QR points to a short URL `/x/{code}` that renders a landing page and counts scans. Admins manage everything in a Filament v2 panel at `/admin`.
- **Stack:** Laravel 10.48 · Livewire 2.12.5 · Filament 2.17.0 · two different purchased Bootstrap 5.3 HTML themes served as static files from `public/` (~128 MB). Tailwind/Vite/Alpine are installed but **not used by any view**.
- **Everything is 2–3 major versions behind** (Laravel 13, Livewire 4, Filament 5, Tailwind 4, Vite 8 are current). `composer audit` reports **61 advisories across 19 packages**, including a **Livewire file-upload RCE (CVE-2024-47823)** that applies to this app.
- **Security is the most urgent issue, ahead of the redesign:** every dashboard action (`edit`, `status`, `delete`, `download`) loads a QR by raw `id` with **no ownership check**, so any logged-in user can pause, delete, download or take over other users' QR codes.
- **The code does not match its migrations (inferred):** social login, Stripe checkout, QR creation and admin plan creation all write columns that the migrations either lack or declare NOT NULL. Either production has a hand-altered schema or these flows are broken.
- **Architecture:** no service layer, no policies, no form requests for domain logic. QR-generation code is copy-pasted about 14 times across 3 Livewire components and 1 controller, and each dashboard page repeats the full `<html>` shell (14 copies).
- **Tests:** only the stock Breeze tests exist, and some of them target views that were deleted (`profile.edit`, `auth.*`). There is **zero coverage** of QR, billing or admin logic.
- **Redesign implication:** the UI layer is effectively vendor template markup with no design system, no i18n/RTL and no shared layout. Replacing it is low-risk for data but high-effort. The backend needs a security and correctness pass before or alongside it.

---

## 2. Tech stack

### 2.1 Backend (composer)

| Package | Locked | Latest stable | Upgrade difficulty | Notes |
|---|---|---|---|---|
| PHP (constraint) | `^8.1` (local CLI 8.4.25) | Laravel 13 needs ≥ 8.3 | Low | Confirm the hosting PHP version |
| laravel/framework | 10.48.10 | 13.35.0 | **High** (3 majors) | L10 is past security EOL. Advisories incl. CVE-2024-52301 (env manipulation), CVE-2025-27515 |
| livewire/livewire | **2.12.5** (pinned exact) | 4.4.7 | **High** | **CVE-2024-47823 RCE via file uploads** (<2.12.7). `QrxCreate` uses `WithFileUploads` |
| filament/filament | **2.17.0** (pinned exact) | 5.10.1 | **High** | v2 → v3 is a near-rewrite of resources. v4/v5 then build on v3 |
| filament/spatie-laravel-media-library-plugin | 2.17.55 | 5.10.1 | Med | Moves with Filament |
| spatie/laravel-medialibrary | 10.15.0 | 11.x | Med | 2 advisories (<11.23.0) |
| laravel/cashier (Stripe) | 15.3.2 | 16.8.0 | Med | `stripe/stripe-php` 13.18 |
| laravel/breeze | 1.29.1 | 2.4.2 | Low | Scaffolding only. Its views were deleted, so the package can likely be removed |
| laravel/sanctum | 3.3.3 | 4.3.3 | Low | Only used by the default `/api/user` route |
| laravel/socialite | 5.14.0 | 5.31.0 | Low | |
| tymon/jwt-auth | 2.1.1 | 2.3.0 | Low | **Effectively unused** (no `api` guard, no routes). Candidate for removal |
| simplesoftwareio/simple-qrcode | 4.2.0 | 4.2.0 (no newer) | Med | Core dependency, on `bacon/bacon-qr-code` 2.0.8. Check compatibility with PHP 8.4/Laravel 13 and consider alternatives |
| arielmejiadev/larapex-charts | 8.1.0 | 10.0.0 | Med | Client dashboard charts |
| flowframe/laravel-trend | 0.2.0 | 0.5.0 | Low | Filament widget |
| spatie/color | 1.5.3 | 2.1.0 | Low | |
| laravolt/avatar | 5.1.0 | 7.0.0 | Low | |
| guzzlehttp/guzzle | 7.8.1 | 7.15.2 / 8.2.0 | Low | ~10 advisories, fixed in 7.15.2 |
| phpunit/phpunit | 10.5.20 | 13.4.1 | Med | Advisory CVE-2026-24765 |
| laravel/pint, sail, collision, ignition | — | — | Low | Dev only |

Other transitive packages with advisories: `league/commonmark`, `phpseclib/phpseclib`, `symfony/http-foundation`, `symfony/mailer`, `nesbot/carbon`, `psy/psysh`, `league/flysystem`, `firebase/php-jwt`. Most are cleared by a framework upgrade.

### 2.2 Frontend

| Item | Current | Latest | Status |
|---|---|---|---|
| Tailwind CSS (npm) | 3.4.3 | 4.3.3 | **Installed but unused.** No `@vite` directive in any view. `tailwind.config.js` sets the font to `Figtree`, which nothing uses |
| Vite / laravel-vite-plugin | 4.5.3 / 0.7.8 | 8.3.4 / 3.2.0 | Unused (no build output referenced) |
| Alpine.js | 3.13.10 | 3.17.4 | Unused by views (Livewire 2 does not bundle it) |
| axios | 1.6.8 | 1.20.0 | High advisories (SSRF, prototype pollution) |
| **Public site theme** | "Martex" by DSAThemes, Bootstrap **5.3.0** + jQuery 3.7.0 + Owl, Magnific, WOW… | — | Static files in `public/assets-frontend` (13 MB) |
| **Client dashboard theme** | Themesbrand admin template (`--tb-*` CSS vars), Bootstrap **5.3.0-alpha2**, ApexCharts 3.37 | — | Static files in `public/assets-dashboard` (115 MB, 33 libs, about 7 actually referenced) |
| **Admin panel** | Filament v2 (Tailwind 3 + Alpine, bundled by Filament) | Filament 5 | Default look, no custom theme |

`npm audit` also flags `brace-expansion`, `braces`, `browserslist` and `cross-spawn` (all dev/build-time).

### 2.3 Infrastructure / config

| Concern | Current |
|---|---|
| Database | MySQL (`.env.example`), `strict => true` ([config/database.php:59](../config/database.php#L59)) |
| Cache / session / queue | `file` / `file` / `sync`. No jobs, no queues in use |
| Storage | `public` disk. QR PNGs are written to `storage/app/public/qrxs/{code}.png` |
| Auth | **Two session guards:** `user` (default, `users` table) and `web` (→ `admins` table, used by Filament) in [config/auth.php](../config/auth.php). Breeze-style login/register for clients. Socialite (provider-agnostic `/auth/{provider}/…`). Sanctum and JWT are installed but have no real use |
| Payments | Laravel Cashier (Stripe Checkout). Plans map to Stripe via `plans.plan_id` / `price_id` |
| Media | spatie/medialibrary (configured on `User`, `QrxCode`, `FeatureMp3`; barely used) |
| Mail | Default SMTP/mailpit; email verification is **not** enforced (`MustVerifyEmail` is commented out in [app/Models/User.php:5](../app/Models/User.php#L5)) |
| Scheduler | Empty |
| README | Stock Laravel README, no project docs |

---

## 3. Architecture overview

### 3.1 Folder map

| Path | Contents |
|---|---|
| `app/Http/Controllers/` | `QrxController` (client dashboard + public scan page), `HomeController` (landing), `CheckoutController`, `ContactController`, `SocialiteController`, `ProfileController` (Breeze, view missing) |
| `app/Http/Controllers/Auth/` | Breeze auth controllers (login/register re-pointed at `Dashboard.*` views) |
| `app/Http/Controllers/Api/` | `AuthController` (**wrong namespace** `App\Http\Controllers`), `ApiSocialiteController`. **Neither is routed** |
| `app/Http/Livewire/` | `QrxCreate` (260 lines), `QrxEdit` (361 lines), `QrDemo` (154 lines, public landing demo) |
| `app/Filament/Resources/` | 11 CRUD resources: Users (+QR relation manager), Plans, QrxCodes, Contacts, and 7 `Feature*` resources |
| `app/Filament/Widgets/` | `StatsOverview`, `QrxCodeChart`, `LatestUsers` (in file `LatestClients.php`, a PSR-4 mismatch) |
| `app/Charts/` | Larapex charts for the client dashboard |
| `app/Models/` | 18 models (below) |
| `resources/views/Frontend/` | Public one-page landing (sections + partials) |
| `resources/views/Dashboard/` | Client dashboard pages, auth pages, public QR landing (`view.blade.php`), error pages |
| `resources/views/livewire/` | Livewire templates + `features/*` and `settings/*` partials |
| `public/assets-frontend`, `public/assets-dashboard` | Vendored theme assets (committed to git) |

**Patterns present:** MVC + Livewire + Filament only. **Absent:** Services, Actions, Repositories, DTOs, Policies, Observers, Events/Listeners (beyond `Registered`), Form Requests for domain input, Enums (status and type are magic strings), Jobs.

### 3.2 Routes (from [routes/web.php](../routes/web.php), [routes/auth.php](../routes/auth.php), [routes/api.php](../routes/api.php))

| Group | Routes | Middleware |
|---|---|---|
| **Public** | `GET /` landing + plans · `POST /` contact form (name `countact`) · `GET /x/{code}` public QR landing + scan count | web |
| **Client dashboard** `/dashboard` | `GET /` stats · `GET /qr`, `/qr-active`, `/qr-pause`, `/qr-expire` lists · `GET /create` (Livewire) · `GET /edit/{id}` (Livewire) · `PUT /status/{id}` · `DELETE /delete/{id}` · `GET /download` · `GET /plan-subscripe`, `/plan-upgrade` (static error views) | `auth` (guard `user`) |
| **Billing** | `GET /checkout/{id?}` → Stripe Checkout | `auth` |
| **Profile** (Breeze) | `GET/PATCH/DELETE /profile` | `auth`. **View `profile.edit` does not exist** |
| **Auth** | register, login, forgot/reset password, verify email, confirm password, logout | Breeze. **`auth.forgot-password`, `auth.reset-password`, `auth.verify-email`, `auth.confirm-password` views are missing** |
| **Social** | `GET /auth/{provider}/redirect` · `/auth/{provider}/callback` | none (provider not whitelisted) |
| **Admin** | `/admin/*` (Filament auto-routes) | Filament, guard `web` → `admins` |
| **API** | `GET /api/user` (Sanctum default) | `auth:sanctum`. Imports a non-existent class at [routes/api.php:5](../routes/api.php#L5) |
| Cashier | `/stripe/webhook` (registered by Cashier) | — |

### 3.3 Data model

- `User` (Billable, JWTSubject, HasMedia) **hasMany** `QrxCode`. Cashier adds `subscriptions` → `subscription_items`.
- `QrxCode` **belongsTo** `User`; **hasOne** `Style`; **hasOne** one of `FeatureText | FeatureUrl | FeatureEmail | FeatureMessage | FeaturePhone | FeatureVcard | FeatureWifi`, selected by `qrx_codes.type`. This is a hand-rolled polymorphic design: 7 tables, 7 relations, 7 Filament resources.
- `FeatureLocation`, `FeatureMp3` are half-built (UI tabs commented out; `FeatureLocation` references a non-existent `QrCode` model and a `qr_code_id` column).
- `Plan` (Stripe product/price ids, `qr_count`, `scan_count`) is linked to a user **indirectly** via `subscription_items.stripe_product = plans.plan_id`. That lookup is repeated in 5 places.
- `Transaction` (amount, type) has no relation to users.
- `Contact` (contact-form submissions). `Admin` (Filament users; has a meaningless `qrxCodes()` relation).

### 3.4 Diagram

```mermaid
flowchart LR
    subgraph Public
        L[Landing page<br/>Frontend/index] -->|Livewire| D[QrDemo]
        L -->|POST /| C[ContactController]
        S[Scanner phone] -->|GET /x/code| Q[QrxController@showQr<br/>scan_count++]
    end

    subgraph Client["Client dashboard (guard: user)"]
        DB1[QrxController<br/>stats, lists, status, delete, download]
        LC[Livewire QrxCreate]
        LE[Livewire QrxEdit]
        CO[CheckoutController] -->|Cashier| STRIPE[(Stripe)]
    end

    subgraph Admin["Admin /admin (guard: web → admins)"]
        F[Filament v2<br/>11 resources + 3 widgets]
    end

    AUTH[Breeze auth + Socialite] --> Client
    LC & LE -->|simple-qrcode| PNG[(storage/app/public/qrxs/*.png)]
    DB1 & LC & LE & Q & F --> M[(MySQL<br/>users · qrx_codes · styles<br/>feature_* x7 · plans<br/>subscriptions · transactions · contacts · admins)]
    STRIPE -->|webhook| M
```

### 3.5 Multi-language / RTL

**None.** There is no `lang/` directory and no use of `__()`, `@lang` or `trans()`. Every string is hard-coded English, many with typos ("Generat", "Countact Us", "Get srarted", "subscripe", "careate"). There is no `dir` attribute and no RTL CSS in use. `<html lang>` is hard-coded `en` on the landing page, and `app()->getLocale()` in the dashboard. The phone placeholder `(+20)` suggests an Egyptian audience (see Open Questions).

---

## 4. UI inventory

### 4.1 Pages

| Area | Page / view | Route | Layout | Notes |
|---|---|---|---|---|
| Public | Landing (one page): nav, hero slider, about, **live QR demo**, features, pricing, contact, footer | `/` | `Frontend/index.blade.php` (own shell) | Martex theme. Meta tags still advertise "Martex – Software, App, SaaS…" ([Frontend/index.blade.php:6-8](../resources/views/Frontend/index.blade.php#L6-L8)) |
| Public | QR landing (what a scanner sees) | `/x/{code}` | `Dashboard/view.blade.php` (own shell) | 7 type-specific partials in `Dashboard/sections/view/*`. **Most user-visible page in the product** |
| Public | Not found / paused / expired | (from showQr) | `Dashboard/error-not-found.blade.php` | |
| Auth | Login | `/login` | `Dashboard/login.blade.php` | Themesbrand auth layout |
| Auth | Register | `/register` | `Dashboard/register.blade.php` | Collects phone/country/city/area |
| Auth | Forgot / reset / verify / confirm password | Breeze routes | — | **Views missing → 500 error** |
| Client | Dashboard home (plan usage, 2 charts, top 3 QRs) | `/dashboard` | `Dashboard/index.blade.php` | |
| Client | QR lists: all / active / paused / expired | `/dashboard/qr*` | 4 near-identical files (`qrx-all`, `qrx-active`, `qrx-pause`, `qrx-exp`) | Same markup ×4 |
| Client | Create QR | `/dashboard/create` | `Dashboard/qrx-create.blade.php` + Livewire | |
| Client | Edit QR | `/dashboard/edit/{id}` | `Dashboard/qrx-edit.blade.php` + Livewire | |
| Client | Plan required / plan upgrade | `/dashboard/plan-*` | 2 static views | |
| Client | Profile | `/profile` | — | **View missing → 500 error** |
| Admin | Filament dashboard (stats, weekly chart, latest users) | `/admin` | Filament | |
| Admin | CRUD: Users (+ their QRs), Plans, QR Codes, Contacts, 7 × Feature tables | `/admin/*` | Filament | Exposing every feature table separately is data-model leakage, not a useful admin UX |

### 4.2 Reusable pieces

| Kind | Items | Observations |
|---|---|---|
| Blade **components** | `AppLayout`, `GuestLayout` ([app/View/Components](../app/View/Components)) | **Dead.** They render `layouts.app` / `layouts.guest`, which do not exist. `resources/views/components/` does not exist |
| Blade **partials** (dashboard) | `main/{css,scripts,topbar,sidebar,footer,customizer}`, `sections/home/*`, `sections/qrx/*`, `sections/models/download-qrx`, `sections/view/*`, `sections/auth/*`, `sections/errors/*` | `@include`-based. There is **no `@extends` / `@yield` layout**: 14 views each contain a full `<!doctype html>` shell |
| Blade **partials** (public) | `Frontend/main/{css,scripts,nav,footer}`, `Frontend/sections/*` | |
| **Livewire** | `qrx-create`, `qrx-edit`, `qr-demo` + 8 `features/*` + 4 `settings/*` partials | Create and edit templates are ~95 % identical |
| Filament | 11 resources, 1 relation manager, 3 widgets | Stock v2 components |

### 4.3 Responsiveness, accessibility, dark mode

- **Responsiveness:** both themes use the Bootstrap grid, so the layouts adapt to screen size. The QR preview is server-rendered SVG at a fixed pixel `size` (150–500), with no responsive scaling.
- **Accessibility (problems):**
  - The right-click context menu is disabled site-wide, and every mouse click is logged to the console ([Frontend/main/scripts.blade.php:18-32](../resources/views/Frontend/main/scripts.blade.php#L18-L32)).
  - Duplicate `id="foo"` on up to 4 inputs per QR landing page (e.g. `sections/view/vcard.blade.php`), so labels are not associated with their fields.
  - Download triggers are `<a type="button">` without `href`, and one anchor is empty ([qrs.blade.php:62-67](../resources/views/Dashboard/sections/qrx/qrs.blade.php#L62-L67)).
  - Logo `alt=""`. QR images use the type (`"url"`) as alt text. Dashboard progress bars have a fixed `aria-valuemax="100"` that does not match their values.
  - The Livewire tab buttons have no `role="tab"` / `aria-selected`, and inputs lack `<label for>`.
  - Error messages are not tied to fields with `aria-describedby`.
- **Dark mode:**
  - Public site: a jQuery toggle targets a `.switch` element that **does not exist** (dead code).
  - Client dashboard: only via the 683-line theme **customizer** offcanvas ([Dashboard/main/customizer.blade.php](../resources/views/Dashboard/main/customizer.blade.php)), a template demo panel that also lets users change layout, sidebar colour, etc.
  - Filament: off (no config published).

---

## 5. Current design-system findings

There is **no design system.** Three unrelated visual languages are glued together:

| Token | Public site | Client dashboard | Admin (Filament) | Unused Tailwind config |
|---|---|---|---|---|
| CSS framework | Bootstrap 5.3.0 + Martex CSS | Bootstrap 5.3.0-alpha2 + Themesbrand `app.min.css` | Tailwind 3 (bundled) | Tailwind 3.4 |
| Fonts | **3 Google families loaded** (Rubik, Plus Jakarta Sans, Inter), only Jakarta used ([Frontend/main/css.blade.php:10-12](../resources/views/Frontend/main/css.blade.php#L10-L12)) | Poppins | Filament default | Figtree |
| Primary colour | `purple-theme.css` (logo is `logo-pink.png`). 7 alternate theme stylesheets linked **with a missing `/`, so every one 404s** ([css.blade.php:33-39](../resources/views/Frontend/main/css.blade.php#L33-L39)) | Template "success" green used as the primary action colour | Filament amber default | — |
| Icons | Flaticon + Font Awesome 6.5.2 (CDN) | **5 icon sets:** Remix (`ri-`), MDI (`mdi-`), Phosphor (`ph-`), Bootstrap Icons (`bi-`), plus Font Awesome on the QR landing | Heroicons | — |
| Chart colours | — | Hard-coded hex arrays in PHP ([app/Charts/MonthlyQrxCodesChart.php:32](../app/Charts/MonthlyQrxCodesChart.php#L32), [MonthlyStatusChart.php:26](../app/Charts/MonthlyStatusChart.php#L26)) | Hard-coded `#36A2EB` ([QrxCodeChart.php:37](../app/Filament/Widgets/QrxCodeChart.php#L37)) | — |
| Spacing / radii / breakpoints | Theme utility classes (`r-04`, `s-52`, `w-700`, `mb-70`) | Bootstrap + template utilities (`fs-3xl`, `fs-3xs`) | Tailwind | — |
| Status colours | — | `bg-success` / `bg-warning` / `bg-danger` inline in Blade conditionals ([qrs.blade.php:49](../resources/views/Dashboard/sections/qrx/qrs.blade.php#L49)) | `'success '` and `'danger '` keys **with trailing spaces** ([QrxCodeResource.php:56-58](../app/Filament/Resources/QrxCodeResource.php#L56-L58)), so those badge colours never apply | — |

**Other UI inconsistencies, duplication and dead code:**
- Inline styles are rare: only the progress-bar widths ([plan-detail.blade.php:16,33](../resources/views/Dashboard/sections/home/plan-detail.blade.php#L16)). That template also divides by `$plan->qr_count` and `$totalDays` with no zero guard.
- **14 duplicated HTML shells.** `qrx-all/active/pause/exp.blade.php` are byte-for-byte copies except the title.
- `Dashboard/view.blade.php:51-57` loads **clipboard.js 7 times** from 6 CDNs, including the dead `rawgit.com` and a relative `dist/clipboard.min.js` that 404s.
- Every dashboard page ships the template customizer (683 lines) and the "Premium Multipurpose Admin & Dashboard Template / Themesbrand" meta description.
- `public/assets-dashboard` is 115 MB: 63 MB of libs (gmaps docs and tests, choices.js TypeScript sources, CKEditor translations, 471 crypto SVG icons, etc.). Views reference only about 7 files from it.
- A placeholder URL `https://hossam-x-studios.com` is hard-coded as preview content in all 3 Livewire components, and the name also appears in both footers.
- `type-chart.blade.php` is an empty file. `livewire/features/mp3.blade.php` and the location tab are unused or commented out.
- A JS theme switcher (`changer.js`, `styleswitch.js`) is loaded on the landing page, but nothing uses it.

---

## 6. Issues & risks

### 🔴 High

| # | Issue | Where | Impact |
|---|---|---|---|
| H1 | **No authorization on any QR action (IDOR).** `find($id)` with no `user_id` check | [QrxController.php:107-175](../app/Http/Controllers/QrxController.php#L107-L175) (`switchStatuQr`, `editQr`, `deleteQr`, `downloadQr`) | Any logged-in user can pause, delete, download or open the edit form for **any** QR by iterating ids |
| H2 | **QR takeover via edit.** `QrxEdit::generat()` sets `user_id = Auth()->user()->id` on the QR it was given | [QrxEdit.php:183](../app/Http/Livewire/QrxEdit.php#L183) combined with H1 | An attacker can move another customer's QR into their own account |
| H3 | **Livewire 2.12.5 RCE (CVE-2024-47823)** | `composer.lock`. `QrxCreate` uses `WithFileUploads` | Remote code execution. Patch to ≥ 2.12.7 immediately, independent of the redesign |
| H4 | **Stored XSS on the public QR landing.** URL feature values are not validated, so a `javascript:` URL is output into `href` | [sections/view/url.blade.php:9](../resources/views/Dashboard/sections/view/url.blade.php#L9); no validation in [QrxCreate.php:200-205](../app/Http/Livewire/QrxCreate.php#L200-L205) | Script runs on the app's origin when a victim scans and taps the button |
| H5 | **No input validation in Livewire create/edit.** No `rules()` or `validate()` anywhere | `QrxCreate`, `QrxEdit`, `QrDemo` | Arbitrary strings reach the DB and the QR renderer. `Hex::fromString()` throws on bad input, causing 500s |
| H6 | **Unbounded QR rendering (DoS).** `size` comes from the client: Livewire public property (public, **unauthenticated** `QrDemo`) and `?size=` on download | [QrDemo.php:14](../app/Http/Livewire/QrDemo.php#L14), [QrxController.php:146](../app/Http/Controllers/QrxController.php#L146) | A request with `size=100000` ties up PHP workers. `?type=` also sets `format()` and the `Content-Type` header with no whitelist |
| H7 | **Schema ↔ code mismatches (inferred) that break core flows under MySQL strict mode** | see list below | Social login, checkout, QR creation and admin plan creation would fail on a freshly migrated DB |
| H8 | **Public scan page crashes for owners without a subscription.** `$user->subscriptions->first()->items` on null | [QrxController.php:84-86](../app/Http/Controllers/QrxController.php#L84-L86) | Printed QR codes return HTTP 500 once a subscription lapses or is deleted, instead of a friendly page |
| H9 | **Framework and dependencies out of support with 61 advisories** | `composer.lock`, `package-lock.json` | Ongoing exposure. Laravel 10 receives no security fixes |

**H7 details (each confirmed by reading code against migrations):**
- `users` has **no `provider` / `provider_id` columns**, and `phone`, `country`, `city`, `area` are NOT NULL. Yet Socialite writes the provider fields and leaves the address fields empty ([SocialiteController.php:30-37](../app/Http/Controllers/SocialiteController.php#L30-L37)).
- `qrx_codes.path` is NOT NULL and unique, but `QrxCode::generateCode()` calls `save()` **before** `path` is set ([QrxCode.php:58-61](../app/Models/QrxCode.php#L58-L61), [QrxCreate.php:147](../app/Http/Livewire/QrxCreate.php#L147)).
- `transactions.type` is NOT NULL, but [CheckoutController.php:24-26](../app/Http/Controllers/CheckoutController.php#L24-L26) only sets `amount`. The transaction is also recorded **before** payment happens, so the admin "Transaction" revenue card is fiction.
- `Plan::$fillable` lists `type`, `details`, `duration`, `scans_count`, `qrs_count`, `is_hidden`, none of which exist. It omits the real `plan_id`, `price_id`, `qr_count` and `scan_count` ([Plan.php:16-26](../app/Models/Plan.php#L16-L26)), so Filament **cannot create plans** (mass-assignment drops the required fields).
- `QrxCode::$fillable` lacks `user_id` and `type` but includes the non-existent `client_id`, so Filament edits to the owner are silently dropped. `FeatureVcard::$fillable` has the typo `qrs_code_id` and lacks `phone`. `Contact::$fillable` lacks `message`.

### 🟠 Medium

| # | Issue | Where |
|---|---|---|
| M1 | Missing `break` after the `email` case in both create and edit, so an Email QR also creates an empty `FeatureMessage` row. The same fall-through in `QrxEdit::mount()` overwrites phone/message state | [QrxCreate.php:207-214](../app/Http/Livewire/QrxCreate.php#L207-L214), [QrxEdit.php:63-67](../app/Http/Livewire/QrxEdit.php#L63-L67), [QrxEdit.php:243-250](../app/Http/Livewire/QrxEdit.php#L243-L250) |
| M2 | Changing a QR's type in edit creates a new feature row but **never deletes the old one**. Orphans accumulate | [QrxEdit.php:289-351](../app/Http/Livewire/QrxEdit.php#L289-L351) |
| M3 | Editing is blocked once the user reaches their QR quota (`qr_count > count` check copied from create). Editing the Wi-Fi type **wipes the password** because it is never loaded in `mount()` | [QrxEdit.php:171](../app/Http/Livewire/QrxEdit.php#L171), [QrxEdit.php:82-85](../app/Http/Livewire/QrxEdit.php#L82-L85) |
| M4 | Subscription validity is computed as `created_at + 1 year`, ignoring Stripe status (cancelled, past_due, trial). Cashier's `subscribed()` / `onGracePeriod()` are never used. Duration is hard-coded | `QrxCreate.php:132-134`, `QrxEdit.php:169-171`, [QrxController.php:35-39](../app/Http/Controllers/QrxController.php#L35-L39) |
| M5 | "Current plan" lookup (`subscriptions->first()->items->first()->stripe_product` → `Plan`) is duplicated 5×. QR-rendering code is duplicated ~14× | `HomeController`, `QrxController`, `QrxCreate`, `QrxEdit` |
| M6 | Short codes are `'qx-'.rand(100000,999999)`: guessable, enumerable, and collision-prone (unique-constraint exception with no retry) | [QrxCode.php:59](../app/Models/QrxCode.php#L59) |
| M7 | Scan counting: a non-atomic `+= 1` (race), counted on every page load (link previews, bots, refreshes), and status is flipped to `exp` on a read request (GET with side effects) | [QrxController.php:89-96](../app/Http/Controllers/QrxController.php#L89-L96) |
| M8 | `wire:poll` on the root of the create form re-renders and regenerates the QR SVG server-side every 2 s while the page is open | [livewire/qrx-create.blade.php:1](../resources/views/livewire/qrx-create.blade.php#L1) |
| M9 | Wi-Fi passwords are stored in plain text and rendered into the public page as `value=` and a `WIFI:` link. This is inherent to the feature, but it is visible to anyone with the short URL (and codes are guessable, M6) | [sections/view/wifi.blade.php:21-34](../resources/views/Dashboard/sections/view/wifi.blade.php#L21-L34) |
| M10 | Socialite: `{provider}` is not whitelisted, there is no `services.*` config for any provider, and the raw exception message is shown to the user. `DB::commit()` only runs on the create path, which leaves a transaction open on the login path | [SocialiteController.php](../app/Http/Controllers/SocialiteController.php), [config/services.php](../config/services.php) |
| M11 | `CheckoutController` uses `Plan::find($id)` with an optional `{id?}` but a required `string $id` parameter. No null check (500 on a bad id). Inactive plans can be bought, and the landing page lists inactive plans too | [CheckoutController.php:14-16](../app/Http/Controllers/CheckoutController.php#L14-L16), [HomeController.php:21](../app/Http/Controllers/HomeController.php#L21) |
| M12 | Contact form: no throttling or captcha, `phone` rule `numeric|min:11` means value ≥ 11 (not length), `message` max 255 while the column is `longText`, and the response is a blank 200 (missing `return` on the redirect) | [ContactController.php:12-27](../app/Http/Controllers/ContactController.php#L12-L27) |
| M13 | N+1 / redundant queries: the sidebar runs 4 queries on every page from a raw `<?php` block, the dashboard charts run 11 separate `count()` queries, and `Auth()->user()->subscriptions` is lazy-loaded repeatedly | [sidebar.blade.php:1-8](../resources/views/Dashboard/main/sidebar.blade.php#L1-L8), [app/Charts/*](../app/Charts) |
| M14 | Missing views for routed pages: `profile.edit`, `auth.forgot-password`, `auth.reset-password`, `auth.verify-email`, `auth.confirm-password`, `layouts.app`, `layouts.guest` | routes vs `resources/views` |
| M15 | The Filament admin has no `FilamentUser::canAccessFilament()` on `Admin`, and there is no documented way to create admins (no seeder or command) | [app/Models/Admin.php](../app/Models/Admin.php) |
| M16 | Duplicate Wi-Fi/QR names and the `delete` route leave the media and PNG out of sync. `deleteQr` deletes the file before checking that the record exists | [QrxController.php:129-135](../app/Http/Controllers/QrxController.php#L129-L135) |

### 🟡 Low

| # | Issue |
|---|---|
| L1 | Dead code: `app/Http/Controllers/Api/*` (wrong namespace, unrouted, references non-existent `Cart`/`Client` models and an undefined `auth:api` guard), [routes/api.php:5](../routes/api.php#L5) import, `tymon/jwt-auth`, `User::generateCode()/destoryCode()`, `FeatureLocation`, `FeatureMp3`, unused `use` statements (e.g. `App\Models\Folder` at [QrxController.php:8](../app/Http/Controllers/QrxController.php#L8)), Breeze `AppLayout`/`GuestLayout` |
| L2 | `LatestClients.php` declares class `LatestUsers` (PSR-4 violation; Filament discovery may skip it) |
| L3 | Magic strings for status (`active`/`pause`/`exp`) and type (7 values) with no Enum or casts. `styles.color` defaults to `'(0, 0, 0)'`, which is not a hex value that the code can parse |
| L4 | Typos baked into identifiers and route names: `countact`, `switchStatuQr`, `generat`, `subscripe`, `destoryCode`. Renaming them is a breaking change for any bookmarked or linked URLs |
| L5 | `Cache::flush('some-key')` clears the **entire** cache on every QR edit ([QrxEdit.php:225](../app/Http/Livewire/QrxEdit.php#L225)) |
| L6 | `expires_in` multiplies the TTL by 36,000,000 ([Api/AuthController.php:72](../app/Http/Controllers/Api/AuthController.php#L72)); dead code, but wrong |
| L7 | 128 MB of vendored theme assets are committed to git, inflating clones and deploys |
| L8 | Email verification is not enforced, and there is no `MustVerifyEmail` |
| L9 | Stock Laravel README, and every commit message is "go", so the git history gives no context |
| L10 | Livewire views use `wire:model.live` (Livewire 3 syntax) on Livewire 2. It happens to work, which makes the upgrade easier |

### Tests

- **Present:** stock Breeze feature tests ([tests/Feature/Auth/*](../tests/Feature/Auth), [ProfileTest.php](../tests/Feature/ProfileTest.php)) and example tests.
- **Expected to fail (not run):**
  - `ProfileTest`, `PasswordResetTest` and the password-confirmation test hit views that don't exist.
  - `RegistrationTest` posts without `phone/country/city/area`, so validation fails.
  - `UserFactory` does not fill the NOT NULL address columns.
- **Missing entirely:** QR create/edit/delete/status/download, ownership/authorization, the public scan flow and plan limits, checkout/subscription gating, the contact form, Filament resources, and model factories for every model except `User`.
- **No CI** configuration in the repo.

### Risks specific to the redesign / upgrade

1. **Livewire 2 → 3/4 and Filament 2 → 3+ must move together** (Filament v3 requires Livewire 3). This is the largest single step: all 11 resources, the widgets and both Livewire components change API.
2. **The public QR landing (`/x/{code}`) and the short-URL format are a hard contract.** Printed QR codes in the wild point at them, so the URL, route name and behaviour must stay byte-compatible.
3. **Schema drift (H7):** we need the real production schema before writing any migration. Otherwise an upgrade migration could fail or destroy data.
4. **Stripe:** the Cashier 15 → 16 upgrade and any change to subscription gating must be tested against Stripe test mode with real webhook payloads. Existing subscribers must keep access.
5. **Stored PNGs** (`storage/app/public/qrxs`) are referenced by path. Any change to rendering or storage needs a regeneration or migration plan.
6. **Two auth guards** (`user` vs `web` → admins) are unusual. Upgrading Filament and Breeze can silently change which guard is used.
7. Fixing the authorization holes (H1/H2) will **change behaviour for anyone who currently relies on them**, for example support staff editing customer QRs through the client dashboard.
8. No test safety net: characterization tests have to be written first.

---

## 7. Open questions (please answer before Phase 2)

1. **Production state:** is this app live with real customers and paying Stripe subscribers? Can you share the **production schema** (`SHOW CREATE TABLE` / `mysqldump --no-data`) so I can reconcile it with the migrations (H7)?
2. **Hosting:** which PHP version, web server and deploy method are used in production? Can we target **PHP 8.3+ / Laravel 13**, or must we stop at an intermediate version?
3. **Urgent fixes:** may I fix the **security issues (H1–H6, Livewire patch) as a separate first step on its own branch**, ahead of the redesign?
4. **Language:** do you need **Arabic + RTL** (the `+20` phone placeholder suggests Egypt)? Which locales, and which is the default?
5. **Brand:** keep the "Qrx" name and logos? Do you have brand colours or fonts, or should I propose them? What is the relationship to **hossam-x-studios.com** (footer credit + preview URL): keep it, replace it, or remove it?
6. **Frontend direction:** are you open to dropping both Bootstrap themes in favour of **Tailwind** (which Filament already uses), or must Bootstrap stay? Is **dark mode** a requirement?
7. **Client dashboard:** should it stay a custom Blade/Livewire app, or would you accept moving it into a **second Filament panel** (much less custom UI to maintain, but less design freedom)?
8. **Admin panel:** keep Filament and upgrade it (v2 → v5)? Should the 7 per-type "Feature" resources be merged into the QR resource?
9. **Billing rules:** is the plan always **1 year**? Should access follow Stripe's subscription status (cancel, past-due, grace period)? Is there or should there be a **free tier**? What should a scanner see when the owner's plan has lapsed?
10. **QR types:** finish **Location** and **MP3/file**, or remove them? Any new types wanted (PDF, app links, social links, logo-in-QR)?
11. **Scan analytics:** should scans be unique, bot-filtered and time-series (date, device, country), or is a raw counter enough? This affects the data model.
12. **API / mobile:** is any **mobile app or third party** using JWT/Sanctum or the dead API controllers? If not, can I remove `tymon/jwt-auth` and `app/Http/Controllers/Api`?
13. **Social login:** which providers (Google, Facebook…) are actually configured in production?
14. **URLs:** may I rename misspelled **route names** (`countact`, `subscripe`…) and URLs, or must the existing URLs keep working (redirects)?
15. **Assets in git:** OK to remove the ~128 MB of vendored theme assets from the repo once nothing references them? (History rewrite optional, not required.)
