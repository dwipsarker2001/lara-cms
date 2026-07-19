<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessEmailFileJob;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    // Show upload form
    public function index()
    {
        return view('marketing.verify.index');
    }

    // Handle file upload
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:txt|max:2048',
        ]);

        // Store file
        $fileName = time().'_'.$request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storeAs('uploads', $fileName, 'public');

        // Dispatch queue job
        ProcessEmailFileJob::dispatch($fileName);

        return back()->with('success', 'File uploaded and added to processing queue!')
            ->with('file', $fileName);
    }
}
