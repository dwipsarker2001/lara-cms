---
name: widget-development
description: Complete guide for creating, styling, and testing admin dashboard widgets in Lara-CMS — both core (app/Widgets/) and plugin-based (plugins/*/Widgets/).
---

# 🧩 Widget Development Skill

Activate this skill whenever you are:
- Creating a new admin dashboard widget
- Editing or debugging an existing widget
- Building a plugin that contributes widgets to the dashboard
- Updating the widget picker panel or `WidgetRegistry`

---

## Critical Rules (Read First)

1. **Never touch `dashboard.blade.php` to add a widget** — widgets are auto-discovered.
2. **Never manually register a widget** — `WidgetRegistry` auto-discovers from `app/Widgets/` and `plugins/*/Widgets/`.
3. **Always use an Artisan command to scaffold** — never create widget files by hand.
4. **Always run `vendor/bin/pint --dirty --format agent`** after editing any PHP.
5. **Always write a test** — see `tests/Feature/PluginWidgetTest.php` as reference.

---

## Scaffolding Commands

```bash
# Core widget (inside CMS)
php artisan make:widget {ClassName} --zone={zone}

# Plugin widget (recommended — keeps core clean)
php artisan make:plugin-widget {plugin-slug} {ClassName} --zone={zone}
```

Valid `--zone` values: `grid` | `chart` | `table` | `list`

---

## Widget Class Contract

```php
namespace App\Widgets; // or Plugins\{StudlySlug}\Widgets;

class MyWidget extends Widget
{
    // REQUIRED: one of grid|chart|table|list
    public static function zone(): string { return 'grid'; }

    // REQUIRED: human label shown in picker + widget header
    public function label(): string { return 'My Widget'; }

    // REQUIRED: return view() or HTML string
    public function render() { return view('...', []); }

    // OPTIONAL: override to accept config params (e.g. formId, collectionSlug)
    public static function make(array $config): static { return new static; }

    // OPTIONAL: icon shown in widget header (URL or null)
    public ?string $image = null;
}
```

### Type Auto-Derivation
`type()` is derived automatically: `RevenueWidget` → `revenue_widget`. Override only if needed.

---

## View Namespace Convention

| Widget Location | View Call |
|---|---|
| `app/Widgets/` | `view('admin.widgets.{kebab-name}', [...])` |
| `plugins/{slug}/Widgets/` | `view('{slug}::widgets.{kebab-name}', [...])` |

Plugin view templates live at: `plugins/{slug}/views/widgets/{kebab-name}.blade.php`

---

## Zone-Specific Patterns

### grid — Stat card with sparkline
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

### chart — ApexCharts bar/area
```blade
<div x-data="{ init() { new ApexCharts(this.$refs.el, options).render(); } }">
    <div x-ref="el" class="w-full min-h-[200px]"></div>
</div>
@once<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>@endonce
```

### table — Ring-bordered table
```blade
<div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-[5px]">
    <table class="w-full border-separate border-spacing-y-0 text-left text-[13px]">
        <thead><tr class="bg-[#f9fafb]">...</tr><tr class="h-2"><td colspan="N"></td></tr></thead>
        <tbody>
            @foreach($rows as $i => $row)
            <tr class="group hover:bg-gray-50/50">
                <td class="border-b border-gray-100 bg-white px-4 py-3 {{ $i===0?'rounded-tl-xl':'' }}">...</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

### list — Feed with ring card
```blade
<div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm py-4 flex flex-col min-h-0 flex-1">
    <ul class="flex-1 divide-y divide-gray-100">
        @foreach($items as $item)
        <li class="flex items-start gap-3 py-3 px-4 hover:bg-gray-50/90 transition-colors">
            ...
        </li>
        @endforeach
    </ul>
</div>
```

---

## CSS Design Tokens (Use These)

| Token | Usage |
|---|---|
| `text-text-heading` | Primary bold text, titles, values |
| `text-text-muted` | Secondary/supporting text |
| `text-text-primary` | Default body text |
| `text-emerald-600` | Positive delta / success |
| `text-red-500` | Negative delta / error |
| `text-amber-600` / `bg-amber-50` | Warning state |
| `border-content-border` | Standard border colour |
| `ring-gray-200` | Widget card ring |
| `shadow-sm` | Widget card shadow |

---

## Reference Files

| Purpose | File |
|---|---|
| Widget base class | `app/Widgets/Widget.php` |
| Discovery engine | `app/Widgets/WidgetRegistry.php` |
| Dashboard controller | `app/Http/Controllers/Admin/DashboardController.php` |
| Widget render/layout API | `app/Http/Controllers/Admin/WidgetController.php` |
| Plugin scaffold | `app/Console/Commands/MakePluginWidgetCommand.php` |
| Core scaffold | `app/Console/Commands/MakeWidgetCommand.php` |
| Full docs | `docs/widgets.md` |
| Demo implementations | `plugins/demo-widgets/Widgets/` |
| Test patterns | `tests/Feature/PluginWidgetTest.php` |

---

## Step-by-Step Checklist

- [ ] Run `php artisan make:plugin-widget {plugin} {Name} --zone={zone}`
- [ ] Implement `zone()`, `label()`, `render()` in the class
- [ ] Populate the Blade template with real data, matching zone styling
- [ ] If config-driven: override `make(array $config)` and `__construct(...)`
- [ ] Run `vendor/bin/pint --dirty --format agent`
- [ ] Write test in `tests/Feature/` verifying discovery + render
- [ ] Run `php artisan test --compact --filter=PluginWidget`
