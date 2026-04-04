@extends('layouts.main')

@section('title', $post->title . ' - Touristik Blog')
@section('meta_description', Str::limit(strip_tags($post->excerpt ?? $post->body), 155))
@section('og_image', $post->image_url ?? 'https://touristik.am/img/og-image.jpg')

@push('styles')
<style>
    .post-hero { position: relative; height: 400px; display: flex; align-items: flex-end; overflow: hidden; }
    .post-hero-bg { position: absolute; inset: 0; background-size: cover; background-position: center; }
    .post-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.1) 60%); }
    .post-hero-content { position: relative; z-index: 2; padding: 2rem; max-width: 900px; margin: 0 auto; width: 100%; }
    .post-hero-content h1 { font-size: 2.2rem; color: #fff; margin-bottom: 0.5rem; text-shadow: 0 2px 8px rgba(0,0,0,0.3); line-height: 1.3; }
    .post-meta { color: rgba(255,255,255,0.8); font-size: 0.9rem; }
    .post-body { max-width: 780px; margin: 0 auto; padding: 2.5rem 2rem 4rem; font-size: 1.05rem; line-height: 1.9; color: var(--text-secondary); }
    .post-body p { margin-bottom: 1.5rem; }
    .post-body h2 { color: var(--text-heading); font-size: 1.5rem; margin: 2rem 0 1rem; }
    .post-body h3 { color: var(--text-heading); font-size: 1.2rem; margin: 1.5rem 0 0.8rem; }
    .post-body img { max-width: 100%; border-radius: var(--radius); margin: 1.5rem 0; }
    .post-body blockquote { border-left: 4px solid #FF6B35; padding: 1rem 1.5rem; margin: 1.5rem 0; background: var(--bg-card); border-radius: 0 var(--radius) var(--radius) 0; font-style: italic; }
    .post-nav { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color); }
    .post-nav a { padding: 0.7rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
    .btn-back-blog { background: transparent; color: var(--text-heading); border: 2px solid var(--border-color); }
    .btn-back-blog:hover { border-color: #FF6B35; color: #FF6B35; }
    .related-section { max-width: 900px; margin: 0 auto; padding: 0 2rem 4rem; }
    .related-section h2 { font-size: 1.4rem; color: var(--text-heading); margin-bottom: 1.5rem; }
    .related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; }
    .related-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
    .related-card-img { height: 140px; background-size: cover; background-position: center; }
    .related-card-body { padding: 1rem; }
    .related-card-body h4 { font-size: 0.95rem; margin: 0 0 0.3rem; }
    .related-card-body h4 a { color: var(--text-heading); text-decoration: none; }
    .related-card-body h4 a:hover { color: #FF6B35; }
    .related-card-body span { font-size: 0.8rem; color: var(--text-secondary); }
    @media (max-width: 768px) { .post-hero { height: 280px; } .post-hero-content h1 { font-size: 1.6rem; } }
</style>
@endpush

@section('content')
@if($post->image_url)
<div class="post-hero">
    <div class="post-hero-bg" style="background-image:url('{{ $post->image_url }}')"></div>
    <div class="post-hero-overlay"></div>
    <div class="post-hero-content reveal">
        <h1>{{ $post->title }}</h1>
        <div class="post-meta">{{ $post->published_at->format('M d, Y') }} &middot; by {{ $post->author->name ?? 'Touristik' }}</div>
    </div>
</div>
@endif

<div class="post-body reveal">
    @if(!$post->image_url)
    <h1 style="font-size:2rem;color:var(--text-heading);margin-bottom:0.5rem;">{{ $post->title }}</h1>
    <div style="color:var(--text-secondary);font-size:0.9rem;margin-bottom:2rem;">{{ $post->published_at->format('M d, Y') }} &middot; by {{ $post->author->name ?? 'Touristik' }}</div>
    @endif

    {!! $post->body !!}

    <div class="post-nav">
        <a href="/blog" class="btn-back-blog">&larr; All Posts</a>
    </div>
</div>

@if($related->count() > 0)
<div class="related-section">
    <h2>Related Articles</h2>
    <div class="related-grid">
        @foreach($related as $r)
        <div class="related-card">
            @if($r->image_url)
            <a href="/blog/{{ $r->slug }}"><div class="related-card-img" style="background-image:url('{{ $r->image_url }}')"></div></a>
            @endif
            <div class="related-card-body">
                <h4><a href="/blog/{{ $r->slug }}">{{ $r->title }}</a></h4>
                <span>{{ $r->published_at->format('M d, Y') }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
