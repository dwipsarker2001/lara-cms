<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\CollectionEntry;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $page = CollectionEntry::where('slug', 'home')->where('published', true)->first();

        if (! $page) {
            $page = CollectionEntry::where('published', true)->firstOrFail();
        }

        return view('public.page', ['page' => $page]);
    }

    public function show(string $slug)
    {
        $page = CollectionEntry::where('slug', $slug)->where('published', true)->firstOrFail();

        return view('public.page', ['page' => $page]);
    }

    public function showCollectionEntry(string $collectionSlug, string $slug)
    {
        if ($collectionSlug === 'pages') {
            abort(404);
        }

        $collection = Collection::where('slug', $collectionSlug)->firstOrFail();

        $entry = $collection->entries()
            ->where('slug', $slug)
            ->where('published', true)
            ->firstOrFail();

        return view('public.page', ['page' => $entry]);
    }

    public function resolve(Request $request)
    {
        $segments = array_values(array_filter(explode('/', trim($request->path(), '/'))));

        if (empty($segments)) {
            return $this->home();
        }

        if (count($segments) === 1) {
            return $this->show($segments[0]);
        }

        if (count($segments) === 2) {
            return $this->showCollectionEntry($segments[0], $segments[1]);
        }

        abort(404);
    }
}
