<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Package;
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

        if ($page->collectionEntry()->exists()) {
            $page->loadMissing('collectionEntry.collection');
            if ($page->collectionEntry->collection->slug !== 'pages') {
                abort(404);
            }
        }

        $package = Package::where('slug', $slug)->first();

        return view('public.page', ['page' => $page, 'package' => $package]);
    }

    public function showCollectionEntry(string $collectionSlug, string $slug)
    {
        if ($collectionSlug === 'pages') {
            abort(404);
        }

        $collection = Collection::where('slug', $collectionSlug)->firstOrFail();

        $entry = $collection->entries()
            ->whereHas('page', function ($query) use ($slug) {
                $query->where('slug', $slug)->where('published', true);
            })->firstOrFail();

        $page = $entry->page;

        return view('public.page', ['page' => $page]);
    }
}
