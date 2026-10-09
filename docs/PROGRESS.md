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
| M9 MVP cutover | 🔄 next | |
| L1 Social login | ⏳ | |
| L2 Analytics | ⏳ | |
| L3 Design completion | ⏳ | |
| L4 Marketing site | ⏳ | |
| L5 Free tier | ⏳ (folded into M2/M7, see DECISIONS) | |
| L6 Admin reporting | ⏳ | |
| L7 Location QR | ⏳ (folded into M2, see DECISIONS) | |
| L8 Arabic | ⏳ | |

## Current phase notes

M8 done: Filament 5 panel at `/admin` (is_admin only, brand colours, dark mode, SetLocale), resources Users / QR codes / Plans, StatsOverview widget, `app:make-admin` command.

**Next:** M9 MVP cutover: end-to-end journey test (register → verify → create QR → scan → edit → download; RTL), behaviour-inventory parity check (every row → v2 test), README + deploy docs, remove `spikes/` vendor leftovers, merge `redesign/v2` into `master` (local only, no push).
