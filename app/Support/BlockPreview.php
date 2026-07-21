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
            if (($section['enabled'] ?? true) === false) {
                continue;
            }

            $block = $registry->get($section['name'] ?? '');
            if (! $block) {
                continue;
            }

            $inner = $block->render(
                data: $section['data'] ?? [],
                _key: $section['_key'] ?? '',
                preview: true,
                page: $page,
            );

            if ($inner === '') {
                continue;
            }

            $html .= '<div data-section-index="'.$i.'" class="p-0.5">'.$inner.'</div>';
        }

        return $html;
    }
}
