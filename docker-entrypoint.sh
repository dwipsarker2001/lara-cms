#!/bin/sh
set -e

if [ ! -d "vendor" ]; then
    composer install --no-interaction --prefer-dist
fi

if [ ! -f ".env" ]; then
    cp .env.example .env
fi

if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    php artisan key:generate --force
fi

if [ ! -L "public/storage" ]; then
    ln -s ../storage/app/public public/storage 2>/dev/null || true
fi

chmod -R 775 storage bootstrap/cache 2>/dev/null || true

exec "$@"
