<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
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
        return view('admin.collections.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'show_in_menu' => 'nullable|boolean',
        ]);
        $data['show_in_menu'] = $request->boolean('show_in_menu');
        $data['slug'] = Str::slug($data['name']);
        $data['position'] = Collection::max('position') + 1;
        Collection::create($data);

        return redirect()->route('admin.collections.index')->with('success', 'Collection created successfully.');
    }

    public function edit(Collection $collection)
    {
        return view('admin.collections.edit', ['collection' => $collection]);
    }

    public function update(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'show_in_menu' => 'nullable|boolean',
            'fields' => 'nullable|json',
        ]);
        $data['show_in_menu'] = $request->boolean('show_in_menu');

        if (isset($data['fields'])) {
            $data['fields'] = json_decode($data['fields'], true);
        }

        $collection->update($data);

        return redirect()->route('admin.collections.index')->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect()->route('admin.collections.index')->with('success', 'Collection deleted successfully.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->collection_ids ?? [] as $i => $id) {
            Collection::where('id', $id)->update(['position' => $i]);
        }

        return response()->noContent();
    }
}
