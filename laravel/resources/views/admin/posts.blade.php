@extends('layouts.main')

@section('title', 'Blog Posts - Admin')

@push('styles')
<style>
    .admin-header { margin-bottom: 2rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .admin-header p { color: #6c757d; }
    .admin-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; }
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { text-align: left; padding: 0.8rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.85rem; text-transform: uppercase; }
    .admin-table td { padding: 0.8rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.95rem; }
    .admin-table tr:hover td { background: #f8f9fa; }
    .badge-published { background: #d4edda; color: #155724; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
    .badge-draft { background: #fff3cd; color: #856404; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
    .new-post-form { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 2rem; margin-bottom: 2rem; }
    .new-post-form h3 { margin: 0 0 1.2rem; color: var(--text-heading); }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.3rem; font-size: 0.9rem; color: #6c757d; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.7rem 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 0.95rem; }
    .form-group textarea { min-height: 200px; font-family: inherit; }
    .form-row { display: flex; gap: 1rem; }
    .form-row .form-group { flex: 1; }
    .btn-publish { padding: 0.8rem 2rem; background: #FF6B35; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .btn-sm { padding: 0.3rem 0.8rem; border: 1px solid #ddd; border-radius: 4px; background: #fff; cursor: pointer; font-size: 0.8rem; text-decoration: none; color: #333; }
    .btn-sm:hover { background: #f0f0f0; }
    .btn-danger { color: #dc3545; border-color: #dc3545; }
</style>
@endpush

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:2rem;">
    <div class="admin-header">
        <h1>Blog Posts</h1>
        <p>Create and manage blog articles</p>
    </div>

    <div class="new-post-form">
        <h3>New Post</h3>
        <form method="POST" action="/admin/posts">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" required placeholder="Post title">
                </div>
                <div class="form-group" style="max-width:150px;">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="excerpt">Excerpt (optional)</label>
                <input type="text" name="excerpt" id="excerpt" placeholder="Brief summary for listing pages">
            </div>
            <div class="form-group">
                <label for="image_url">Image URL (optional)</label>
                <input type="url" name="image_url" id="image_url" placeholder="https://images.unsplash.com/...">
            </div>
            <div class="form-group">
                <label for="body">Content (HTML supported)</label>
                <textarea name="body" id="body" required placeholder="Write your article here..."></textarea>
            </div>
            <button type="submit" class="btn-publish">Publish Post</button>
        </form>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td><strong>{{ $post->title }}</strong></td>
                    <td><span class="badge-{{ $post->status }}">{{ ucfirst($post->status) }}</span></td>
                    <td>{{ $post->published_at ? $post->published_at->format('M d, Y') : ($post->created_at ? $post->created_at->format('M d, Y') : '—') }}</td>
                    <td>
                        <a href="/admin/posts/{{ $post->id }}/edit" class="btn-sm">Edit</a>
                        @if($post->status === 'published')
                        <a href="/blog/{{ $post->slug }}" target="_blank" class="btn-sm">View</a>
                        @endif
                        <form method="POST" action="/admin/posts/delete" style="display:inline;">
                            @csrf
                            <input type="hidden" name="id" value="{{ $post->id }}">
                            <button type="submit" class="btn-sm btn-danger" onclick="return confirm('Delete this post?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#999;padding:2rem;">No posts yet. Create your first article above!</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
    <div style="margin-top:1rem;">{{ $posts->links() }}</div>
    @endif

    <div style="margin-top:1.5rem;"><a href="/admin" style="color:#FF6B35;text-decoration:none;font-weight:600;">&larr; Back to Dashboard</a></div>
</div>
@endsection
