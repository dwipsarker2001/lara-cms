<?php

namespace App\Http\Controllers\Admin;

use App\Blocks\BlockRegistry;
use App\Http\Controllers\Controller;
use App\Models\Layout;
use App\Models\Post;
use App\Models\Taxonomy;
use App\Support\Sections;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('date', 'desc')->orderBy('position')->get();

        return view('admin.posts.index', ['posts' => $posts]);
    }

    public function create()
    {
        $taxonomies = Taxonomy::with('terms')->orderBy('title')->get();

        return view('admin.posts.create', ['taxonomies' => $taxonomies, 'selectedTerms' => []]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug',
            'excerpt' => 'nullable|string',
            'body' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'tags' => 'nullable|string',
            'term_ids' => 'nullable|string',
            'hero_img' => 'nullable|string',
            'banner_img' => 'nullable|string',
            'layout_id' => 'nullable|exists:layouts,id,collection,blog',
            'published' => 'boolean',
        ]);

        $data['published'] = $request->boolean('published', true);
        $data['tags'] = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];
        $data['position'] = Post::max('position') + 1;

        $termIds = $request->term_ids ? array_map('trim', explode(',', $request->term_ids)) : [];

        if ($request->layout_id) {
            $layout = Layout::findOrFail($request->layout_id);
            $data['sections'] = $layout->sections ?? [];
        }

        unset($data['layout_id'], $data['term_ids']);

        $post = Post::create($data);

        $post->terms()->sync($termIds);

        return redirect()->route('admin.posts.index')->with('success', 'Post created.');
    }

    public function edit(Post $post)
    {
        $taxonomies = Taxonomy::with('terms')->orderBy('title')->get();

        $post->load('terms');
        $selectedTerms = $post->terms->pluck('id')->toArray();
        $termTitles = $post->terms->pluck('title')->toArray();
        $customTags = array_values(array_filter($post->tags ?? [], fn ($t) => ! in_array($t, $termTitles)));

        return view('admin.posts.edit', [
            'post' => $post,
            'taxonomies' => $taxonomies,
            'selectedTerms' => $selectedTerms,
            'customTags' => $customTags,
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,'.$post->id,
            'excerpt' => 'nullable|string',
            'body' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'tags' => 'nullable|string',
            'term_ids' => 'nullable|string',
            'hero_img' => 'nullable|string',
            'banner_img' => 'nullable|string',
            'published' => 'boolean',
        ]);

        $data['published'] = $request->boolean('published', true);
        $data['tags'] = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];

        $termIds = $request->term_ids ? array_map('trim', explode(',', $request->term_ids)) : [];

        unset($data['term_ids']);

        $post->update($data);

        $post->terms()->sync($termIds);

        return redirect()->route('admin.posts.index')->with('success', 'Post updated.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'exists:posts,id',
        ]);

        foreach ($request->post_ids as $index => $id) {
            Post::where('id', $id)->update(['position' => $index]);
        }

        return response()->json(['message' => 'Reordered.']);
    }

    public function editor(Post $post)
    {
        $registry = app(BlockRegistry::class);

        $blockList = collect($registry->pickerList())->map(function ($item) use ($registry) {
            $block = $registry->get($item['name']);
            $section = Sections::createDefaultSection($item['name']);
            $html = '';
            if ($block && $section && view()->exists($block->view())) {
                $html = view($block->view(), [
                    'data' => $section['data'],
                    '_key' => '',
                    'preview' => true,
                ])->render();
            }

            return [...$item, 'previewHtml' => $html];
        })->all();

        return view('admin.pages.editor', [
            'page' => $post,
            'blockSchemas' => $registry->schemas(),
            'blockList' => $blockList,
            'pages' => [],
            'homeGlobals' => [],
        ])->with('editorSaveRoute', route('admin.posts.update-sections', $post));
    }

    public function updateSections(Request $request, Post $post)
    {
        $request->validate([
            'sections' => 'present|array',
            'sections.*._key' => 'required|string',
            'sections.*.name' => 'required|string',
            'sections.*.data' => 'required',
        ]);

        $post->update(['sections' => $request->sections]);

        return response()->json(['message' => 'Sections saved.']);
    }
}
