<?php

namespace App\Widgets;

use Symfony\Component\Finder\Finder;

class WidgetRegistry
{
    protected ?array $widgets = null;

    /** @return array<string, class-string<Widget>> */
    public function all(): array
    {
        return $this->widgets ??= $this->discover();
    }

    public function get(string $type): ?string
    {
        return $this->all()[$type] ?? null;
    }

    /** @return array<int, class-string<Widget>> */
    public function types(): array
    {
        return array_values($this->all());
    }

    /** @return array<string, class-string<Widget>> */
    protected function discover(): array
    {
        $map = [];

        foreach (Finder::create()->files()->in(app_path('Widgets'))->name('*.php') as $file) {
            $class = $this->classFromPath($file->getRealPath());

            if (! class_exists($class)) {
                continue;
            }

            $ref = new \ReflectionClass($class);

            if (! $ref->isSubclassOf(Widget::class) || $ref->isAbstract()) {
                continue;
            }

            $type = $class::type();
            $map[$type] = $class;
        }

        foreach ($this->discoverFromPlugins() as $type => $class) {
            $map[$type] = $class;
        }

        return $map;
    }

    /**
     * Scan every plugin's Widgets/ directory and register any Widget subclasses found.
     *
     * @return array<string, class-string<Widget>>
     */
    protected function discoverFromPlugins(): array
    {
        $map = [];
        $pluginsPath = base_path('plugins');

        if (! is_dir($pluginsPath)) {
            return $map;
        }

        foreach (Finder::create()->directories()->in($pluginsPath)->depth(0) as $pluginDir) {
            $widgetsPath = $pluginDir->getRealPath().DIRECTORY_SEPARATOR.'Widgets';

            if (! is_dir($widgetsPath)) {
                continue;
            }

            foreach (Finder::create()->files()->in($widgetsPath)->name('*.php') as $file) {
                $path = $file->getRealPath();
                $class = $this->classFromFile($path);

                if (! $class) {
                    continue;
                }

                if (! class_exists($class)) {
                    require_once $path;
                }

                if (! class_exists($class)) {
                    continue;
                }

                $ref = new \ReflectionClass($class);

                if (! $ref->isSubclassOf(Widget::class) || $ref->isAbstract()) {
                    continue;
                }

                $type = $class::type();
                $map[$type] = $class;
            }
        }

        return $map;
    }

    /** Flush the cached widgets so discovery runs again (useful in tests). */
    public function flush(): void
    {
        $this->widgets = null;
    }

    protected function classFromPath(string $path): string
    {
        $relative = str_replace([app_path().DIRECTORY_SEPARATOR, '.php'], '', $path);

        return 'App\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
    }

    /**
     * Extract a fully-qualified class name from a PHP file using token parsing.
     * Used for plugin widgets that may not yet be autoloaded.
     */
    protected function classFromFile(string $path): ?string
    {
        $contents = @file_get_contents($path);
        if (! $contents) {
            return null;
        }

        $tokens = token_get_all($contents);
        $namespace = '';
        $class = '';
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            if (! is_array($token)) {
                continue;
            }

            if ($token[0] === T_NAMESPACE) {
                $namespace = '';
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j] === ';') {
                        $i = $j;
                        break;
                    }
                    if (is_array($tokens[$j]) && in_array($tokens[$j][0], [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NS_SEPARATOR], true)) {
                        $namespace .= $tokens[$j][1];
                    }
                }
            } elseif ($token[0] === T_CLASS) {
                $prevToken = null;
                for ($k = $i - 1; $k >= 0; $k--) {
                    if (is_array($tokens[$k]) && in_array($tokens[$k][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                        continue;
                    }
                    $prevToken = is_array($tokens[$k]) ? $tokens[$k][0] : $tokens[$k];
                    break;
                }

                if ($prevToken === T_DOUBLE_COLON) {
                    continue;
                }

                for ($j = $i + 1; $j < $count; $j++) {
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                        $class = $tokens[$j][1];
                        break 2;
                    }
                }
            }
        }

        if (! $class) {
            return null;
        }

        return $namespace ? $namespace.'\\'.$class : $class;
    }
}
