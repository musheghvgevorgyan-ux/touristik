@extends('layouts.main')

@section('title', 'Blog - Touristik')
@section('meta_description', 'Travel tips, destination guides, and news from Touristik Travel Club. Discover Armenia and explore the world.')

@push('styles')
<style>
    .blog-page { max-width: 1200px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .blog-header { text-align: center; margin-bottom: 2.5rem; }
    .blog-header h1 { font-size: 2.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .blog-header p { color: var(--text-secondary); font-size: 1.1rem; }
    .blog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 2rem; }
    .blog-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; }
    .blog-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
    .blog-card-img { height: 200px; background-size: cover; background-position: center; background-color: #f0f0f0; }
    .blog-card-body { padding: 1.5rem; }
    .blog-card-body h3 { font-size: 1.15rem; color: var(--text-heading); margin-bottom: 0.5rem; line-height: 1.4; }
    .blog-card-body h3 a { color: inherit; text-decoration: none; }
    .blog-card-body h3 a:hover { color: #FF6B35; }
    .blog-card-meta { font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.6rem; }
    .blog-card-excerpt { color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .blog-card-link { display: inline-block; margin-top: 0.8rem; color: #FF6B35; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
    .blog-card-link:hover { text-decoration: underline; }
    .blog-empty { text-align: center; padding: 4rem 0; color: var(--text-secondary); }
    .blog-empty h2 { color: var(--text-heading); margin-bottom: 0.5rem; }
    @media (max-width: 768px) { .blog-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="blog-page">
    <div class="blog-header reveal">
        <h1>Travel Blog</h1>
        <p>Tips, guides, and stories from our travel experts</p>
    </div>

    @if($posts->count() > 0)
    <div class="blog-grid">
        @foreach($posts as $post)
        <article class="blog-card reveal">
            @if($post->image_url)
            <a href="/blog/{{ $post->slug }}"><div class="blog-card-img" style="background-image:url('{{ $post->image_url }}')"></div></a>
            @endif
            <div class="blog-card-body">
                <div class="blog-card-meta">{{ $post->published_at->format('M d, Y') }} &middot; by {{ $post->author->name ?? 'Touristik' }}</div>
                <h3><a href="/blog/{{ $post->slug }}">{{ $post->title }}</a></h3>
                <p class="blog-card-excerpt">{{ $post->excerpt ?? Str::limit(strip_tags($post->body), 150) }}</p>
                <a href="/blog/{{ $post->slug }}" class="blog-card-link">Read More &rarr;</a>
            </div>
        </article>
        @endforeach
    </div>
    @if($posts->hasPages())
    <div style="margin-top:2rem;text-align:center;">{{ $posts->links() }}</div>
    @endif
    @else
    <div class="blog-empty reveal">
        <h2>Coming Soon</h2>
        <p>We're preparing exciting travel stories and guides. Check back soon!</p>
    </div>
    @endif
</div>
@endsection
