# 🧱 Guide to Creating Blocks in Lara-CMS

This document provides a comprehensive guide on creating **Section Blocks** and **Global Site Blocks** (Navbars, Footers) in **Lara-CMS**, drawing directly from real production blocks in the Travel project (`app/Blocks/custom/` & `resources/views/blocks/custom/`).

---

## 📐 Block Architecture Overview

Every block in Lara-CMS consists of two synchronized components:

```
app/Blocks/custom/                       resources/views/blocks/custom/
├── SiteNavbar.php      ───────────────► ├── site-navbar.blade.php
├── TravelDeals.php     ───────────────► ├── travel-deals.blade.php
├── TeamCards.php       ───────────────► ├── team-cards.blade.php
└── DestinationsGrid.php───────────────► └── destinations-grid.blade.php
```

1. **PHP Schema Class (`app/Blocks/custom/Name.php`)**: Extends `App\Blocks\Block`. Defines the block metadata (name, label, global status) and schema fields (`fields()`).
2. **Blade Template View (`resources/views/blocks/custom/kebab-case-name.blade.php`)**: Renders the HTML markup and binds visual editor data attributes (`data-block`, `data-edit`, `data-list`).

---

## ⚙️ 1. Block Class Anatomy (`App\Blocks\Block`)

### Basic Properties

| Property | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| `$name` | `string` | *(Required)* | Machine identifier in `camelCase` (e.g. `siteNavbar`, `travelDeals`, `teamCards`). |
| `$label` | `string` | *(Required)* | Human-readable title shown in the Admin block picker UI. |
| `$global` | `bool` | `false` | When `true`, marks the block as a site-wide **Global Block** (Header, Footer, TopBar). |
| `$background` | `bool` | `true` | When `true`, enables built-in block background controls (Image, Color, Opacity overlay). |

---

## 🛠️ 2. Available Field Types (`App\Blocks\Field`)

| Method | Field Type | Description & UI Control |
| :--- | :--- | :--- |
| `Field::string('key', 'Label', default: '')` | Text | Single-line text input |
| `Field::text('key', 'Label', default: '')` | Textarea | Multi-line text block |
| `Field::number('key', 'Label', default: 0)` | Number | Numeric value |
| `Field::boolean('key', 'Label', default: false)` | Toggle | `true`/`false` switch |
| `Field::image('key', 'Label')` | Image | Asset manager selector & upload modal |
| `Field::icon('key', 'Label', default: 'fa-solid fa-check')` | Icon | Font Awesome icon picker with search |
| `Field::select('key', 'Label', $options)` | Dropdown | Select dropdown options array `[['value'=>'', 'label'=>'']]` |
| `Field::link('key', 'Label', default: '#')` | Link | URL / Route input |
| `Field::richText('key', 'Label')` | Rich Text | HTML / Markdown WYSIWYG editor |
| `Field::taxonomies('key', 'Label', taxonomyId: 'slug', routePattern: '/path?term={slug}')` | Taxonomy Picker | Select term from taxonomy group with dynamic route & data hydration |
| `Field::collection('key', 'Label', collection: 'slug')` | Collection Picker | Select entry from collection with dynamic data binding |
| `Field::group('key', 'Label', $fields)` | Field Group | Object container for nested fields |
| `Field::list('key', 'Label', $fields, count: 3)` | Repeater List | Re-orderable array repeater with initial item count |

---

## 🎨 3. Visual Editor Data Attributes

To enable **live visual inline editing** inside the Lara-CMS Admin Page Builder, attach these special `data-*` attributes to your Blade template HTML elements:

| Attribute | Applied To | Effect in Visual Editor |
| :--- | :--- | :--- |
| `data-block="blockName"` | Root `<section>` or `<div>` | Highlights the block container and binds block-level settings. |
| `data-edit="fieldName"` | Any text/heading/image/link | Shows dashed border on hover. Clicking opens and focuses that field in the editor panel. |
| `data-edit="image"` | `<img>` or wrapper `<div>` | Opens media asset selector when clicked. |
| `data-edit="icon"` | `<i>` icon element | Opens Font Awesome icon library picker when clicked. |
| `data-list="listName"` | Repeatable card/item container | Enables drag-and-drop item reordering and list item management. |

---

## 💡 4. Real-World Examples from Travel Project

