<?php

namespace App\Http\Controllers\Admin;

use App\Blocks\BlockRegistry;
use App\Http\Controllers\Controller;
use App\Models\Layout;
use App\Models\Page;
use App\Support\Sections;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    public function index()
    {
        $layouts = Layout::orderBy('position')->orderBy('name')->get();

        return view('admin.layouts.index', ['layouts' => $layouts]);
    }

    public function create()
    {
        return view('admin.layouts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'collection' => 'required|string|in:page,blog,package',
        ]);

        $data['position'] = Layout::max('position') + 1;

        Layout::create($data);

        return redirect()->route('admin.layouts.index')->with('success', 'Layout created.');
    }

    public function edit(Layout $layout)
    {
        return view('admin.layouts.edit', ['layout' => $layout]);
    }

    public function update(Request $request, Layout $layout)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'collection' => 'required|string|in:page,blog,package',
        ]);

        $layout->update($data);

        return redirect()->route('admin.layouts.index')->with('success', 'Layout updated.');
    }

    public function destroy(Layout $layout)
    {
        $layout->delete();

        return redirect()->route('admin.layouts.index')->with('success', 'Layout deleted.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'layout_ids' => 'required|array',
            'layout_ids.*' => 'exists:layouts,id',
        ]);

        foreach ($request->layout_ids as $index => $id) {
            Layout::where('id', $id)->update(['position' => $index]);
        }

        return response()->json(['message' => 'Reordered.']);
    }

    public function editor(Layout $layout)
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
            'page' => $layout,
            'editorSaveRoute' => route('admin.layouts.update-sections', $layout),
            'blockSchemas' => $registry->schemas(),
            'blockList' => $blockList,
            'pages' => Page::orderBy('position')->orderBy('title')->get(['id', 'slug', 'title'])->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'route' => $p->slug === 'home' ? '/' : '/'.$p->slug,
            ]),
            'homeGlobals' => [],
        ]);
    }

    public function updateSections(Request $request, Layout $layout)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*._key' => 'required|string',
            'sections.*.name' => 'required|string',
            'sections.*.data' => 'required',
        ]);

        $layout->update(['sections' => $request->sections]);

        return response()->json(['message' => 'Sections saved.']);
    }
}
