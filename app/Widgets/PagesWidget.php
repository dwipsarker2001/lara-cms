<?php

namespace App\Widgets;

use App\Models\Page;

class PagesWidget extends Widget
{
    public ?string $image = null;

    public static function type(): string
    {
        return 'pages';
    }

    public static function zone(): string
    {
        return 'grid';
    }

    public function label(): string
    {
        return 'Total Pages';
    }

    public function render()
    {
        $count = Page::count();
        $published = Page::where('published', true)->count();

        return view('admin.widgets.pages', [
            'widget' => (object) [
                'value' => number_format($count),
                'published' => $published,
                'delta' => $published === $count ? 'All published' : $published.' / '.$count.' published',
                'up' => $published === $count,
            ],
        ]);
    }
}
