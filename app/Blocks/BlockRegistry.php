<?php

namespace App\Blocks;

use App\Models\DynamicBlock;
use Illuminate\Support\Collection;
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

    /** Flush the cached blocks so discovery runs again. */
    public function flush(): void
    {
        $this->blocks = null;
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
        $blocks = [];

        if (is_dir(app_path('Blocks'))) {
            foreach (Finder::create()->files()->in(app_path('Blocks'))->name('*.php')->notName(['TemplateBlock.php', 'JsonBlock.php']) as $file) {
                $class = $this->classFromPath($file->getRealPath());

                if (! $class || ! class_exists($class)) {
                    continue;
                }

                $ref = new \ReflectionClass($class);
                if (! $ref->isSubclassOf(Block::class) || $ref->isAbstract()) {
                    continue;
                }

                /** @var Block $block */
                $block = $ref->newInstance();
                $blocks[$block->name] = $block;
            }
        }

        $pluginsPath = base_path('plugins');
        if (is_dir($pluginsPath)) {
            foreach (Finder::create()->files()->in($pluginsPath)->name('*.php')->notName(['TemplateBlock.php', 'JsonBlock.php']) as $file) {
                $path = $file->getRealPath();
                $class = $this->getClassFromFile($path);

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
                if (! $ref->isSubclassOf(Block::class) || $ref->isAbstract()) {
                    continue;
                }

                /** @var Block $block */
                $block = $ref->newInstance();
                $blocks[$block->name] = $block;
            }
        }

        foreach (DynamicBlock::all() as $dbBlock) {
            $blocks[$dbBlock->name] = new TemplateBlock($dbBlock);
        }

        return $blocks;
    }

    /** Map an absolute path under app/Blocks to its FQCN via PSR-4. */
    protected function classFromPath(string $path): string
    {
        $relative = str_replace([app_path().DIRECTORY_SEPARATOR, '.php'], '', $path);

        return 'App\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
    }

    /** Extract class FQCN from a PHP file via PHP tokens. */
    protected function getClassFromFile(string $path): ?string
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

            if (is_array($token)) {
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
                    // Check that it is not ::class
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

                    // Find class name
                    for ($j = $i + 1; $j < $count; $j++) {
                        if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                            $class = $tokens[$j][1];
                            break 2;
                        }
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
