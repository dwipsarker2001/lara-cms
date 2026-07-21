<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\CollectionEntry;

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
}
