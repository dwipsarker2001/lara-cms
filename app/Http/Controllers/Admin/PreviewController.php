<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\BlockPreview;
use Illuminate\Http\Request;

class PreviewController extends Controller
{
    public function render(Request $request)
    {
        $request->validate([
            'sections' => 'required|array',
        ]);

        $html = BlockPreview::render($request->sections, withGlobals: true);

        return response()->json(['html' => $html]);
    }
}
