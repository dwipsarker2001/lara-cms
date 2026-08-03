# Collections Documentation

Collections in **Lara-CMS** form the core content modeling system. They allow administrators to build dynamic content structures (e.g. Pages, Blog Posts, Products, Services, Case Studies, or Layout Templates) with customizable schema fields, visual page builder sections, and SEO controls.

---

## 1. Architecture & Data Model

Collections consist of two primary entities:
- **Collection (`collections` table)**: Defines the content type blueprint, icon, SEO flag, ordering position, and custom JSON schema fields.
- **Collection Entry (`collection_entries` table)**: Individual content items belonging to a collection, containing field data, page builder visual sections, metadata, published status, and URL slug.

### Database Schema

#### `collections` Table
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary key |
| `name` | `string` | Display name of the collection (e.g., "Products") |
| `slug` | `string` | Unique URL-friendly slug (auto-generated) |
| `icon` | `string` | FontAwesome icon CSS class |
| `show_in_menu` | `boolean` | Flag for sidebar menu display |
| `enable_seo` | `boolean` | Enables SEO metadata tabs on entries |
| `description` | `text` | Description of the collection |
| `fields` | `json` | Custom schema field definitions |
| `position` | `integer` | Sidebar & list sorting order |

#### `collection_entries` Table
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary key |
| `collection_id` | `foreignId` | Belongs to `collections.id` |
| `data` | `json` | Field values (including `title`, custom schema inputs) |
| `slug` | `string` | Unique URL slug for public routing |
| `published` | `boolean` | Published visibility status (`true`/`false`) |
| `sections` | `json` | Page builder drag-and-drop widget sections |
| `meta` | `json` | SEO & social sharing metadata |
| `position` | `integer` | Drag-and-drop ordering index within collection |

---

## 2. Managing Collections

### Creating a Collection (`GET /admin/collections/create`)
- **Controller**: `App\Http\Controllers\Admin\CollectionController@create`
- **View**: `resources/views/admin/collections/create.blade.php`

#### Collection Attributes:
1. **Name**: Descriptive title (e.g., "Services"). Converts automatically to a unique slug via `Str::slug()`. Duplicate slugs append numerical suffixes (`services`, `services-1`).
2. **Icon**: Searchable FontAwesome icon selector.
3. **Enable SEO**: Toggles SEO metadata fields on/off for entries in this collection.
4. **Fields (JSON Schema)**: Defines custom entry input fields.

---

## 3. Schema Fields & Supported Field Types

A collection's `fields` column defines the dynamic schema presented when creating entries. Supported field types include:

| Field Type | Description |
| :--- | :--- |
| `text` | Standard text input field |
| `textarea` | Multi-line text block |
| `number` | Numeric input |
| `collection` | Relation lookup referencing entries in another collection (used for templates/layouts) |
| `taxonomies` | Category/Term selector dropdown |
| `tags` | Interactive multi-tag input |

---

## 4. Collection Entries

### Managing Entries (`GET /admin/collections/{collection}/entries`)
- **Controller**: `App\Http\Controllers\Admin\CollectionEntryController@index`
- **Reordering**: Entries can be reordered via drag-and-drop (uses `SortableJS` sending `PATCH /admin/collections/{collection}/entries/reorder`).

### Creating an Entry (`GET /admin/collections/{collection}/entries/create`)
- **Fields**:
  - **Title**: Entry title.
  * **URL Slug**: Auto-slugified from title; customizable.
  * **Author**: Selected from system administrators.
  * **Custom Fields**: Dynamic inputs rendered from the parent collection's `fields` schema.
  * **Published**: Toggle to set entry live (`true`) or draft (`false`).

### Automatic Layout / Section Copying
When creating an entry, if a custom field of type `collection` is present and points to a layout/template entry:
- Upon submission, `CollectionEntryController@store` finds the referenced entry (`CollectionEntry::find($id)`).
- It extracts `$selectedEntry->sections` and copies all visual page builder blocks into the new entry's `sections` column.
- The user is redirected straight to the Page Builder Editor (`/admin/collections/{collection}/entries/{entry}/editor`) with all layout blocks pre-populated.

---

## 5. SEO & SEO Pro Features

