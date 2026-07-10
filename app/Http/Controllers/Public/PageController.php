<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function home()
    {
        $page = Page::where('slug', 'home')->where('published', true)->firstOrFail();

        return view('public.page', ['page' => $page]);
    }

    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->where('published', true)->firstOrFail();

        return view('public.page', ['page' => $page]);
    }
}
