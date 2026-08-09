<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TermController extends Controller
{
    protected function ensureSchemaColumns(): void
    {
        if (Schema::hasTable('terms')) {
            if (! Schema::hasColumn('terms', 'data')) {
                Schema::table('terms', function ($table) {
                    $table->json('data')->nullable();
                });
            }
            if (! Schema::hasColumn('terms', 'description')) {
                Schema::table('terms', function ($table) {
                    $table->text('description')->nullable();
                });
            }
        }
    }

    public function create(Taxonomy $taxonomy)
    {
        $this->ensureSchemaColumns();

        return view('admin.taxonomies.terms.create', ['taxonomy' => $taxonomy]);
    }

    public function edit(Taxonomy $taxonomy, Term $term)
    {
        $this->ensureSchemaColumns();

        return view('admin.taxonomies.terms.edit', [
            'taxonomy' => $taxonomy,
            'term' => $term,
        ]);
    }

    public function store(Request $request, Taxonomy $taxonomy): RedirectResponse|JsonResponse
    {
        $this->ensureSchemaColumns();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'position' => 'nullable|integer',
            'description' => 'nullable|string',
            'data' => 'nullable|array',
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
        $this->ensureSchemaColumns();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'position' => 'nullable|integer',
            'description' => 'nullable|string',
            'data' => 'nullable|array',
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
        $this->ensureSchemaColumns();

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
