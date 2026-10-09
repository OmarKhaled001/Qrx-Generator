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
| M3 UI foundations | ✅ done | see git log |
| M4 Auth | ✅ done | see git log |
| M5 QR management | ✅ done | see git log |
| M6 Public | ✅ done | see git log |
| M7 Billing | ✅ done | see git log |
| M8 Admin | ✅ done | see git log |
| M9 MVP cutover | ✅ done (merged to master locally) | see git log |
| L1 Social login | ✅ done | see git log |
| L2 Analytics | ✅ done | see git log |
| L3 Design completion | ✅ done | see git log |
| L4 Marketing site | ✅ done | see git log |
| L5 Free tier | ⏳ (folded into M2/M7, see DECISIONS) | |
| L6 Admin reporting | 🔄 next | |
| L7 Location QR | ⏳ (folded into M2, see DECISIONS) | |
| L8 Arabic | ⏳ | |

## Current phase notes

L4 done: landing live demo (`App\Livewire\Public\QrDemo`), contact form (`ContactForm`, `contacts` table, honeypot, throttle), admin Messages inbox (`ContactResource`).

**Next:** L6 admin reporting: store Stripe `invoice.paid` events (Cashier `WebhookReceived` listener → `payments` table), revenue + signups chart widgets. Then L8 Arabic QA (validation/auth/password messages in `lang/ar/*.php`, Filament in Arabic, RTL review), then final merge + local run + report.
