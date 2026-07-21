<?php

namespace App\Blocks;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Finder\Finder;

/**
 * BlockRegistry
 * Auto-discovers every Block subclass under app/Blocks and exposes lookups.
 * Registered as a singleton, so discovery runs once per request.
 *
 * Add a block class anywhere under app/Blocks — it appears here automatically.
 */
class BlockRegistry
{
    /** @var array<string, Block>|null */
    protected ?array $blocks = null;

    /** @return array<string, Block> keyed by block name */
    public function all(): array
    {
        return $this->blocks ??= $this->discover();
    }

    public function get(string $name): ?Block
    {
        return $this->all()[$name] ?? null;
    }

    /** Non-global blocks offered in the page block picker. */
    public function pickable(): Collection
    {
        return collect($this->all())->reject(fn (Block $b) => $b->global)->values();
    }

    public function globals(): Collection
    {
        return collect($this->all())->filter(fn (Block $b) => $b->global)->values();
    }

    /** Compact list for the editor's "add section" picker: [{name,label}]. Includes all blocks. */
    public function pickerList(): array
    {
        return collect($this->all())->map(fn (Block $b) => ['name' => $b->name, 'label' => $b->label])->values()->all();
    }

    /** All schemas keyed by name for the editor: { name: fields[] }. */
    public function schemas(): array
    {
        return collect($this->all())->mapWithKeys(fn (Block $b) => [$b->name => $b->resolvedFields()])->all();
    }

    /** @return array<string, Block> */
    protected function discover(): array
    {
        $dir = app_path('Blocks');
        $blocks = [];

        foreach (Finder::create()->files()->in($dir)->name('*.php') as $file) {
            $class = $this->classFromPath($file->getRealPath());

            if (! $class || ! class_exists($class)) {
                continue;
            }

            $ref = new \ReflectionClass($class);
            if (! $ref->isSubclassOf(Block::class) || $ref->isAbstract()) {
                continue;
            }

            try {
                /** @var Block $block */
                $block = $ref->newInstance();
            } catch (\Throwable $e) {
                Log::warning("BlockRegistry: skipping {$class} — {$e->getMessage()}");

                continue;
            }

            $blocks[$block->name] = $block;
        }

        return $blocks;
    }

    /** Map an absolute path under app/Blocks to its FQCN via PSR-4. */
    protected function classFromPath(string $path): string
    {
        $relative = str_replace([app_path().DIRECTORY_SEPARATOR, '.php'], '', $path);

        return 'App\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
    }
}
