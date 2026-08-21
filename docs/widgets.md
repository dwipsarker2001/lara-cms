# Dashboard Widget System

Widgets are the cards, charts, tables, and activity feeds that appear on `/admin`. The system is **auto-discovery-based** — drop a PHP class in the right place and it appears in the dashboard widget picker immediately, with no registration required.

---

## Architecture at a Glance

```
WidgetRegistry (singleton)
├── Scans app/Widgets/          → core CMS widgets
└── Scans plugins/*/Widgets/    → plugin widgets (auto-discovered)
         │
         ▼
DashboardController builds zone data → dashboard.blade.php
         │
         ▼
Widget Picker Panel (Alpine.js) shows all discovered widgets per zone
```

---

## The 4 Dashboard Zones

| Zone | Location on Dashboard | Suitable For |
|---|---|---|
| **`grid`** | Top metric cards row | KPI numbers, sparklines, stat counters |
| **`chart`** | Main visual area | Line/bar/area charts (ApexCharts) |
| **`table`** | Data table section | Paginated rows, searchable tables |
| **`list`** | Activity feed column | Notifications, logs, ordered lists |

Each widget must declare which zone it belongs to via `zone()`.

---

## Widget Base Class Contract

Every widget extends `App\Widgets\Widget`:

```php
abstract class Widget
{
    public ?string $image = null;          // optional icon URL shown in picker

    abstract public function label(): string;   // display name in UI
    abstract public function render();          // returns a view or string
    abstract public static function zone(): string;  // 'grid'|'chart'|'table'|'list'

    public static function type(): string
    {
        // auto-derived: RevenueWidget → revenue_widget
        return Str::snake(class_basename(static::class));
    }

    public static function make(array $config): static
    {
        return new static;   // override to accept config params
    }
}
```

---

## Option A — Core Widget (in `app/Widgets/`)

Use this for widgets that are part of the CMS itself.

### 1. Scaffold with Artisan

```bash
php artisan make:widget RevenueWidget --zone=grid
```

This creates:
- `app/Widgets/RevenueWidget.php` — the PHP class
- `resources/views/admin/widgets/revenue-widget.blade.php` — the Blade template

### 2. Edit the class

```php
<?php

namespace App\Widgets;

class RevenueWidget extends Widget
{
    public static function zone(): string
    {
        return 'grid';
    }

    public function label(): string
    {
        return 'Monthly Revenue';
    }

    public function render()
    {
        $total = 12450; // fetch from DB

        return view('admin.widgets.revenue-widget', compact('total'));
    }
}
```

### 3. Edit the Blade template

```blade
{{-- resources/views/admin/widgets/revenue-widget.blade.php --}}
<div class="flex items-end justify-between gap-2">
    <div>
        <div class="text-[26px] font-semibold leading-none text-text-heading">
            ${{ number_format($total) }}
        </div>
        <div class="mt-2 text-[12px] text-text-muted">Monthly Revenue</div>
    </div>
</div>
```

That's it. Reload `/admin` → open the widget picker → the widget appears under the correct zone tab.

---

## Option B — Plugin Widget (in `plugins/{slug}/Widgets/`)

Use this to keep widgets **outside the CMS core**, bundled inside a plugin. This is the recommended approach for client-specific or feature-specific widgets.

### 1. Create the plugin (if it doesn't exist)

```bash
php artisan make:plugin "My Plugin"
```

### 2. Scaffold the widget inside the plugin

```bash
php artisan make:plugin-widget my-plugin RevenueWidget --zone=grid
```

This creates:
- `plugins/my-plugin/Widgets/RevenueWidget.php`
- `plugins/my-plugin/views/widgets/revenue-widget.blade.php`

### 3. Edit the class

```php
<?php

namespace Plugins\MyPlugin\Widgets;

use App\Widgets\Widget;

class RevenueWidget extends Widget
{
    public static function zone(): string
    {
        return 'grid';
    }

    public function label(): string
    {
        return 'Monthly Revenue';
    }

    public function render()
    {
        $total = 12450; // fetch from DB

        return view('my-plugin::widgets.revenue-widget', compact('total'));
    }
}
```

> **View namespace:** Plugin views are namespaced as `{plugin-slug}::widgets.{view-name}`.  
> The `PluginLoader` registers `plugins/{slug}/views/` automatically.

### 4. Edit the Blade template

```blade
{{-- plugins/my-plugin/views/widgets/revenue-widget.blade.php --}}
<div class="flex items-end justify-between gap-2">
    <div>
        <div class="text-[26px] font-semibold leading-none text-text-heading">
            ${{ number_format($total) }}
        </div>
        <div class="mt-2 text-[12px] text-text-muted">Monthly Revenue</div>
    </div>
</div>
```

Widget is auto-discovered. No registration needed.

---

## Plugin Directory Layout (Widget-Ready)

```
plugins/
└── my-plugin/
    ├── plugin.json
    ├── Widgets/                        ← dashboard widgets (NEW)
    │   └── RevenueWidget.php
    ├── Blocks/                         ← page builder blocks
    ├── views/
    │   ├── admin/
    │   └── widgets/                    ← widget Blade templates (NEW)
    │       └── revenue-widget.blade.php
    ├── routes/
    ├── src/
    └── database/migrations/
```

---

## Config-Driven Widgets (Multiple Instances)

When a widget needs constructor params (e.g. a form ID filter), override `make()`:

