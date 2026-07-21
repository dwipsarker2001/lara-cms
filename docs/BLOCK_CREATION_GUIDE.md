# Lara CMS — Complete Block Creation Guide & AI Blueprint

This document provides a complete guide for creating content blocks for Lara CMS. Use it as a developer reference or paste it as context into Large Language Models (LLMs) & Vision AI models alongside screenshots of website sections to automatically generate 100% compatible Lara CMS block components.

---

## 1. System Architecture Overview

Lara CMS uses a **Schema-Driven Block Engine**:
1. **PHP Block Class (`app/Blocks/Common/BlockName.php`)**: Defines the block's schema (name, label, fields, default content).
2. **Auto-Discovery (`BlockRegistry`)**: Scans `app/Blocks/` automatically — no manual route or config registration needed.
3. **Blade Template (`resources/views/blocks/block-name.blade.php`)**: Renders HTML based on the block's `$data` array (`$d = $data`).
4. **Live Preview & Editor Integration (`data-edit` & `data-list`)**: Clicking elements in the iframe preview focuses the corresponding sidebar input.

```
       PHP Schema                BlockRegistry               Alpine.js Editor
(app/Blocks/Common/Hero.php)  ---> Auto-Discovery ---> (resources/views/admin/pages/editor.blade.php)
           |                                                        |
           v                                                        v
      Blade Template                                         Live Preview Editor
 (resources/views/blocks/                                  (Click-to-edit elements via
      hero.blade.php)                                        data-edit & data-list)
```

---

## 2. Step 1: Create the PHP Block Class

Create a new file in `app/Blocks/Common/YourBlock.php` (or `app/Blocks/Global/` for site-wide blocks like Header/Footer).

### Class Rules:
* Must extend `App\Blocks\Block`.
* Must specify `public string $name` (camelCase) and `public string $label`.
* Set `public bool $background = true` (auto-prepends image/color/opacity background controls unless set to `false`).
* **CRITICAL**: Every field MUST specify a `default:` value so default content renders immediately without empty fields.

```php
<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class FeatureGridRow extends Block
{
    public string $name = 'featureGridRow';

    public string $label = 'Feature Row (2x2 Grid)';

    public bool $background = true;

    public function fields(): array
    {
        return [
            Field::string('badge', 'Badge Text', default: 'Task Management'),
            Field::string('headline', 'Headline', default: 'All Your Tasks, Organized Effortlessly'),
            Field::text('description', 'Description', default: 'Effortlessly manage tasks, collaborate with teams, and meet deadlines with precision.'),
            Field::image('image', 'Mockup Image', default: '/placeholder-image.png'),
            Field::list('features', 'Features', [
                Field::string('title', 'Title', default: 'Feature Title'),
                Field::text('description', 'Description', default: 'Feature description text goes here.'),
                Field::icon('icon', 'Icon', default: 'fa-solid fa-star'),
                Field::string('iconColor', 'Icon Color', default: '#8B5CF6'),
            ], count: 2),
        ];
    }
}
```

### Available `Field::` Helpers

| Helper | Arguments | Description |
| :--- | :--- | :--- |
| `Field::string()` | `name`, `label`, `default: ''` | Single-line text input |
| `Field::text()` | `name`, `label`, `default: ''` | Multi-line textarea |
| `Field::number()` | `name`, `label`, `default: 0` | Numeric input |
| `Field::boolean()` | `name`, `label`, `default: false` | Toggle switch |
| `Field::image()` | `name`, `label`, `default: ''` | Asset / Image picker |
| `Field::icon()` | `name`, `label`, `default: ''` | Icon picker (FontAwesome / Lucide) |
| `Field::link()` | `name`, `label`, `default: '#'` | URL / Link field |
| `Field::select()` | `name`, `label`, `options`, `default: ''` | Dropdown select |
| `Field::list()` | `name`, `label`, `fields[]`, `count: 0` | Repeatable list of items |
| `Field::group()` | `name`, `label`, `fields[]` | Single nested object |

---

## 3. Step 2: Create the Blade Template View

Create the matching Blade template in `resources/views/blocks/{kebab-name}.blade.php` (e.g. `feature-grid-row.blade.php`).

### Mandatory View Conventions:

1. **Extract Data**: Start with `@php $d = $data; @endphp`.
2. **Root Container**: Root element MUST include `data-block="blockName"`:
   ```blade
   <section data-block="featureGridRow" class="...">
   ```
