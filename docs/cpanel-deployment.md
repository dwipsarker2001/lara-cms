# cPanel Deployment Guide for Lara-CMS

This guide details how to deploy **Lara-CMS** to cPanel or standard shared hosting environments via SSH, Terminal, or Git.

---

## Server Requirements

Before deploying, ensure your cPanel hosting environment meets the following requirements:

- **PHP**: 8.3+ (PHP 8.5 recommended)
- **Database**: MySQL 8.0+ or MariaDB 10.4+
- **PHP Extensions**: `pdo_mysql`, `mbstring`, `zip`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- **Composer**: v2.x
- **Node.js**: v20+ (for building frontend assets via Vite)
- **SSH / Terminal Access**: Enabled in cPanel

---

## 1. Document Root Configuration

Lara-CMS follows standard Laravel architecture. The web server must point the domain or subdomain document root directly to the **`public/`** directory.

### Example Directory Structure:
```
/home/username/
└── travel.eapply.site/         <-- Repository Root
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── public/                <-- Set Domain Document Root Here
    ├── storage/
    ├── vendor/
    └── .env
```

If your cPanel primary domain forces `public_html`, point the document root in cPanel (Domains / Subdomains) to `/public_html/public` or move the application root outside `public_html` and symlink `public_html` to `public`.

---

## 2. Setting Up Storage & Cache Permissions

Laravel requires writable permissions on `storage` and `bootstrap/cache`. On a fresh deployment or git pull, ensure all framework subdirectories exist with correct permissions (`775` or `755` depending on your host user context):

```bash
mkdir -p storage/framework/views \
         storage/framework/cache/data \
         storage/framework/sessions \
         storage/logs \
         bootstrap/cache

chmod -R 775 storage bootstrap/cache
```

---

## 3. Environment Configuration (`.env`)

1. Copy `.env.example` to `.env` if not present:
   ```bash
   cp .env.example .env
   ```
2. Edit `.env` with your production settings:
   ```env
   APP_NAME="Lara CMS"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_cpanel_dbname
   DB_USERNAME=your_cpanel_dbuser
   DB_PASSWORD="your_strong_db_password"

   CACHE_STORE=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=sync
   ```
3. Generate the application encryption key:
   ```bash
   php artisan key:generate
   ```

---

## 4. Initial Build & Deployment Steps

Run the following commands via cPanel SSH or Terminal:

```bash
# 1. Install PHP production dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# 2. Install Node dependencies & build frontend assets
npm install --include=dev
npm run build

# 3. Create storage symlink
php artisan storage:link

# 4. Run database migrations
php artisan migrate --force

# 5. Optimize production caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Ongoing Updates (Pulling New Releases)

When updating Lara-CMS to a new release:

```bash
# 1. Pull latest release code
git pull origin main

# 2. Ensure storage folders exist
mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs bootstrap/cache

# 3. Re-install & build assets
composer install --no-dev --optimize-autoloader --no-interaction
npm run build

# 4. Run pending migrations & refresh cache
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 6. Optional: Automated GitHub Actions Deployment Workflow

If you wish to set up continuous deployment from GitHub to your cPanel server via SSH, create `.github/workflows/deploy.yml` in your custom repo:

```yaml
name: Deploy to cPanel

on:
  push:
    branches:
      - main
  workflow_dispatch:

jobs:
  deploy:
    name: Deploy to Production
    runs-on: ubuntu-latest

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Execute Remote SSH Commands
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: ${{ secrets.SSH_HOST }}
          port: ${{ secrets.SSH_PORT }}
          username: ${{ secrets.SSH_USERNAME }}
          password: ${{ secrets.SSH_PASSWORD }}
          script_stop: true
          script: |
            set -e
            cd ~/your-domain-folder

            echo "1. Pulling latest code..."
            git pull origin main

            echo "2. Ensuring storage directories exist..."
            mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs bootstrap/cache
            chmod -R 775 storage bootstrap/cache

            echo "3. Installing PHP dependencies..."
            composer install --no-dev --optimize-autoloader --no-interaction

            echo "4. Building assets..."
            npm install --include=dev
            npm run build

            echo "5. Migrating & caching..."
            php artisan migrate --force
            php artisan optimize:clear
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache

            echo "Deployment completed successfully!"
```

---

## Troubleshooting Common cPanel Issues

### Issue 1: `Compiler.php: Please provide a valid cache path`
- **Cause**: Missing `storage/framework/views` directory.
- **Fix**: Run `mkdir -p storage/framework/views && chmod -R 775 storage`.

### Issue 2: `404 Not Found` on collection entry routes in production
- **Cause**: Shared host PDO drivers returning numeric columns as strings (`"1"` vs `1`).
- **Fix**: Lara-CMS core handles integer casting automatically as of `v1.2.3+`. Ensure models cast foreign keys to `'integer'` and controllers compare using `(int)`.

### Issue 3: In-app "Check for Updates" showing old version
- **Cause**: Release metadata cached for 30 minutes.
- **Fix**: Click "Check for Updates" in Admin Settings (`v1.2.5+`) which appends `?force=1` to bypass the cache.