### Example A: Global Site Navbar Block (`SiteNavbar`)

Global blocks appear in header/footer slots and remain consistent across pages.

#### 1. Schema: `app/Blocks/custom/SiteNavbar.php`
```php
<?php

namespace App\Blocks\custom;

use App\Blocks\Block;
use App\Blocks\Field;

class SiteNavbar extends Block
{
    public string $name = 'siteNavbar';
    public string $label = 'Site Navbar';
    public bool $global = true;        // Marks as Global Block
    public bool $background = false;   // Handles custom header styling

    public function fields(): array
    {
        return [
            Field::image('logo', 'Logo'),
            Field::number('logoHeight', 'Logo Height', default: 40),
            Field::list('nav', 'Navigation', [
                Field::string('label', 'Label', default: 'Home'),
                Field::link('href', 'Href', default: '/'),
                Field::list('dropdown', 'Dropdown', [
                    Field::string('label', 'Label', default: 'Item'),
                    Field::link('href', 'Href'),
                ]),
            ], count: 4),
            Field::string('contactLabel', 'Contact Label', default: 'Chat with us'),
            Field::string('contactNumber', 'Contact Number', default: '+8801771868382'),
            Field::link('contactLink', 'Contact Link', default: 'https://wa.me/8801771868382'),
            Field::image('contactIcon', 'Contact Icon'),
        ];
    }
}
```

#### 2. View: `resources/views/blocks/custom/site-navbar.blade.php`
```blade
@php $d = $data; @endphp
<div data-block="siteNavbar" class="relative z-50" x-data="{ menuOpen: false, openDropdown: null }">
    <header class="bg-white border-b border-gray-300">
        <nav>
            <div class="mx-auto max-w-7xl px-6 flex items-center justify-between py-6">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2">
                    @if($d['logo'] ?? false)
                        <img src="{{ $d['logo'] }}" alt="Logo" data-edit="logo" style="height: {{ $d['logoHeight'] ?? 40 }}px;" />
                    @else
                        <span class="text-xl font-bold text-gray-900">Lara-CMS</span>
                    @endif
                </a>

                {{-- Navigation Links --}}
                <div class="hidden lg:flex items-center gap-8">
                    @foreach(($d['nav'] ?? []) as $index => $item)
                        @if($item)
                            <div data-list="nav" class="relative">
                                <a href="{{ $item['href'] ?? '#' }}" data-edit="label" class="text-sm font-medium hover:text-brand">
                                    {{ $item['label'] ?? '' }}
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Contact Badge --}}
                <div class="hidden lg:flex items-center gap-2">
                    <span data-edit="contactLabel" class="text-gray-500">{{ $d['contactLabel'] ?? '' }}</span>
                    <a href="{{ $d['contactLink'] ?? '#' }}" data-edit="contactNumber" class="font-semibold text-brand">{{ $d['contactNumber'] ?? '' }}</a>
                </div>
            </div>
        </nav>
    </header>
</div>
```

---

### Example B: Content Section Block (`TravelDeals`)

Standard section block with background overlay, card repeater, price badges, and nested checklists.

#### 1. Schema: `app/Blocks/custom/TravelDeals.php`
```php
<?php

namespace App\Blocks\custom;

use App\Blocks\Block;
use App\Blocks\Field;

class TravelDeals extends Block
{
    public string $name = 'travelDeals';
    public string $label = 'Travel Deals';

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
                Field::string('price', 'Price', default: '৳299'),
                Field::string('originalPrice', 'Original Price', default: '৳499'),
                Field::string('buttonLabel', 'Button Label', default: 'Book Now'),
                Field::list('features', 'Features', [
                    Field::icon('icon', 'Icon', default: 'fa-solid fa-check'),
                    Field::string('text', 'Text', default: 'Included'),
                ]),
            ], count: 3),
        ];
    }
}
```

