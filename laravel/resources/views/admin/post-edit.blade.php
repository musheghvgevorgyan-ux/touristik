@extends('layouts.main')

@section('title', 'Edit Post - Admin')

@push('styles')
<style>
    .edit-form { max-width: 800px; margin: 0 auto; padding: 2rem; }
    .edit-form h1 { font-size: 1.8rem; color: var(--text-heading); margin-bottom: 1.5rem; }
    .form-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 2rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.3rem; font-size: 0.9rem; color: #6c757d; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.7rem 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 0.95rem; }
    .form-group textarea { min-height: 300px; font-family: inherit; }
    .form-row { display: flex; gap: 1rem; }
    .form-row .form-group { flex: 1; }
    .btn-save { padding: 0.8rem 2rem; background: #FF6B35; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
</style>
@endpush

@section('content')
<div class="edit-form">
    <h1>Edit Post</h1>
    <div class="form-card">
        <form method="POST" action="/admin/posts/{{ $post->id }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" value="{{ old('title', $post->title) }}" required>
                </div>
                <div class="form-group" style="max-width:150px;">
                    <label for="status">Status</label>
                    <select name="status">
                        <option value="draft" {{ $post->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ $post->status === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="excerpt">Excerpt</label>
                <input type="text" name="excerpt" value="{{ old('excerpt', $post->excerpt) }}">
            </div>
            <div class="form-group">
                <label for="image_url">Image URL</label>
                <input type="url" name="image_url" value="{{ old('image_url', $post->image_url) }}">
            </div>
            <div class="form-group">
                <label for="body">Content (HTML supported)</label>
                <textarea name="body">{{ old('body', $post->body) }}</textarea>
            </div>
            <button type="submit" class="btn-save">Save Changes</button>
        </form>
    </div>
    <div style="margin-top:1rem;"><a href="/admin/posts" style="color:#FF6B35;text-decoration:none;font-weight:600;">&larr; Back to Posts</a></div>
</div>
@endsection
