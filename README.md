# LaraCMS

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <strong>A modern, schema-driven, visual block-based Content Management System</strong><br>
  Built with Laravel 13, Alpine.js, Tailwind CSS, and Docker.
</p>

---

## 🚀 Features

- **Schema-Driven Block System:** Define editable content blocks as pure PHP classes with auto-discovery in `app/Blocks/`.
- **Interactive Visual Editor:** Live preview and drag-and-drop/inline element editing using Alpine.js and HTML `data-edit` attributes.
- **Dockerized Environment:** Pre-configured `docker-compose` setup with automated storage directory generation, permission management, MySQL 8.0, and phpMyAdmin.
- **Dynamic Content & Collections:** Manage pages, custom entry collections, global blocks (navbars/footers), and media assets effortlessly.
- **Extensible Field Registry:** Supports text, rich text, images, icons (Font Awesome), repeaters/lists, nested field groups, select dropdowns, and background customization out-of-the-box.
- **Comprehensive Test Suite:** Powered by Pest 4 for reliable feature and unit testing.

---

## 🛠️ Quick Start Guide

### Option 1: Quick Start with Docker (Recommended)

#### Prerequisites
- Docker Engine & Docker Compose installed on your host machine.

#### Step-by-Step Launch

1. **Clone the Repository:**
   ```bash
   git clone <repository-url> lara-cms
   cd lara-cms
   ```

2. **Start the Docker Containers:**
   ```bash
   docker compose up -d
   ```
   *Note: The container startup script (`docker-entrypoint.sh`) will automatically generate `.env` if missing, install composer dependencies, set up `APP_KEY`, and ensure all necessary `storage/` and `bootstrap/cache/` directories exist with proper write permissions.*

3. **Run Database Migrations & Seed Initial Data:**
   ```bash
   docker compose exec app php artisan migrate:fresh --seed
   ```

