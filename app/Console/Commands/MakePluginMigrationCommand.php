<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePluginMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:plugin-migration {plugin : The slug of the plugin (e.g. blog-comments)} {name : The name of the migration (e.g. create_blog_comments_table)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration inside a plugin directory';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $plugin = Str::slug((string) $this->argument('plugin'));
        $name = Str::snake(trim((string) $this->argument('name')));

        $pluginPath = base_path('plugins/'.$plugin);

        if (! File::isDirectory($pluginPath)) {
            $this->error("Plugin [{$plugin}] does not exist at [plugins/{$plugin}]!");

            return self::FAILURE;
        }

        $migrationsDir = $pluginPath.'/database/migrations';
        if (! File::isDirectory($migrationsDir)) {
            File::makeDirectory($migrationsDir, 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_{$name}.php";
        $filePath = "{$migrationsDir}/{$fileName}";

        // Guess table name if create_xxx_table format
        $tableName = 'table_name';
        if (preg_match('/^create_(.+)_table$/', $name, $matches)) {
            $tableName = $matches[1];
        }

        $stub = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;

        File::put($filePath, $stub);

        $this->info("✓ Created migration: [plugins/{$plugin}/database/migrations/{$fileName}]");
        $this->line('Run <comment>php artisan migrate</comment> to apply it.');

        return self::SUCCESS;
    }
}
