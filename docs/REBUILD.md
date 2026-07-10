# Lara-CMS — Full Rebuild Specification

> A block-based, Statamic-inspired CMS. **Website** and **Admin panel** are fully
> separated. The website is rendered from **pages**; each page is an ordered list
> of **sections** (blocks). In the admin panel you build the website visually:
> create a page, add sections to it, edit each section's fields with a live
> preview, save.
>
> This document is the single source of truth to rebuild the project on
> **Laravel + Blade + Tailwind CSS + Alpine.js**. It is written so any developer
> or AI model can implement the whole thing from scratch without seeing the
> original (a Next.js/React reference implementation).

---

## Table of contents

1. [Core concept & mental model](#1-core-concept--mental-model)
2. [Tech stack & project setup](#2-tech-stack--project-setup)
3. [Architecture & directory layout](#3-architecture--directory-layout)
4. [Data model & migrations](#4-data-model--migrations)
5. [The block system (the engine)](#5-the-block-system-the-engine)
6. [Field types reference](#6-field-types-reference)
7. [Public website rendering](#7-public-website-rendering)
8. [Admin panel — shell, nav, layout](#8-admin-panel--shell-nav-layout)
9. [Pages CRUD](#9-pages-crud)
10. [The Visual Editor (the crown jewel)](#10-the-visual-editor-the-crown-jewel)
11. [Field editor widgets (Alpine)](#11-field-editor-widgets-alpine)
12. [Global sections (site-wide navbar/footer)](#12-global-sections-site-wide-navbarfooter)
13. [Other collections: Blog, Packages, Bookings](#13-other-collections-blog-packages-bookings)
14. [Settings, SEO, Taxonomies, Assets, Users](#14-settings-seo-taxonomies-assets-users)
15. [Theme system](#15-theme-system)
16. [Routes reference](#16-routes-reference)
17. [Build order / milestones](#17-build-order--milestones)
18. [Appendix A — full block catalog](#appendix-a--full-block-catalog)

---

## 1. Core concept & mental model

Everything on the public website is a **page**. A page is:

```
Page {
  slug        // "home", "about", "contact" — URL segment
  title
  sections[]  // ORDERED list of blocks — THIS is the page content
  meta        // per-page SEO overrides
  published
  position    // sort order in the admin pages list
}
```

A **section** is one instance of a **block**:

```
Section {
  _key   // stable unique id (uuid) — survives reorder, used as loop key
  name   // which block: "heroBanner", "siteNavbar", "featureImageCards"...
  data   // the block's content, a NESTED tree keyed by field name
}
```

A **block** is a reusable content type defined in code. It has:

- a **schema** — the list of editable fields (`FieldDef[]`)
- a **view** — a Blade partial that renders `data` into HTML

The **block registry** is the master list of all blocks. Given a section's
`name`, the registry finds the block's schema (to build the editor form) and
its view (to render the public HTML). **This registry is the heart of the whole
system** — add a block once and it automatically appears in the admin's block
picker, gets an auto-generated editor form, and renders on the public site. No
switch statements, no per-block editor code.

The admin's **Visual Editor** is a schema-driven form generator + live preview:
it reads a block's schema, renders form inputs, and shows the rendered block
next to it. Editing a field updates the preview. Clicking an element in the
preview focuses its field in the form (`data-edit` markers).

### Why this design

- **Content is JSON.** `pages.sections` is a single JSON column. No per-block
  tables, no migrations when you add a block. The schema lives in code.
- **One editor for everything.** Blog posts, packages, and pages all reuse the
  same recursive form generator, driven by different schemas.
- **Blocks are self-contained.** A block = a schema array + a Blade view. Drop
  in a folder, register it, done.

---

## 2. Tech stack & project setup

| Layer | Choice |
|-------|--------|
| Framework | Laravel 12+ (PHP 8.3+) |
| Views | Blade |
| Styling | Tailwind CSS v4 (`@import "tailwindcss"` + `@theme`, **no** `tailwind.config.js`) |
| Interactivity | Alpine.js v3 (admin editor + public micro-interactions) |
| Bundler | Vite |
| DB | MySQL / MariaDB (JSON columns) |
| Auth | Laravel starter auth (admin only) |
| Rich text | Alpine + a lightweight editor (e.g. [Tiptap](https://tiptap.dev) via CDN/npm, or `contenteditable` with a small toolbar). Stored as HTML string. |
| Icons | [Lucide](https://lucide.dev) (public site) + an icon-picker over a curated set (admin). Store icons as `{lib,name}` strings. |

### Setup

```bash
composer create-project laravel/laravel lara-cms
cd lara-cms
npm install
npm install alpinejs @tailwindcss/vite
```

`resources/css/app.css`:

```css
@import "tailwindcss";
@source "../views";

@theme {
  /* see §15 Theme system for the full token list */
}
```

`vite.config.js` — add `@tailwindcss/vite` plugin and the css/js entry points.

`resources/js/app.js`:

```js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
// register editor components here (see §10, §11)
Alpine.start();
```

Load Vite in both layouts (`@vite(['resources/css/app.css','resources/js/app.js'])`).

---

## 3. Architecture & directory layout

**Hard separation** between the public website and the admin panel. They share
only the database and the block registry — different layouts, different route
files, different middleware, different CSS scope.

```
app/
├── Blocks/                      # block schema definitions (the registry source)
│   ├── BlockRegistry.php        # master list + lookup
│   ├── Block.php                # value object: name, label, fields, global, view
│   ├── Field.php                # field-def builder (scalar + object)
│   ├── Home/                    # home-collection blocks
│   │   ├── HeroBanner.php
│   │   ├── FeatureImageCards.php
│   │   └── ...
│   ├── Global/                  # global blocks (navbar/footer/topbar/pageBanner)
│   ├── Blog/
│   └── Packages/
├── Http/
│   ├── Controllers/
│   │   ├── Public/PageController.php        # public rendering
│   │   ├── Admin/PageController.php         # pages CRUD + editor
│   │   ├── Admin/BlogController.php
│   │   ├── Admin/PackageController.php
│   │   ├── Admin/BookingController.php
│   │   ├── Admin/AssetController.php
│   │   ├── Admin/TaxonomyController.php
│   │   ├── Admin/UserController.php
│   │   ├── Admin/SettingsController.php
│   │   ├── Admin/SeoController.php
│   │   └── Admin/PaymentSettingsController.php
│   └── Middleware/
├── Models/
│   ├── Page.php  Post.php  Package.php  Booking.php
│   ├── Taxonomy.php  Term.php  Asset.php  User.php
│   └── Setting.php  (+ SeoSetting, PaymentSetting or a settings table)
└── Support/
    ├── Sections.php             # helpers: default data, coerce, path get/set
    └── BlockPreview.php         # renders a section array -> HTML (for editor preview)

resources/views/
├── blocks/                      # ONE Blade partial per block (public render)
│   ├── hero-banner.blade.php
│   ├── feature-image-cards.blade.php
│   ├── site-navbar.blade.php
│   └── ...
├── public/
│   ├── layout.blade.php         # public <html> shell (fonts, theme vars, footer/nav)
│   └── page.blade.php           # loops sections -> @include blocks
└── admin/
    ├── layout.blade.php         # admin shell (sidebar + header)
    ├── pages/ blog/ packages/ ...
    └── editor/                  # visual editor views + field-widget partials

routes/
├── web.php                      # public routes
└── admin.php                    # admin routes (prefix "admin", auth middleware)
```

Register `routes/admin.php` in `bootstrap/app.php` (or a route service provider)
with prefix `admin` and `auth` middleware. Public routes stay unauthenticated.

CSS scoping: admin views wrap content in `.admin-root`, public in
`.preview-shell` (see §15) so theme overrides don't leak between them.

---

## 4. Data model & migrations

All "content with sections" tables (`pages`, `posts`, `packages`) store a
`sections` **JSON** column — the ordered section array. Scalars inside a
section's `data` are stored as their natural JSON types.

### `pages`

| column | type | notes |
|--------|------|-------|
| id | bigint pk | |
| slug | string, unique | URL segment; `home` is the homepage |
| title | string | |
| sections | json | `[{ _key, name, data }]` |
| meta | json, nullable | `PageMeta` (SEO overrides, see below) |
| published | boolean, default true | |
| position | integer, default 0 | admin sort order |
| timestamps | | |

### `posts` (blog)

| column | type |
|--------|------|
| id, slug (unique), title | |
| excerpt | text nullable |
| body | longtext nullable (rich HTML) |
| author | string nullable |
| date | date nullable |
| tags | json nullable (`string[]`) |
| hero_img, banner_img | string nullable (asset URL) |
| position | integer |
| published | boolean |
| timestamps | |

### `packages`

All of `posts`' columns **plus**: `price`, `original_price`, `sale_price`
(decimal nullable), `duration`, `destination`, `category`, `discount_badge`
(string nullable), and `blocks` (json nullable — the package detail's own
ordered block list, same shape as `sections`).

### `bookings`

`id, name, email, phone, destination, date, guests (int), notes (text nullable),
status (string, default "pending"), amount (decimal nullable), timestamps`.
Created from the public booking form; listed/read-only in admin.

### `taxonomies` & `terms`

- `taxonomies`: `id, title, slug (unique), description nullable, timestamps`
- `terms`: `id, taxonomy_id (fk), title, slug, position, timestamps`
- `page_term` pivot (optional): `page_id, term_id` — pages can be tagged.

### `assets`

`id, name, path (string), directory (string, default ""), mime (string), size
(int), width (int nullable), height (int nullable), timestamps`. Files live in
`storage/app/public/uploads/...`; `path` is the public URL. Directories can be
modeled as rows with a directory mime, or purely by the `directory` string.

### `users`

Standard Laravel users + `roles` (json or a roles table), `last_login_at`.

### `settings` (single-row or key-value)

Simplest: one `settings` row (id=1) with columns, or a generic
`settings(key, value json)` table. Keys/fields needed:

- **Global**: `site_title`, `theme_color` (hex), `logo_light`, `logo_dark`,
  `contact_number`, `admin_theme`.
- **SEO defaults** (`seo_settings`): default meta description, site name, name
  position, separator, robots (indexing/following/archive/imageindex/snippet),
  og image/site name/locale, X handle, X card type, sitemap enabled/frequency/
  priority/url-limit, search-engine indexing + extra meta tags.
- **Payment** (`payment_settings`): `provider` (disabled|piprapay|sslcommerz),
  `currency`, piprapay `{base_url, api_key}`, sslcommerz `{store_id,
  store_password, sandbox}`. **Secrets are write-only**: never return the key in
  API responses — return a `*_set: bool` flag instead.

### `PageMeta` shape (the `pages.meta` JSON)

Per-page SEO overrides. Every field defaults to `"Inherit"` (fall back to the
site SEO defaults). Fields: `metaTitle, metaDescription, author, siteName,
canonicalUrl, robots, indexing, linkFollowing, noArchive, noImageIndex,
noSnippet, ogType, ogTitle, socialImage, xHandle, xCardTitle, xCardDescription,
sitemap, sitemapPriority, sitemapFrequency` (+ `*Source` selectors like
"From Field"/"Inherit"/"Custom"). Keep it as a flat JSON object; the SEO
resolver merges page meta over site defaults at render time.

### Model casts

```php
// Page.php
protected $casts = [
    'sections'  => 'array',
    'meta'      => 'array',
    'published' => 'boolean',
];
```

Seed a `home` page, `blog-post` template page, and `package-post` template page
(see §13).

---

## 5. The block system (the engine)

### 5.1 A field definition (`FieldDef`)

A field is either a **scalar** or an **object** (composite). This mirrors the
reference implementation's TinaCMS-style schema.

```php
// Scalar field
[
  'name'         => 'headline',      // key in data
  'label'        => 'Headline',
  'type'         => 'string',        // see §6 for all types
  'defaultValue' => 'Discover New Places',
  'multiline'    => false,           // string only: textarea vs input
  'description'  => null,            // optional helper text
]

// Object field (the ONE composite type)
[
  'name'         => 'cards',
  'label'        => 'Cards',
  'type'         => 'object',
  'list'         => true,            // true = array of objects (repeatable)
  'defaultCount' => 4,               // seed N items on new section
  'itemLabel'    => null,            // optional: derive card title (default: auto)
  'fields'       => [ /* nested FieldDef[] — recurses arbitrarily deep */ ],
]
```

Objects nest arbitrarily: a card list can contain a feature list which contains
links. One uniform model — there is no separate "repeatable" concept, a
repeatable is just `type: object, list: true`.

Provide a small `Field` builder for ergonomics (optional):

```php
Field::string('headline', 'Headline', default: 'Discover New Places');
Field::image('backgroundImage', 'Background Image');
Field::list('cards', 'Cards', count: 4, fields: [ ... ]);
```

### 5.2 A block definition (`Block`)

```php
// app/Blocks/Home/HeroBanner.php
return new Block(
    name:   'heroBanner',
    label:  'Hero Banner',
    view:   'blocks.hero-banner',       // resources/views/blocks/hero-banner.blade.php
    global: false,                       // true = site-wide (see §12)
    fields: [
        Field::image('backgroundImage', 'Background Image'),
        Field::string('badge', 'Badge Text', default: 'Your Next Adventure'),
        Field::string('headline', 'Headline', default: 'Discover New Places, Create Lasting Memories'),
        Field::string('description', 'Description', multiline: true, default: '...'),
        Field::link('searchUrl', 'Search URL', default: '/tours'),
        Field::string('searchPlaceholder', 'Search Placeholder', default: 'Where do you want to go?'),
        Field::string('datePlaceholder', 'Date Placeholder', default: 'Add dates'),
    ],
);
```

### 5.3 The registry

```php
// app/Blocks/BlockRegistry.php
class BlockRegistry
{
    /** @return Block[] */
    public function all(): array {
        // collections are assembled per §12: each non-global block gets a
        // `background` field prepended; global blocks (navbar/footer/topbar) are
        // shared. Dedupe by name.
        return $this->cached(fn () => [
            ...$this->globalBlocks(),
            ...$this->homeBlocks(),
            ...$this->blogBlocks(),
            ...$this->packageBlocks(),
        ]);
    }

    public function get(string $name): ?Block {
        return collect($this->all())->firstWhere('name', $name);
    }
}
```

Bind it as a singleton. Expose it to Blade via a view composer or
`app(BlockRegistry::class)`.

### 5.4 Default data from schema

When a section is added, build its `data` from the schema defaults:

```php
// app/Support/Sections.php
function defaultData(array $fields): array {
    $out = [];
    foreach ($fields as $f) {
        if (($f['type'] ?? '') === 'object') {
            $out[$f['name']] = ($f['list'] ?? false)
                ? array_map(fn () => newListItem($f), range(1, $f['defaultCount'] ?? 0) ?: [])
                : defaultData($f['fields']);
        } else {
            $out[$f['name']] = $f['defaultValue'] ?? '';
        }
    }
    return $out;
}

function newListItem(array $objectField): array {
    return ['_key' => (string) Str::uuid(), ...defaultData($objectField['fields'])];
}

function createDefaultSection(string $name): ?array {
    $block = app(BlockRegistry::class)->get($name);
    return $block ? ['_key' => (string) Str::uuid(), 'name' => $name, 'data' => defaultData($block->fields)] : null;
}
```

Also implement immutable-ish **path helpers** for the editor (get/set a value at
a drill path like `cards.0.features.1.text`) — used by the Alpine editor via a
JS port (see §10.4). In PHP they're only needed if you do server-side patching;
the Alpine editor manipulates the JSON client-side.

---

## 6. Field types reference

Scalars are edited as their type and stored as their natural JSON type.

| type | editor widget | stored as | public render |
|------|--------------|-----------|---------------|
| `string` | text input, or textarea if `multiline` | string | text |
| `number` | number input | number | number |
| `boolean` | toggle | bool | conditional |
| `datetime` | date picker | ISO `YYYY-MM-DD` string | formatted date |
| `image` | asset picker + drag-drop | URL string | `<img>` |
| `icon` | icon picker | `{lib,name}` or `"lib:name"` string | icon component |
| `rich-text` | WYSIWYG (Tiptap/contenteditable) | HTML string | `{!! $html !!}` (sanitize) |
| `background` | image + color + opacity panel | JSON string `{image,color,opacity}` | wrapper styles |
| `link` | "Page/Custom" switch → page dropdown or URL input | href string | `href` |
| `tags` | tag input (chips) | `string[]` | list |
| `object` | drill-in (single) or sortable card list (`list`) | object / array | repeated markup |

**`background`** is special: every non-global block auto-gets a `background`
field prepended (see §12). Its value is a JSON string; `{image, color, opacity}`.
Public render: wrap the section in a container that paints an absolute color
layer and/or a `background-image` layer at `opacity/100`. Blocks with their own
full-bleed background (hero, navbar) ignore it.

**`link`** UX: a segmented "Page / Custom" control. In **Page** mode it's a
dropdown of existing pages (value = the page route, `/` for home, `/{slug}`
otherwise). In **Custom** mode it's a free URL text input. Stored value is just
the href string.

---

## 7. Public website rendering

### 7.1 Routes (`routes/web.php`)

```php
Route::get('/', [PageController::class, 'home']);                 // slug "home"
Route::get('/blogs/{slug}', [PageController::class, 'blogPost']);
Route::get('/packages/{slug}', [PageController::class, 'packageDetail']);
Route::get('/packages', [PageController::class, 'packageList']);
Route::get('/{slug}', [PageController::class, 'show']);           // catch-all CMS page (LAST)
```

Order matters: the `/{slug}` catch-all must be **last**. It dispatches by slug
to a CMS page. The original uses a single catch-all that inspects segments; in
Laravel, explicit routes for `blogs/*` and `packages/*` are cleaner.

### 7.2 The renderer

The whole public render is: fetch page → merge global sections (§12) → loop
sections → include each block's view.

```php
// PageController@show
public function show(string $slug) {
    $page = Page::where('slug', $slug)->where('published', true)->firstOrFail();
    return view('public.page', ['page' => $page]);
}
```

```blade
{{-- resources/views/public/page.blade.php --}}
@extends('public.layout')

@section('content')
    @php $registry = app(\App\Blocks\BlockRegistry::class); @endphp
    @foreach (\App\Support\Sections::withGlobals($page->sections) as $section)
        @php $block = $registry->get($section['name']); @endphp
        @if ($block && view()->exists($block->view))
            @include($block->view, ['data' => $section['data'], '_key' => $section['_key']])
        @endif
    @endforeach
@endsection
```

`Sections::withGlobals()` injects the shared navbar/footer/topbar from the home
page and overrides any global sections' data with the canonical home copy (§12).

### 7.3 A block view

Each block is a self-contained Blade partial receiving `$data`. Example
`hero-banner.blade.php` (condensed):

```blade
@php $d = $data; @endphp
<section class="relative w-full overflow-hidden md:rounded-xl" data-block="heroBanner">
  <div class="relative min-h-[560px] md:min-h-[650px] flex items-center justify-center">
    @if(!empty($d['backgroundImage']))
      <img src="{{ $d['backgroundImage'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" data-edit="backgroundImage">
    @endif
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="relative z-10 flex flex-col items-center text-center px-6 py-20 max-w-4xl mx-auto">
      @if(!empty($d['badge']))
        <span data-edit="badge" class="inline-flex items-center gap-2 rounded-full bg-orange-400 px-6 py-2 text-sm font-medium text-white mb-6">{{ $d['badge'] }}</span>
      @endif
      @if(!empty($d['headline']))
        <h1 data-edit="headline" class="text-3xl md:text-6xl font-bold text-white italic leading-tight">{{ $d['headline'] }}</h1>
      @endif
      @if(!empty($d['description']))
        <p data-edit="description" class="mt-6 text-sm md:text-base text-gray-200 max-w-2xl">{{ $d['description'] }}</p>
      @endif
      <form action="{{ $d['searchUrl'] ?? '#' }}" method="get" class="mt-10 w-full max-w-2xl flex items-center bg-white rounded-full shadow-lg px-4 py-2">
        <input name="destination" placeholder="{{ $d['searchPlaceholder'] ?? '' }}" class="flex-1 bg-transparent text-sm text-gray-700 outline-none px-3 py-2">
        <input name="dates" placeholder="{{ $d['datePlaceholder'] ?? '' }}" class="flex-1 bg-transparent text-sm text-gray-700 outline-none px-3 py-2 border-l">
        <button type="submit" class="ml-2 flex h-10 w-10 items-center justify-center rounded-full bg-orange-500 text-white">→</button>
      </form>
    </div>
  </div>
</section>
```

**The `data-edit="fieldName"` attributes are load-bearing.** They are how the
Visual Editor's click-to-edit works (§10.5). Put `data-edit` on the element that
renders a field. For fields inside list items, use a path form:
`data-edit="cards:0/title"` (built from the loop index). For opening a list card
without focusing a field, use a trailing slash: `data-edit="cards:0/"`.

Repeatable list markers: wrap the repeated container in `data-list="cards"` so
the editor highlights it on hover.

### 7.4 Public layout

`public.layout.blade.php`: `<html>` with fonts (Inter), Vite assets, the
site theme CSS variables baked in from settings (§15), a `.preview-shell` class
on `<body>` or the content wrapper, and `@yield('content')`. The navbar/footer
are just global blocks rendered by the section loop, so the layout itself is
minimal.

---

## 8. Admin panel — shell, nav, layout

### 8.1 Layout (`admin/layout.blade.php`)

- **Fixed sidebar**, `w-48` (192px), dark (`bg-sidebar-bg`), `z-3`.
- **Fixed header**, `h-14`, `px-4`, `top-0 left-0 right-0`, `z-[1]`, dark. Left:
  toggle button + breadcrumb (Site name · "Pro" · current page). Right: Search
  (⌘K), Support link, "View Site" link, user menu dropdown.
- **Main content** offset left by `pl-46` (184px), `bg-body-bg`, inner
  `rounded-t-[16px]`.
- Wrap everything in `.admin-root` so admin theme overrides scope correctly.
- An optional **right-side editor sidebar** used by the Visual Editor (default
  ~350px, resizable up to 1200px). Model this as an Alpine-driven panel that the
  editor page fills; other pages leave it empty.

### 8.2 Sidebar nav groups (`nav-client`)

```
Dashboard (/admin)

COLLECTION
  Pages          /admin/pages
  Blog           /admin/blog
  Package        /admin/packages
  Booking        /admin/bookings
  Payment        /admin/payments        (list of charges/orders)
  Taxonomies     /admin/taxonomies      (expandable — child terms)
  Assets         /admin/assets
  Destinations   /admin/destinations

SETTINGS
  Globals        /admin/global
  SEO Pro        /admin/seo
  Preferences    /admin/preferences
  Payment Gateway /admin/payment-settings

USERS
  Users          /admin/users
```

Active-state highlight (`bg-sidebar-link-active-bg`), hover states, section
labels in `text-sidebar-section`. Taxonomies expands to show term groups.

### 8.3 Dashboard (`/admin`)

Overview: 3 stat cards (with mini spark charts), a bar chart (ticket/visit
volume), an activity feed, and a data table. This is decorative — hardcode or
compute simple counts (pages, posts, bookings). Charts: a tiny SVG or a CDN
chart lib; not worth a heavy dependency.

---

## 9. Pages CRUD

### 9.1 List (`/admin/pages`)

- Table of pages: status dot (published/draft), title, route badge (`/` or
  `/{slug}`), home indicator.
- Row action menu: **Edit** (→ visual editor), **Structure/Settings** (→ the
  page entry form), **Delete**.
- Drag-and-drop reorder (Alpine + a sortable helper) → `PATCH /admin/pages/reorder`.
- "Create" button → create form.

### 9.2 Create / edit page settings (Page Entry Form)

A tabbed form (Basics + SEO/meta). Fields:

- **Basics**: Title, Slug (auto-slugified from title until manually edited),
  Published toggle.
- **SEO/Meta**: the `PageMeta` fields (§4), each with an "Inherit / custom"
  selector. Group into tabs: Meta, Robots, Open Graph, Social, Sitemap.

`slugify($v)`: lowercase, trim, non-alphanumeric → `-`, strip leading/trailing
`-`.

On submit: `POST /admin/pages` (create) or `PATCH /admin/pages/{slug}` (update).
Creating a page starts with empty `sections: []`; content is added in the Visual
Editor.

---

## 10. The Visual Editor (the crown jewel)

This is the screen where you build a page: **left = form, right = live preview**
(or the reverse). It is entirely schema-driven — no per-block editor code.

### 10.1 Layout

```
┌───────────────── admin shell ─────────────────┐
│ ┌─ editor sidebar (form) ─┐ ┌─ preview pane ─┐ │
│ │ [Section list]          │ │  rendered      │ │
│ │  ⠿ Hero Banner    ✎ ✕   │ │  page sections │ │
│ │  ⠿ Features       ✎ ✕   │ │  (.preview-    │ │
│ │  ⠿ Contact        ✎ ✕   │ │   shell)       │ │
│ │  [+ Add section]        │ │                │ │
│ │ ── OR (when editing) ── │ │  clicking an   │ │
│ │ [Auto field form for    │ │  element here  │ │
│ │  the active section]    │ │  focuses its   │ │
│ │ [Save]                  │ │  field ◄───────┼─┤
│ └─────────────────────────┘ └────────────────┘ │
└────────────────────────────────────────────────┘
```

Two sidebar modes:
1. **Section list** — add / remove / reorder sections.
2. **Field editor** — the auto-generated recursive form for the active section.

### 10.2 State (Alpine store)

The editor holds the working page in an Alpine store. Bootstrap it with the
page's sections and the block schemas (as JSON) rendered into the page.

```blade
<div x-data="pageEditor()"
     x-init="init(@js($page->sections), @js($blockSchemas), @js($blockList))">
```

Where the controller passes:
- `$page->sections` — the current sections array.
- `$blockSchemas` — `{ blockName: fields[] }` for every block (for the form).
- `$blockList` — `[{name,label}]` for the "add section" picker.

```js
// resources/js/editor/page-editor.js
export default function pageEditor() {
  return {
    sections: [],          // working copy
    schemas: {},           // name -> fields[]
    blockList: [],
    active: null,          // index of section being edited, or null
    crumbs: [],            // drill path within the active section
    dirty: false,

    init(sections, schemas, blockList) {
      this.sections = structuredClone(sections);
      this.schemas = schemas;
      this.blockList = blockList;
    },

    addSection(name) {
      this.sections.push(this.createDefault(name));
      this.dirty = true;
      this.refreshPreview();
    },
    removeSection(i) { this.sections.splice(i,1); this.dirty = true; this.refreshPreview(); },
    moveSection(from, to) { /* array splice reorder */ this.dirty = true; this.refreshPreview(); },

    edit(i) { this.active = i; this.crumbs = []; },
    exit() { this.active = null; this.crumbs = []; },

    // field read/write by drill path (see 10.4)
    setField(key, value) { /* set at crumbs path */ this.dirty = true; this.refreshPreview(); },

    async save() {
      await fetch(`/admin/pages/${this.slug}/sections`, {
        method: 'PATCH',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrf},
        body: JSON.stringify({ sections: this.sections }),
      });
      this.dirty = false;
    },
  };
}
```

### 10.3 The auto form (recursive, schema-driven)

Given the active section's `fields` (schema) and its `data`, render the current
drill level:

- **scalar field** → its widget (§11), bound to the value at the current path.
- **single object field** → a "drill-in" button; clicking pushes a crumb.
- **object list field** → a sortable card list; each card shows an auto-derived
  label; card actions: Edit (drill in), Remove; a "+ Add {label}" button;
  drag to reorder.
- **background field** → a summary button that opens the background sub-editor
  (image picker + opacity slider + color select).

A **breadcrumb stack** (`crumbs`) tracks depth. A back button pops one crumb; at
the root, the back button exits to the section list.

**Card label derivation** (no schema needed): try the item's `title`, `label`,
`name`, `heading`, `text` (first non-empty), else the first non-empty string
field in schema order, else `"{Label} {n}"`. A field may override with a custom
`itemLabel`.

Blade + Alpine approach: render the form with an `<template>` that iterates
`fieldsAtPath(schema, crumbs)` and switches on `field.type` to the right widget
partial. Because Alpine can't easily recurse in templates, either:
- flatten to "current level only" (the drill model already does this — you only
  ever render one level at a time), **or**
- use a recursive Alpine component (`x-data` per level) if you render nested
  forms. The **current-level-only** model (matching the reference) is simpler:
  the form always shows exactly one level, navigation via crumbs.

### 10.4 Path helpers (JS)

The `data` tree is addressed by a path of `{key, index?}` segments. Port these
to JS for the Alpine store:

```js
// path = [{key:'cards',index:0},{key:'features',index:1}]
getAtPath(root, path)                 // -> object at path (or {})
setAtPath(root, path, key, value)     // -> new root with key=value set at path
fieldsAtPath(rootFields, path)        // -> FieldDef[] defined at that path
```

`fieldsAtPath` walks object/list fields' `fields` to find the schema for the
current level. `getAtPath`/`setAtPath` walk the data, creating arrays/objects as
needed. Reorder/add/remove on a list mutate the array at the parent path.

### 10.5 Live preview + click-to-edit

The preview shows the **real rendered blocks**. Don't reimplement block
rendering in JS — reuse Blade:

- **Preview render endpoint**: `POST /admin/preview` with `{ sections }` returns
  the rendered HTML (same block loop as public, wrapped in `.preview-shell`).
  The editor swaps it into the preview pane (`x-html` or `innerHTML`) after each
  change (debounced ~150ms). `refreshPreview()` calls this.
- Alternative (fewer round-trips): render the preview server-side on load, and
  for text edits update the matching `[data-edit]` element's text directly in
  JS; only re-fetch on structural changes (add/remove/reorder/list edits). Start
  with the simple full-refresh; optimize later. `// ponytail: full preview
  refetch per edit; switch to targeted DOM patch if it feels laggy.`

**Click-to-edit**: the preview markup carries `data-edit="..."` markers (§7.3).
Attach one delegated click listener on the preview pane:

```js
previewEl.addEventListener('click', (e) => {
  const el = e.target.closest('[data-edit]');
  if (!el) return;
  e.preventDefault();
  this.focusField(el.dataset.edit);   // e.g. "headline" or "cards:0/title"
});
```

`focusField(cmd)` parses the slash/colon path:
- `"headline"` → top-level field.
- `"cards:0/title"` → field inside card 0.
- `"cards:0/features:1/text"` → arbitrarily deep.
- `"cards:0/"` (trailing slash) → drill into the card, focus nothing.
- `"_root"` → back to the section top level.

It sets `crumbs` to navigate to that level, then focuses/scrolls the field input
and flashes a highlight ring (`box-shadow: 0 0 0 2px var(--color-primary)`).

Hover affordance in preview (CSS, §15): `[data-edit]:hover` gets a dashed blue
outline + faint blue tint; `[data-list]:hover` highlights list containers.

### 10.6 Add-section picker

Clicking "+ Add section" shows a picker of `blockList` (label + optional
thumbnail). Selecting one calls `addSection(name)`, which appends a section with
schema-default data and refreshes the preview. Global blocks (navbar/footer)
usually aren't in the per-page picker — they're managed once (§12).

### 10.7 Save

`PATCH /admin/pages/{slug}/sections` with `{ sections }`. If the page contains
**global** sections and it isn't `home`, also propagate those sections' data to
the home page so they persist site-wide (§12). Clear `dirty`, toast success.

---

## 11. Field editor widgets (Alpine)

Each scalar type maps to a small Blade partial + Alpine behavior. All widgets
take a label, the current value, and an `onChange` that writes back via
`setField`. Keep local input state to avoid cursor jumps while typing, syncing
from the store only when not focused.

| widget | behavior |
|--------|----------|
| **text / textarea** | `<input>` / `<textarea>`; `multiline` picks textarea. Debounce writes. |
| **number** | `<input type=number>`; store as number (empty → null). |
| **boolean** | toggle switch; store `true`/`false`. |
| **datetime** | native `<input type=date>` (ponytail: native over a JS datepicker) → ISO `YYYY-MM-DD`. |
| **image** | drop zone + "browse" that opens the **Asset Picker** modal; shows thumbnail + remove. |
| **icon** | button opening an **Icon Picker** modal (searchable grid over a curated Lucide set); stores `{lib,name}` / `"lib:name"`. |
| **rich-text** | Tiptap (or contenteditable) with a small toolbar (bold/italic/link/list); stores HTML. |
| **link** | "Page/Custom" segmented control → page dropdown (routes from `GET /admin/pages`) or URL input. |
| **tags** | chip input: type + Enter adds; backspace/×removes; stores `string[]`. |
| **background** | sub-panel: image picker + opacity range (0–100) + color select (preset swatches + custom); stores JSON `{image,color,opacity}`. |

**Asset Picker** (shared modal): grid of assets from `GET /admin/assets`,
directory breadcrumb, upload (drag-drop → `POST /admin/assets`), select → returns
URL to the caller. Used by image/background widgets and settings logo pickers.

Sortable list (drag reorder) is reused across the editor, pages list, taxonomy
terms, footer link columns, etc. Use a single Alpine sortable helper (small
custom drag handler or a lib like `@shopify/draggable`/SortableJS via CDN —
ponytail: SortableJS is ~10KB, don't hand-roll drag physics).

---

## 12. Global sections (site-wide navbar/footer)

Some blocks are marked `global: true`: **siteTopBar, siteNavbar, siteFooter**
(and `pageBanner`). Their content is shared across every page. The canonical
copy lives on the **home** page's sections.

Rules:
1. Each collection prepends the global blocks to its available blocks, and
   prepends a `background` field to every **non-global** block.
2. When rendering any non-home page, **merge** global sections from home:
   - override the page's own global sections' `data` with home's copy,
   - inject any global sections missing from the page.
3. When saving global sections' edits on a non-home page, also write them back
   to home so the change is site-wide.

```php
// app/Support/Sections.php
public static function withGlobals(array $sections): array {
    $home = Page::where('slug','home')->first();
    if (!$home) return $sections;
    $registry = app(BlockRegistry::class);
    $globals = collect($home->sections)
        ->filter(fn ($s) => $registry->get($s['name'])?->global);

    $merged = collect($sections)->map(function ($s) use ($globals) {
        $g = $globals->firstWhere('name', $s['name']);
        return $g ? [...$s, 'data' => $g['data']] : $s;
    });
    // append globals not present on the page
    foreach ($globals as $g) {
        if (!$merged->contains(fn ($s) => $s['name'] === $g['name'])) $merged->push($g);
    }
    return $merged->all();
}
```

The home page is where you edit navbar/footer; other pages inherit them
automatically. (You can present the global blocks in a dedicated "Globals"
admin screen that edits the home page's global sections directly.)

---

## 13. Other collections: Blog, Packages, Bookings

Blog and Packages reuse the **same Visual Editor** via a "content type config" —
the only differences are the schema fields, the API resource, and a template
page that positions the dynamic content.

### 13.1 Content-type config

A content type binds: the entry fields (schema), a **template page** (a normal
CMS page whose sections wrap the entry), and the **slot** section name where the
entry content renders.

```
Blog:
  entry fields: title, slug, banner_img, hero_img, author, date, tags, body(rich-text)
  template page slug: "blog-post"  (sections: siteTopBar → siteNavbar → blogPostSlot → siteFooter)
  slot section name:  "blogPostSlot"
  public URL:         /blogs/{slug}

Package:
  entry fields: title, slug, hero_img, price, original_price, sale_price, duration,
                destination, category, discount_badge, body, + a per-package `blocks[]`
                (its own ordered detail blocks — travelDetailHero, ...About, ...Itinerary, etc.)
  template page slug: "package-post" (sections: ...topbar/nav → packagePostSlot → footer)
  slot section name:  "packagePostSlot"
  public URL:         /packages/{slug}
```

Public render of a blog post: fetch the `blog-post` template page, render its
sections; when the loop hits `blogPostSlot`, render the actual post
(banner/meta/body). Same for packages with `packagePostSlot`. The template page
is itself editable in the pages editor — so the chrome around every post is
CMS-controlled.

### 13.2 Package detail blocks

Packages additionally have their own block list stored in `packages.blocks`
(same section shape), edited by the package's visual editor via a secondary
block panel. The 11 package-detail blocks (hero, gallery hero, about, booking
card, locations, highlights, itinerary, features, info, FAQ, map) are in
Appendix A. This is the same engine pointed at a different JSON column.

### 13.3 Admin screens

- **Blog** (`/admin/blog`): list (status, title, `/blogs/{slug}` badge,
  edit/view/delete, reorder), create form (the entry fields), visual editor at
  `/admin/blog/{id}`.
- **Packages** (`/admin/packages`): same, plus package basics (price, duration,
  etc.) and the detail block editor.
- **Bookings** (`/admin/bookings`): read-only list (Date, Name → detail, Email),
  export button; detail view. Bookings are created by the public booking form
  (`POST /bookings`), optionally via a payment charge flow.

### 13.4 Booking + payment flow (optional)

Public booking form → `POST /bookings`. If a payment provider is enabled,
`POST /payments/charge` creates the booking and returns a redirect URL to the
gateway (PipraPay or SSLCommerz); success/cancel return pages confirm. Keep
provider secrets server-side and write-only.

---

## 14. Settings, SEO, Taxonomies, Assets, Users

- **Globals** (`/admin/global`): site title, logo (light/dark via asset picker),
  contact/WhatsApp number, theme color (preset swatches + live preview). Writes
  `settings`. Theme color drives the site CSS vars (§15).
- **SEO Pro** (`/admin/seo`): tabbed (Meta, Robots, Open Graph, Social, Sitemap,
  Search Engines). Tab state in `?tab=`. Writes `seo_settings`. These are the
  defaults each page's `meta` inherits from.
- **Payment Gateway** (`/admin/payment-settings`): provider select, currency,
  PipraPay (base URL + API key, write-only), SSLCommerz (store id/password +
  sandbox toggle). Writes `payment_settings`; never echo secrets.
- **Taxonomies** (`/admin/taxonomies`): CRUD taxonomy groups (title, handle) and
  their terms (title, slug, reorderable). `[handle]` sub-page manages terms.
- **Assets** (`/admin/assets`): file browser with directory breadcrumb,
  drag-drop upload, create directory, rename/delete, preview with next/prev,
  image dimensions. Backed by `assets` table + `storage/app/public`.
- **Users** (`/admin/users`): list (email+avatar, name, roles, last login,
  search), create/edit forms. Standard Laravel auth.
- **Preferences / Profile**: user-level admin prefs (language, start page, WCAG
  mode, dirty-nav confirm, admin theme) and profile (name, email, avatar,
  change password).
- **Destinations** (`/admin/destinations`): simple list (image, name, country)
  with a slide-in create drawer; feeds the package/booking destination selects.

---

## 15. Theme system

All UI colors are CSS custom properties. **Never hardcode colors** — use the
tokens. Define them in `app.css` under `@theme` (Tailwind v4 exposes each
`--color-*` as a `bg-*`/`text-*`/`border-*` utility).

```css
@theme {
  /* sidebar */
  --color-sidebar-bg: oklch(0.274 0.006 286.033);
  --color-sidebar-link: oklch(0.37 0.013 285.805);
  --color-sidebar-link-active: oklch(0.21 0.006 285.885);
  --color-sidebar-link-active-bg: oklch(0 0 0 / 0.07);
  --color-sidebar-section: oklch(0.44 0.012 286.033);
  /* header */
  --color-header-bg: oklch(0.274 0.006 286.033);
  --color-header-text: #fff;
  /* surfaces */
  --color-body-bg: oklch(0.967 0.001 286.375);
  --color-content-bg: #fff;
  --color-content-border: oklch(0.92 0.004 286.32);
  --color-panel-bg: oklch(0.956 0.0022 286.32);
  --color-border: #e7e3e4;
  /* text */
  --color-text-primary: oklch(0.21 0.006 285.885);
  --color-text-muted: oklch(0.552 0.016 285.938);
  --color-text-heading: oklch(0.21 0.006 285.885);
  /* accent (driven by settings.theme_color) */
  --color-primary: oklch(0.457 0.24 277.023);
  --color-brand: var(--color-primary);
  --color-success: oklch(0.792 0.209 151.711);
  --color-danger: oklch(0.577 0.245 27.325);

  --spacing-46: 11.5rem;   /* main content offset */
}
```

**Dynamic accent**: the site's `theme_color` (hex, from Globals) overrides
`--color-primary`/`--color-brand`. Compute hover (darken ~10%), soft
(lighten ~80%), and foreground (black/white by luminance) server-side and emit
them as inline CSS vars on the public `<html>`/`<body>` so there's no flash.

**Scope isolation**: admin overrides `--color-*` on `.admin-root` with its own
accent. The public preview inside the editor wraps content in `.preview-shell`,
which **re-declares** the surface/text tokens to their site defaults and
re-derives `--color-primary` from a stable `--site-*` alias (that admin never
touches) — so the editor preview shows the real site theme, not the admin
chrome. Emit theme vars twice: `--color-*` (consumed) and `--site-*` (stable
alias) for exactly this reason.

**Preview hover CSS** (in `app.css`):

```css
.preview-shell [data-edit] { cursor: pointer; }
.preview-shell [data-edit]:hover {
  outline: 2px dashed #3b82f6 !important; outline-offset: -2px;
  background-color: rgba(59,130,246,.07) !important; position: relative; z-index: 50;
}
.preview-shell [data-list]:hover {
  outline: 2px dashed #3b82f6 !important; outline-offset: -2px;
  background-color: rgba(59,130,246,.07);
}
```

### Layout constants

| part | value |
|------|-------|
| Sidebar width | `w-48` (192px) |
| Main content offset | `pl-46` (184px) |
| Main inner radius | `rounded-t-[16px]` |
| Header | `fixed top-0 left-0 right-0 h-14 px-4 z-[1]` |
| Nav z-index | `z-3` |
| Editor sidebar | ~350px default, resizable up to 1200px |

---

## 16. Routes reference

### Public (`web.php`)

```
GET  /                        home page (slug "home")
GET  /blogs/{slug}            blog post (blog-post template + post)
GET  /packages               package listing
GET  /packages/{slug}        package detail (package-post template + package)
POST /bookings                create booking (public form)
POST /payments/charge         start payment -> redirect
GET  /{slug}                  CMS page (catch-all, LAST)
```

### Admin (`admin.php`, prefix `admin`, `auth`)

Pages: `GET /pages`, `GET /pages/create`, `POST /pages`,
`GET /pages/{slug}/edit` (settings), `GET /pages/{slug}` (visual editor),
`PATCH /pages/{slug}` (settings), `PATCH /pages/{slug}/sections` (save sections),
`PATCH /pages/reorder`, `DELETE /pages/{slug}`, `POST /preview` (render preview).

Same CRUD shape for `posts`(blog), `packages`, `taxonomies` (+ nested
`/terms`), `assets`, `users`. Settings: `GET/PUT /global`, `GET/PUT /seo`,
`GET/PUT /payment-settings`. Bookings: `GET /bookings`, `GET /bookings/{id}`,
`DELETE /bookings/{id}`.

### Reference API payloads (JSON shapes)

```
GET   /pages                 -> { pages: Page[] }
GET   /pages/{slug}          -> { page: Page }
POST  /pages { title, slug, sections?, meta?, published? } -> { page }
PATCH /pages/{slug} { title?, slug?, sections?, meta?, published? } -> { page }
PATCH /pages/reorder { page_ids: number[] } -> { pages }
DELETE /pages/{slug}
GET   /posts?perPage=100     -> { posts, totalPages, totalCount }
GET   /posts/by-slug/{slug}  -> { post }
POST/PATCH/DELETE /posts     (same shape as pages, fields per §4)
GET   /packages ...          (same, + package fields + blocks[])
GET   /bookings              -> { bookings }
POST  /bookings { name,email,phone,destination,date,guests,notes? } -> { booking }
GET   /taxonomies            -> { taxonomies }
POST  /taxonomies/{slug}/terms { title, slug } -> { term }
PATCH /taxonomies/{slug}/terms/reorder { term_ids } -> { taxonomy }
GET   /assets?directory=     -> { assets }
POST  /assets  (multipart: file, directory?) -> { asset }
GET/PUT /settings            -> { settings: { siteTitle, themeColor, logoLight, logoDark, contactNumber, adminTheme } }
GET/PUT /seo-settings        -> { seo }
GET/PUT /payments/settings   -> { settings }   (secrets returned as *_set flags only)
```

In a pure-Blade build these can be classic form POSTs + redirects; the JSON
endpoints are only needed for the Alpine editor (sections save, preview, asset
picker, page-list-for-links). Keep those as JSON, the rest as normal Laravel
resource controllers with Blade views.

---

## 17. Build order / milestones

Build the engine first, then blocks, then the rest. Each milestone is
independently testable.

1. **Foundation**: Laravel + Tailwind v4 + Alpine wired; `app.css` theme tokens;
   admin layout + public layout; auth for admin.
2. **Data**: migrations for `pages` (+ seed `home`); `Page` model with JSON
   casts; `Sections` helpers (`defaultData`, `newListItem`, path get/set).
3. **Block engine**: `Field`, `Block`, `BlockRegistry`; register **one** block
   (`heroBanner`) with a Blade view. Prove public render (`/`) works.
4. **Public renderer**: `public.page` section loop + `withGlobals` (stub until
   globals exist). Add the `background` wrapper.
5. **Pages admin**: list, create/settings form (slug + published + meta),
   delete, reorder.
6. **Visual Editor v1**: section list (add/remove/reorder) + auto form for
   scalar fields + full-refresh live preview + save sections. This unlocks
   everything.
7. **Field widgets**: image (asset picker), boolean, number, datetime, tags,
   link, rich-text, icon, background sub-editor.
8. **Object/list fields**: drill-in crumbs, sortable card lists, click-to-edit
   (`data-edit` path parsing).
9. **Global sections**: navbar/footer/topbar blocks + `withGlobals` merge +
   save-back-to-home; Globals admin screen.
10. **Remaining home blocks** (Appendix A) — each is just a schema + a Blade view.
11. **Blog + Packages**: content-type configs, template pages (`blog-post`,
    `package-post`), slot blocks, entry forms, public routes, package detail
    blocks.
12. **Settings / SEO / Taxonomies / Assets / Users / Bookings / Destinations**.
13. **Theme polish**: dynamic accent from settings, preview scope isolation,
    dashboard.

**Testing** (Pest): feature-test the engine — `defaultData` builds correct
trees; adding/removing/reordering sections; `withGlobals` merges home globals;
public page renders each registered block without error (a smoke test that
visits `/` and every seeded page asserting 200 + no missing-view). One test per
non-trivial helper (path get/set, slugify, coerce).

---

## Appendix A — full block catalog

Notation: `name (Label) [collection]: field:type[modifiers]=default, ...`.
`object[list,defaultCount=N]{...children...}` is a repeatable. Every non-global
block also gets a prepended `background` field (auto-added by the collection).

### Global blocks (`global: true`, shared site-wide)

```
siteTopBar (Top Bar): topBarEnabled:boolean=true, topBarEmail:string="hello@…",
  promoText:string="Get 10% off your first booking!", promoLinkText:string="Learn more",
  promoLinkUrl:link="#",
  topBarSocial:object[list,defaultCount=3]{ icon:icon, platform:string="facebook", url:link="#" }

siteNavbar (Navbar): logo:image, logoHeight:number=40, brandName:string="E CMS",
  nav:object[list,defaultCount=4]{ label:string="Home", href:link="/",
      dropdown:object[list]{ label:string="Item", href:link="#" } },
  contactLabel:string="Chat with us", contactNumber:string="+8801771868382",
  contactLink:link="https://wa.me/…", contactIcon:image

siteFooter (Footer): bannerImage:image, logo:image, logoHeight:number=40,
  brandName:string="E CMS", description:string(multiline), email:string, phone:string,
  linkColumns:object[list,defaultCount=3]{ heading:string="Quick Links",
      links:object[list,defaultCount=3]{ label:string="Contact Us", href:link="#" } },
  socialHeading:string="Connect with us",
  social:object[list]{ icon:icon, label:string="Facebook", url:link="#" },
  copyrightBrand:string="E CMS", copyright:string="© 2026 …",
  legalLinks:object[list]{ label:string="Privacy Policy", href:link="#" },
  languageCurrency:string="EN / USD"

pageBanner (Page Banner): title:string="Page Title", backgroundImage:image
```

### Home collection blocks

```
heroBanner (Hero Banner): backgroundImage:image, badge:string="Your Next Adventure",
  headline:string="Discover New Places, Create Lasting Memories",
  description:string(multiline), searchUrl:link="/tours",
  searchPlaceholder:string="Where do you want to go?", datePlaceholder:string="Add dates"

aboutIntro (About Intro): image1:image, image2:image, image3:image, badge:image,
  heading:string="Why We're Best Agency", subheading:string,
  body1:string(multiline), body2:string(multiline), signature:image,
  signerName:string="Jane Doe", signerTitle:string="Founder & CEO"

featureImageCards (Feature Image Cards): headline:string="Our Premium Travel Services",
  description:string(multiline),
  cards:object[list,defaultCount=4]{ image:image, title:string="Hotel & Resort Booking",
      description:string(multiline) }

contact (Contact): heading:string="Contact Us", subheading:string(multiline),
  mapEmbedUrl:string, emailTitle:string="Email", emailDescription:string,
  emailValue:string, phoneTitle:string="Phone", phoneDescription:string,
  phoneValue:string, officeTitle:string="Office", officeDescription:string, officeValue:string

latestBlog (Latest Blog): headline:string="Latest Travel Blog", description:string(multiline)
  (renders the N most recent posts — pulls from posts table)

profileBento (Profile Bento): profileImage:image, profileName:string, profileRole:string(multiline),
  profileStatus:string="Available",
  profileSocial:object[list,defaultCount=4]{ icon:icon, platform:string, url:link="#" },
  aboutText:string(multiline), quoteText:string(multiline),
  imageTopRight:image, imageBottomLeft:image,
  stats:object[list,defaultCount=4]{ icon:icon, count:string="1000", handle:string="@travelagency" }

teamCards (Team Cards): headline:string="Meet our Team", description:string(multiline),
  members:object[list,defaultCount=4]{ image:image, name:string="Team Member", role:string="Role",
      social:object[list,defaultCount=3]{ icon:icon, platform:string, url:link="#" } }

clientTestimonials (Testimonials): headline:string="What our clients say",
  description:string(multiline), animationSpeed:number=60,
  testimonials:object[list,defaultCount=6]{ avatar:image, name:string, role:string="Happy Traveler",
      rating:number=5, handle:string, mentionLabel:string, quote:string(multiline), twitterUrl:link }

travelDeals (Travel Deals): headline:string="Travel Deals", description:string(multiline),
  button:object{ label:string, link:link="#" },
  cards:object[list,defaultCount=3]{ image:image, badge:string="Popular", title:string,
      description:string(multiline), priceLabel:string="Per Person", price:number=299,
      originalPrice:number=499, buttonLabel:string="Book Now",
      features:object[list]{ icon:icon, text:string="Included", tooltip:rich-text } }

whyChooseUs (Why Choose Us): heading:string="Why Choose Us", subtitle:string(multiline), image:image,
  features:object[list,defaultCount=4]{ number:string="01", title:string, description:string(multiline) }
```

### Blog collection blocks

```
blogPostSlot (Blog Post Content): blogListHref:string="/blog"
  (template slot — renders the actual post: banner, meta, body, sidebar)
blogList (Blog List): layout:string="grid", postsPerPage:number=6
```

### Packages collection blocks

```
packagePostSlot (Package Content): packageListHref:string="/packages"   (template slot)
packageList (Package List): packagesPerPage:number=5, priceMax:number=500000
```

### Package detail blocks (stored in `packages.blocks`)

```
travelDetailHero:        title:string, duration:string, price:number, images:object[list]{ image:image }
travelDetailGalleryHero: title:string, location:string, rating:number, reviewCount:number, images:object[list]{ image:image }
travelDetailAbout:       headline:string="About Tour Package", description:rich-text, infoItems:object[list]{ label:string, value:string }
travelDetailBooking:     discountBadge:string, originalPrice:number, salePrice:number,
                         guarantees:object[list]{ text:string }, bookLabel:string="Book Now",
                         whatsappPhone:string, bonusNote:string
travelDetailLocations:   headline:string="Explore Locations", locations:object[list]{ name:string, duration:string, image:image }
travelDetailHighlights:  headline:string="Highlights of the Tour", items:object[list]{ text:string(multiline) }
travelDetailItinerary:   headline:string="Tour Itinerary",
                         sections:object[list]{ location:string, departure:string,
                             days:object[list]{ dayLabel:string="Day 1", title:string, body:rich-text } }
travelDetailFeatures:    headline:string="Package Features List",
                         includes:object[list]{ text:string(multiline) }, excludes:object[list]{ text:string(multiline) }
travelDetailInfo:        headline:string="Additional Info",
                         items:object[list,defaultCount=6]{ title:string, description:string(multiline) }
travelDetailFaq:         headline:string="Frequently Asked & Question", items:object[list]{ question:string, answer:rich-text }
travelDetailMap:         headline:string="Package Destination Map", mapImage:image
```

---

### Reference implementation notes

The original is a Next.js 16 / React 19 app talking to a Laravel API. Key
mechanics ported above: the schema-driven registry, the recursive drill-in
editor with breadcrumb navigation, `data-edit` click-to-edit markers, the
global-sections sync from home, and the CSS-variable theme with admin/preview
scope isolation. In this Blade rebuild, **reuse Blade for the preview** (render
sections server-side) instead of re-implementing block rendering in JS — that's
the single biggest simplification versus the React original.
