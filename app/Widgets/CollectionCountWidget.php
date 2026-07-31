<?php

namespace App\Widgets;

use App\Models\Collection;

class CollectionCountWidget extends Widget
{
    public static function type(): string
    {
        return 'collection_count';
    }

    public static function zone(): string
    {
        return 'grid';
    }

    public function label(): string
    {
        return 'Collection Count';
    }

    public function render()
    {
        $collections = Collection::with(['entries' => function ($query) {
            $query->select('id', 'collection_id', 'published');
        }])->orderBy('name')->get();

        $data = $collections->map(function ($c) {
            $count = $c->entries->count();
            $published = $c->entries->where('published', true)->count();

            return [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'icon' => $c->icon,
                'count' => number_format($count),
                'published' => $published,
                'delta' => $published === $count ? 'All published' : $published.' / '.$count.' published',
                'up' => $published === $count,
            ];
        });

        return view('admin.widgets.collection-count', [
            'collections' => $data,
        ]);
    }
}
