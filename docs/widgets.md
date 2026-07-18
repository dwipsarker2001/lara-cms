# Dashboard Widgets

Widgets are cards on `/admin`. Create a folder with your widget files in `app/Widgets/` — auto-discovered, auto-works.

---

## How to Create a Widget

### Step 1 — Create the widget folder and files

```
app/Widgets/ActiveUsers/
├── ActiveUsersWidget.php     ← logic
├── active-users.blade.php    ← view (optional, can use inline HTML)
└── icon.svg                  ← shown in side panel when widget is hidden (optional)
```

### Step 2 — The logic file

`app/Widgets/ActiveUsers/ActiveUsersWidget.php`:

```php
<?php

namespace App\Widgets;

class ActiveUsersWidget extends Widget
{
    public ?string $image = '/widget-icons/active-users.svg';

    public function label(): string
    {
        return 'Active Users';
    }

    public function render()
    {
        return view('admin.widgets.active-users', ['widget' => (object) [
            'value' => number_format(rand(100, 500)),
            'delta' => '+12%',
            'up' => true,
        ]]);
    }
}
```

Reload `/admin`. Widget appears automatically.

---

## Side Panel Icon

When a widget is hidden, it appears in the side panel. Set `$image` to show an icon next to its name:

```php
public ?string $image = '/img/widgets/users.svg';
```

Place your SVG icon in `public/img/widgets/` (create the folder if needed). Can also be any URL.

If you don't set `$image`, no icon is shown.

---

## Stat Widgets (config-driven, one view shared)

Put values in config instead of hardcoding. One Blade view, many instances.

```php
class StatWidget extends Widget
{
    public static function type(): string { return 'stat'; }

    public function __construct(
        public string $label, public string $value,
        public string $delta, public bool $up, public array $data,
    ) {}

    public function render()
    {
        return view('admin.widgets.stat', ['widget' => $this]);
    }
}
```

Config:

```php
['type' => 'stat', 'label' => 'Tickets', 'value' => '3,484', 'delta' => '+7%', 'up' => true, 'data' => [12, 18, 10]],
['type' => 'stat', 'label' => 'Resolution', 'value' => '486', 'delta' => '+2%', 'up' => true, 'data' => [20, 16, 24]],
['type' => 'visitor'],
```

Default `make()` strips `type` from config and passes rest as constructor params.

---

## DB-Backed Widgets (no config params)

```php
class VisitorWidget extends Widget
{
    public static function type(): string { return 'visitor'; }

    public static function make(array $config): static { return new static; }

    public function render() { /* query DB, return view */ }
}
```

---

## How It Works

`WidgetRegistry` scans `app/Widgets/` for all `Widget` subclasses → builds `type → class` map → route resolves each config entry via registry → calls `$class::make($config)`.

No manual type mapping.

---

## Built-in

| Widget | Type | Source |
|--------|------|--------|
| `StatWidget` | `stat` | Config array |
| `VisitorWidget` | `visitor` | `page_views` table |

Grid: `grid-cols-1 sm:grid-cols-3`. Drag to reorder, hide/restore from side panel.
