<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePluginWidgetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:plugin-widget
                            {plugin : The slug of the plugin (e.g. my-plugin)}
                            {name : The class name of the widget (e.g. RevenueWidget)}
                            {--zone=grid : Dashboard zone — grid, chart, table, or list}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new dashboard widget inside a plugin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $plugin = Str::slug((string) $this->argument('plugin'));
        $rawName = trim((string) $this->argument('name'));
        $className = Str::studly($rawName);
        $zone = (string) $this->option('zone');
        $validZones = ['grid', 'chart', 'table', 'list'];

        if (! in_array($zone, $validZones, true)) {
            $this->error("Invalid zone [{$zone}]. Valid zones: ".implode(', ', $validZones));

            return self::FAILURE;
        }

        $pluginPath = base_path('plugins/'.$plugin);

        if (! File::isDirectory($pluginPath)) {
            $this->error("Plugin [{$plugin}] does not exist at [plugins/{$plugin}]!");

            return self::FAILURE;
        }

        $widgetsDir = $pluginPath.'/Widgets';
        $widgetFile = $widgetsDir.'/'.$className.'.php';

        if (File::exists($widgetFile)) {
            $this->error("Widget [{$className}] already exists at [plugins/{$plugin}/Widgets/{$className}.php]!");

            return self::FAILURE;
        }

        File::ensureDirectoryExists($widgetsDir, 0755);

        $studlyPlugin = Str::studly($plugin);
        $type = Str::snake($className);
        $label = Str::title(str_replace('_', ' ', $type));

        $viewsDir = $pluginPath.'/views/widgets';
        $viewSlug = Str::kebab($className);
        $viewPath = $viewsDir.'/'.$viewSlug.'.blade.php';
        $viewName = "{$plugin}::widgets.{$viewSlug}";

        $phpStub = <<<PHP
<?php

namespace Plugins\\{$studlyPlugin}\\Widgets;

use App\\Widgets\\Widget;

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

        File::ensureDirectoryExists($viewsDir, 0755);

        $bladeStub = <<<BLADE
<div class="text-sm text-gray-700">
    <p class="font-semibold">{$label}</p>
    {{-- TODO: render your widget content here --}}
    <p class="text-text-muted mt-1">No data yet.</p>
</div>
BLADE;

        File::put($viewPath, $bladeStub);

        $this->info("✓ Widget [{$className}] created successfully inside [{$plugin}]!");
        $this->line("  - Class: <comment>plugins/{$plugin}/Widgets/{$className}.php</comment>");
        $this->line("  - View:  <comment>plugins/{$plugin}/views/widgets/{$viewSlug}.blade.php</comment>");
        $this->line("  - Zone:  <comment>{$zone}</comment>");
        $this->line("  - Type:  <comment>{$type}</comment>  (auto-derived)");
        $this->line('Widget is auto-discovered — no registration needed!');

        return self::SUCCESS;
    }
}
