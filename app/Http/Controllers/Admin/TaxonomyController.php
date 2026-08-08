<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TaxonomyController extends Controller
{
    public function index(): View
    {
        if (! Schema::hasColumn('taxonomies', 'position')) {
            Schema::table('taxonomies', function ($table) {
                $table->integer('position')->default(0);
            });
        }

        $taxonomies = Taxonomy::withCount('terms')
            ->with(['terms' => fn ($q) => $q->orderBy('position')->limit(6)])
            ->orderBy('position')
            ->orderBy('title')
            ->get();

        return view('admin.taxonomies.index', ['taxonomies' => $taxonomies]);
    }

    public function reorder(Request $request)
    {
        if (! Schema::hasColumn('taxonomies', 'position')) {
            Schema::table('taxonomies', function ($table) {
                $table->integer('position')->default(0);
            });
        }

        foreach ($request->taxonomy_ids ?? [] as $i => $id) {
            Taxonomy::where('id', $id)->update(['position' => $i]);
        }

        return response()->noContent();
    }

    public function create(): View
    {
        return view('admin.taxonomies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (empty($request->slug) && ! empty($request->title)) {
            $request->merge([
                'slug' => str($request->title)->slug()->limit(255)->toString(),
            ]);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:taxonomies,slug',
            'description' => 'nullable|string',
        ]);

        $taxonomy = Taxonomy::create($data);

        return redirect()->route('admin.taxonomies.show', $taxonomy)->with('success', 'Taxonomy created successfully.');
    }

    public function show(Taxonomy $taxonomy): View
    {
        $taxonomy->load(['terms' => fn ($q) => $q->orderBy('position')->orderBy('title')]);

        return view('admin.taxonomies.show', ['taxonomy' => $taxonomy]);
    }

    public function edit(Taxonomy $taxonomy): View
    {
        return view('admin.taxonomies.edit', ['taxonomy' => $taxonomy]);
    }

    public function update(Request $request, Taxonomy $taxonomy): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:taxonomies,slug,'.$taxonomy->id,
            'description' => 'nullable|string',
        ]);

        $taxonomy->update($data);

        return redirect()->route('admin.taxonomies.show', $taxonomy)->with('success', 'Taxonomy updated successfully.');
    }

    public function destroy(Taxonomy $taxonomy): RedirectResponse
    {
        $taxonomy->delete();

        return redirect()->route('admin.taxonomies.index')->with('success', 'Taxonomy deleted successfully.');
    }
}
