# Layouts Collection Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create a Layouts collection — reusable presets of sections (blocks) that can be selected when creating pages or blog posts.

**Architecture:** Layout model with `sections` JSON (same format as pages/posts). Admin CRUD + block editor (reuses existing `admin.pages.editor`). Page/Post create forms get a layout dropdown filtered by `collection` type.

**Tech Stack:** Laravel, Blade, Alpine.js, MySQL

---

### Task 1: Migration + Model

**Files:**
- Create: `database/migrations/2026_07_15_000001_create_layouts_table.php`
- Create: `app/Models/Layout.php`

- [ ] **Step 1: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('collection'); // 'page', 'blog', 'package'
            $table->json('sections')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layouts');
    }
};
```

- [ ] **Step 2: Create the Layout model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'sections' => 'array',
    ];

    public function getTitleAttribute(): string
    {
        return $this->name;
    }
}
```

- [ ] **Step 3: Run the migration**

```bash
php artisan migrate
```

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_07_15_000001_create_layouts_table.php app/Models/Layout.php
git commit -m "feat: add layouts table and model"
```

---

### Task 2: Factory + Seeder

**Files:**
- Create: `database/factories/LayoutFactory.php`
- Create: `database/seeders/LayoutSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Create LayoutFactory**

```php
<?php

namespace Database\Factories;

use App\Models\Layout;
use Illuminate\Database\Eloquent\Factories\Factory;

class LayoutFactory extends Factory
{
    protected $model = Layout::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'collection' => fake()->randomElement(['page', 'blog']),
            'sections' => [],
            'position' => 0,
        ];
    }
}
```

- [ ] **Step 2: Create LayoutSeeder**

```php
<?php

namespace Database\Seeders;

use App\Models\Layout;
use Illuminate\Database\Seeder;

class LayoutSeeder extends Seeder
{
    public function run(): void
    {
        Layout::create([
            'name' => 'Default Page',
            'collection' => 'page',
            'sections' => [],
            'position' => 1,
        ]);

        Layout::create([
            'name' => 'Default Blog',
            'collection' => 'blog',
            'sections' => [],
            'position' => 2,
        ]);
    }
}
```

- [ ] **Step 3: Update DatabaseSeeder**

```php
$this->call([
    HomePageSeeder::class,
    LayoutSeeder::class,
]);
```

- [ ] **Step 4: Run seeder**

```bash
php artisan db:seed --class=LayoutSeeder
```

- [ ] **Step 5: Commit**

```bash
git add database/factories/LayoutFactory.php database/seeders/LayoutSeeder.php database/seeders/DatabaseSeeder.php
git commit -m "feat: add layout factory and seeder"
```

---

### Task 3: Admin Routes + LayoutController

**Files:**
- Create: `app/Http/Controllers/Admin/LayoutController.php`
- Modify: `routes/admin.php`

