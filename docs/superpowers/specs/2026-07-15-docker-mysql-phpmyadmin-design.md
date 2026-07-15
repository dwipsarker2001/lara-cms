# Docker MySQL + phpMyAdmin Setup

## Purpose
Provide a local MySQL database with phpMyAdmin admin UI for the Lara CMS Laravel application, switching from SQLite to MySQL.

## Services

### MySQL 8.0
- Image: `mysql:8.0`
- Port: `3306:3306`
- Database: `lara_cms`
- Root password: from environment variable
- Persistent data via named volume `mysql_data`

### phpMyAdmin
- Image: `phpmyadmin/phpmyadmin`
- Port: `8080:80`
- Links to MySQL service automatically via `PMA_HOST=mysql`

## Laravel .env Changes
- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=lara_cms`
- `DB_USERNAME=root`
- `DB_PASSWORD=${DB_PASSWORD}`

## File Changes
1. Create `docker-compose.yml` in project root
2. Update `.env` to use MySQL

## Usage
- `docker compose up -d` to start services
- `http://localhost:8080` for phpMyAdmin
- `php artisan serve` to run the app (unchanged)
- `php artisan migrate:fresh` to set up database schema

## Non-Goals
- No app containerization (app stays on `artisan serve`)
- No Laravel Sail migration
