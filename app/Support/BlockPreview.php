<?php

namespace App\Support;

use App\Blocks\BlockRegistry;

class BlockPreview
{
    public static function render(array $sections, bool $withGlobals = true, mixed $page = null): string
    {
        $resolved = $withGlobals ? Sections::withGlobals($sections) : $sections;
        $registry = app(BlockRegistry::class);
        $html = '';

        foreach ($resolved as $i => $section) {
            $block = $registry->get($section['name'] ?? '');

            if (($section['enabled'] ?? true) === false || ! $block || ! view()->exists($block->view())) {
                continue;
            }

            $inner = view($block->view(), [
                'data' => $section['data'] ?? [],
                '_key' => $section['_key'] ?? '',
                'preview' => true,
                'page' => $page,
            ])->render();

            $html .= '<div data-section-index="'.$i.'" class="p-0.5">'.$inner.'</div>';
        }

        return $html;
    }
}
