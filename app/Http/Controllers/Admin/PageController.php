<?php

namespace App\Http\Controllers\Admin;

use App\Blocks\BlockRegistry;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\Sections;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('position')->orderBy('title')->get();

        return view('admin.pages.index', ['pages' => $pages]);
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'published' => 'boolean',
            'meta' => 'nullable|array',
        ]);

        $data['published'] = $request->boolean('published', true);
        $data['meta'] = array_merge($request->meta ?? [], [
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
        $data['sections'] = [];
        $data['position'] = Page::max('position') + 1;

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page created.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', ['page' => $page]);
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,'.$page->id,
            'published' => 'boolean',
        ]);

        $data['published'] = $request->boolean('published', true);

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated.');
    }

    public function destroy(Page $page)
    {
        if ($page->slug === 'home') {
            return back()->with('error', 'Cannot delete the home page.');
        }

        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'page_ids' => 'required|array',
            'page_ids.*' => 'exists:pages,id',
        ]);

        foreach ($request->page_ids as $index => $id) {
            Page::where('id', $id)->update(['position' => $index]);
        }

        return response()->json(['message' => 'Reordered.']);
    }

    public function editor(Page $page)
    {
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

        return view('admin.pages.editor', [
            'page' => $page,
            'blockSchemas' => $registry->schemas(),
            'blockList' => $blockList,
        ]);
    }

    public function updateSections(Request $request, Page $page)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*._key' => 'required|string',
            'sections.*.name' => 'required|string',
            'sections.*.data' => 'required|array',
        ]);

        $page->update(['sections' => $request->sections]);

        $registry = app(BlockRegistry::class);
        $globalNames = $registry->globals()->pluck('name')->toArray();

        if ($page->slug !== 'home') {
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
        }

        return response()->json(['message' => 'Sections saved.']);
    }
}
