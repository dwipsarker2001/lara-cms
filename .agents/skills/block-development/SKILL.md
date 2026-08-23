---
name: block-development
description: Guide, schema standards, and auto-binding protocols for defining PHP Block classes and interactive Blade templates in Lara-CMS.
---

# 🧱 Lara-CMS Block Development & Auto-Bind Skill

Activate this skill when creating, modifying, or styling custom page blocks, global components, or repeater-based data cards in Lara-CMS.

---

## 1. Block Architecture Overview

Lara-CMS blocks are schema-driven PHP classes paired with Blade templates. They can be created either as:
- **Core Blocks**: Placed in `app/Blocks/custom/{Name}.php` with views in `resources/views/blocks/custom/{name}.blade.php`.
- **Plugin Blocks**: Placed in `plugins/{plugin-slug}/Blocks/{Name}/{Name}.php` with views in `plugins/{plugin-slug}/Blocks/{Name}/view.blade.php`.

Every block class extends `App\Blocks\Block` and implements:
1. `name()`: CamelCase unique identifier (e.g. `destinationsGrid`, `travelDeals`).
2. `label()`: Human-readable display label in the CMS sidebar.
3. `fields()`: Array of schema definitions built with `App\Blocks\Field`.
4. `view()`: Blade view path (e.g. `custom-blocks::DestinationsGrid.view`).

---

## 2. Auto-Bind System Architecture (`_sources`)

Lara-CMS features a built-in **Dynamic Source Binding System**. When an admin selects a Collection Entry or Taxonomy Term in a block's repeater, fields are bound programmatically rather than statically copied.

### How Binding Works
1. **Binding Reference Format**:
   - `entry:{id}:{field}`: Binds to a `CollectionEntry` model (e.g. `entry:14:price`, `entry:14:featured_image`, `entry:14:route`, `entry:14:title`).
   - `term:{id}:{field}`: Binds to a `Term` taxonomy model (e.g. `term:5:title`, `term:5:image`, `term:5:slug`, `term:5:route`).
2. **Storage in Section Data**:
   - Section data stores bindings under `data._sources[fieldPath]`:
     ```json
     {
       "headline": "Popular Packages",
       "_sources": {
         "cards.0.price": "entry:14:price",
         "cards.0.originalPrice": "entry:14:original_price",
         "cards.0.image": "entry:14:featured_image",
         "cards.0.buttonLink": "entry:14:route",
         "places.0.name": "term:5:title",
         "places.0.image": "term:5:image",
         "places.0.link": "term:5:route"
       }
     }
     ```
3. **Live Server-Side Hydration**:
   - When rendering a block, `Block::render()` preloads all referenced `CollectionEntry` and `Term` records in batch.
   - `Block::mergeSourceData()` overlays the latest database values for bound keys.
   - If an admin updates a package's price or a destination's photo in the admin area, **all pages and blocks referencing it update instantly without re-saving the page**.
4. **User Override / Unlinking**:
   - If an admin types a custom price or clicks **Unlink** in the sidebar, `_sources[fieldPath]` is cleared (`__none__`), allowing custom local overrides per card.

---

## 3. Schema Definition & Auto-Mapping Fields

Use `Field::taxonomies()` and `Field::collections()` inside your block's `fields()` array:

### A. Taxonomy Selection with URL Route Pattern
```php
use App\Blocks\Block;
use App\Blocks\Field;

class DestinationsGrid extends Block
{
    public function name(): string
    {
        return 'destinationsGrid';
    }

    public function label(): string
    {
        return 'Destinations Grid';
    }

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline')->default('Explore Top Destinations'),
            Field::list('places', 'Destinations', [
                Field::taxonomies(
                    name: 'term_id',
                    label: 'Destination',
                    taxonomy: 'destinations',
                    routePattern: '/packages?destination={slug}'
                ),
                Field::string('name', 'Display Name')->default(''),
                Field::image('image', 'Card Image')->default(''),
                Field::string('slug', 'Slug')->default(''),
                Field::string('link', 'Target Link')->default(''),
            ]),
        ];
    }
}
```

