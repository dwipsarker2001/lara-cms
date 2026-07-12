<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Creating Content Blocks

This CMS uses a schema-driven block system. Every section on a page is a **block** — a PHP class defining editable fields and a Blade view that renders the HTML. Blocks are auto-discovered; just create the files and they appear in the editor.

### Architecture Overview

```
app/Blocks/                     → PHP block classes (auto-discovered)
├── Block.php                   → Base class
├── Field.php                   → Field definition helpers
├── BlockRegistry.php           → Auto-discovery & lookup (singleton)
└── Home/
    ├── TravelDeals.php         → Travel Deals block (reference example)
    ├── HeroBanner.php
    └── ...
resources/views/blocks/         → Blade templates for each block
└── travel-deals.blade.php
```

### Creating a Block (Two Files)

#### 1. PHP Block Class

Create a class in `app/Blocks` (any subdirectory). It is auto-discovered.

```php
<?php

namespace App\Blocks\Home;

use App\Blocks\Block;
use App\Blocks\Field;

class TravelDeals extends Block
{
    public string $name = 'travelDeals';       // Machine name (camelCase)
    public string $label = 'Travel Deals';      // Human label in the picker

    // Optional overrides:
    // public bool $global = true;              // Global (shared site-wide, edited on home page)
    // public bool $background = false;         // Set false if block has its own bg (hero, navbar)

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

The view name auto-resolves to `blocks.{kebab-name}` — e.g. `travelDeals` → `blocks.travel-deals`.

#### 2. Blade View

Create `resources/views/blocks/travel-deals.blade.php`:

```blade
@php $d = $data; @endphp
@php
    // Background handling (auto-injected by $background = true)
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
                <h2 data-edit="headline" class="...">{{ $d['headline'] }}</h2>
            @endif
            @if($d['description'] ?? false)
                <p data-edit="description" class="...">{{ $d['description'] }}</p>
            @endif
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach(($d['cards'] ?? []) as $i => $card)
                    @if($card)
                        <div data-list="cards" class="...">
                            <div data-edit="image" class="relative h-52 overflow-hidden rounded-xl">
                                @if($card['image'] ?? false)
                                    <img src="{{ $card['image'] }}" alt="" class="w-full h-full object-cover" />
                                @endif
                            </div>
                            ...
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
```

### Field Types Reference

| Method | Type | Editor Control |
|--------|------|---------------|
| `Field::string($name, $label, default: '')` | Single-line text | `<input type="text">` |
| `Field::text($name, $label, default: '')` | Multi-line text | `<textarea>` |
| `Field::number($name, $label, default: '')` | Number | `<input type="number">` |
| `Field::boolean($name, $label, default: false)` | Toggle | Switch (`true`/`false`) |
| `Field::image($name, $label)` | Image | Asset picker, drag-and-drop |
| `Field::icon($name, $label)` | Font Awesome icon | Icon picker with search |
| `Field::select($name, $label, $options, default: '')` | Dropdown | Select with color swatches |
| `Field::link($name, $label, default: '')` | URL | `<input type="text">` |
| `Field::richText($name, $label)` | HTML | `<textarea>` (monospace) |
| `Field::tags($name, $label)` | Tag list | Tag input (JSON array) |
| `Field::datetime($name, $label)` | Date/time | Text input |
| `Field::group($name, $label, $fields)` | Nested group | Drill-in object editor |
| `Field::list($name, $label, $fields, count: 0)` | Repeatable group | Sortable list with add/remove |

### Editor Integration Attributes

Add these attributes to elements in your Blade view to make them editable in the preview:

| Attribute | Purpose |
|-----------|---------|
| `data-edit="fieldName"` | Marks a field as editable. Hover shows blue dashed border. Click navigates editor to that field. |
| `data-edit="image"` on container div (not just `<img>`) | Ensures the drop area is always hittable even without an image |
| `data-edit-button` (as class/attribute) | Marks buttons — shows border on hover but no background tint |
| `data-list="listName"` | Marks a repeatable list item container for nested sortable lists |

**Important:** Always put `data-edit` on a container `<div>` for image fields, not on the `<img>` tag directly. This ensures the placeholder area is clickable when no image is set.

### The `$background` Property

When `$background = true` (default), the editor automatically prepends a `background` field group with:
- Background image
- Background color (select with 12 preset colors)
- Opacity (0–100%)

Your view must handle the background data:

```blade
@php
    $d = $data;
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    $bgImg = $bg['image'] ?? '';
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;
@endphp
<section class="py-20 relative overflow-hidden">
    @if($bgColor)
        <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
    @endif
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
    @endif
    <div class="relative"><!-- content --></div>
</section>
```

Set `$background = false` for full-bleed blocks with their own background (hero banners, navbar, footer).

### Global Blocks

Set `$global = true` to make a block site-wide. Global blocks are:
- Excluded from the page block picker (they auto-appear on all pages)
- Edited only on the home page
- Changes propagate to every page that includes them

Use for: site navbar, top bar, footer.

### How It Works

1. **Discovery:** `BlockRegistry` scans `app/Blocks/` for all `Block` subclasses on every request (cached per-request as a singleton).
2. **Editor:** `PageController@editor` passes `schemas()` and `pickerList()` to the Alpine.js editor. The editor auto-generates form controls for each field.
3. **Default data:** When a block is added, `buildDefaultData()` walks the schema and creates default values from `default: '...'` in each field.
4. **Preview:** `BlockPreview::render()` includes each block's Blade view with `$data` and `$preview = true`.
5. **Public render:** `page.blade.php` iterates sections, merges globals via `Sections::withGlobals()`, and includes each block view with `$preview = false`.
6. **Views receive:** `$data` (field values array), `$_key` (UUID of the section instance), `$preview` (boolean, true when rendered inside the editor preview).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
