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
| L1 Social login | 🔄 next | |
| L2 Analytics | ⏳ | |
| L3 Design completion | ⏳ | |
| L4 Marketing site | ⏳ | |
| L5 Free tier | ⏳ (folded into M2/M7, see DECISIONS) | |
| L6 Admin reporting | ⏳ | |
| L7 Location QR | ⏳ (folded into M2, see DECISIONS) | |
| L8 Arabic | ⏳ | |

## Current phase notes

M9 done: end-to-end journey test (LTR + RTL), 165 tests green on PHP 8.5 with SQLite and MySQL 8.4, behaviour inventory mapped to v2 tests, README rewritten, MVP merged into `master` (local).

**Next:** L1 social login (Google/Facebook via Socialite; buttons only when configured), then L2 analytics, L3 design completion (Filament theme, gallery), L4 marketing (demo, contact form), L6 admin reporting (invoice.paid), L8 Arabic QA (validation/auth messages, Filament). L5 (free tier) and L7 (Location) were folded into the MVP.
