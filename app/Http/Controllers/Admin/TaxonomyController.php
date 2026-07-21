<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use Illuminate\Http\Request;

class TaxonomyController extends Controller
{
    public function index()
    {
        $taxonomies = Taxonomy::orderBy('title')->get();

        return view('admin.taxonomies.index', ['taxonomies' => $taxonomies]);
    }

    public function create()
    {
        return view('admin.taxonomies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:taxonomies,slug',
            'description' => 'nullable|string',
        ]);

        Taxonomy::create($data);

        return redirect()->route('admin.taxonomies.index')->with('success', 'Taxonomy created.');
    }

    public function edit(Taxonomy $taxonomy)
    {
        return view('admin.taxonomies.edit', ['taxonomy' => $taxonomy]);
    }

    public function update(Request $request, Taxonomy $taxonomy)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:taxonomies,slug,'.$taxonomy->id,
            'description' => 'nullable|string',
        ]);

        $taxonomy->update($data);

        return redirect()->route('admin.taxonomies.index')->with('success', 'Taxonomy updated.');
    }

    public function destroy(Taxonomy $taxonomy)
    {
        $taxonomy->delete();

        return redirect()->route('admin.taxonomies.index')->with('success', 'Taxonomy deleted.');
    }
}