- [ ] **Step 1: Create LayoutController**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Blocks\BlockRegistry;
use App\Http\Controllers\Controller;
use App\Models\Layout;
use App\Models\Page;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    public function index()
    {
        $layouts = Layout::orderBy('position')->orderBy('name')->get();

        return view('admin.layouts.index', ['layouts' => $layouts]);
    }

    public function create()
    {
        return view('admin.layouts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'collection' => 'required|string|in:page,blog,package',
        ]);

        $data['position'] = Layout::max('position') + 1;

        Layout::create($data);

        return redirect()->route('admin.layouts.index')->with('success', 'Layout created.');
    }

    public function edit(Layout $layout)
    {
        return view('admin.layouts.edit', ['layout' => $layout]);
    }

    public function update(Request $request, Layout $layout)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'collection' => 'required|string|in:page,blog,package',
        ]);

        $layout->update($data);

        return redirect()->route('admin.layouts.index')->with('success', 'Layout updated.');
    }

    public function destroy(Layout $layout)
    {
        $layout->delete();

        return redirect()->route('admin.layouts.index')->with('success', 'Layout deleted.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'layout_ids' => 'required|array',
            'layout_ids.*' => 'exists:layouts,id',
        ]);

        foreach ($request->layout_ids as $index => $id) {
            Layout::where('id', $id)->update(['position' => $index]);
        }

        return response()->json(['message' => 'Reordered.']);
    }

    public function editor(Layout $layout)
    {
        $registry = app(BlockRegistry::class);

        $blockList = collect($registry->pickerList())->map(function ($item) use ($registry) {
            $block = $registry->get($item['name']);
            $section = \App\Support\Sections::createDefaultSection($item['name']);
            $html = '';
            if ($block && $section && view()->exists($block->view())) {
                $html = view($block->view(), [
                    'data' => $section['data'],
                    '_key' => '',
                    'preview' => true,
                ])->render();
            }

            return [...$item, 'previewHtml' => $html];
        })->all();

        return view('admin.pages.editor', [
            'page' => $layout,
            'blockSchemas' => $registry->schemas(),
            'blockList' => $blockList,
            'pages' => Page::orderBy('position')->orderBy('title')->get(['id', 'slug', 'title'])->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'route' => $p->slug === 'home' ? '/' : '/'.$p->slug,
            ]),
            'homeGlobals' => [],
        ]);
    }

    public function updateSections(Request $request, Layout $layout)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*._key' => 'required|string',
            'sections.*.name' => 'required|string',
            'sections.*.data' => 'required',
        ]);

        $layout->update(['sections' => $request->sections]);

        return response()->json(['message' => 'Sections saved.']);
    }
}
```

- [ ] **Step 2: Add routes to admin.php**

Insert before the `Route::post('preview'` line (around line 35):

```php
Route::patch('layouts/reorder', [LayoutController::class, 'reorder'])->name('layouts.reorder');
Route::resource('layouts', LayoutController::class)->except(['show']);
Route::patch('layouts/{layout}/sections', [LayoutController::class, 'updateSections'])->name('layouts.update-sections');
Route::get('layouts/{layout}/editor', [LayoutController::class, 'editor'])->name('layouts.editor');
```

Add the import at the top of `routes/admin.php`:
```php
use App\Http\Controllers\Admin\LayoutController;
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Admin/LayoutController.php routes/admin.php
git commit -m "feat: add layout controller and routes"
```

---

### Task 4: Admin Views (index, create, edit)

**Files:**
- Create: `resources/views/admin/layouts/index.blade.php`
- Create: `resources/views/admin/layouts/create.blade.php`
- Create: `resources/views/admin/layouts/edit.blade.php`

- [ ] **Step 1: Create `admin/layouts/index.blade.php`**

```blade
@extends('admin.layout')

@section('title', 'Layouts')
@section('breadcrumb', 'Layouts')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="9" y1="21" x2="9" y2="9" />
                </svg>
                Layouts
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.layouts.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Create Layout
                </a>
            </div>
        </header>

        <x-admin::sortable-list
            title="All Layouts"
            :items="$layouts"
            sortable-id="sortable-layouts"
            data-key="layoutId"
            reorder-route="admin.layouts.reorder"
            edit-route="admin.layouts.editor"
            delete-route="admin.layouts.destroy"
            empty-text="No layouts yet."
            empty-link-text="Create your first layout"
            empty-link-route="admin.layouts.create"
        />
    </div>
@endsection

@push('scripts')
    @if(count($layouts) > 1)
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('sortable-layouts');
                if (!el || typeof Sortable === 'undefined') return;
                Sortable.create(el, {
                    handle: '[data-drag-handle]',
                    animation: 200,
                    ghostClass: 'opacity-40',
                    dragClass: '!bg-white !shadow-lg !rounded-xl',
                    onEnd() {
                        const ids = Array.from(el.querySelectorAll('[data-layout-id]')).map(el => el.dataset.layoutId);
                        fetch('{{ route('admin.layouts.reorder') }}', {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ layout_ids: ids }),
                        }).catch(() => {});
                    },
                });
            });
        </script>
    @endif
@endpush
```

- [ ] **Step 2: Create `admin/layouts/create.blade.php`**

```blade
@extends('admin.layout')

@section('title', 'Create Layout')
@section('breadcrumb', 'Create Layout')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <form method="POST" action="{{ route('admin.layouts.store') }}">
            @csrf

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <line x1="3" y1="9" x2="21" y2="9" />
                        <line x1="9" y1="21" x2="9" y2="9" />
                    </svg>
                    Create Layout
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        <span>Create Layout</span>
                    </button>
                </div>
            </header>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Layout Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Set the name and content type for this layout.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-name" class="text-sm font-medium text-text-heading">Name</label>
                                    <div class="text-sm text-text-muted">A descriptive name for this layout.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-name"
                                            type="text"
                                            name="name"
                                            value="{{ old('name') }}"
                                            placeholder="e.g. Default Page"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-collection" class="text-sm font-medium text-text-heading">Collection</label>
                                    <div class="text-sm text-text-muted">Which content type this layout applies to.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <select
                                            id="field-collection"
                                            name="collection"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                            <option value="page" @selected(old('collection') === 'page')>Page</option>
                                            <option value="blog" @selected(old('collection') === 'blog')>Blog</option>
                                            <option value="package" @selected(old('collection') === 'package')>Package</option>
                                        </select>
                                        @error('for') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
```

- [ ] **Step 3: Create `admin/layouts/edit.blade.php`**

```blade
@extends('admin.layout')

