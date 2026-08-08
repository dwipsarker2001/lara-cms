<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function store(Request $request, Taxonomy $taxonomy): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'position' => 'nullable|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = str($validated['title'])->slug()->limit(255)->toString();
        }

        if (! isset($validated['position'])) {
            $maxPos = $taxonomy->terms()->max('position');
            $validated['position'] = is_null($maxPos) ? 0 : $maxPos + 1;
        }

        $term = $taxonomy->terms()->create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Term created successfully.',
                'term' => $term,
            ]);
        }

        return redirect()->route('admin.taxonomies.show', $taxonomy)->with('success', 'Term created successfully.');
    }

    public function update(Request $request, Taxonomy $taxonomy, Term $term): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'position' => 'nullable|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = str($validated['title'])->slug()->limit(255)->toString();
        }

        $term->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Term updated successfully.',
                'term' => $term,
            ]);
        }

        return redirect()->route('admin.taxonomies.show', $taxonomy)->with('success', 'Term updated successfully.');
    }

    public function destroy(Taxonomy $taxonomy, Term $term): RedirectResponse|JsonResponse
    {
        $term->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Term deleted successfully.',
            ]);
        }

        return redirect()->route('admin.taxonomies.show', $taxonomy)->with('success', 'Term deleted successfully.');
    }

    public function reorder(Request $request, Taxonomy $taxonomy): JsonResponse
    {
        $validated = $request->validate([
            'term_ids' => 'required|array',
            'term_ids.*' => 'integer|exists:terms,id',
        ]);

        foreach ($validated['term_ids'] as $index => $id) {
            Term::where('id', $id)
                ->where('taxonomy_id', $taxonomy->id)
                ->update(['position' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Terms reordered successfully.',
        ]);
    }
}