3. **Editable Fields (`data-edit="fieldName"`)**:
   - Add `data-edit="fieldName"` directly to text/heading elements.
   - **DO NOT** hardcode fallback strings in HTML; render `{{ $d['fieldName'] }}` directly inside `@if($d['fieldName'] ?? false)`.
   ```blade
   @if($d['headline'] ?? false)
       <h2 data-edit="headline" class="...">{{ $d['headline'] }}</h2>
   @endif
   ```
4. **Images (`data-edit="imageField"`)**:
   - Place `data-edit="imageField"` on a container `<div>` around the `<img>` tag so empty image placeholders are clickable.
   ```blade
   <div data-edit="image" class="w-full h-full rounded-2xl overflow-hidden bg-gray-100">
       @if($d['image'] ?? false)
           <img src="{{ $d['image'] }}" alt="{{ $d['headline'] ?? '' }}" class="w-full h-full object-cover" />
       @endif
   </div>
   ```
5. **Buttons / CTAs (`data-edit-button`)**:
   - Add `data-edit-button` to buttons/links alongside `data-edit="ctaLabel"` for correct hover highlights.
   ```blade
   @if($d['ctaLabel'] ?? false)
       <a href="{{ $d['ctaUrl'] ?? '#' }}" data-edit="ctaLabel" data-edit-button class="...">
           {{ $d['ctaLabel'] }}
       </a>
   @endif
   ```
6. **List Items (`data-list="listName"`)**:
   - Place `data-list="listName"` on **each individual list item container inside `@foreach`**.
   - **DO NOT** put `data-list` on the outer `<ul>` or `<div>` wrapper.
   - **DO NOT** put `data-list` on duplicate marquee tracks or mobile menus (keep `data-list` only on primary rendered track to avoid index offset bugs).
   ```blade
   <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
       @foreach(($d['features'] ?? []) as $f)
           @if($f)
               <div data-list="features" class="flex flex-col gap-4">
                   <h3 data-edit="title">{{ $f['title'] ?? '' }}</h3>
                   <p data-edit="description">{{ $f['description'] ?? '' }}</p>
               </div>
           @endif
       @endforeach
   </div>
   ```

---

## 4. Prompt Template for AI / Vision Models (Screenshot to Block Converter)

Use the prompt template below when feeding a website screenshot, design mockup, or section description to ChatGPT, Claude, Gemini, or any LLM agent:

```text
You are an expert Laravel developer building custom content blocks for Lara CMS.

TASK:
Convert the provided screenshot / design description into a Lara CMS Block component pair:
1. PHP Block Class: app/Blocks/Common/{ClassName}.php
2. Blade Template: resources/views/blocks/{kebab-name}.blade.php

STRICT GUIDELINES:

1. PHP BLOCK CLASS:
- Extend `App\Blocks\Block`.
- Define `$name` (camelCase) and `$label` (Human Label).
- Define `fields()` using `Field::string()`, `Field::text()`, `Field::image()`, `Field::icon()`, `Field::link()`, `Field::list()`, etc.
- EVERY field MUST specify a `default:` value containing realistic demo copy matching the screenshot design.

2. BLADE TEMPLATE:
- Start with `@php $d = $data; @endphp`.
- The root tag MUST have `data-block="{name}"`.
- Text elements MUST use `data-edit="{fieldName}"` and render `{{ $d['{fieldName}'] }}` (no hardcoded text).
- Wrap text in `@if($d['{fieldName}'] ?? false)`.
- Image fields MUST put `data-edit="{imageName}"` on a wrapper `<div>` around the `<img>`.
- Buttons MUST include `data-edit="{ctaLabel}" data-edit-button`.
- List items MUST put `data-list="{listName}"` on each item container inside `@foreach` (NOT on the parent wrapper).
- Use Vanilla Tailwind CSS utility classes matching the aesthetics of the design.

OUTPUT FORMAT:
Generate the full PHP class code followed by the full Blade template view code.
```

---

## 5. Verification Checklist

Before finalizing any new block component, verify:

- [ ] PHP Class exists in `app/Blocks/Common/` or `app/Blocks/Global/`.
- [ ] Every field has a sensible `default:` value.
- [ ] Blade file exists in `resources/views/blocks/` matching `kebab-case` name.
- [ ] Root element has `data-block="blockName"`.
- [ ] All editable text fields have `data-edit="fieldName"`.
- [ ] Image fields have `data-edit="imageField"` on wrapper `<div>`.
- [ ] Buttons have `data-edit-button`.
- [ ] List items have `data-list="listName"` on child container `<div>`/`<li>` elements inside `@foreach`.
- [ ] Tests pass via `php artisan test --compact`.
