<?php

namespace App\Http\Controllers\Admin;

use App\Blocks\BlockRegistry;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Models\Layout;
use App\Models\Page;
use App\Support\Sections;
use Illuminate\Http\Request;

class CollectionEntryController extends Controller
{
    public function index(Collection $collection)
    {
        $entries = $collection->entries()->with('page')->orderBy('position')->get();

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
            'slug' => 'required|string|max:255|unique:pages,slug',
            'meta' => 'nullable|array',
            'layout_id' => 'nullable|exists:layouts,id,collection,page',
            'published' => 'boolean',
        ]);

        $pageData = [
            'title' => $data['data']['title'],
            'slug' => $data['slug'],
            'published' => $request->boolean('published', true),
            'meta' => array_merge($request->meta ?? [], [
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
            ]),
        ];

        if ($request->layout_id) {
            $layout = Layout::findOrFail($request->layout_id);
            $pageData['sections'] = [...($layout->sections ?? []), ...Sections::injectGlobals()];
        } else {
            $pageData['sections'] = Sections::injectGlobals();
        }

        $pageData['position'] = Page::max('position') + 1;

        $page = Page::create($pageData);

        $entry = $collection->entries()->create([
            'data' => $data['data'] ?? [],
            'page_id' => $page->id,
            'sections' => $page->sections,
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

        if ($entry->page_id && isset($data['data']['title'])) {
            $entry->page->update(['title' => $data['data']['title']]);
        }

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

        if ($entry->page_id) {
            $entry->page->update(['sections' => $request->sections]);
        }

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

        $page = $entry->page;
        $entry->delete();
        if ($page) {
            $page->delete();
        }

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
