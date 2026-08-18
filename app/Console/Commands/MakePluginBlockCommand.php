<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePluginBlockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:plugin-block {plugin : The slug of the plugin (e.g. site-blocks or blog-comments)} {name : The name of the block (e.g. HeroSection or PricingTable)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new co-located block (PHP + Blade in one folder) inside a plugin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $plugin = Str::slug((string) $this->argument('plugin'));
        $rawName = trim((string) $this->argument('name'));
        $className = Str::studly($rawName);
        $blockName = Str::snake($rawName);
        $blockLabel = Str::title(str_replace(['-', '_'], ' ', Str::snake($rawName)));
        $studlyPlugin = Str::studly($plugin);

        $pluginPath = base_path('plugins/'.$plugin);

        if (! File::isDirectory($pluginPath)) {
            $this->error("Plugin [{$plugin}] does not exist at [plugins/{$plugin}]!");

            return self::FAILURE;
        }

        $blockDir = "{$pluginPath}/Blocks/{$className}";

        if (File::isDirectory($blockDir)) {
            $this->error("Block [{$className}] already exists at [plugins/{$plugin}/Blocks/{$className}]!");

            return self::FAILURE;
        }

        File::makeDirectory($blockDir, 0755, true);

        // 1. Block PHP class
        $phpStub = <<<PHP
<?php

namespace Plugins\\{$studlyPlugin}\\Blocks\\{$className};

use App\Blocks\Block;
use App\Blocks\Field;

class {$className} extends Block
{
    /** Machine name referenced in page sections */
    public string \$name = '{$blockName}';

    /** Human label in block picker */
    public string \$label = '{$blockLabel}';

    /**
     * Define the editable fields for this block.
     *
     * @return array<int, array>
     */
    public function fields(): array
    {
        return [
            Field::make('title')->text()->label('Title')->default('{$blockLabel} Title'),
            Field::make('subtitle')->text()->label('Subtitle')->default('A brief subtitle description'),
        ];
    }
}
PHP;
        File::put("{$blockDir}/{$className}.php", $phpStub);

        // 2. Co-located Blade template
        $bladeStub = <<<BLADE
<section class="py-12 px-4 max-w-6xl mx-auto">
    <div class="text-center">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
            {{ \$data['title'] ?? '{$blockLabel}' }}
        </h2>
        @if(!empty(\$data['subtitle']))
            <p class="mt-4 text-lg text-gray-600">
                {{ \$data['subtitle'] }}
            </p>
        @endif
    </div>
</section>
BLADE;
        File::put("{$blockDir}/view.blade.php", $bladeStub);

        $this->info("✓ Co-located Block [{$className}] created successfully!");
        $this->line("  - Logic: <comment>plugins/{$plugin}/Blocks/{$className}/{$className}.php</comment>");
        $this->line("  - View:  <comment>plugins/{$plugin}/Blocks/{$className}/view.blade.php</comment>");
        $this->line('Block is auto-discovered and ready in the CMS editor!');

        return self::SUCCESS;
    }
}
