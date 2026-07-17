<?php

namespace App\Http\Controllers\Admin;

use App\Blocks\BlockRegistry;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Models\Page;
use App\Support\Sections;
use Illuminate\Http\Request;

class CollectionEntryController extends Controller
{
    public function index(Collection $collection)
    {
        $entries = $collection->entries()->orderBy('position')->get();

        return view('admin.collections.entries.index', compact('collection', 'entries'));
    }

    public function create(Collection $collection)
    {
        return view('admin.collections.entries.create', compact('collection'));
    }

    public function store(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'data' => 'nullable|array',
        ]);

        $entry = $collection->entries()->create([
            'data' => $data['data'] ?? [],
            'position' => $collection->entries()->max('position') + 1,
        ]);

        return redirect()->route('admin.collections.entries.editor', [$collection, $entry]);
    }

    public function edit(Collection $collection, CollectionEntry $entry)
    {
        abort_if($entry->collection_id !== $collection->id, 404);

        return view('admin.collections.entries.edit', compact('collection', 'entry'));
    }

    public function update(Request $request, Collection $collection, CollectionEntry $entry)
    {
        abort_if($entry->collection_id !== $collection->id, 404);

        $data = $request->validate([
            'data' => 'nullable|array',
        ]);

        $entry->update($data);

        return redirect()->route('admin.collections.entries.index', $collection)
            ->with('success', 'Entry updated successfully.');
    }

    public function editor(Collection $collection, CollectionEntry $entry)
    {
        abort_if($entry->collection_id !== $collection->id, 404);

        $registry = app(BlockRegistry::class);

        $blockList = collect($registry->pickerList())->map(function ($item) use ($registry) {
            $block = $registry->get($item['name']);
            $section = Sections::createDefaultSection($item['name']);
            $html = '';
            if ($block && $section && view()->exists($block->view())) {
                $html = view($block->view(), [
                    'data' => $section['data'],
                    '_key' => '',
                    'preview' => true,
                ])->render();
            }

            return [...$item, 'previewHtml' => $html];
        })->all();

        $homeGlobals = Sections::injectGlobals();

        return view('admin.collections.entries.editor', [
            'collection' => $collection,
            'entry' => $entry,
            'blockSchemas' => $registry->schemas(),
            'homeGlobals' => $homeGlobals,
            'blockList' => $blockList,
            'pages' => Page::orderBy('position')->orderBy('title')->get(['id', 'slug', 'title'])->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'route' => $p->slug === 'home' ? '/' : '/'.$p->slug,
            ]),
        ]);
    }

    public function updateSections(Request $request, Collection $collection, CollectionEntry $entry)
    {
        abort_if($entry->collection_id !== $collection->id, 404);

        $request->validate([
            'sections' => 'present|array',
            'sections.*._key' => 'required|string',
            'sections.*.name' => 'required|string',
            'sections.*.data' => 'required',
        ]);

        $entry->update(['sections' => $request->sections]);

        $registry = app(BlockRegistry::class);
        $globalNames = $registry->globals()->pluck('name')->toArray();

        $propagated = Sections::sectionsToPropagate($request->sections, $globalNames);

        if (! empty($propagated)) {
            $home = Page::where('slug', 'home')->first();

            if ($home) {
                $homeSections = collect($home->sections)->map(function ($s) use ($propagated) {
                    $match = collect($propagated)->firstWhere('name', $s['name']);

                    return $match ? [...$s, 'data' => $match['data']] : $s;
                })->all();

                $home->update(['sections' => $homeSections]);
            }
        }

        return response()->json(['message' => 'Sections saved.']);
    }

    public function destroy(Collection $collection, CollectionEntry $entry)
    {
        abort_if($entry->collection_id !== $collection->id, 404);

        $entry->delete();

        return redirect()->route('admin.collections.entries.index', $collection)
            ->with('success', 'Entry deleted successfully.');
    }

    public function reorder(Request $request, Collection $collection)
    {
        foreach ($request->entry_ids ?? [] as $i => $id) {
            CollectionEntry::where('collection_id', $collection->id)
                ->where('id', $id)
                ->update(['position' => $i]);
        }

        return response()->noContent();
    }
}
