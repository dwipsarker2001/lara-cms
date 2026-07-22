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
        ]);

        $html = BlockPreview::render($data['sections'], withGlobals: false);

        return response()->json(['html' => $html]);
    }
}
