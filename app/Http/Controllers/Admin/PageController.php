<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\CollectionEntry;

class PageController extends Controller
{
    protected function getPagesCollection(): Collection
    {
        return Collection::firstOrCreate(
            ['slug' => 'pages'],
            [
                'name' => 'Pages',
                'icon' => 'fa-solid fa-file-lines',
                'show_in_menu' => true,
                'enable_seo' => true,
                'description' => 'System Pages',
            ]
        );
    }

    public function index()
    {
        $collection = $this->getPagesCollection();

        return redirect()->route('admin.collections.entries.index', $collection);
    }

    public function create()
    {
        $collection = $this->getPagesCollection();

        return redirect()->route('admin.collections.entries.create', $collection);
    }

    public function edit(mixed $id)
    {
        $collection = $this->getPagesCollection();
        $entry = CollectionEntry::find($id);
        if (! $entry) {
            return redirect()->route('admin.collections.entries.index', $collection);
        }

        return redirect()->route('admin.collections.entries.edit', [$collection, $entry]);
    }

    public function editor(mixed $id)
    {
        $collection = $this->getPagesCollection();
        $entry = CollectionEntry::find($id);
        if (! $entry) {
            return redirect()->route('admin.collections.entries.index', $collection);
        }

        return redirect()->route('admin.collections.entries.editor', [$collection, $entry]);
    }
}
