<?php

namespace App\Support;

use App\Blocks\BlockRegistry;
use App\Models\CollectionEntry;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Sections
{
    public static function defaultData(array $fields): array
    {
        $out = [];
        foreach ($fields as $f) {
            if (($f['type'] ?? '') === 'object') {
                $out[$f['name']] = ($f['list'] ?? false)
                    ? array_map(fn () => static::newListItem($f), range(1, $f['defaultCount'] ?? 0))
                    : static::defaultData($f['fields'] ?? []);
            } else {
                $out[$f['name']] = $f['defaultValue'] ?? '';
            }
        }

        return $out;
    }

    public static function newListItem(array $objectField): array
    {
        return ['_key' => (string) Str::uuid(), ...static::defaultData($objectField['fields'] ?? [])];
    }

    public static function createDefaultSection(string $name): ?array
    {
        $block = app(BlockRegistry::class)->get($name);

        if (! $block) {
            return null;
        }

        return [
            '_key' => (string) Str::uuid(),
            'name' => $name,
            'enabled' => true,
            'data' => static::defaultData($block->resolvedFields()),
        ];
    }

    public static function withGlobals(?array $sections): array
    {
        if (! Schema::hasTable('collection_entries')) {
            return $sections ?? [];
        }

        $home = CollectionEntry::where('slug', 'home')->first();

        if (! $home || ! $home->sections) {
            return $sections ?? [];
        }

        $registry = app(BlockRegistry::class);

        $homeGlobals = collect($home->sections)
            ->filter(fn ($s) => $registry->get($s['name'])?->global)
            ->keyBy('name');

        $merged = collect($sections)->map(function ($s) use ($homeGlobals) {
            $global = $homeGlobals->get($s['name'] ?? '');

            return $global ? [...$s, 'data' => $global['data']] : $s;
        });

        $present = $merged->pluck('name');

        $missing = $homeGlobals->reject(fn ($g, $name) => $present->contains($name));

        return $missing->values()->merge($merged)->all();
    }

    public static function injectGlobals(): array
    {
        if (! Schema::hasTable('collection_entries')) {
            return [];
        }

        $home = CollectionEntry::where('slug', 'home')->first();
        if (! $home || ! $home->sections) {
            return [];
        }

        $registry = app(BlockRegistry::class);

        return collect($home->sections)
            ->filter(fn ($s) => $registry->get($s['name'])?->global)
            ->map(fn ($s) => [
                '_key' => (string) Str::uuid(),
                'name' => $s['name'],
                'enabled' => true,
                'data' => $s['data'],
            ])
            ->values()
            ->all();
    }

    public static function sectionsToPropagate(array $sections, array $globalNames): array
    {
        return collect($sections)
            ->filter(fn ($s) => in_array($s['name'] ?? '', $globalNames))
            ->values()
            ->all();
    }

    public static function slugify(string $value): string
    {
        $slug = mb_strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/u', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }

    public static function getAtPath(array $root, array $path): mixed
    {
        $current = $root;
        foreach ($path as $segment) {
            $key = $segment['key'] ?? '';
            $idx = $segment['index'] ?? null;

            if ($idx !== null) {
                $current = $current[$key][$idx] ?? [];
            } else {
                $current = $current[$key] ?? [];
            }
        }

        return $current;
    }

    public static function setAtPath(array $root, array $path, string $key, mixed $value): array
    {
        if (empty($path)) {
            $root[$key] = $value;

            return $root;
        }

        $segment = array_shift($path);
        $k = $segment['key'];
        $idx = $segment['index'] ?? null;

        if ($idx !== null) {
            $root[$k][$idx] = static::setAtPath($root[$k][$idx] ?? [], $path, $key, $value);
        } else {
            $root[$k] = static::setAtPath($root[$k] ?? [], $path, $key, $value);
        }

        return $root;
    }

    public static function fieldsAtPath(array $schema, array $path): array
    {
        $fields = $schema;
        foreach ($path as $segment) {
            $key = $segment['key'];
            $field = collect($fields)->firstWhere('name', $key);

            if (! $field || ($field['type'] ?? '') !== 'object') {
                return [];
            }

            $fields = $field['fields'] ?? [];
        }

        return $fields;
    }
}