### B. Collection Selection for Deals / Products
```php
class TravelDeals extends Block
{
    public function name(): string
    {
        return 'travelDeals';
    }

    public function label(): string
    {
        return 'Travel Deals';
    }

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline')->default('Exclusive Travel Deals'),
            Field::list('cards', 'Deal Cards', [
                Field::collections(
                    name: 'entry_id',
                    label: 'Package Item',
                    collection: 'packages'
                ),
                Field::string('title', 'Deal Title')->default(''),
                Field::image('image', 'Card Image')->default(''),
                Field::string('price', 'Price')->default(''),
                Field::string('originalPrice', 'Original Price')->default(''),
                Field::string('badge', 'Badge Tag')->default(''),
                Field::string('buttonLink', 'Button Link')->default(''),
            ]),
        ];
    }
}
```

---

## 4. Blade View Standards for Visual Editor & Auto-Binding

To ensure full compatibility with the visual page editor (live click-to-edit, visual highlighting, and continuous index resolution):

### Rule 1: Root Container Identification
The outermost HTML element must declare `data-block="camelCaseName"`:
```blade
<section data-block="destinationsGrid" class="py-20">
```

### Rule 2: Editable Field Attributes
Mark inline editable fields with `data-edit="fieldName"`:
```blade
<h2 data-edit="headline">{{ $data['headline'] ?? '' }}</h2>
```

### Rule 3: Explicit Repeater Indexing (`data-list-index`)
When rendering list items (especially split grids, carousels, or columns), **always add `data-list="listKey"` AND `data-list-index="{{ $i }}"`** to the card's container or link:
```blade
@php
    $places = $data['places'] ?? [];
@endphp

@foreach($places as $i => $place)
    @php
        // Resolve URL: Check bound link -> term link -> url -> slug fallback
        $placeUrl = $place['link'] ?? ($place['_term_link'] ?? ($place['url'] ?? ($place['slug'] ?? '#')));
        if ($placeUrl && $placeUrl !== '#' && !str_starts_with($placeUrl, '/') && !str_starts_with($placeUrl, 'http') && !str_starts_with($placeUrl, '?')) {
            $placeUrl = '/destinations/' . $placeUrl;
        }
    @endphp

    <a href="{{ $placeUrl }}" data-list="places" data-list-index="{{ $i }}" class="group relative block rounded-2xl">
        <img src="{{ $place['image'] ?? '' }}" alt="{{ $place['name'] ?? '' }}" data-edit="image" />
        <h3 data-edit="name">{{ $place['name'] ?? '' }}</h3>
    </a>
@endforeach
```

### Rule 4: Multi-Row / Split Grid Continuous Indexing
If a block splits a repeater into top row and bottom row (e.g. 3 large cards on top, 4 small cards below), compute continuous continuous `$placeIndex`:
```blade
{{-- Top Row --}}
@foreach(array_slice($places, 0, 3) as $i => $place)
    <a href="{{ $placeUrl }}" data-list="places" data-list-index="{{ $i }}">...</a>
@endforeach

{{-- Bottom Row --}}
@php
    $remaining = array_slice($places, 3);
    $startIndex = 3;
@endphp
@foreach($remaining as $j => $place)
    @php
        $placeIndex = $startIndex + $j;
    @endphp
    <a href="{{ $placeUrl }}" data-list="places" data-list-index="{{ $placeIndex }}">...</a>
@endforeach
```

---

## 5. Summary Checklist for Creating New Blocks

- [ ] Extended `App\Blocks\Block` with `name()`, `label()`, `fields()`, `view()`.
- [ ] Used `Field::taxonomies()` or `Field::collections()` for dynamic items.
- [ ] Configured `routePattern: '/...'` when custom taxonomy URL query/path patterns are needed.
- [ ] Added `data-block="{name}"` to the root view element.
- [ ] Added `data-edit="{field}"` to editable text/image tags.
- [ ] Added `data-list="{listName}"` and `data-list-index="{index}"` on repeater item elements.
- [ ] Ran `php artisan test --compact` and `vendor/bin/pint --format agent` to verify.
