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

        return $map;
    }

    protected function classFromPath(string $path): string
    {
        $relative = str_replace([app_path().DIRECTORY_SEPARATOR, '.php'], '', $path);

        return 'App\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
    }
}