```php
class FormStatWidget extends Widget
{
    public function __construct(
        public ?int $formId = null,
    ) {}

    public static function make(array $config): static
    {
        return new static(
            formId: isset($config['form_id']) ? (int) $config['form_id'] : null,
        );
    }

    public function label(): string
    {
        return $this->formId
            ? 'Form #'.$this->formId.' Stats'
            : 'Total Submissions';
    }

    public function render()
    {
        // use $this->formId to filter queries
        return view('admin.widgets.form-stat', [...]);
    }
}
```

---

## Widget View Styling Guide

Match the existing dashboard look by following these patterns:

### Grid Zone — Stat Card Pattern
```blade
<div class="flex items-end justify-between gap-2">
    <div>
        <div class="text-[26px] font-semibold leading-none text-text-heading">{{ $value }}</div>
        <div class="mt-2 flex items-center gap-1 text-[12px]">
            <span class="{{ $up ? 'font-medium text-emerald-600' : 'font-medium text-red-500' }}">{{ $delta }}</span>
            <span class="text-text-muted">vs yesterday</span>
        </div>
    </div>
</div>
```

### Table Zone — Table Pattern
```blade
<div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-[5px]">
    <div class="overflow-x-auto">
        <table class="w-full border-separate border-spacing-y-0 text-left text-[13px]">
            <thead>
                <tr class="bg-[#f9fafb]">
                    <th class="rounded-l-xl px-4 py-3 font-medium text-text-muted text-[12px]">Column</th>
                </tr>
                <tr class="h-2"><td colspan="1"></td></tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $row)
                    <tr class="group transition-colors hover:bg-gray-50/50">
                        <td class="border-b border-gray-100 bg-white px-4 py-3 ...">{{ $row['col'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
```

### List Zone — Feed Pattern
```blade
<div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm py-4 flex flex-col min-h-0 flex-1">
    <ul class="flex-1 divide-y divide-gray-100">
        @foreach ($items as $item)
            <li class="flex items-start gap-3 py-3 px-4 hover:bg-gray-50/90 transition-colors">
                <span class="text-[13px] font-semibold text-text-heading">{{ $item->title }}</span>
            </li>
        @endforeach
    </ul>
</div>
```

### Chart Zone — ApexCharts Pattern
```blade
<div x-data="{ chart: null, init() { /* ApexCharts init */ } }">
    <div x-ref="chartEl" class="w-full min-h-[200px]"></div>
</div>
@once
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endonce
```

---

## Type Naming Convention

The `type()` method is auto-derived from the class name using `Str::snake(class_basename())`:

| Class Name | Auto Type |
|---|---|
| `RevenueWidget` | `revenue_widget` |
| `ContentGrowthChartWidget` | `content_growth_chart_widget` |
| `SlaTableWidget` | `sla_table_widget` |

> **Types must be globally unique.** If two plugins define the same type string, the last one loaded wins.

---

## Artisan Commands Reference

| Command | What It Does |
|---|---|
| `php artisan make:widget {Name} --zone={zone}` | Create a core widget in `app/Widgets/` |
| `php artisan make:plugin-widget {plugin} {Name} --zone={zone}` | Create a widget inside a plugin |

**Valid zones:** `grid`, `chart`, `table`, `list`

---

## Demo Widgets (Reference Implementation)

The `demo-widgets` plugin ships 4 ready-made widgets covering every zone:

| Widget | Type | Zone | File |
|---|---|---|---|
| `QuickStatsWidget` | `quick_stats_widget` | grid | `plugins/demo-widgets/Widgets/QuickStatsWidget.php` |
| `ContentGrowthChartWidget` | `content_growth_chart_widget` | chart | `plugins/demo-widgets/Widgets/ContentGrowthChartWidget.php` |
| `RecentPagesTableWidget` | `recent_pages_table_widget` | table | `plugins/demo-widgets/Widgets/RecentPagesTableWidget.php` |
| `SystemHealthWidget` | `system_health_widget` | list | `plugins/demo-widgets/Widgets/SystemHealthWidget.php` |

Study these as working references before building your own.

---

## How Discovery Works (Internals)

`WidgetRegistry` is registered as a singleton. On first call to `all()`, it:

1. Scans `app/Widgets/*.php` — extracts class via PSR-4 path mapping
2. Scans `plugins/*/Widgets/*.php` — extracts class via PHP token parsing (`require_once` if not autoloaded)
3. Checks `isSubclassOf(Widget::class)` and `!isAbstract()`
4. Calls `$class::type()` → builds `['type_string' => FullClassName]` map
5. Caches result in `$this->widgets` for the request lifetime

`DashboardController` calls `$registry->all()`, groups by zone into `$allByZone`, and passes it to the Blade view. The Alpine.js dashboard reads `allByZone` to populate the picker panel dynamically.

---

## Testing Your Widget

```bash
# Run all widget tests
php artisan test --compact --filter=Widget

# Run specifically plugin widget tests
php artisan test --compact --filter=PluginWidget
```

Write a feature test using the existing pattern in `tests/Feature/PluginWidgetTest.php`.

---

## Checklist for Building a Widget

- [ ] Widget class extends `App\Widgets\Widget`
- [ ] `zone()` returns one of: `grid`, `chart`, `table`, `list`
- [ ] `label()` returns a human-readable name
- [ ] `render()` returns a `view()` call or HTML string
- [ ] Blade template uses correct CSS token classes (`text-text-heading`, `text-text-muted`, etc.)
- [ ] If plugin widget: view uses `{plugin-slug}::widgets.{name}` namespace
- [ ] Run `vendor/bin/pint --dirty --format agent` to format PHP
- [ ] Write or update a test in `tests/Feature/`
