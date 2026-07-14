<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;

class BlogController extends Controller
{
    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)->where('published', true)->firstOrFail();

        return view('public.page', ['page' => $post]);
    }
}