4. **Access the Application:**
   - **Public Site / Editor:** [http://localhost:8000](http://localhost:8000)
   - **Admin Login:** [http://localhost:8000/login](http://localhost:8000/login)
     - **Email:** `admin@admin.com`
     - **Password:** `password`
   - **phpMyAdmin:** [http://localhost:8080](http://localhost:8080)
     - **Username:** `root`
     - **Password:** `secret`

---

### Option 2: Manual Local Setup (Without Docker)

#### Prerequisites
- PHP 8.4+ with `pdo`, `pdo_mysql`, `zip`, `curl` extensions
- Composer 2.x
- MySQL 8.0+ / MariaDB
- Node.js 18+ & NPM

#### Installation Steps

1. **Clone the repository and install PHP dependencies:**
   ```bash
   git clone <repository-url> lara-cms
   cd lara-cms
   composer install
   ```

2. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env` to configure your local MySQL database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=lara_cms
   DB_USERNAME=root
   DB_PASSWORD=secret
   ```

3. **Ensure Storage Directories & Permissions Exist:**
   ```bash
   mkdir -p storage/framework/views \
            storage/framework/cache/data \
            storage/framework/sessions \
            storage/framework/testing \
            storage/logs \
            bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

4. **Run Migrations & Seed Database:**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Link Public Storage:**
   ```bash
   php artisan storage:link
   ```

6. **Install & Build Frontend Assets:**
   ```bash
   npm install
   npm run dev   # Or npm run build for production assets
   ```

7. **Start Application Server:**
   ```bash
   php artisan serve
   ```
   Visit [http://localhost:8000](http://localhost:8000) in your web browser.

---

## 🏗️ Content Block System Architecture

Every section on a page is represented by a **Block** — a PHP class defining schema fields, and a corresponding Blade view for HTML rendering. Blocks are auto-discovered from `app/Blocks/`.

### Directory Structure

```
app/Blocks/                     → PHP Block Schema Definitions (auto-discovered)
├── Block.php                   → Abstract Base Block Class
├── Field.php                   → Field Definition Factory Helpers
├── BlockRegistry.php           → Auto-discovery Singleton
└── Home/
    ├── TravelDeals.php         → Example Block Schema Class
    ├── HeroBanner.php
    └── ...
resources/views/blocks/         → Blade Templates for Each Block
└── travel-deals.blade.php      → Blade View (auto-mapped to travelDeals)
```

---

### Creating a Custom Content Block

#### 1. Define the PHP Block Class

Create a PHP file under `app/Blocks/` (e.g., `app/Blocks/Home/TravelDeals.php`):

```php
<?php

namespace App\Blocks\Home;

use App\Blocks\Block;
use App\Blocks\Field;

class TravelDeals extends Block
{
    public string $name = 'travelDeals';       // Machine identifier (camelCase)
    public string $label = 'Travel Deals';      // Display title in block picker

    // Optional settings:
    // public bool $global = true;              // Global block (site-wide header/footer)
    // public bool $background = false;         // Set false if block manages custom backgrounds

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Travel Deals'),
            Field::text('description', 'Description', default: 'Find Your Perfect Escape'),
            Field::group('button', 'Button', [
                Field::string('label', 'Label'),
                Field::link('link', 'Link'),
            ]),
            Field::list('cards', 'Cards', [
                Field::image('image', 'Image', default: '/placeholder-image.png'),
                Field::string('badge', 'Badge', default: 'Popular'),
                Field::string('title', 'Title', default: 'Paris Getaway'),
                Field::text('description', 'Description', default: 'Explore the city of lights...'),
                Field::string('priceLabel', 'Price Label', default: 'Per Person'),
                Field::number('price', 'Price', default: 299),
                Field::number('originalPrice', 'Original Price', default: 499),
                Field::string('buttonLabel', 'Button Label', default: 'Book Now'),
                Field::list('features', 'Features', [
                    Field::icon('icon', 'Icon', default: 'fa-solid fa-check'),
                    Field::string('text', 'Text', default: 'Included'),
                    Field::richText('tooltip', 'Tooltip'),
                ]),
            ], count: 3),
        ];
    }
}
```

*Note: The machine name `travelDeals` automatically maps to the Blade view template `resources/views/blocks/travel-deals.blade.php`.*

#### 2. Create the Blade View Template

Create `resources/views/blocks/travel-deals.blade.php`:

```blade
@php $d = $data; @endphp
@php
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    if (empty($bg) && isset($d['background']) && is_string($d['background'])) {
        try { $bg = json_decode($d['background'], true) ?? []; } catch (\Exception) { $bg = []; }
    }
    $bgImg = $bg['image'] ?? '';
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;
@endphp
<section data-block="travelDeals" class="py-20 relative overflow-hidden">
    @if($bgColor)
        <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
    @endif
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
    @endif
    <div class="relative">
        <div class="max-w-6xl mx-auto px-6">
            @if($d['headline'] ?? false)
                <h2 data-edit="headline" class="text-3xl font-bold">{{ $d['headline'] }}</h2>
            @endif
            @if($d['description'] ?? false)
                <p data-edit="description" class="mt-2 text-gray-600">{{ $d['description'] }}</p>
            @endif
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 mt-8">
                @foreach(($d['cards'] ?? []) as $i => $card)
                    @if($card)
                        <div data-list="cards" class="bg-white p-4 rounded-xl shadow">
                            <div data-edit="image" class="relative h-52 overflow-hidden rounded-xl">
                                @if($card['image'] ?? false)
                                    <img src="{{ $card['image'] }}" alt="" class="w-full h-full object-cover" />
                                @endif
                            </div>
                            <h3 data-edit="title" class="font-semibold text-lg mt-3">{{ $card['title'] ?? '' }}</h3>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
```

---

### Field Types Reference

| Method | Type | Editor UI Control |
|--------|------|-------------------|
| `Field::string($name, $label, default: '')` | Single-line string | `<input type="text">` |
| `Field::text($name, $label, default: '')` | Multi-line text | `<textarea>` |
| `Field::number($name, $label, default: '')` | Numeric value | `<input type="number">` |
| `Field::boolean($name, $label, default: false)` | Boolean flag | Toggle switch (`true`/`false`) |
| `Field::image($name, $label)` | Image URL / Asset | Asset selector & file dropzone |
| `Field::icon($name, $label)` | Icon class string | Font Awesome icon picker with search |
| `Field::select($name, $label, $options)` | Choice selection | Dropdown select |
| `Field::link($name, $label, default: '')` | Target URL | Link input field |
| `Field::richText($name, $label)` | HTML / Markdown | Rich text area |
| `Field::tags($name, $label)` | Tag array | Multi-tag input field |
| `Field::datetime($name, $label)` | Date & Time | Date/time picker |
| `Field::group($name, $label, $fields)` | Nested Field Group | Group object editor |
| `Field::list($name, $label, $fields, count: 0)` | Repeatable Array | Re-orderable list repeater |

---

### Interactive Editor Attributes

Attach these HTML attributes to your Blade templates to connect element hover and click events directly to the visual page editor:

| Attribute | Description & Functionality |
|-----------|-----------------------------|
| `data-edit="fieldName"` | Highlights field with a dashed border on hover. Clicking focuses that field in the editor panel. |
| `data-edit="image"` on wrapper `<div>` | Recommended wrapper for images so drop zones are clickable even without an image loaded. |
| `data-edit-button` | Special attribute for action buttons (retains border styling on hover without changing background). |
| `data-list="listName"` | Identifies repeatable repeater item containers for drag-and-drop reordering. |

---

## 🧪 Testing & Code Style

### Running Tests

This project uses **Pest 4** for unit and feature testing.

```bash
# Run all Pest tests
php artisan test

# Run tests in compact output mode
php artisan test --compact

# Filter tests by name
php artisan test --filter=PageControllerTest
```

### Code Formatting

Format PHP code according to project conventions with **Laravel Pint**:

```bash
vendor/bin/pint --dirty --format agent
```

---

## 📦 Release Packaging

To build and package production releases (creates a clean, production-ready `dist/lara-cms-v<version>.zip` ready for deployment):

### Option 1: Via Docker (Zero host setup required)
Run without needing local Node.js, PHP, or Composer installed on your host machine:
```bash
docker run --rm -v $(pwd):/app -w /app lara-cms-app ./scripts/build-release.sh
```
*Or via Docker Compose:*
```bash
docker compose run --rm app ./scripts/build-release.sh
```

### Option 2: Via Local Terminal (Host setup)
```bash
npm run release
# OR
composer build-zip
# OR
./scripts/build-release.sh
```

The compiled release package will be created at `dist/lara-cms-v<version>.zip`.

---

## 🐋 Docker & Troubleshooting Notes

- **Cache Directories:** The `docker-entrypoint.sh` script automatically creates `storage/framework/views`, `storage/framework/cache/data`, `storage/framework/sessions`, `storage/framework/testing`, `storage/logs`, and `bootstrap/cache` upon container startup to prevent `Please provide a valid cache path` startup loops on fresh clones.
- **Permissions:** Automated `chmod 775` is applied to `storage` and `bootstrap/cache` during container entrypoint initialization.
- **Missing Tables:** If you see `Base table or view not found` on first run, execute `docker compose exec app php artisan migrate --force`.

---

## 📄 License

LaraCMS is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
