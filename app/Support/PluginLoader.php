<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * PluginLoader
 *
 * Auto-discovers plugins inside the /plugins directory at the project root.
 * Each plugin must have a plugin.json file at its root.
 * Optionally, a plugin can provide:
 *   - routes.php      → web routes loaded into the app
 *   - routes/api.php  → api routes loaded under /api prefix
 *   - views/          → blade views, namespaced as "plugin-slug::view-name"
 *   - src/            → PHP classes, autoloaded via PSR-4 if composer.json exists
 *
 * Plugin structure example:
 *   plugins/
 *   └── email-marketing/
 *       ├── plugin.json          ← required
 *       ├── routes.php           ← optional: web routes
 *       ├── views/               ← optional: blade views
 *       └── src/                 ← optional: PHP classes
 */
class PluginLoader
{
    /** @var array<string, array> */
    protected array $loaded = [];

    /**
     * Scan the plugins directory and load all valid plugins.
     */
    public function boot(): void
    {
        $pluginsPath = base_path('plugins');

        if (! is_dir($pluginsPath)) {
            return;
        }

        foreach (scandir($pluginsPath) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $pluginPath = $pluginsPath.DIRECTORY_SEPARATOR.$entry;

            if (! is_dir($pluginPath)) {
                continue;
            }

            $this->loadPlugin($pluginPath, $entry);
        }
    }

    /**
     * Load a single plugin from its directory.
     */
    protected function loadPlugin(string $pluginPath, string $slug): void
    {
        $manifestPath = $pluginPath.DIRECTORY_SEPARATOR.'plugin.json';

        if (! file_exists($manifestPath)) {
            return;
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (! is_array($manifest) || empty($manifest['name'])) {
            return;
        }

        // Register blade views namespace: "slug::view-name"
        $viewsPath = $pluginPath.DIRECTORY_SEPARATOR.'views';
        if (is_dir($viewsPath)) {
            app('view')->addNamespace($slug, $viewsPath);
        }

        // Register web routes
        $routesFile = $pluginPath.DIRECTORY_SEPARATOR.'routes.php';
        if (file_exists($routesFile)) {
            Route::middleware('web')->group($routesFile);
        }

        // Register API routes
        $apiRoutesFile = $pluginPath.DIRECTORY_SEPARATOR.'routes'.DIRECTORY_SEPARATOR.'api.php';
        if (file_exists($apiRoutesFile)) {
            Route::middleware('api')->prefix('api')->group($apiRoutesFile);
        }

        // Register admin routes (protected by auth:admin middleware)
        $adminRoutesFile = $pluginPath.DIRECTORY_SEPARATOR.'routes'.DIRECTORY_SEPARATOR.'admin.php';
        if (file_exists($adminRoutesFile)) {
            Route::middleware(['web', 'auth:admin'])->prefix('admin')->name('admin.')->group($adminRoutesFile);
        }

        // Track loaded plugins
        $this->loaded[$slug] = array_merge($manifest, [
            'slug' => $slug,
            'path' => $pluginPath,
        ]);
    }

    /**
     * Return all successfully loaded plugins.
     *
     * @return array<string, array>
     */
    public function all(): array
    {
        return $this->loaded;
    }

    /**
     * Return a specific plugin's manifest by slug.
     */
    public function get(string $slug): ?array
    {
        return $this->loaded[$slug] ?? null;
    }

    /**
     * Check if a plugin is loaded.
     */
    public function has(string $slug): bool
    {
        return isset($this->loaded[$slug]);
    }
}
