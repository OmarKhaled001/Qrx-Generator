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
| L5 Free tier | ✅ folded into M2/M5/M7 | |
| L6 Admin reporting | ✅ done | see git log |
| L7 Location QR | ✅ folded into M2/M5/M6 | |
| L8 Arabic | ✅ done | see git log |

## Current phase notes

All phases done (M0–M9, L1–L8). 197 tests green on PHP 8.5 (SQLite; MySQL 8.4 verified at M9). Pint and Larastan clean.

**Done.** Merged into `master` (local, not pushed); app running at http://localhost:8010 (`docker rm -f qrx-serve` to stop). Real-browser visual check still to do by a person (no Chrome on this machine).
