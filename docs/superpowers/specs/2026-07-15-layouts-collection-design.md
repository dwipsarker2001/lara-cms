# Layouts Collection

## Overview

A Layout is a reusable preset of sections (blocks) that can be applied when creating a new page, blog post, or package. Layouts store the same `sections` JSON format as pages/posts and are edited using the existing block editor.

## Data Model

**`layouts` table:**

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint, auto-increment | Primary key |
| `name` | string(255) | Human-readable name |
| `collection` | string(50) | Content type: `"page"`, `"blog"`, or `"package"` |
| `sections` | JSON | `[{_key, name, data}]` same format as pages/posts |
| `position` | integer | For ordering in selector dropdown |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

## Admin CRUD

### LayoutController

| Method | Action |
|--------|--------|
| `index()` | List all layouts, sortable |
| `create()` | Form: name + for dropdown |
| `store(Request)` | Validate name/for, create with empty sections |
| `edit(Layout)` | Form: name + for dropdown + delete |
| `update(Request, Layout)` | Validate and update |
| `destroy(Layout)` | Delete |
| `editor(Layout)` | Block editor (reuses `admin.pages.editor`) |
| `updateSections(Request, Layout)` | Save sections JSON |

### Routes (`/admin/layouts`)

```php
Route::resource('layouts', LayoutController::class)->except(['show']);
Route::patch('layouts/{layout}/sections', [LayoutController::class, 'updateSections'])->name('layouts.update-sections');
Route::get('layouts/{layout}/editor', [LayoutController::class, 'editor'])->name('layouts.editor');
```

### Admin Nav

Add a "Layouts" link to the admin sidebar/navigation.

## Admin Views

### `admin.layouts.index`

Same sortable list pattern as `admin.posts.index` using `x-admin::sortable-list` component. Each item shows layout name and "for" type.

### `admin.layouts.create`

Form with:
- Name (text input)
- For (select: Page / Blog / Package)
- Same admin styling as `admin.posts.create`

### `admin.layouts.edit`

Same form + delete section.

## Layout Editor

Reuses `admin.pages.editor`. The controller passes the Layout model as `$page` (since the editor expects a `page` object with `sections`, `id`, `slug`, `title`). Passes empty `$pages` and empty `$homeGlobals`. The editor save route points to `admin.layouts.update-sections`.

## Page/Post/Package Create Integration

### Layout Selector

A "Layout" dropdown is added to the create forms for:
- `admin.pages.create` — filtered to layouts where `for = 'page'`
- `admin.posts.create` — filtered to layouts where `for = 'blog'`

The dropdown is placed at the top of the form. Alpine.js can optionally show description text when a layout is selected.

### Store Method Changes

**PageController@store:**
```php
if ($request->layout_id) {
    $layout = Layout::findOrFail($request->layout_id);
    $data['sections'] = [...($layout->sections ?? []), ...Sections::injectGlobals()];
} else {
    $data['sections'] = Sections::injectGlobals();
}
```

**PostController@store:**
```php
if ($request->layout_id) {
    $layout = Layout::findOrFail($request->layout_id);
    $data['sections'] = $layout->sections ?? [];
}
```

**Validation rules** both add: `'layout_id' => 'nullable|exists:layouts,id'`

## Factory & Seeder

### LayoutFactory

Generates a layout with name, random for type, and empty sections.

### Database Seeder

Creates a default "Default Page" layout (for: page) with common blocks pre-added, so new users have something to start with.

## Files to Create

1. `database/migrations/XXXX_XX_XX_create_layouts_table.php`
2. `app/Models/Layout.php`
3. `app/Http/Controllers/Admin/LayoutController.php`
4. `database/factories/LayoutFactory.php`
5. `database/seeders/LayoutSeeder.php` (referenced from DatabaseSeeder)
6. `resources/views/admin/layouts/index.blade.php`
7. `resources/views/admin/layouts/create.blade.php`
8. `resources/views/admin/layouts/edit.blade.php`

## Files to Modify

1. `routes/admin.php` — add layout routes
2. `app/Http/Controllers/Admin/PageController.php` — add layout_id handling to store()
3. `app/Http/Controllers/Admin/PostController.php` — add layout_id handling to store()
4. `resources/views/admin/pages/create.blade.php` — add layout selector
5. `resources/views/admin/posts/create.blade.php` — add layout selector
6. `resources/views/admin/layout.blade.php` or nav component — add Layouts nav link