@section('title', 'Edit Layout')
@section('breadcrumb', 'Edit Layout')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <form method="POST" action="{{ route('admin.layouts.update', $layout) }}">
            @csrf @method('PATCH')

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <line x1="3" y1="9" x2="21" y2="9" />
                        <line x1="9" y1="21" x2="9" y2="9" />
                    </svg>
                    Edit Layout
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <a href="{{ route('admin.layouts.index') }}"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-gradient-to-b from-content-bg to-gray-50 hover:to-gray-100 text-text-primary border border-content-border shadow-sm"
                    >
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        <span>Update Layout</span>
                    </button>
                </div>
            </header>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Layout Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Update the name and content type for this layout.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-name" class="text-sm font-medium text-text-heading">Name</label>
                                    <div class="text-sm text-text-muted">A descriptive name for this layout.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <input
                                            id="field-name"
                                            type="text"
                                            name="name"
                                            value="{{ old('name', $layout->name) }}"
                                            placeholder="e.g. Default Page"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                        @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="field-collection" class="text-sm font-medium text-text-heading">Collection</label>
                                    <div class="text-sm text-text-muted">Which content type this layout applies to.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <select
                                            id="field-collection"
                                            name="collection"
                                            class="w-full block bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                            <option value="page" @selected(old('for', $layout->collection) === 'page')>Page</option>
                                            <option value="blog" @selected(old('for', $layout->collection) === 'blog')>Blog</option>
                                            <option value="package" @selected(old('for', $layout->collection) === 'package')>Package</option>
                                        </select>
                                        @error('for') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="mt-12 border-t border-content-border pt-8 px-2 sm:px-0">
            <div class="bg-panel-bg rounded-2xl p-[7px]">
                <div class="px-[18px] py-3 text-sm font-medium text-text-heading">Delete Layout</div>
                <div class="px-1.5 pb-2 max-w-2xl">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-4">
                        <p class="text-sm text-text-muted mb-4">Permanently delete this layout. This action cannot be undone.</p>
                        <form method="POST" action="{{ route('admin.layouts.destroy', $layout) }}" onsubmit="return confirm('Delete this layout permanently?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-red-600 hover:bg-red-700 text-white shadow-sm"
                            >
                                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                </svg>
                                Delete Layout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/admin/layouts/
git commit -m "feat: add layout admin views (index, create, edit)"
```

---

### Task 5: Admin Nav Link

**Files:**
- Modify: `resources/views/admin/layout.blade.php`

- [ ] **Step 1: Add Layouts link to sidebar nav**

Insert after the Blog nav item (after line 220):

```blade
<li>
    <a href="{{ route('admin.layouts.index') }}"
        class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.layouts.*')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
    >
        <span class="flex w-4 shrink-0 items-center justify-center">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                <line x1="3" y1="9" x2="21" y2="9" />
                <line x1="9" y1="21" x2="9" y2="9" />
            </svg>
        </span>
        Layouts
    </a>
