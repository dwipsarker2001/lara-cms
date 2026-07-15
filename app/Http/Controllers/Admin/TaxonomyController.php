<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Http\Request;

class TaxonomyController extends Controller
{
    public function index()
    {
        $taxonomies = Taxonomy::withCount('terms')->orderBy('title')->get();

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
        $taxonomy->load('terms');

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

        // Handle terms
        if ($request->has('terms')) {
            $existingIds = $taxonomy->terms()->pluck('id')->toArray();
            $submittedIds = [];

            foreach ($request->terms as $i => $termData) {
                if (! empty($termData['title'])) {
                    if (! empty($termData['id'])) {
                        $term = Term::find($termData['id']);
                        if ($term) {
                            $term->update([
                                'title' => $termData['title'],
                                'position' => $i,
                            ]);
                            $submittedIds[] = $term->id;
                        }
                    } else {
                        $term = $taxonomy->terms()->create([
                            'title' => $termData['title'],
                            'slug' => str($termData['title'])->slug()->limit(255)->toString(),
                            'position' => $i,
                        ]);
                        $submittedIds[] = $term->id;
                    }
                }
            }

            $toDelete = array_diff($existingIds, $submittedIds);
            if (! empty($toDelete)) {
                Term::whereIn('id', $toDelete)->delete();
            }
        }

        return redirect()->route('admin.taxonomies.index')->with('success', 'Taxonomy updated.');
    }

    public function destroy(Taxonomy $taxonomy)
    {
        $taxonomy->terms()->delete();
        $taxonomy->delete();

        return redirect()->route('admin.taxonomies.index')->with('success', 'Taxonomy deleted.');
    }
}
