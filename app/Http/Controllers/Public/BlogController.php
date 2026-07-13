<?php

namespace App\Http\Controllers\Public;

use App\Blocks\BlockRegistry;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\Sections;

class BlogController extends Controller
{
    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)->where('published', true)->firstOrFail();

        $registry = app(BlockRegistry::class);
        $globals = Sections::injectGlobals();

        return view('public.post', compact('post', 'registry', 'globals'));
    }
}