#### 2. View: `resources/views/blocks/custom/travel-deals.blade.php`
```blade
@php
    $d = $data;
    $bg = is_array($d['background'] ?? null) ? $d['background'] : [];
    $bgImg = $bg['image'] ?? '';
    $bgColor = $bg['color'] ?? '';
    $bgOpacity = $bg['opacity'] ?? 100;
@endphp
<section data-block="travelDeals" class="py-20 relative overflow-hidden">
    {{-- Background Image / Color Support --}}
    @if($bgColor)
        <div class="absolute inset-0" style="background-color: {{ $bgColor }}"></div>
    @endif
    @if($bgImg)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ $bgImg }}); opacity: {{ $bgOpacity / 100 }}"></div>
    @endif

    <div class="relative max-w-6xl mx-auto px-6">
        {{-- Header --}}
        @if($d['headline'] ?? false)
            <h2 data-edit="headline" class="text-3xl font-bold">{{ $d['headline'] }}</h2>
        @endif
        @if($d['description'] ?? false)
            <p data-edit="description" class="mt-2 text-gray-600">{{ $d['description'] }}</p>
        @endif

        {{-- Cards Grid Repeater --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 mt-8">
            @foreach(($d['cards'] ?? []) as $card)
                @if($card)
                    <div data-list="cards" class="bg-white p-6 rounded-2xl shadow">
                        <div data-edit="image" class="relative h-48 overflow-hidden rounded-xl">
                            <img src="{{ $card['image'] ?? '/placeholder.png' }}" class="w-full h-full object-cover" />
                            @if($card['badge'] ?? false)
                                <span data-edit="badge" class="absolute top-3 left-3 bg-brand text-white text-xs px-2.5 py-1 rounded-full">{{ $card['badge'] }}</span>
                            @endif
                        </div>
                        <h3 data-edit="title" class="font-bold text-xl mt-4">{{ $card['title'] ?? '' }}</h3>
                        <p data-edit="description" class="text-sm text-gray-500 mt-1">{{ $card['description'] ?? '' }}</p>
                        
                        <div class="mt-4 flex items-baseline gap-2">
                            <span data-edit="price" class="text-2xl font-bold text-brand">{{ $card['price'] ?? '' }}</span>
                            <span data-edit="originalPrice" class="text-sm line-through text-gray-400">{{ $card['originalPrice'] ?? '' }}</span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
```

---

### Example C: Team Cards Block (`TeamCards`)

Demonstrates nested social icon lists inside repeated team member cards.

#### 1. Schema: `app/Blocks/custom/TeamCards.php`
```php
<?php

namespace App\Blocks\custom;

use App\Blocks\Block;
use App\Blocks\Field;

class TeamCards extends Block
{
    public string $name = 'teamCards';
    public string $label = 'Team Cards';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Meet our Team'),
            Field::text('description', 'Description', default: 'Meet the dedicated team...'),
            Field::list('members', 'Members', [
                Field::image('image', 'Image', default: '/placeholder-image.png'),
                Field::string('name', 'Name', default: 'Team Member'),
                Field::string('role', 'Role', default: 'Role'),
                Field::list('social', 'Social', [
                    Field::icon('icon', 'Icon', default: 'fa-brands fa-linkedin'),
                    Field::string('platform', 'Platform', default: 'linkedin'),
                    Field::link('url', 'URL', default: '#'),
                ], count: 3),
            ], count: 4),
        ];
    }
}
```

---

### Example D: Dynamic Taxonomy Selection Block (`DestinationsGrid`)

Demonstrates selecting terms from a taxonomy (e.g. `destinations`) with route pattern generation, auto-binding, and fallback links.

#### 1. Schema: `plugins/travel-theme/Blocks/DestinationsGrid/DestinationsGrid.php`
```php
<?php

namespace Plugins\TravelTheme\Blocks\DestinationsGrid;

use App\Blocks\Block;
use App\Blocks\Field;

class DestinationsGrid extends Block
{
    public string $name = 'destinationsGrid';

    public string $label = 'Destinations Grid';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'All Destinations'),
            Field::text('description', 'Description', default: 'Explore our handpicked destinations for your next adventure'),
            Field::list('places', 'Places', [
                Field::taxonomies('term_id', 'Destination', taxonomyId: 'destinations', routePattern: '/packages?destination={slug}'),
                Field::image('image', 'Image', default: '/placeholder-image.png'),
                Field::string('name', 'Name', default: 'Destination Name'),
                Field::string('slug', 'Slug'),
            ], count: 7),
        ];
    }
}
```

