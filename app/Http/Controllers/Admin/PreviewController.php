<?php

namespace App\Http\Controllers\Admin;

use App\Blocks\BlockRegistry;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\BlockPreview;
use App\Support\Sections;
use Illuminate\Http\Request;

class PreviewController extends Controller
{
    public function render(Request $request)
    {
        $request->validate([
            'sections' => 'present|array',
        ]);

        $page = null;
        if ($request->post_id) {
            $page = Post::find($request->post_id);
        }

        $html = BlockPreview::render($request->sections, withGlobals: false, page: $page);

        return response()->json(['html' => $html]);
    }

    public function blockPreview(string $blockName)
    {
        $registry = app(BlockRegistry::class);
        $block = $registry->get($blockName);

        if (! $block) {
            abort(404);
        }

        $data = Sections::defaultData($block->resolvedFields());

        return view('admin.block-preview-iframe', [
            'block' => $block,
            'data' => $data,
        ]);
    }
}
