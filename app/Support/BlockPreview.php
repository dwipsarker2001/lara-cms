<?php

namespace App\Support;

use App\Blocks\BlockRegistry;

class BlockPreview
{
    public static function render(array $sections, bool $withGlobals = true): string
    {
        $resolved = $withGlobals ? Sections::withGlobals($sections) : $sections;
        $registry = app(BlockRegistry::class);
        $html = '';

        foreach ($resolved as $section) {
            $block = $registry->get($section['name'] ?? '');

            if (! $block || ! view()->exists($block->view())) {
                continue;
            }

            $html .= view($block->view(), [
                'data' => $section['data'] ?? [],
                '_key' => $section['_key'] ?? '',
                'preview' => true,
            ])->render();
        }

        return $html;
    }
}
