<?php

namespace App\Http\Controllers\Admin;

use App\Blocks\BlockRegistry;
use App\Http\Controllers\Controller;
use App\Models\Layout;
use App\Models\Package;
use App\Support\Sections;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('position')->get();

        return view('admin.packages.index', ['packages' => $packages]);
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:packages,slug',
            'excerpt' => 'nullable|string',
            'body' => 'nullable|string',
            'hero_img' => 'nullable|string',
            'banner_img' => 'nullable|string',
            'price' => 'nullable|numeric',
            'original_price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'duration' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'discount_badge' => 'nullable|string|max:255',
            'published' => 'boolean',
            'layout_id' => 'nullable|exists:layouts,id,collection,package',
        ]);

        $data['published'] = $request->boolean('published', true);
        $data['position'] = Package::max('position') + 1;

        if ($request->layout_id) {
            $layout = Layout::findOrFail($request->layout_id);
            $data['blocks'] = [...($layout->sections ?? []), ...Sections::injectGlobals()];
        } else {
            $data['blocks'] = Sections::injectGlobals();
        }

        unset($data['layout_id']);

        Package::create($data);

        return redirect()->route('admin.packages.index')->with('success', 'Package created.');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', ['package' => $package]);
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:packages,slug,'.$package->id,
            'excerpt' => 'nullable|string',
            'body' => 'nullable|string',
            'hero_img' => 'nullable|string',
            'banner_img' => 'nullable|string',
            'price' => 'nullable|numeric',
            'original_price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'duration' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'discount_badge' => 'nullable|string|max:255',
            'published' => 'boolean',
        ]);

        $data['published'] = $request->boolean('published', true);

        $package->update($data);

        return redirect()->route('admin.packages.index')->with('success', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')->with('success', 'Package deleted.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'package_ids' => 'required|array',
            'package_ids.*' => 'exists:packages,id',
        ]);

        foreach ($request->package_ids as $index => $id) {
            Package::where('id', $id)->update(['position' => $index]);
        }

        return response()->json(['message' => 'Reordered.']);
    }

    public function editor(Package $package)
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
            'page' => $package,
            'blockSchemas' => $registry->schemas(),
            'blockList' => $blockList,
            'pages' => [],
            'homeGlobals' => [],
        ])->with('editorSaveRoute', route('admin.packages.update-sections', $package));
    }

    public function updateSections(Request $request, Package $package)
    {
        $request->validate([
            'sections' => 'present|array',
            'sections.*._key' => 'required|string',
            'sections.*.name' => 'required|string',
            'sections.*.data' => 'required',
        ]);

        $package->update(['blocks' => $request->sections]);

        return response()->json(['message' => 'Sections saved.']);
    }
}