</li>
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/admin/layout.blade.php
git commit -m "feat: add layouts link to admin nav"
```

---

### Task 6: Integration with Page/Post Create Forms

**Files:**
- Modify: `app/Http/Controllers/Admin/PageController.php`
- Modify: `app/Http/Controllers/Admin/PostController.php`
- Modify: `resources/views/admin/pages/create.blade.php`
- Modify: `resources/views/admin/posts/create.blade.php`

- [ ] **Step 1: Update PageController@store to accept layout_id**

Add import at top:
```php
use App\Models\Layout;
```

Change the store method's section handling (around line 47):
```php
if ($request->layout_id) {
    $layout = Layout::findOrFail($request->layout_id);
    $data['sections'] = [...($layout->sections ?? []), ...Sections::injectGlobals()];
} else {
    $data['sections'] = Sections::injectGlobals();
}
```

Add layout_id to validation rules:
```php
'layout_id' => 'nullable|exists:layouts,id',
```

- [ ] **Step 2: Update PostController@store to accept layout_id**

Add import at top:
```php
use App\Models\Layout;
```

After `$data['position'] = Post::max('position') + 1;`:
```php
if ($request->layout_id) {
    $layout = Layout::findOrFail($request->layout_id);
    $data['sections'] = $layout->sections ?? [];
}
```

Add layout_id to validation rules:
```php
'layout_id' => 'nullable|exists:layouts,id',
```

- [ ] **Step 3: Add layout selector to `admin/pages/create.blade.php`**

After `@csrf`, add:
```blade
@php $pageLayouts = \App\Models\Layout::where('collection', 'page')->orderBy('position')->orderBy('name')->get(); @endphp
@if($pageLayouts->isNotEmpty())
    <input type="hidden" name="layout_id" :value="layoutId">
    <div class="bg-panel-bg rounded-2xl mb-6 p-[7px]" x-data="{ layoutId: '{{ old('layout_id') }}' }">
        <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Layout</div>
        <p class="px-[18px] pb-3 text-sm text-text-muted">Choose a layout to pre-populate sections.</p>
        <div class="px-1.5 pb-2">
            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-text-heading">Start from layout</label>
                        <div class="text-sm text-text-muted">Optionally pre-fill this page with sections from a layout.</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                            <select x-model="layoutId" class="w-full block bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="">None (empty page)</option>
                                @foreach($pageLayouts as $pl)
                                    <option value="{{ $pl->id }}">{{ $pl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
```

- [ ] **Step 4: Add layout selector to `admin/posts/create.blade.php`**

After `@csrf`, add:
```blade
@php $blogLayouts = \App\Models\Layout::where('collection', 'blog')->orderBy('position')->orderBy('name')->get(); @endphp
@if($blogLayouts->isNotEmpty())
    <input type="hidden" name="layout_id" :value="layoutId">
    <div class="bg-panel-bg rounded-2xl mb-6 p-[7px]" x-data="{ layoutId: '{{ old('layout_id') }}' }">
        <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Layout</div>
        <p class="px-[18px] pb-3 text-sm text-text-muted">Choose a layout to pre-populate sections.</p>
        <div class="px-1.5 pb-2">
            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-medium text-text-heading">Start from layout</label>
                        <div class="text-sm text-text-muted">Optionally pre-fill this post with sections from a layout.</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                            <select x-model="layoutId" class="w-full block bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="">None</option>
                                @foreach($blogLayouts as $bl)
                                    <option value="{{ $bl->id }}">{{ $bl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
```

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Admin/PageController.php app/Http/Controllers/Admin/PostController.php resources/views/admin/pages/create.blade.php resources/views/admin/posts/create.blade.php
git commit -m "feat: integrate layout selection into page and post creation"
```

---

### Task 7: Tests

**Files:**
- Create: `tests/Feature/LayoutTest.php`

- [ ] **Step 1: Create LayoutTest**

```php
<?php

use App\Models\Layout;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\patch;
use function Pest\Laravel\delete;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('lists layouts', function () {
    $layouts = Layout::factory()->count(3)->create();

    get(route('admin.layouts.index'))
        ->assertSuccessful()
        ->assertSee($layouts[0]->name);
});

it('creates a layout', function () {
    post(route('admin.layouts.store'), [
        'name' => 'My Layout',
        'collection' => 'page',
    ])->assertRedirect(route('admin.layouts.index'));

    expect(Layout::where('name', 'My Layout')->exists())->toBeTrue();
});

it('validates layout creation', function () {
    post(route('admin.layouts.store'), [
        'name' => '',
        'collection' => '',
    ])->assertSessionHasErrors(['name', 'collection']);
});

it('updates a layout', function () {
    $layout = Layout::factory()->create();

    patch(route('admin.layouts.update', $layout), [
        'name' => 'Updated Name',
        'collection' => 'blog',
    ])->assertRedirect(route('admin.layouts.index'));

    expect($layout->fresh()->name)->toBe('Updated Name');
    expect($layout->fresh()->collection)->toBe('blog');
});

it('deletes a layout', function () {
    $layout = Layout::factory()->create();

    delete(route('admin.layouts.destroy', $layout))
        ->assertRedirect(route('admin.layouts.index'));

    expect(Layout::find($layout->id))->toBeNull();
});

it('loads the layout editor', function () {
    $layout = Layout::factory()->create();

    get(route('admin.layouts.editor', $layout))
        ->assertSuccessful()
        ->assertSee('Sections');
});

it('updates layout sections', function () {
    $layout = Layout::factory()->create();

    $sections = [
        ['_key' => 'key-1', 'name' => 'PageBanner', 'data' => ['title' => 'Hello'], 'enabled' => true],
    ];

    patch(route('admin.layouts.update-sections', $layout), [
        'sections' => $sections,
    ])->assertSuccessful()->assertJson(['message' => 'Sections saved.']);

    expect($layout->fresh()->sections)->toBe($sections);
});
```

- [ ] **Step 2: Run tests**

```bash
php artisan test --filter=LayoutTest --compact
```

Expected: all tests PASS

- [ ] **Step 3: Run Pint to fix formatting**

```bash
vendor/bin/pint --dirty
```

- [ ] **Step 4: Run all existing tests to make sure nothing broke**

```bash
php artisan test --compact
```

Expected: all tests PASS

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/LayoutTest.php
git commit -m "test: add layout tests"
```
