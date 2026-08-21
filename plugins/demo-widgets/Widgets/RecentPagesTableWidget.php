<?php

namespace Plugins\DemoWidgets\Widgets;

use App\Widgets\Widget;

class RecentPagesTableWidget extends Widget
{
    public static function zone(): string
    {
        return 'table';
    }

    public function label(): string
    {
        return 'Recent Pages';
    }

    public function render()
    {
        // Demo static rows — replace with real model queries in production
        $pages = collect([
            ['title' => 'Home',         'slug' => '/',             'status' => 'Published', 'updated' => '2 min ago'],
            ['title' => 'About Us',     'slug' => '/about',        'status' => 'Published', 'updated' => '1 hr ago'],
            ['title' => 'Services',     'slug' => '/services',     'status' => 'Draft',     'updated' => '3 hrs ago'],
            ['title' => 'Pricing',      'slug' => '/pricing',      'status' => 'Published', 'updated' => 'Yesterday'],
            ['title' => 'Contact',      'slug' => '/contact',      'status' => 'Published', 'updated' => '2 days ago'],
            ['title' => 'Blog',         'slug' => '/blog',         'status' => 'Draft',     'updated' => '3 days ago'],
        ]);

        return view('demo-widgets::widgets.recent-pages-table', compact('pages'));
    }
}
