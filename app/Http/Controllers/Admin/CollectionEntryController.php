<?php

namespace App\Http\Controllers\Admin;

use App\Blocks\BlockRegistry;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Models\Layout;
use App\Support\Sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CollectionEntryController extends Controller
{
    public function index(Collection $collection)
    {
        $entries = $collection->entries()->orderBy('position')->get();

        return view('admin.collections.entries.index', compact('collection', 'entries'));
    }

    public function create(Collection $collection)
    {
        $admins = Admin::orderBy('name')->get();
        $layouts = Layout::where('collection', 'page')->orderBy('position')->orderBy('name')->get();

        return view('admin.collections.entries.create', compact('collection', 'admins', 'layouts'));
    }

    public function store(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'data' => 'required|array',
            'data.title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:collection_entries,slug',
            'meta' => 'nullable|array',
            'layout_id' => 'nullable|exists:layouts,id',
            'published' => 'boolean',
        ]);

        $entryMeta = array_merge($request->meta ?? [], [
            'metaTitleSource' => 'From Field',
            'metaDescriptionSource' => 'Inherit',
            'canonicalUrlSource' => 'Inherit',
            'schemaSource' => 'Inherit',
            'maxSnippetSource' => 'Inherit',
            'maxVideoPreviewSource' => 'Inherit',
            'socialImageSource' => 'Inherit',
            'xHandleSource' => 'Inherit',
            'xCardTitleSource' => 'Inherit',
            'xCardDescriptionSource' => 'Inherit',
        ]);

        // Copy sections from selected collection reference if exists
        $copiedSections = null;
        foreach ($collection->fields ?? [] as $field) {
            if (($field['type'] ?? '') === 'collection') {
                $key = $field['template'] ?? null;
                $val = $request->input("data.{$key}");
                if ($key && ! empty($val)) {
                    $selectedEntry = CollectionEntry::find($val);
                    if ($selectedEntry) {
                        $copiedSections = $selectedEntry->sections ?? [];
                        break;
                    }
                }
            }
        }

        $sections = $copiedSections ?? [];

        $entry = $collection->entries()->create([
            'data' => $request->input('data', []),
            'slug' => $data['slug'],
            'published' => $request->boolean('published', true),
            'meta' => $entryMeta,
            'sections' => $sections,
            'position' => $collection->entries()->max('position') + 1,
        ]);

        return redirect()->route('admin.collections.entries.editor', [$collection, $entry]);
    }

    public function edit(Collection $collection, CollectionEntry $entry)
    {
        abort_if((int) $entry->collection_id !== (int) $collection->id, 404);
        $admins = Admin::orderBy('name')->get();

        return view('admin.collections.entries.edit', compact('collection', 'entry', 'admins'));
    }

    public function update(Request $request, Collection $collection, CollectionEntry $entry)
    {
        abort_if((int) $entry->collection_id !== (int) $collection->id, 404);

        $data = $request->validate([
            'data' => 'nullable|array',
            'slug' => 'required|string|max:255|unique:collection_entries,slug,'.$entry->id,
            'meta' => 'nullable|array',
            'published' => 'boolean',
        ]);

        $entryData = [
            'data' => $data['data'] ?? $entry->data,
            'slug' => $data['slug'],
            'published' => $request->boolean('published', true),
        ];

        if ($request->has('meta')) {
            $entryData['meta'] = $request->meta;
        }

        $entry->update($entryData);

        return redirect()->route('admin.collections.entries.index', $collection)
            ->with('success', 'Entry updated successfully.');
    }

    public function editor(Collection $collection, CollectionEntry $entry)
    {
        abort_if((int) $entry->collection_id !== (int) $collection->id, 404);

        $registry = app(BlockRegistry::class);

        $blockList = collect($registry->pickerList())->map(function ($item) use ($registry) {
            $block = $registry->get($item['name']);
            $section = Sections::createDefaultSection($item['name']);
            $html = '';
            if ($block && $section) {
                $html = $block->render(
                    data: $section['data'],
                    _key: '',
                    preview: true,
                );
            }

            return [...$item, 'previewHtml' => $html];
        })->all();

        $homeGlobals = Sections::injectGlobals();

        $pages = collect();
        if (Schema::hasTable('collection_entries')) {
            $pages = CollectionEntry::whereHas('collection', fn ($q) => $q->where('slug', 'pages'))
                ->orderBy('position')
                ->get(['id', 'slug', 'data'])
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'route' => $p->route(),
                ]);
        }

        return view('admin.collections.entries.editor', [
            'collection' => $collection,
            'entry' => $entry,
            'blockSchemas' => $registry->schemas(),
            'homeGlobals' => $homeGlobals,
            'blockList' => $blockList,
            'pages' => $pages,

        ]);
    }

    public function updateSections(Request $request, Collection $collection, CollectionEntry $entry)
    {
        abort_if((int) $entry->collection_id !== (int) $collection->id, 404);

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
            $home = CollectionEntry::where('slug', 'home')->first();

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
        abort_if((int) $entry->collection_id !== (int) $collection->id, 404);

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