#### 2. View: `plugins/travel-theme/Blocks/DestinationsGrid/view.blade.php`
```blade
@php
    $d = $data;
    $places = array_values(array_filter($d['places'] ?? []));
    $spans = ['sm:col-span-1', 'sm:col-span-2', 'sm:col-span-1'];
@endphp
<section data-block="destinationsGrid" class="py-20">
    <div class="max-w-6xl mx-auto px-6">
        @if($d['headline'] ?? false)
            <h2 data-edit="headline" class="text-center text-3xl md:text-4xl font-bold text-gray-900">{{ $d['headline'] }}</h2>
        @endif
        @if($d['description'] ?? false)
            <p data-edit="description" class="mx-auto mt-3 max-w-xl text-center text-gray-500">{{ $d['description'] }}</p>
        @endif

        @if(empty($places))
            <p class="mt-10 text-center text-gray-500">No destinations selected.</p>
        @else
            <div class="mt-10 space-y-6">
                @if(count($places) >= 3)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
                        @foreach(array_slice($places, 0, 3) as $i => $place)
                            @php
                                $slug = $place['slug'] ?? ($place['_term_slug'] ?? '');
                                if ($slug && !str_starts_with($slug, '/') && !str_starts_with($slug, 'http') && !str_starts_with($slug, '?')) {
                                    $placeUrl = '/packages?destination=' . $slug;
                                } else {
                                    $placeUrl = $slug ?: '#';
                                }
                            @endphp
                            <div class="{{ $spans[$i] }}">
                                <a href="{{ $placeUrl }}" data-list="places" data-list-index="{{ $i }}" class="group relative block h-[280px] overflow-hidden rounded-2xl bg-gray-100">
                                    <img src="{{ $place['image'] ?? '' }}" alt="{{ $place['name'] ?? '' }}" data-edit="image" class="absolute inset-0 w-full h-full object-cover {{ empty($place['image']) ? 'hidden' : '' }}" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                    <div class="absolute bottom-5 left-5 text-white">
                                        <h3 data-edit="name" class="text-base font-bold">{{ $place['name'] ?? '' }}</h3>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                @php
                    $remaining = array_slice($places, 3);
                    $gridItems = count($places) >= 3 ? $remaining : $places;
                    $startIndex = count($places) >= 3 ? 3 : 0;
                @endphp
                @if(!empty($gridItems))
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($gridItems as $j => $place)
                            @php
                                $placeIndex = $startIndex + $j;
                                $slug = $place['slug'] ?? ($place['_term_slug'] ?? '');
                                if ($slug && !str_starts_with($slug, '/') && !str_starts_with($slug, 'http') && !str_starts_with($slug, '?')) {
                                    $placeUrl = '/packages?destination=' . $slug;
                                } else {
                                    $placeUrl = $slug ?: '#';
                                }
                            @endphp
                            <a href="{{ $placeUrl }}" data-list="places" data-list-index="{{ $placeIndex }}" class="group relative block h-[280px] overflow-hidden rounded-2xl bg-gray-100">
                                <img src="{{ $place['image'] ?? '' }}" alt="{{ $place['name'] ?? '' }}" data-edit="image" class="absolute inset-0 w-full h-full object-cover {{ empty($place['image']) ? 'hidden' : '' }}" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                <div class="absolute bottom-5 left-5 text-white">
                                    <h3 data-edit="name" class="text-base font-bold">{{ $place['name'] ?? '' }}</h3>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
```

---

## ⚡ 5. Step-by-Step Checklist for Creating New Blocks

1. **Create the PHP Schema Class:**
   - File location: `app/Blocks/custom/YourBlockName.php`
   - Class name matches filename (`class YourBlockName extends Block`).
   - Set `$name` in `camelCase` (e.g. `yourBlockName`).
   - Set `$label` (e.g. `'Your Block Name'`).
   - If header/footer, set `public bool $global = true;`.
   - Return field definitions in `fields()`.

2. **Create the Blade Template View:**
   - File location: `resources/views/blocks/custom/your-block-name.blade.php` (kebab-case matching `$name`).
   - Alias `$d = $data;`.
   - Add root container attribute `data-block="yourBlockName"`.
   - Include standard background handling snippet if `$background = true`.
   - Add `data-edit="fieldKey"` and `data-list="listKey"` to inner HTML elements.

3. **Auto-Discovery:**
   - Lara-CMS automatically registers any new class placed in `app/Blocks/custom/` without needing manual service provider registration.
