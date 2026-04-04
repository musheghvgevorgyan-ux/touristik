<?php

namespace App\Http\Controllers;

use App\Models\Post;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }
}
