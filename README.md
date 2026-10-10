# Qrx

Dynamic QR codes you can edit, track and brand. Every printed code opens a short link (`/x/{code}`), so its content can change without reprinting.

- **8 QR types:** website, text, phone call, SMS, email, contact card (vCard), Wi-Fi, location
- **Builder:** live preview, module and corner styles, colours or gradients, quiet zone, low-contrast warning
- **Scan analytics:** counts and per-scan rows (device, referrer, country) with **no raw IP addresses stored**
- **Plans:** a free tier (3 codes, 100 scans each) and paid plans through Stripe (Laravel Cashier)
- **English and Arabic,** right-to-left throughout (including emails and the admin); light and dark mode
- **Admin panel (Filament)** for users, QR codes, plans and messages, in the same brand theme
- **Landing page** with a scroll-driven hero animation (GSAP), use cases, style gallery, live demo, analytics preview, plan comparison and FAQ

## Stack

PHP 8.5 · Laravel 13 · Livewire 4 · Filament 5 · Fortify · Cashier 16 (Stripe) · Socialite · bacon/bacon-qr-code 3 · Tailwind CSS 4 · Vite 8

## Requirements

- PHP 8.3+ (8.5 recommended) with `imagick` (PNG/EPS QR files), `gd`, `intl`, `pdo_sqlite` or `pdo_mysql`, `bcmath`
- Composer 2, Node 22+
- SQLite (local) or MySQL 8.4 (production)

## Local setup

```sh
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve            # http://localhost:8000
npm run dev                  # optional: hot reload while editing views/CSS
```

Local PHP without `imagick` still runs everything except PNG/EPS downloads. The Docker image in `docker/php-test` has every extension:

```sh
docker build --build-arg PHP_VERSION=8.5 -t qrx-php85 docker/php-test
docker run --rm -p 8000:8000 -v "$PWD":/app -w /app qrx-php85 php artisan serve --host=0.0.0.0
```

### Demo accounts (seeded outside production; password `password`)

| Account | Email | Use |
|---|---|---|
| Admin | `admin@qrx.test` | Admin panel at `/admin` |
| Demo customer | `demo@qrx.test` | Free plan; one QR code of every type and sample scans (more than the free limit, to show the "limit reached" states) |
| Subscriber | `subscriber@qrx.test` | Business plan, Arabic interface. Created by hand in the local database (a local subscription record, no Stripe), not by the seeder; `scripts/screenshots.mjs` uses it for the builder. |

Mail goes to the log (`MAIL_MAILER=log`), so verification and reset links are in `storage/logs/laravel.log`.

## Stripe (payments)

1. Create test-mode keys and set `STRIPE_KEY`, `STRIPE_SECRET`.
2. Create a product with a recurring monthly price for each paid plan and set `STRIPE_PRICE_STARTER`, `STRIPE_PRICE_BUSINESS`, `STRIPE_PRICE_AGENCY`, then `php artisan db:seed --class=PlanSeeder` (or edit plans in `/admin`).
3. Webhooks: `stripe listen --forward-to localhost:8000/stripe/webhook`, and set `STRIPE_WEBHOOK_SECRET` to the secret it prints. Events needed: `customer.subscription.created/updated/deleted`, `customer.updated`, `customer.deleted`, `invoice.payment_action_required`, `invoice.paid`.
4. Enable the Customer Portal in Stripe for "Manage billing".

Without keys, everything works except the checkout and billing-portal buttons.

## Social login (optional)

Set `GOOGLE_CLIENT_ID/SECRET` and/or `FACEBOOK_CLIENT_ID/SECRET`. Callback URLs are `{APP_URL}/auth/{google|facebook}/callback`. The buttons appear only for configured providers.

## Tests and checks

```sh
bin/test                      # full suite in Docker (PHP 8.5 + imagick)
php artisan test              # locally (imagick-dependent paths need the extension)
vendor/bin/pint --test        # code style
vendor/bin/phpstan analyse    # static analysis (Larastan level 5)
```

Guardrails in the suite:

- no physical left/right CSS utilities (RTL);
- no untranslated view text;
- every key present in both `lang/en*` and `lang/ar*` (JSON and PHP groups);
- no English left on any page rendered in Arabic (public, signed-in and admin);
- Alpine loaded on every layout.

CI (`.github/workflows/ci.yml`) runs everything on SQLite and MySQL 8.4.

## Brand, language and screenshots

- **Logo and icons:** `public/brand/` (SVG masters, PNG sizes, Open Graph cards), favicons and the web manifest in `public/`. Usage and colours: [docs/BRAND.md](docs/BRAND.md).
- **Terminology and writing rules (English and Arabic):** [docs/GLOSSARY.md](docs/GLOSSARY.md).
- **Screenshots** of every main page in English and Arabic, desktop and mobile, plus the hero animation frame by frame, are in [docs/screenshots/](docs/screenshots). To regenerate them against a running, seeded app:

```sh
npm run build
docker run --rm --network host --user "$(id -u):$(id -g)" -e HOME=/tmp -v "$PWD":/app -w /app \
  mcr.microsoft.com/playwright:v1.64.0-noble node scripts/screenshots.mjs   # APP_URL=… to target another URL
```

## Production notes

The live deployment (Hostinger, `qrx-generator.omarkhaled.info`) and the update flow are documented in [DEPLOYMENT.md](DEPLOYMENT.md), in Arabic. `.env.production.example` is the server's settings template; `deploy.sh` is the update script.

- `APP_ENV=production`, `APP_DEBUG=false`, MySQL, a real mail driver.
- `php artisan migrate --force && php artisan db:seed --class=PlanSeeder --force`, then `php artisan app:make-admin you@example.com`.
- `php artisan config:cache route:cache view:cache` and `npm run build` on deploy.
- Behind Cloudflare, scans get a country automatically (`CF-IPCountry`); otherwise set `QRX_COUNTRY_HEADER` or leave it empty.
- The `/x/{code}` URL is what printed codes contain: keep it stable.

## Project map

| Path | What |
|---|---|
| `app/Enums/QrType.php` | The 8 types: labels, fields, validation |
| `app/Services/QrRenderer.php` | SVG/PNG/EPS rendering (bacon-qr-code 3) |
| `app/Services/Subscriptions.php` | Current plan, quotas, scan limits |
| `app/Actions/Qr/*` | Create / update / record scan |
| `app/Livewire/Dashboard/*` | QR list, builder, detail page |
| `app/Http/Controllers/ScanController.php` | Public scan page `/x/{code}` |
| `app/Filament/*` | Admin panel |
| `resources/css/tokens.css` | Design tokens (colours, type, radii) |
| `resources/views/components/ui/*` | UI components (`/dev/ui` gallery locally) |
| `resources/views/landing/*` | Landing page sections; `resources/js/hero.js` is the hero animation |
| `lang/en.json`, `lang/ar.json`, `lang/{en,ar}/*.php` | Translations (English copy edits go in `en.json` values) |
| `public/brand/`, `scripts/brand-assets.mjs` | Logo files and the script that rasterises them |
| `docs/` | Audit, plan, decisions log, progress, brand guide, glossary, screenshots |
