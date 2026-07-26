<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\BlockPreview;
use Illuminate\Http\Request;

class PreviewController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'sections' => 'present|array',
            'sections.*._key' => 'required|string',
            'sections.*.name' => 'required|string',
            'sections.*.data' => 'required',
            'entry_data' => 'nullable|array',
        ]);

        // Build a lightweight $page stub so blocks can read custom field values
        // via $page->data just as they would with a real CollectionEntry model.
        $page = null;
        if (! empty($data['entry_data'])) {
            $page = new \stdClass;
            $page->data = $data['entry_data'];
        }

        $html = BlockPreview::render($data['sections'], withGlobals: false, page: $page, isEditor: true);

        return response()->json(['html' => $html]);
    }
}
