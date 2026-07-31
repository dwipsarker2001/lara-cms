#!/bin/sh
set -e

# 1. Ensure required storage and cache directories exist with proper write permissions
mkdir -p storage/framework/views \
         storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/testing \
         storage/logs \
         bootstrap/cache

chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 2. Install dependencies if vendor directory is missing
if [ ! -d "vendor" ]; then
    composer install --no-interaction --prefer-dist
fi

# 3. Copy environment configuration if missing
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# 4. Generate application key if missing
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    php artisan key:generate --force
fi

# 5. Create storage symlink if missing
if [ ! -L "public/storage" ]; then
    ln -s ../storage/app/public public/storage 2>/dev/null || true
fi

exec "$@"
