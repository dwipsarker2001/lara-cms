# Installation

## Requirements

- PHP 8.3+
- MySQL 8.0
- Composer
- Node.js 20+
- Docker (optional, for local dev)

## Quick Start (Docker)

```bash
# Clone & enter
git clone <repo> lara-cms
cd lara-cms

# Copy env
cp .env.example .env

# Start containers
docker compose up -d

# Install dependencies
docker compose exec app composer install
docker compose exec app npm install

# Build frontend
docker compose exec app npm run build

# Run migrations & seed
docker compose exec app php artisan migrate --seed

# Generate app key
docker compose exec app php artisan key:generate

# Storage link
docker compose exec app php artisan storage:link
```

Visit `http://localhost:8000` — admin at `http://localhost:8000/login`.

Default admin: `admin@admin.com` / `password`

phpMyAdmin: `http://localhost:8080` (root / secret)

## Manual Setup

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Configuration

Key `.env` values:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=lara_cms
DB_USERNAME=root
DB_PASSWORD=secret
```

## Frontend Dev

```bash
npm run dev
```

Or inside Docker:

```bash
docker compose exec app npm run dev
```
