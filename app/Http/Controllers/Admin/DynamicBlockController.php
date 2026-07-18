<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DynamicBlock;
use Illuminate\Http\Request;

class DynamicBlockController extends Controller
{
    public function index()
    {
        $blocks = DynamicBlock::orderBy('name')->get();

        return view('admin.dynamic-blocks.index', compact('blocks'));
    }

    public function create()
    {
        return view('admin.dynamic-blocks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:dynamic_blocks,name',
            'label' => 'required|string|max:255',
            'global' => 'boolean',
        ]);

        $data['global'] = $request->boolean('global');
        $data['background'] = true;
        $data['fields'] = [];
        $data['template'] = '';

        DynamicBlock::create($data);

        return redirect()->route('admin.dynamic-blocks.index')
            ->with('success', 'Block created.');
    }

    public function edit(DynamicBlock $dynamicBlock)
    {
        return view('admin.dynamic-blocks.edit', ['block' => $dynamicBlock]);
    }

    public function update(Request $request, DynamicBlock $dynamicBlock)
    {
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'global' => 'boolean',
        ]);

        $data['global'] = $request->boolean('global');

        $dynamicBlock->update($data);

        return redirect()->route('admin.dynamic-blocks.index')
            ->with('success', 'Block updated.');
    }

    public function editor(DynamicBlock $dynamicBlock)
    {
        return view('admin.dynamic-blocks.editor', ['block' => $dynamicBlock]);
    }

    public function updateEditor(Request $request, DynamicBlock $dynamicBlock)
    {
        $data = $request->validate([
            'fields' => 'required|json',
            'template' => 'required|string',
        ]);

        $data['fields'] = json_decode($data['fields'], true);

        $dynamicBlock->update($data);

        return redirect()->route('admin.dynamic-blocks.editor', $dynamicBlock)
            ->with('success', 'Block editor updated.');
    }

    public function destroy(DynamicBlock $dynamicBlock)
    {
        $dynamicBlock->delete();

        return redirect()->route('admin.dynamic-blocks.index')
            ->with('success', 'Block deleted.');
    }
}
