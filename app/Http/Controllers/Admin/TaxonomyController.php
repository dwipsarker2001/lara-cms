<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Taxonomy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TaxonomyController extends Controller
{
    protected function ensureSchemaColumns(): void
    {
        if (Schema::hasTable('taxonomies')) {
            if (! Schema::hasColumn('taxonomies', 'position')) {
                Schema::table('taxonomies', function ($table) {
                    $table->integer('position')->default(0);
                });
            }
            if (! Schema::hasColumn('taxonomies', 'fields')) {
                Schema::table('taxonomies', function ($table) {
                    $table->json('fields')->nullable();
                });
            }
            if (! Schema::hasColumn('taxonomies', 'icon')) {
                Schema::table('taxonomies', function ($table) {
                    $table->string('icon')->nullable();
                });
            }
            if (! Schema::hasColumn('taxonomies', 'route_pattern')) {
                Schema::table('taxonomies', function ($table) {
                    $table->string('route_pattern')->nullable();
                });
            }
        }
    }

    public function index(): View
    {
        $this->ensureSchemaColumns();

        $taxonomies = Taxonomy::withCount('terms')
            ->with(['terms' => fn ($q) => $q->orderBy('position')->limit(6)])
            ->orderBy('position')
            ->orderBy('title')
            ->get();

        return view('admin.taxonomies.index', ['taxonomies' => $taxonomies]);
    }

    public function reorder(Request $request)
    {
        $this->ensureSchemaColumns();

        foreach ($request->taxonomy_ids ?? [] as $i => $id) {
            Taxonomy::where('id', $id)->update(['position' => $i]);
        }

        return response()->noContent();
    }

    public function create(): View
    {
        $this->ensureSchemaColumns();

        $collections = Collection::whereRaw('LOWER(name) != ?', ['layout'])
            ->whereRaw('LOWER(slug) != ?', ['layout'])
            ->whereRaw('LOWER(slug) != ?', ['layouts'])
            ->with(['entries' => fn ($q) => $q->select('id', 'collection_id', 'slug', 'data')->where('published', true)])
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        $taxonomies = Taxonomy::orderBy('title')->get();

        return view('admin.taxonomies.create', compact('collections', 'taxonomies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureSchemaColumns();

        if (empty($request->slug) && ! empty($request->title)) {
            $request->merge([
                'slug' => str($request->title)->slug()->limit(255)->toString(),
            ]);
        }

        if ($request->has('fields') && is_string($request->fields)) {
            $decoded = json_decode($request->fields, true);
            $request->merge(['fields' => is_array($decoded) ? $decoded : []]);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:taxonomies,slug',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'route_pattern' => 'nullable|string|max:500',
            'fields' => 'nullable|array',
        ]);

        $taxonomy = Taxonomy::create($data);

        return redirect()->route('admin.taxonomies.show', $taxonomy)->with('success', 'Taxonomy created successfully.');
    }

    public function show(Taxonomy $taxonomy): View
    {
        $this->ensureSchemaColumns();

        $taxonomy->load(['terms' => fn ($q) => $q->orderBy('position')->orderBy('title')]);

        return view('admin.taxonomies.show', ['taxonomy' => $taxonomy]);
    }

    public function edit(Taxonomy $taxonomy): View
    {
        $this->ensureSchemaColumns();

        $collections = Collection::whereRaw('LOWER(name) != ?', ['layout'])
            ->whereRaw('LOWER(slug) != ?', ['layout'])
            ->whereRaw('LOWER(slug) != ?', ['layouts'])
            ->with(['entries' => fn ($q) => $q->select('id', 'collection_id', 'slug', 'data')->where('published', true)])
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        $taxonomies = Taxonomy::where('id', '!=', $taxonomy->id)->orderBy('title')->get();

        return view('admin.taxonomies.edit', compact('taxonomy', 'collections', 'taxonomies'));
    }

    public function update(Request $request, Taxonomy $taxonomy): RedirectResponse
    {
        $this->ensureSchemaColumns();

        if ($request->has('fields') && is_string($request->fields)) {
            $decoded = json_decode($request->fields, true);
            $request->merge(['fields' => is_array($decoded) ? $decoded : []]);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:taxonomies,slug,'.$taxonomy->id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'route_pattern' => 'nullable|string|max:500',
            'fields' => 'nullable|array',
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
