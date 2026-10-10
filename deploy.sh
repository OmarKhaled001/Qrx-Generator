#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"
branch="${1:-master}"
php artisan down --retry=60 || true
trap 'php artisan up' EXIT
git pull --ff-only origin "$branch"
php -d disable_functions= -d memory_limit=-1 "$(command -v composer)" install \
    --no-dev --prefer-dist --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan optimize
php artisan queue:restart
echo "Deployed $(git rev-parse --short HEAD) from $branch."
