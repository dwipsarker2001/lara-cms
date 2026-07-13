<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
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
        return view('admin.posts.create');
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
            'hero_img' => 'nullable|string',
            'banner_img' => 'nullable|string',
            'published' => 'boolean',
        ]);

        $data['published'] = $request->boolean('published', true);
        $data['tags'] = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];
        $data['position'] = Post::max('position') + 1;

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Post created.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', ['post' => $post]);
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
            'hero_img' => 'nullable|string',
            'banner_img' => 'nullable|string',
            'published' => 'boolean',
        ]);

        $data['published'] = $request->boolean('published', true);
        $data['tags'] = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];

        $post->update($data);

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
}
