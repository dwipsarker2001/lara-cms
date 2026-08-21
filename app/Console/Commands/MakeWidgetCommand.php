<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeWidgetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:widget
                            {name : The class name of the widget (e.g. RevenueWidget)}
                            {--zone=grid : Dashboard zone — grid, chart, table, or list}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new core dashboard widget in app/Widgets/';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $rawName = trim((string) $this->argument('name'));
        $className = Str::studly($rawName);
        $zone = (string) $this->option('zone');
        $validZones = ['grid', 'chart', 'table', 'list'];

        if (! in_array($zone, $validZones, true)) {
            $this->error("Invalid zone [{$zone}]. Valid zones: ".implode(', ', $validZones));

            return self::FAILURE;
        }

        $widgetFile = app_path("Widgets/{$className}.php");

        if (File::exists($widgetFile)) {
            $this->error("Widget [{$className}] already exists at [app/Widgets/{$className}.php]!");

            return self::FAILURE;
        }

        $type = Str::snake($className);
        $label = Str::title(str_replace('_', ' ', $type));
        $viewSlug = Str::kebab($className);
        $viewName = "admin.widgets.{$viewSlug}";
        $viewPath = resource_path("views/admin/widgets/{$viewSlug}.blade.php");

        $phpStub = <<<PHP
<?php

namespace App\\Widgets;

class {$className} extends Widget
{
    public static function zone(): string
    {
        return '{$zone}';
    }

    public function label(): string
    {
        return '{$label}';
    }

    public function render()
    {
        // TODO: fetch your widget data here
        \$data = [];

        return view('{$viewName}', compact('data'));
    }
}
PHP;

        File::put($widgetFile, $phpStub);

        File::ensureDirectoryExists(dirname($viewPath), 0755);

        $bladeStub = <<<BLADE
<div class="text-sm text-gray-700">
    <p class="font-semibold">{$label}</p>
    {{-- TODO: render your widget content here --}}
    <p class="text-text-muted mt-1">No data yet.</p>
</div>
BLADE;

        File::put($viewPath, $bladeStub);

        $this->info("✓ Widget [{$className}] created successfully!");
        $this->line("  - Class: <comment>app/Widgets/{$className}.php</comment>");
        $this->line("  - View:  <comment>resources/views/admin/widgets/{$viewSlug}.blade.php</comment>");
        $this->line("  - Zone:  <comment>{$zone}</comment>");
        $this->line("  - Type:  <comment>{$type}</comment>  (auto-derived)");
        $this->line('Widget is auto-discovered — no registration needed!');

        return self::SUCCESS;
    }
}
