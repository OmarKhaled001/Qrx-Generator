# Progress (resume file)

Autonomous build of Qrx v2 on branch `redesign/v2`. Plan: [REDESIGN_PLAN.md](REDESIGN_PLAN.md). Judgement calls: [DECISIONS.md](DECISIONS.md). Behaviour map: [behaviour-inventory.md](behaviour-inventory.md).

**Rules:** nothing is pushed (origin is public). Each phase ends with the full suite green (`bin/test`), then a commit. This file is updated after every phase.

## How to resume

```sh
git checkout redesign/v2
docker build --build-arg PHP_VERSION=8.5 -t qrx-php85 docker/php-test   # once
composer install && npm install && npm run build
cp -n .env.example .env && php artisan key:generate
bin/test                      # full suite in PHP 8.5 + imagick
vendor/bin/pint --test && vendor/bin/phpstan analyse
```

## Status

| Phase | State | Commit |
|---|---|---|
| M0 Prep | ✅ done | `1f9899b` |
| M1 Skeleton | ✅ done | see git log |
| M2 Domain | ✅ done | see git log |
| M3 UI foundations | 🔄 next | |
| M4 Auth | ⏳ | |
| M5 QR management | ⏳ | |
| M6 Public | ⏳ | |
| M7 Billing | ⏳ | |
| M8 Admin | ⏳ | |
| M9 MVP cutover | ⏳ | |
| L1 Social login | ⏳ | |
| L2 Analytics | ⏳ | |
| L3 Design completion | ⏳ | |
| L4 Marketing site | ⏳ | |
| L5 Free tier | ⏳ (folded into M2/M7, see DECISIONS) | |
| L6 Admin reporting | ⏳ | |
| L7 Location QR | ⏳ (folded into M2, see DECISIONS) | |
| L8 Arabic | ⏳ | |

## Current phase notes

M2 done: plans / qr_codes / scans schema, `QrType` (8 types incl. Location) and `QrStatus` enums, `QrDesign`, `QrOptions`, `ShortCode`, `QrRenderer` (bacon 3), `Subscriptions` (Cashier → plan, free fallback, quotas), actions (Create/Update/RecordScan), `QrCodePolicy`, factories and seeders (admin@qrx.test / demo@qrx.test, password `password`).

**Next:** M3 UI foundations (tokens, fonts, layouts, Blade components, i18n/RTL infrastructure, lint tests).
