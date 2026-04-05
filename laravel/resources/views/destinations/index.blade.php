@extends('layouts.main')

@section('title', 'Destinations - Touristik')
@section('meta_description', 'Explore popular travel destinations with Touristik. Find the best deals on flights and hotels to Paris, Tokyo, Bali, Rome, and more.')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .dest-map-wrapper { max-width: 1200px; margin: 0 auto 2rem; padding: 0 2rem; }
    .dest-map-container { position: relative; height: 420px; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 40px rgba(0,0,0,0.12); }
    .dest-map-overlay { position: absolute; top: 1.5rem; left: 1.5rem; z-index: 1000; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 12px; padding: 1rem 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
    .dest-map-overlay h3 { margin: 0 0 0.2rem; font-size: 1rem; color: #1a1a2e; }
    .dest-map-overlay p { margin: 0; font-size: 0.8rem; color: #666; }
    .leaflet-popup-content-wrapper { border-radius: 12px !important; box-shadow: 0 8px 30px rgba(0,0,0,0.15) !important; }
    .leaflet-popup-content { margin: 0 !important; padding: 0 !important; }
    .dest-popup { padding: 0; overflow: hidden; border-radius: 12px; min-width: 200px; }
    .dest-popup-img { width: 100%; height: 120px; object-fit: cover; display: block; }
    .dest-popup-body { padding: 0.8rem 1rem; }
    .dest-popup-body h4 { margin: 0 0 0.2rem; font-size: 1rem; color: #1a1a2e; }
    .dest-popup-body .dest-popup-country { font-size: 0.8rem; color: #888; margin-bottom: 0.4rem; }
    .dest-popup-body .dest-popup-price { font-size: 0.9rem; color: #FF6B35; font-weight: 700; }
    .dest-popup-body a { display: inline-block; margin-top: 0.5rem; padding: 0.4rem 1rem; background: #FF6B35; color: #fff; text-decoration: none; border-radius: 6px; font-size: 0.8rem; font-weight: 600; transition: background 0.2s; }
    .dest-popup-body a:hover { background: #e55a2b; }
    @media (max-width: 768px) { .dest-map-container { height: 300px; } .dest-map-overlay { top: 0.8rem; left: 0.8rem; padding: 0.7rem 1rem; } }
    .destinations-page { max-width: 1200px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .destinations-page .section-header { text-align: center; margin-bottom: 2.5rem; }
    .destinations-page .section-header h1 { font-size: 2.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .destinations-page .section-header p { color: var(--text-secondary); font-size: 1.1rem; }
    .destinations-page .card-image { position: relative; }
    .destinations-page .card-body h3 a { color: var(--text-heading); text-decoration: none; }
    .destinations-page .card-body h3 a:hover { color: var(--primary); }
    .destinations-page .dest-country { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.3rem; }
    .destinations-page .dest-desc { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .destinations-page .card-footer { display: flex; justify-content: space-between; align-items: center; }
    .destinations-page .btn-explore { display: inline-block; padding: 0.5rem 1.2rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: var(--radius); font-weight: 600; font-size: 0.9rem; transition: background 0.2s; }
    .destinations-page .btn-explore:hover { background: var(--primary-dark); }
    .destinations-page .empty-state { text-align: center; padding: 4rem 0; color: var(--text-secondary); }
    .destinations-page .empty-state h2 { color: var(--text-heading); margin-bottom: 0.5rem; }
</style>
@endpush

@section('content')
<div class="dest-map-wrapper reveal">
    <div class="dest-map-container">
        <div class="dest-map-overlay">
            <h3>Explore Destinations</h3>
            <p>Click a pin to discover more</p>
        </div>
        <div id="destMap" style="height:100%;width:100%;"></div>
    </div>
</div>
<div class="destinations-page">
    <div class="section-header reveal">
        <h1 data-t="destinations_title">{{ __('site.destinations_title') }}</h1>
        <p data-t="destinations_subtitle">{{ __('site.destinations_subtitle') }}</p>
    </div>

    @if(!empty($destinations))
        <div class="card-grid">
            @foreach($destinations as $i => $dest)
                <div class="card reveal" style="transition-delay: {!! $i * 0.1 !!}s">
                    <div class="card-image lazy-bg" data-bg="{{ $dest['image'] ?? '' }}">
                    </div>
                    <div class="card-body">
                        <span class="dest-country">{{ $dest['country'] ?? '' }}</span>
                        <h3><a href="{{ lurl('/destinations/' . ($dest['slug'] ?? '')) }}">{{ $dest['name'] ?? '' }}</a></h3>
                        <p class="dest-desc">{{ $dest['description'] ?? '' }}</p>
                        <div class="card-footer">
                            <span class="price" data-t="from_price">From {!! "$" . number_format($dest['price_from'] ?? 0, 2) !!}</span>
                            <a href="{{ lurl('/destinations/' . ($dest['slug'] ?? '')) }}" class="btn-explore" data-t="explore">{{ __('site.explore') }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state reveal">
            <h2 data-t="no_destinations_title">{{ __('site.no_destinations_title') }}</h2>
            <p data-t="no_destinations_text">{{ __('site.no_destinations_text') }}</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('destMap', { scrollWheelZoom: false, zoomControl: true }).setView([30, 30], 2);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
        maxZoom: 19
    }).addTo(map);

    var destinations = @json($mapData);

    var markers = [];
    destinations.forEach(function(d) {
        if (!d.lat || !d.lng) return;

        var icon = L.divIcon({
            className: 'custom-dest-marker',
            html: '<div style="background:#FF6B35;width:36px;height:36px;border-radius:50%;border:3px solid #fff;box-shadow:0 4px 15px rgba(255,107,53,0.4);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:transform 0.2s;"><span style="color:#fff;font-size:16px;">&#9992;</span></div>',
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -22]
        });

        var imgHtml = d.image ? '<img src="' + d.image + '" class="dest-popup-img" alt="' + d.name + '">' : '';
        var priceHtml = d.price > 0 ? '<div class="dest-popup-price">From $' + Number(d.price).toLocaleString() + '</div>' : '';

        var popup = '<div class="dest-popup">' +
            imgHtml +
            '<div class="dest-popup-body">' +
            '<h4>' + d.name + '</h4>' +
            '<div class="dest-popup-country">' + d.country + '</div>' +
            priceHtml +
            '<a href="/destinations/' + d.slug + '">Explore &rarr;</a>' +
            '</div></div>';

        var marker = L.marker([d.lat, d.lng], { icon: icon }).addTo(map).bindPopup(popup, { maxWidth: 250, minWidth: 200 });
        markers.push(marker);
    });

    if (markers.length > 1) {
        var group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.2));
    }
});
</script>
@endpush