When **Enable SEO** (`enable_seo = true`) is turned on for a collection, entry forms expose two extra tabs:

### SEO Tab
- **Meta Title**: Custom Google title snippet (defaults to entry title).
- **Meta Description**: Search engine snippet summary.
- **Canonical URL**: Overrides default canonical tag.
- **JSON-LD Schema**: Custom structured data script injection.

### SEO Pro Tab
- **Robots Directives**: Controls for indexing (`noindex`), link following (`nofollow`), `noarchive`, `nosnippet`, `max-snippet`, and `max-image-preview`.
- **Social Sharing Cards**: Custom OpenGraph / X (Twitter) titles, descriptions, and preview images.
- **Live SERP Preview**: Real-time visual rendering of Google search & social card previews.

---

## 6. Public Routing & Front-end Rendering

Public requests are resolved by `App\Http\Controllers\Public\PageController`:

| Route Pattern | Resolver Method | Example URL |
| :--- | :--- | :--- |
| `/` | `PageController@home` | Root homepage (`slug = 'home'`) |
| `/{slug}` | `PageController@show` | `pages` collection entry (e.g. `/about-us`) |
| `/{collectionSlug}/{slug}` | `PageController@showCollectionEntry` | Custom collection entry (e.g. `/products/laptop`) |

### Entry Route Helper
Model `CollectionEntry` provides `$entry->route()`:
- Returns `/` for slug `home`.
- Returns `/{slug}` for entries in the `pages` collection.
- Returns `/{collection_slug}/{slug}` for custom collections.

### Published Protection
Only entries with `published = true` can be accessed publicly. If `published = false`, public requests yield a `404 Not Found`.

---

## 7. Layout Collections & Template Best Practices

When creating a Collection specifically to act as **Layout Templates**:

1. **Turn "Enable SEO" OFF**: Layouts are internal blueprints, not standalone public pages. Turning SEO off keeps the template interface clean.
2. **Keep Template Entries Unpublished (`published = false`)**: This prevents template entries from generating indexed public URLs while keeping them fully accessible inside the admin panel as layout sources for new pages.

---

## 8. Admin Routes & Controller Summary

| Method | URI | Route Name | Action |
| :--- | :--- | :--- | :--- |
| `GET` | `/admin/collections` | `admin.collections.index` | `CollectionController@index` |
| `GET` | `/admin/collections/create` | `admin.collections.create` | `CollectionController@create` |
| `POST` | `/admin/collections` | `admin.collections.store` | `CollectionController@store` |
| `GET` | `/admin/collections/{collection}/edit` | `admin.collections.edit` | `CollectionController@edit` |
| `PUT/PATCH` | `/admin/collections/{collection}` | `admin.collections.update` | `CollectionController@update` |
| `DELETE` | `/admin/collections/{collection}` | `admin.collections.destroy` | `CollectionController@destroy` |
| `PATCH` | `/admin/collections/reorder` | `admin.collections.reorder` | `CollectionController@reorder` |
| `GET` | `/admin/collections/{collection}/entries` | `admin.collections.entries.index` | `CollectionEntryController@index` |
| `GET` | `/admin/collections/{collection}/entries/create` | `admin.collections.entries.create` | `CollectionEntryController@create` |
| `POST` | `/admin/collections/{collection}/entries` | `admin.collections.entries.store` | `CollectionEntryController@store` |
| `GET` | `/admin/collections/{collection}/entries/{entry}/edit` | `admin.collections.entries.edit` | `CollectionEntryController@edit` |
| `PUT/PATCH` | `/admin/collections/{collection}/entries/{entry}` | `admin.collections.entries.update` | `CollectionEntryController@update` |
| `DELETE` | `/admin/collections/{collection}/entries/{entry}` | `admin.collections.entries.destroy` | `CollectionEntryController@destroy` |
| `GET` | `/admin/collections/{collection}/entries/{entry}/editor` | `admin.collections.entries.editor` | `CollectionEntryController@editor` |
| `PATCH` | `/admin/collections/{collection}/entries/{entry}/update-sections` | `admin.collections.entries.update-sections` | `CollectionEntryController@updateSections` |
| `PATCH` | `/admin/collections/{collection}/entries/reorder` | `admin.collections.entries.reorder` | `CollectionEntryController@reorder` |
