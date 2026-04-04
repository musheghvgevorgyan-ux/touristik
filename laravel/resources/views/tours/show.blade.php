@extends('layouts.main')

@section('title', $tour->title . ' - Touristik')
@section('meta_description', Str::limit(strip_tags($tour->description), 155))
@section('og_image', $tour->image_url ?? 'https://touristik.am/img/og-image.jpg')

@push('styles')
<style>
    .tour-hero { position: relative; height: 450px; display: flex; align-items: flex-end; overflow: hidden; }
    .tour-hero-bg { position: absolute; inset: 0; background-size: cover; background-position: center; }
    .tour-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.15) 50%); }
    .tour-hero-content { position: relative; z-index: 2; padding: 2.5rem; max-width: 1200px; margin: 0 auto; width: 100%; }
    .tour-hero-content h1 { font-size: 2.5rem; color: #fff; margin-bottom: 0.5rem; text-shadow: 0 2px 8px rgba(0,0,0,0.3); }
    .tour-meta { display: flex; gap: 1.5rem; flex-wrap: wrap; }
    .tour-meta-item { display: flex; align-items: center; gap: 0.4rem; color: rgba(255,255,255,0.9); font-size: 0.95rem; }
    .tour-meta-item span { font-size: 1.1rem; }
    .tour-body { max-width: 1100px; margin: 0 auto; padding: 2.5rem 2rem 4rem; display: flex; gap: 2.5rem; }
    .tour-main { flex: 1; min-width: 0; }
    .tour-sidebar { flex: 0 0 320px; }

    /* Gallery */
    .tour-gallery { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-bottom: 2rem; border-radius: 12px; overflow: hidden; }
    .tour-gallery img { width: 100%; height: 120px; object-fit: cover; cursor: pointer; transition: transform 0.3s, filter 0.3s; }
    .tour-gallery img:hover { transform: scale(1.05); filter: brightness(1.1); }
    .tour-gallery img:first-child { grid-column: span 2; grid-row: span 2; height: 248px; }

    /* Description */
    .tour-description { font-size: 1.05rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 2rem; }

    /* Itinerary */
    .tour-section-title { font-size: 1.3rem; color: var(--text-heading); margin-bottom: 1.2rem; font-weight: 700; }
    .itinerary-item { display: flex; gap: 1rem; margin-bottom: 1.2rem; }
    .itinerary-day { flex-shrink: 0; width: 44px; height: 44px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 0.8rem; }
    .itinerary-content h4 { font-size: 1rem; color: var(--text-heading); margin-bottom: 0.2rem; }
    .itinerary-content p { color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin: 0; }

    /* Includes/Excludes */
    .tour-includes-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
    .includes-list, .excludes-list { list-style: none; padding: 0; margin: 0; }
    .includes-list li, .excludes-list li { padding: 0.4rem 0; font-size: 0.9rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem; }
    .includes-list li::before { content: '\2713'; color: #22c55e; font-weight: 700; font-size: 1rem; }
    .excludes-list li::before { content: '\2717'; color: #ef4444; font-weight: 700; font-size: 1rem; }

    /* Sidebar */
    .tour-price-card { background: var(--bg-card); border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 1.8rem; position: sticky; top: 100px; border: 1px solid rgba(0,0,0,0.05); }
    .tour-price-card .price { font-size: 2.2rem; font-weight: 800; color: var(--primary); }
    .tour-price-card .price-label { font-size: 0.85rem; color: var(--text-secondary); display: block; margin-bottom: 1rem; }
    .tour-price-card .price-per { font-size: 0.9rem; color: var(--text-secondary); font-weight: 400; }
    .btn-book { display: block; width: 100%; padding: 0.9rem; background: var(--primary); color: #fff; text-align: center; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 1rem; border: none; cursor: pointer; transition: background 0.2s; margin-bottom: 0.6rem; }
    .btn-book:hover { background: var(--primary-dark); }
    .btn-whatsapp { display: block; width: 100%; padding: 0.9rem; background: #25d366; color: #fff; text-align: center; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 1rem; transition: background 0.2s; }
    .btn-whatsapp:hover { background: #1da851; }
    .sidebar-details { margin-top: 1.2rem; border-top: 1px solid rgba(0,0,0,0.06); padding-top: 1rem; }
    .sidebar-detail { display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9rem; }
    .sidebar-detail .label { color: var(--text-secondary); }
    .sidebar-detail .value { font-weight: 600; color: var(--text-heading); }

    /* Inquiry Form */
    .inquiry-form { margin-top: 1.2rem; border-top: 1px solid rgba(0,0,0,0.06); padding-top: 1rem; display: none; }
    .inquiry-form.visible { display: block; }
    .inquiry-form input, .inquiry-form textarea { width: 100%; padding: 0.6rem 0.8rem; border: 1.5px solid rgba(0,0,0,0.1); border-radius: 8px; font-size: 0.9rem; margin-bottom: 0.6rem; font-family: inherit; background: var(--bg-body); color: var(--text-primary); }
    .inquiry-form textarea { height: 80px; resize: vertical; }
    .inquiry-form button { width: 100%; padding: 0.75rem; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem; }
    .inquiry-form button:hover { background: var(--primary-dark); }

    /* Related Tours */
    .related-tours { margin-top: 2.5rem; }
    .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.2rem; }
    .related-card { background: var(--bg-card); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); transition: transform 0.3s; }
    .related-card:hover { transform: translateY(-4px); }
    .related-card img { width: 100%; height: 140px; object-fit: cover; }
    .related-card-body { padding: 1rem; }
    .related-card-body h4 { font-size: 0.95rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .related-card-body .related-meta { font-size: 0.8rem; color: var(--text-secondary); display: flex; justify-content: space-between; }
    .related-card-body .related-meta .rprice { font-weight: 700; color: var(--primary); }

    /* Back link */
    .tour-back { margin-top: 2rem; }
    .tour-back a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .tour-back a:hover { text-decoration: underline; }

    @media (max-width: 900px) {
        .tour-body { flex-direction: column; }
        .tour-sidebar { flex: none; }
        .tour-price-card { position: static; }
        .tour-gallery { grid-template-columns: repeat(2, 1fr); }
        .tour-gallery img:first-child { height: 180px; }
        .tour-includes-grid { grid-template-columns: 1fr; }
        .related-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 768px) {
        .tour-hero { height: 320px; }
        .tour-hero-content h1 { font-size: 1.8rem; }
        .related-grid { grid-template-columns: 1fr; }
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
            <div class="tour-meta-item"><span>&#128205;</span> {{ ucwords(str_replace('_', ' ', $tour->region ?? $tour->type)) }}</div>
            @if($tour->price_from > 0)
            <div class="tour-meta-item"><span>&#128176;</span> From ${{ number_format($tour->price_from, 0) }}</div>
            @endif
        </div>
    </div>
</div>

<div class="tour-body">
    <div class="tour-main">
        {{-- Gallery --}}
        @if($tour->gallery && count($tour->gallery) > 0)
        <div class="tour-gallery reveal">
            @foreach($tour->gallery as $img)
            <img src="{{ $img }}" alt="{{ $tour->title }}" loading="lazy">
            @endforeach
        </div>
        @endif

        {{-- Description --}}
        <div class="tour-description reveal">
            {!! nl2br(e($tour->description)) !!}
        </div>

        {{-- Itinerary --}}
        @if($tour->itinerary && count($tour->itinerary) > 0)
        <div class="reveal">
            <h2 class="tour-section-title">Itinerary</h2>
            @foreach($tour->itinerary as $i => $item)
            <div class="itinerary-item">
                <div class="itinerary-day">{{ $i + 1 }}</div>
                <div class="itinerary-content">
                    <h4>{{ $item['title'] ?? 'Stop ' . ($i + 1) }}</h4>
                    <p>{{ $item['description'] ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Includes / Excludes --}}
        @if(($tour->includes && count($tour->includes) > 0) || ($tour->excludes && count($tour->excludes) > 0))
        <div class="reveal">
            <h2 class="tour-section-title">What's Included</h2>
            <div class="tour-includes-grid">
                @if($tour->includes)
                <ul class="includes-list">
                    @foreach($tour->includes as $item)
                    <li>{{ $item }}</li>
                    @endforeach
                </ul>
                @endif
                @if($tour->excludes)
                <ul class="excludes-list">
                    @foreach($tour->excludes as $item)
                    <li>{{ $item }}</li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
        @endif

        {{-- Related Tours --}}
        @if(isset($relatedTours) && $relatedTours->count() > 0)
        <div class="related-tours reveal">
            <h2 class="tour-section-title">You Might Also Like</h2>
            <div class="related-grid">
                @foreach($relatedTours as $related)
                <a href="/tours/{{ $related->slug }}" class="related-card">
                    <img src="{{ $related->image_url }}" alt="{{ $related->title }}" loading="lazy">
                    <div class="related-card-body">
                        <h4>{{ $related->title }}</h4>
                        <div class="related-meta">
                            <span>{{ $related->duration }}</span>
                            <span class="rprice">From ${{ number_format($related->price_from, 0) }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="tour-back reveal">
            <a href="/tours/ingoing">&larr; Back to Ingoing Tours</a>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="tour-sidebar">
        <div class="tour-price-card">
            @if($tour->price_from > 0)
            <span class="price">${{ number_format($tour->price_from, 0) }} <span class="price-per">/ person</span></span>
            <span class="price-label">Starting from</span>
            @endif
            <button class="btn-book" id="bookBtn">Book This Tour</button>
            <a href="https://wa.me/37410123456?text={{ urlencode('Hi! I\'m interested in: ' . $tour->title) }}" class="btn-whatsapp" target="_blank">WhatsApp Us</a>

            <div class="sidebar-details">
                <div class="sidebar-detail"><span class="label">Duration</span><span class="value">{{ $tour->duration }}</span></div>
                <div class="sidebar-detail"><span class="label">Type</span><span class="value">{{ ucfirst($tour->type) }}</span></div>
                <div class="sidebar-detail"><span class="label">Region</span><span class="value">{{ ucwords(str_replace('_', ' ', $tour->region ?? '-')) }}</span></div>
                <div class="sidebar-detail"><span class="label">Group Size</span><span class="value">2-12 people</span></div>
            </div>

            {{-- Inline Inquiry Form --}}
            <div class="inquiry-form" id="inquiryForm">
                <form action="/contact" method="POST">
                    @csrf
                    <input type="hidden" name="subject" value="Tour Inquiry: {{ $tour->title }}">
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <input type="text" name="phone" placeholder="Phone (optional)">
                    <textarea name="message" placeholder="Message or special requests...">I'm interested in "{{ $tour->title }}" ({{ $tour->duration }}, from ${{ number_format($tour->price_from, 0) }}). Please send me more details.</textarea>
                    <button type="submit">Send Inquiry</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('bookBtn').addEventListener('click', function() {
    var form = document.getElementById('inquiryForm');
    form.classList.toggle('visible');
    this.textContent = form.classList.contains('visible') ? 'Close Form' : 'Book This Tour';
});
</script>
@endpush
