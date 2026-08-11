<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    public function index()
    {
        return view('admin.collections.index', ['collections' => Collection::orderBy('position')->get()]);
    }

    public function create()
    {
        return view('admin.collections.create', [
            'collections' => Collection::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'enable_seo' => 'nullable|boolean',
            'fields' => 'nullable|json',
        ]);
        $slug = Str::slug($data['name']);
        $original = $slug;
        $i = 1;
        while (Collection::where('slug', $slug)->exists()) {
            $slug = $original.'-'.$i++;
        }
        $data['slug'] = $slug;
        $data['position'] = Collection::max('position') + 1;
        $data['enable_seo'] = (bool) ($data['enable_seo'] ?? false);

        if (isset($data['fields'])) {
            $data['fields'] = json_decode($data['fields'], true);
        }

        $collection = Collection::create($data);

        return redirect()->route('admin.collections.entries.index', $collection)->with('success', 'Collection created successfully.');
    }

    public function edit(Collection $collection)
    {
        return view('admin.collections.edit', [
            'collection' => $collection,
            'collections' => Collection::with('entries')->orderBy('name')->get(),
            'taxonomies' => Taxonomy::orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'enable_seo' => 'nullable|boolean',
            'fields' => 'nullable|json',
        ]);

        $slug = Str::slug($data['name']);
        $original = $slug;
        $i = 1;
        while (Collection::where('slug', $slug)->where('id', '!=', $collection->id)->exists()) {
            $slug = $original.'-'.$i++;
        }
        $data['slug'] = $slug;
        $data['enable_seo'] = (bool) ($data['enable_seo'] ?? false);

        if (isset($data['fields'])) {
            $data['fields'] = json_decode($data['fields'], true);
        }

        $collection->update($data);

        return redirect()->route('admin.collections.edit', $collection)->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect()->route('admin.collections.create')->with('success', 'Collection deleted successfully.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->collection_ids ?? [] as $i => $id) {
            Collection::where('id', $id)->update(['position' => $i]);
        }

        return response()->noContent();
    }
}
