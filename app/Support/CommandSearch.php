<?php

namespace App\Support;

use App\Models\Taxonomy;

/**
 * Admin command palette — ranking + command catalog.
 * Ported from etravel-pro command-search (prefix > boundary > substring > fuzzy).
 */
class CommandSearch
{
    public const PER_GROUP = 7;

    /**
     * @return list<array{id: string, group: string, title: string, subtitle?: string|null, keywords?: string|null, href: string}>
     */
    public static function staticCommands(): array
    {
        return [
            ['id' => 'nav-dashboard', 'group' => 'Navigation', 'title' => 'Dashboard', 'keywords' => 'home overview', 'href' => route('admin.dashboard')],
            ['id' => 'nav-taxonomies', 'group' => 'Navigation', 'title' => 'Collection » Taxonomies', 'keywords' => 'tags categories', 'href' => route('admin.taxonomies.index')],
            ['id' => 'nav-assets', 'group' => 'Navigation', 'title' => 'Collection » Assets', 'keywords' => 'media images files', 'href' => route('admin.assets.index')],
            ['id' => 'nav-globals', 'group' => 'Navigation', 'title' => 'Settings » Globals', 'keywords' => 'site settings', 'href' => route('admin.settings')],
            ['id' => 'act-new-taxonomy', 'group' => 'Navigation', 'title' => 'Create » New Taxonomy', 'keywords' => 'add new taxonomy category', 'href' => route('admin.taxonomies.create')],
        ];
    }

    /**
     * Live content commands (pages, posts, layouts, taxonomies).
     *
     * @return list<array{id: string, group: string, title: string, subtitle?: string|null, keywords?: string|null, href: string}>
     */
    public static function dynamicCommands(): array
    {
        $commands = [];

        foreach (Taxonomy::query()->orderBy('title')->get(['id', 'title', 'slug']) as $taxonomy) {
            $commands[] = [
                'id' => 'tax-'.$taxonomy->id,
                'group' => 'Taxonomies',
                'title' => $taxonomy->title,
                'subtitle' => $taxonomy->slug,
                'href' => route('admin.taxonomies.edit', $taxonomy),
            ];
        }

        return $commands;
    }

    /**
     * Score a command against a query. 0 = no match. Higher is better:
     * prefix (4) > word-boundary (3) > substring (1) > fuzzy subsequence (0.5).
     * Empty query matches everything (score 1) so the palette can show defaults.
     *
     * @param  array{title: string, subtitle?: string|null, keywords?: string|null}  $cmd
     */
    public static function score(array $cmd, string $query): float
    {
        $q = mb_strtolower(trim($query));
        if ($q === '') {
            return 1.0;
        }

        $haystacks = array_values(array_filter([
            $cmd['title'] ?? '',
            $cmd['subtitle'] ?? null,
            $cmd['keywords'] ?? null,
        ], fn ($v) => is_string($v) && $v !== ''));

        $best = 0.0;
        foreach ($haystacks as $h) {
            $t = mb_strtolower($h);
            $i = mb_strpos($t, $q);
            if ($i !== false) {
                $boundary = $i === 0 || preg_match('/[\s\/»·>\-]/u', mb_substr($t, $i - 1, 1));
                $score = $i === 0 ? 4.0 : ($boundary ? 3.0 : 1.0);
                $best = max($best, $score);
            }
        }

        // Weak fallback: letters appear in order in the title (e.g. "blgpst" → "Blog Post").
        if ($best === 0.0 && self::isSubsequence($q, mb_strtolower($cmd['title'] ?? ''))) {
            $best = 0.5;
        }

        return $best;
    }

    /**
     * Filter + rank commands for a query. Stable: equal scores keep input order.
     *
     * @param  list<array{id: string, group: string, title: string, subtitle?: string|null, keywords?: string|null, href: string}>  $commands
     * @return list<array{id: string, group: string, title: string, subtitle?: string|null, keywords?: string|null, href: string}>
     */
    public static function filter(array $commands, string $query): array
    {
        $ranked = [];
        foreach ($commands as $idx => $cmd) {
            $score = self::score($cmd, $query);
            if ($score > 0) {
                $ranked[] = ['cmd' => $cmd, 'idx' => $idx, 'score' => $score];
            }
        }

        usort($ranked, function (array $a, array $b): int {
            if ($a['score'] !== $b['score']) {
                return $b['score'] <=> $a['score'];
            }

            return $a['idx'] <=> $b['idx'];
        });

        return array_map(fn (array $x) => $x['cmd'], $ranked);
    }

    /**
     * Group ranked commands by `group`, preserving first-seen group order.
     *
     * @param  list<array{id: string, group: string, title: string, subtitle?: string|null, keywords?: string|null, href: string}>  $commands
     * @return list<array{group: string, items: list<array{id: string, group: string, title: string, subtitle?: string|null, keywords?: string|null, href: string}>}>
     */
    public static function group(array $commands): array
    {
        $order = [];
        $map = [];

        foreach ($commands as $cmd) {
            $g = $cmd['group'];
            if (! array_key_exists($g, $map)) {
                $map[$g] = [];
                $order[] = $g;
            }
            $map[$g][] = $cmd;
        }

        return array_map(
            fn (string $group) => ['group' => $group, 'items' => $map[$group]],
            $order,
        );
    }

    /**
     * Full palette search: empty query → navigation only; typing → all content.
     *
     * @return list<array{group: string, items: list<array{id: string, group: string, title: string, subtitle?: string|null, keywords?: string|null, href: string}>}>
     */
    public static function search(string $query): array
    {
        $base = trim($query) !== ''
            ? array_merge(self::staticCommands(), self::dynamicCommands())
            : self::staticCommands();

        $ranked = self::filter($base, $query);

        return array_map(
            fn (array $g) => [
                'group' => $g['group'],
                'items' => array_slice($g['items'], 0, self::PER_GROUP),
            ],
            self::group($ranked),
        );
    }

    private static function isSubsequence(string $needle, string $haystack): bool
    {
        $i = 0;
        $nLen = mb_strlen($needle);
        $hLen = mb_strlen($haystack);

        for ($j = 0; $j < $hLen && $i < $nLen; $j++) {
            if (mb_substr($haystack, $j, 1) === mb_substr($needle, $i, 1)) {
                $i++;
            }
        }

        return $i === $nLen;
    }
}
