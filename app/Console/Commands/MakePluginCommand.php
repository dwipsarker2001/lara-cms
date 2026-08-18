<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePluginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:plugin {name : The name of the plugin (e.g. blog-comments or "Blog Comments")}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new modular plugin scaffold for Lara-CMS';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $input = (string) $this->argument('name');
        $slug = Str::slug($input);
        $title = Str::title(str_replace(['-', '_'], ' ', $slug));
        $studly = Str::studly($slug);

        $pluginPath = base_path('plugins/'.$slug);

        if (File::isDirectory($pluginPath)) {
            $this->error("Plugin [{$slug}] already exists at [plugins/{$slug}]!");

            return self::FAILURE;
        }

        // 1. Create directory structure
        File::makeDirectory($pluginPath.'/routes', 0755, true);
        File::makeDirectory($pluginPath.'/src/Http/Controllers', 0755, true);
        File::makeDirectory($pluginPath.'/src/Models', 0755, true);
        File::makeDirectory($pluginPath.'/src/Support', 0755, true);
        File::makeDirectory($pluginPath.'/Blocks', 0755, true);
        File::makeDirectory($pluginPath.'/database/migrations', 0755, true);
        File::makeDirectory($pluginPath.'/views/admin', 0755, true);

        // 2. plugin.json manifest
        $manifest = [
            'name' => $title,
            'slug' => $slug,
            'version' => '1.0.0',
            'description' => "{$title} extension for Lara-CMS.",
            'author' => 'Developer',
            'admin_menu' => [
                [
                    'label' => $title,
                    'route' => "admin.{$slug}.index",
                    'icon' => 'fa-solid fa-puzzle-piece',
                    'badge' => null,
                    'order' => 10,
                ],
            ],
        ];
        File::put($pluginPath.'/plugin.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // 3. routes/admin.php
        $adminRoutes = <<<PHP
<?php

use Illuminate\Support\Facades\Route;

// Admin routes (auto-prefixed with /admin and protected by auth:admin middleware)
Route::get('/{$slug}', function () {
    return view('{$slug}::admin.index');
})->name('{$slug}.index');

PHP;
        File::put($pluginPath.'/routes/admin.php', $adminRoutes);

        // 4. routes.php (public)
        $publicRoutes = <<<PHP
<?php

use Illuminate\Support\Facades\Route;

// Public web routes for {$title}
Route::get('/{$slug}', function () {
    return view('{$slug}::index');
});

PHP;
        File::put($pluginPath.'/routes.php', $publicRoutes);

        // 5. views/admin/index.blade.php
        $adminView = <<<BLADE
@extends('admin.layout')

@section('content')
<div class="py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-text-heading">{$title}</h1>
            <p class="text-sm text-text-muted">Manage {$title} settings and data</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-content-border p-6 shadow-sm">
        <p class="text-text-primary">Welcome to {$title} module!</p>
    </div>
</div>
@endsection
BLADE;
        File::put($pluginPath.'/views/admin/index.blade.php', $adminView);

        // 6. views/index.blade.php
        $publicView = <<<BLADE
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
</head>
<body>
    <h1>Welcome to {$title}</h1>
</body>
</html>
BLADE;
        File::put($pluginPath.'/views/index.blade.php', $publicView);

        $this->info("✓ Plugin [{$title}] successfully created at [plugins/{$slug}]!");
        $this->line("  - Admin Route: <comment>/admin/{$slug}</comment> (Name: <comment>admin.{$slug}.index</comment>)");
        $this->line("  - Views Namespace: <comment>{$slug}::admin.index</comment>");
        $this->line("  - PHP Namespace: <comment>Plugins\\{$studly}\\...</comment>");
        $this->line("  - Migrations: <comment>plugins/{$slug}/database/migrations/</comment>");

        return self::SUCCESS;
    }
}
