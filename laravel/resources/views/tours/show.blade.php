@extends('layouts.main')

@section('title', $tour->title . ' - Touristik')
@section('meta_description', Str::limit(strip_tags($tour->description), 155))
@section('og_image', $tour->image_url ?? 'https://touristik.am/img/og-image.jpg')

@push('styles')
<style>
    .tour-hero { position: relative; height: 420px; display: flex; align-items: flex-end; overflow: hidden; }
    .tour-hero-bg { position: absolute; inset: 0; background-size: cover; background-position: center; }
    .tour-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.1) 60%); }
    .tour-hero-content { position: relative; z-index: 2; padding: 2rem; max-width: 1200px; margin: 0 auto; width: 100%; }
    .tour-hero-content h1 { font-size: 2.5rem; color: #fff; margin-bottom: 0.5rem; text-shadow: 0 2px 8px rgba(0,0,0,0.3); }
    .tour-meta { display: flex; gap: 1.5rem; flex-wrap: wrap; }
    .tour-meta-item { display: flex; align-items: center; gap: 0.4rem; color: rgba(255,255,255,0.9); font-size: 0.95rem; }
    .tour-meta-item span { font-size: 1.1rem; }
    .tour-detail { max-width: 900px; margin: 0 auto; padding: 2.5rem 2rem 4rem; }
    .tour-price-bar { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 1.5rem 2rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; box-shadow: var(--shadow); flex-wrap: wrap; gap: 1rem; }
    .tour-price-bar .price { font-size: 2rem; font-weight: 700; color: #FF6B35; }
    .tour-price-bar .price-label { font-size: 0.85rem; color: var(--text-secondary); display: block; }
    .btn-book { display: inline-block; padding: 0.9rem 2.5rem; background: #FF6B35; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 1rem; transition: background 0.2s; border: none; cursor: pointer; }
    .btn-book:hover { background: #e55a2b; }
    .tour-description { font-size: 1.05rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 2.5rem; }
    .tour-itinerary { margin-bottom: 2.5rem; }
    .tour-itinerary h2 { font-size: 1.4rem; color: var(--text-heading); margin-bottom: 1.2rem; }
    .itinerary-item { display: flex; gap: 1.2rem; margin-bottom: 1.5rem; }
    .itinerary-day { flex-shrink: 0; width: 48px; height: 48px; background: linear-gradient(135deg, #FF6B35, #f7a072); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 0.9rem; }
    .itinerary-content { flex: 1; padding-top: 0.3rem; }
    .itinerary-content h4 { font-size: 1.05rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .itinerary-content p { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; margin: 0; }
    .tour-actions { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 2rem; }
    .btn-back { display: inline-block; padding: 0.9rem 2rem; background: transparent; color: var(--text-heading); text-decoration: none; border-radius: 8px; font-weight: 600; border: 2px solid var(--border-color); transition: all 0.2s; }
    .btn-back:hover { border-color: #FF6B35; color: #FF6B35; }
    @media (max-width: 768px) {
        .tour-hero { height: 300px; }
        .tour-hero-content h1 { font-size: 1.8rem; }
        .tour-price-bar { flex-direction: column; text-align: center; }
    }
</style>
@endpush

@section('content')
<div class="tour-hero">
    <div class="tour-hero-bg lazy-bg" data-bg="{{ $tour->image_url }}"></div>
    <div class="tour-hero-overlay"></div>
    <div class="tour-hero-content reveal">
        <h1>{{ $tour->title }}</h1>
        <div class="tour-meta">
            @if($tour->duration)
            <div class="tour-meta-item"><span>&#128337;</span> {{ $tour->duration }}</div>
            @endif
            <div class="tour-meta-item"><span>&#128205;</span> {{ ucfirst($tour->type) }} Tour</div>
            @if($tour->destination)
            <div class="tour-meta-item"><span>&#127758;</span> {{ $tour->destination->name }}</div>
            @endif
        </div>
    </div>
</div>

<div class="tour-detail">
    @if($tour->price_from > 0)
    <div class="tour-price-bar reveal">
        <div>
            <span class="price-label">Starting from</span>
            <span class="price">${{ number_format($tour->price_from, 0) }}</span>
            <span class="price-label">per person</span>
        </div>
        <a href="/contact" class="btn-book">Book This Tour</a>
    </div>
    @endif

    <div class="tour-description reveal">
        {!! nl2br(e($tour->description)) !!}
    </div>

    @if($tour->itinerary && count($tour->itinerary) > 0)
    <div class="tour-itinerary reveal">
        <h2>Itinerary</h2>
        @foreach($tour->itinerary as $i => $item)
        <div class="itinerary-item">
            <div class="itinerary-day">Day {{ $i + 1 }}</div>
            <div class="itinerary-content">
                <h4>{{ $item['title'] ?? 'Day ' . ($i + 1) }}</h4>
                <p>{{ $item['description'] ?? '' }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="tour-actions reveal">
        <a href="/contact" class="btn-book">Book This Tour</a>
        <a href="/tours" class="btn-back">&larr; All Tours</a>
    </div>
</div>
@endsection
