# Modular Plugin & Extension Architecture

This guide explains how to build custom features (such as **Blog Comments**, **Client Portals**, **eCommerce**, or **Custom Blocks**) on top of Lara-CMS **without modifying core files**, ensuring seamless updates to the CMS engine.

---

## 1. The Core Isolation Principle

To make Lara-CMS reusable across multiple client projects and safe from update overwrites:

| Component | Location | Role | Safe to Modify? |
|---|---|---|---|
| **Core CMS Engine** | `app/`, `resources/views/admin/`, `database/migrations/` | Foundational CMS, Admin Panel, Database Schema | ❌ **Do Not Edit** (Keeps core updatable) |
| **Project Extensions** | `plugins/{plugin-slug}/` | Custom Pages, Custom Migrations, Models, Blocks, Controllers | ✅ **Edit Freely** (100% Protected) |

---

## 2. Directory Structure of a Plugin

Every feature lives inside its own isolated folder under `plugins/`:

```
plugins/
└── blog-comments/
    │
    ├── plugin.json                       ← Manifest & Admin Sidebar Navigation
    │
    ├── database/
    │   ├── migrations/                   ← Isolated Database Migrations (Auto-loaded)
    │   │   └── 2026_08_18_000001_create_blog_comments_table.php
    │   ├── seeders/                      ← Feature-specific seeders
    │   └── factories/                    ← Feature-specific factories
    │
    ├── routes/
    │   ├── admin.php                     ← Admin Routes (auto auth:admin & /admin prefix)
    │   ├── api.php                       ← API Endpoints (auto /api prefix)
    │   └── web.php                       ← Public Web Routes
    │
    ├── src/                              ← Business Logic (PSR-4: Plugins\BlogComments\...)
    │   ├── Models/                       ← Eloquent Models
    │   ├── Http/Controllers/             ← Controllers
    │   └── Support/                      ← Helper Classes & Services
    │
    ├── Blocks/                           ← Co-Located Custom Blocks
    │   └── CommentSection/
    │       ├── CommentSection.php        ← Block Schema & Fields
    │       └── view.blade.php            ← Co-Located Blade Template
    │
    └── views/                            ← Blade Views (blog-comments::admin.index)
        └── admin/
            └── index.blade.php
```

---

## 3. Quick-Start Artisan Commands

Lara-CMS includes built-in Artisan generator commands to scaffold modules and components instantly:

### 1. Create a New Plugin Scaffold
```bash
php artisan make:plugin "Blog Comments"
```
Creates `plugins/blog-comments/` with `plugin.json`, routes, views, and PSR-4 setup.

### 2. Create an Isolated Migration in the Plugin
```bash
php artisan make:plugin-migration blog-comments create_blog_comments_table
```
Creates a timestamped migration inside `plugins/blog-comments/database/migrations/`.
Running `php artisan migrate` automatically executes core migrations AND all plugin migrations!

### 3. Create a Single-Directory Co-Located Block
```bash
php artisan make:plugin-block blog-comments CommentSection
```
Creates `plugins/blog-comments/Blocks/CommentSection/CommentSection.php` and its matching `view.blade.php` side-by-side in one directory.

---

## 4. How-To Walkthroughs

### A. Adding a New Admin Page

1. **Register the Sidebar Item** in `plugins/my-feature/plugin.json`:
   ```json
   {
       "name": "My Feature",
       "slug": "my-feature",
       "version": "1.0.0",
       "admin_menu": [
           {
               "label": "My Feature",
               "route": "admin.my-feature.index",
               "icon": "fa-solid fa-star",
               "badge": "New",
               "order": 10
           }
       ]
   }
   ```

2. **Define the Route** in `plugins/my-feature/routes/admin.php`:
   ```php
   <?php

   use Illuminate\Support\Facades\Route;

   Route::get('/my-feature', function () {
       return view('my-feature::admin.index');
   })->name('my-feature.index');
   ```

3. **Create the View** in `plugins/my-feature/views/admin/index.blade.php`:
   ```blade
   @extends('admin.layout')

   @section('content')
   <div class="py-6">
       <h1 class="text-2xl font-bold text-text-heading">My Feature Dashboard</h1>
       <div class="mt-4 bg-white p-6 rounded-xl border border-content-border">
           Your custom admin page content here.
       </div>
   </div>
   @endsection
   ```

---

### B. Defining Models & Business Logic

Classes inside `plugins/{slug}/src/` are autoloaded under `Plugins\{StudlySlug}\`:

```php
// plugins/blog-comments/src/Models/BlogComment.php
namespace Plugins\BlogComments\Models;

use App\Models\CollectionEntry;
use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(CollectionEntry::class, 'post_id');
    }
}
```

---

### C. Creating Co-Located Custom Blocks

Each block lives in its own folder:

```
plugins/my-plugin/Blocks/HeroSection/
├── HeroSection.php
└── view.blade.php
```

**`HeroSection.php`:**
```php
namespace Plugins\MyPlugin\Blocks\HeroSection;

use App\Blocks\Block;
use App\Blocks\Field;

class HeroSection extends Block
{
    public string $name = 'heroSection';
    public string $label = 'Hero Section';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Welcome to our site'),
            Field::text('subheadline', 'Subheadline'),
            Field::image('image', 'Hero Image'),
        ];
    }
}
```

**`view.blade.php`:**
```blade
<section class="py-16 text-center max-w-5xl mx-auto">
    <h1 class="text-4xl font-extrabold">{{ $data['headline'] ?? '' }}</h1>
    <p class="text-lg text-gray-600 mt-2">{{ $data['subheadline'] ?? '' }}</p>
    @if(!empty($data['image']))
        <img src="{{ $data['image'] }}" class="mt-6 rounded-xl shadow-lg mx-auto max-h-96" />
    @endif
</section>
```

---

## 5. Upgrading Lara-CMS Core

When a new version of Lara-CMS is released:
```bash
git pull upstream main
php artisan migrate
```
Your `plugins/` directory remains 100% untouched and conflict-free.
