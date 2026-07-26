<?php

namespace App\Support;

use App\Blocks\BlockRegistry;

class BlockPreview
{
    public static function render(array $sections, bool $withGlobals = true, mixed $page = null, bool $isEditor = false): string
    {
        $resolved = $withGlobals ? Sections::withGlobals($sections) : $sections;
        $registry = app(BlockRegistry::class);
        $html = '';

        foreach ($resolved as $i => $section) {
            $block = $registry->get($section['name'] ?? '');

            if (($section['enabled'] ?? true) === false || ! $block) {
                continue;
            }

            $inner = $block->render(
                data: $section['data'] ?? [],
                _key: $section['_key'] ?? '',
                preview: true,
                page: $page,
            );

            $class = $isEditor ? ' class="p-0.5"' : '';
            $html .= '<div data-section-index="'.$i.'"'.$class.'>'.$inner.'</div>';
        }

        return $html;
    }
}
