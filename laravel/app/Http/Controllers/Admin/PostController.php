<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderByDesc('created_at')->paginate(20);
        return view('admin.posts', compact('posts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'image_url' => 'nullable|url|max:500',
            'status' => 'required|in:draft,published',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        Post::create($validated);

        return redirect('/admin/posts')->with('success', 'Post created!');
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.post-edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'image_url' => 'nullable|url|max:500',
            'status' => 'required|in:draft,published',
        ]);

        if ($validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        return redirect('/admin/posts')->with('success', 'Post updated!');
    }

    public function delete(Request $request)
    {
        Post::findOrFail($request->id)->delete();
        return redirect('/admin/posts')->with('success', 'Post deleted.');
    }
}
