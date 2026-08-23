<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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

        // Register PSR-4 class autoloader for plugin classes (e.g. Plugins\Slug\...)
        $srcPath = $pluginPath.DIRECTORY_SEPARATOR.'src';
        $blocksPath = $pluginPath.DIRECTORY_SEPARATOR.'Blocks';

        $studlySlug = Str::studly($slug);
        $baseNamespace = $manifest['namespace'] ?? "Plugins\\{$studlySlug}\\";
        $baseNamespace = rtrim($baseNamespace, '\\').'\\';

        if (is_dir($srcPath) || is_dir($blocksPath)) {
            spl_autoload_register(function (string $class) use ($baseNamespace, $pluginPath, $srcPath, $blocksPath): void {
                if (! str_starts_with($class, $baseNamespace)) {
                    return;
                }

                $relativeClass = substr($class, strlen($baseNamespace));
                $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass).'.php';

                if (file_exists($pluginPath.DIRECTORY_SEPARATOR.$relativePath)) {
                    require_once $pluginPath.DIRECTORY_SEPARATOR.$relativePath;

                    return;
                }

                if (is_dir($srcPath) && file_exists($srcPath.DIRECTORY_SEPARATOR.$relativePath)) {
                    require_once $srcPath.DIRECTORY_SEPARATOR.$relativePath;

                    return;
                }

                if (is_dir($blocksPath) && file_exists($blocksPath.DIRECTORY_SEPARATOR.$relativePath)) {
                    require_once $blocksPath.DIRECTORY_SEPARATOR.$relativePath;

                    return;
                }
            });
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

        // Auto-load plugin database migrations (e.g. plugins/comments/database/migrations)
        $migrationsPath = $pluginPath.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';
        if (is_dir($migrationsPath)) {
            app('migrator')->path($migrationsPath);
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

    /**
     * Return all dynamic admin menu items provided by loaded plugins.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAdminMenuItems(): array
    {
        $items = [];

        foreach ($this->loaded as $plugin) {
            $menu = $plugin['admin_menu'] ?? [];
            if (! is_array($menu)) {
                continue;
            }

            foreach ($menu as $item) {
                if (! is_array($item) || empty($item['label'])) {
                    continue;
                }

                $items[] = [
                    'label' => $item['label'],
                    'route' => $item['route'] ?? null,
                    'url' => $item['url'] ?? null,
                    'icon' => $item['icon'] ?? null,
                    'badge' => $item['badge'] ?? null,
                    'group' => $item['group'] ?? 'Extensions',
                    'order' => $item['order'] ?? 10,
                ];
            }
        }

        usort($items, fn ($a, $b) => ($a['order'] ?? 10) <=> ($b['order'] ?? 10));

        return $items;
    }
}
