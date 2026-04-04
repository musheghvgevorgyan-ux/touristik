@extends('layouts.main')

@section('title', 'Outgoing Tours - Touristik')
@section('meta_description', 'Travel the world with Touristik. Greece, Italy, Egypt, Dubai, Thailand, Maldives — all-inclusive tour packages from Yerevan.')

@php
$toursByCountry = $tours->groupBy('region');
$destinations = [
    'greece'   => ['name'=>'Greece',   'lat'=>37.98, 'lng'=>23.73, 'flag'=>"\xF0\x9F\x87\xAC\xF0\x9F\x87\xB7", 'desc'=>'Mediterranean islands, ancient ruins & vibrant culture'],
    'egypt'    => ['name'=>'Egypt',    'lat'=>30.04, 'lng'=>31.24, 'flag'=>"\xF0\x9F\x87\xAA\xF0\x9F\x87\xAC", 'desc'=>'Pyramids, Nile cruises & pharaohs\' treasures'],
    'uae'      => ['name'=>'UAE',      'lat'=>25.20, 'lng'=>55.27, 'flag'=>"\xF0\x9F\x87\xA6\xF0\x9F\x87\xAA", 'desc'=>'Futuristic skylines, desert safaris & luxury'],
    'georgia'  => ['name'=>'Georgia',  'lat'=>41.72, 'lng'=>44.79, 'flag'=>"\xF0\x9F\x87\xAC\xF0\x9F\x87\xAA", 'desc'=>'Mountains, wine country & ancient churches'],
    'turkey'   => ['name'=>'Turkey',   'lat'=>41.01, 'lng'=>28.98, 'flag'=>"\xF0\x9F\x87\xB9\xF0\x9F\x87\xB7", 'desc'=>'Bazaars, fairy chimneys & turquoise coasts'],
    'thailand' => ['name'=>'Thailand', 'lat'=>13.76, 'lng'=>100.50,'flag'=>"\xF0\x9F\x87\xB9\xF0\x9F\x87\xAD", 'desc'=>'Temples, beaches & street food paradise'],
    'italy'    => ['name'=>'Italy',    'lat'=>41.90, 'lng'=>12.50, 'flag'=>"\xF0\x9F\x87\xAE\xF0\x9F\x87\xB9", 'desc'=>'Art, architecture, cuisine & la dolce vita'],
    'maldives' => ['name'=>'Maldives', 'lat'=>3.20,  'lng'=>73.22, 'flag'=>"\xF0\x9F\x87\xB2\xF0\x9F\x87\xBB", 'desc'=>'Overwater villas & crystal-clear lagoons'],
];
@endphp

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    .outgoing-page { max-width: 1200px; margin: 0 auto; padding: 6rem 2rem 4rem; }
    .outgoing-page .section-header { text-align: center; margin-bottom: 2rem; }
    .outgoing-page .section-header h1 { font-size: 2.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }

    /* Map + Info layout */
    .world-explorer { display: flex; gap: 2rem; margin-bottom: 2.5rem; align-items: flex-start; }
    .world-map-wrap { flex: 0 0 55%; max-width: 520px; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
    #worldMap { height: 380px; width: 100%; }
    .world-info { flex: 1; }

    /* Destination info panel */
    .dest-panel { background: var(--bg-card); border-radius: 16px; padding: 1.8rem; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.05); min-height: 380px; display: flex; flex-direction: column; }
    .dest-default { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
    .dest-default h3 { font-size: 1.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .dest-default p { color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.2rem; }
    .dest-chips { display: flex; flex-wrap: wrap; gap: 0.4rem; justify-content: center; }
    .dest-chip { padding: 0.35rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background: rgba(59,130,246,0.08); color: var(--primary); cursor: pointer; transition: all 0.2s; border: 1.5px solid transparent; }
    .dest-chip:hover, .dest-chip.active { background: var(--primary); color: #fff; }

    .dest-info { display: none; flex-direction: column; flex: 1; }
    .dest-info.visible { display: flex; }
    .dest-info h3 { font-size: 1.5rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .dest-info h3 .dflag { font-size: 1.8rem; margin-right: 0.4rem; }
    .dest-info .dest-desc { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1rem; }
    .dest-tour-list { list-style: none; padding: 0; margin: 0 0 1rem; }
    .dest-tour-list li { padding: 0.5rem 0; border-bottom: 1px solid rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: center; }
    .dest-tour-list li:last-child { border: none; }
    .dest-tour-list .dtour-name { font-weight: 600; font-size: 0.9rem; color: var(--text-heading); }
    .dest-tour-list .dtour-meta { font-size: 0.8rem; color: var(--text-secondary); }
    .dest-tour-list .dtour-price { font-weight: 700; color: var(--primary); font-size: 0.9rem; }
    .dest-actions { margin-top: auto; display: flex; gap: 0.6rem; }
    .btn-filter-dest { flex: 1; padding: 0.6rem; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem; }
    .btn-filter-dest:hover { background: var(--primary-dark); }
    .btn-clear-dest { padding: 0.6rem 1rem; background: none; border: 1.5px solid var(--primary); color: var(--primary); border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem; }

    /* Filter bar */
    .filter-bar { text-align: center; margin: 0 0 2rem; display: flex; align-items: center; justify-content: center; gap: 0.8rem; flex-wrap: wrap; }
    .filter-active { background: var(--primary); color: #fff; padding: 0.35rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.9rem; }
    .filter-clear { background: none; border: 1.5px solid var(--primary); color: var(--primary); padding: 0.3rem 0.9rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; cursor: pointer; }
    .filter-clear:hover { background: var(--primary); color: #fff; }

    /* Tour cards */
    .tour-card { display: block; text-decoration: none; color: inherit; background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; }
    .tour-card:hover { transform: translateY(-6px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
    .tour-card.hidden-card { display: none; }
    .tour-card-img { height: 200px; background-size: cover; background-position: center; position: relative; }
    .tour-card-img .tour-duration { position: absolute; bottom: 12px; left: 12px; background: rgba(0,0,0,0.7); color: #fff; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .tour-card-img .tour-country-badge { position: absolute; top: 12px; right: 12px; background: rgba(59,130,246,0.9); color: #fff; padding: 0.25rem 0.7rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
    .tour-card-content { padding: 1.5rem; }
    .tour-card-content h3 { font-size: 1.15rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .tour-card-content .tour-desc { color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .tour-card-footer { display: flex; justify-content: space-between; align-items: center; }
    .tour-card-footer .price { font-size: 1.1rem; font-weight: 600; }
    .tour-card-footer .btn-details { padding: 0.4rem 1rem; background: var(--primary); color: #fff; border-radius: var(--radius); font-weight: 600; font-size: 0.85rem; }
    .no-tours-msg { text-align: center; padding: 3rem; color: var(--text-secondary); display: none; }
    .no-tours-msg.visible { display: block; }
    .tours-back { text-align: center; margin-top: 2.5rem; }
    .tours-back a { color: var(--primary); text-decoration: none; font-weight: 600; }

    /* Leaflet custom marker */
    .dest-marker { background: var(--primary, #3b82f6); border: 3px solid #fff; border-radius: 50%; width: 18px; height: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.3); cursor: pointer; animation: marker-pulse 2s infinite; }
    .dest-marker.active { background: #f97316; transform: scale(1.3); }
    @keyframes marker-pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(59,130,246,0.4); } 50% { box-shadow: 0 0 0 10px rgba(59,130,246,0); } }

    /* Yerevan marker */
    .yer-marker { background: #f59e0b; border: 2px solid #fff; border-radius: 50%; width: 12px; height: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.3); }

    @media (max-width: 900px) {
        .world-explorer { flex-direction: column; }
        .world-map-wrap { flex: none; max-width: 100%; width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="outgoing-page">
    <div class="section-header reveal">
        <h1 data-t="outgoing_title">Explore the World</h1>
    </div>

    <div class="world-explorer reveal">
        <div class="world-map-wrap">
            <div id="worldMap"></div>
        </div>
        <div class="world-info">
            <div class="dest-panel">
                <div class="dest-default" id="destDefault">
                    <h3 data-t="choose_destination">Choose a Destination</h3>
                    <p data-t="choose_destination_desc">Click a marker on the map or pick a country below</p>
                    <div class="dest-chips">
                        @foreach($destinations as $key => $d)
                        <span class="dest-chip" data-dest="{{ $key }}">{{ $d['flag'] }} {{ $d['name'] }}</span>
                        @endforeach
                    </div>
                </div>

                @foreach($destinations as $key => $d)
                <div class="dest-info" data-dest-info="{{ $key }}">
                    <h3><span class="dflag">{{ $d['flag'] }}</span> {{ $d['name'] }}</h3>
                    <p class="dest-desc">{{ $d['desc'] }}</p>
                    @if(isset($toursByCountry[$key]))
                    <ul class="dest-tour-list">
                        @foreach($toursByCountry[$key] as $t)
                        <li>
                            <div>
                                <span class="dtour-name">{{ $t->title }}</span>
                                <span class="dtour-meta">{{ $t->duration }}</span>
                            </div>
                            <span class="dtour-price">From ${{ number_format($t->price_from, 0) }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                    <div class="dest-actions">
                        <button class="btn-filter-dest" onclick="filterByDest('{{ $key }}')">View Tours</button>
                        <button class="btn-clear-dest" onclick="clearDestFilter()">Show All</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="filter-bar" id="filterBar" style="display:none;">
        <span style="color:var(--text-secondary)">Showing tours in:</span>
        <span class="filter-active" id="filterName"></span>
        <button class="filter-clear" onclick="clearDestFilter()">Show All</button>
    </div>

    <div class="card-grid" id="tourGrid">
        @foreach($tours as $tour)
        <a href="/tours/{{ $tour->slug }}" class="tour-card reveal" data-country="{{ $tour->region }}">
            <div class="tour-card-img lazy-bg" data-bg="{{ $tour->image_url }}">
                <span class="tour-duration">{{ $tour->duration }}</span>
                <span class="tour-country-badge">{{ $destinations[$tour->region]['name'] ?? ucfirst($tour->region) }}</span>
            </div>
            <div class="tour-card-content">
                <h3>{{ $tour->title }}</h3>
                <p class="tour-desc">{{ Str::limit($tour->description, 150) }}</p>
                <div class="tour-card-footer">
                    <span class="price">From ${{ number_format($tour->price_from, 0) }}</span>
                    <span class="btn-details">View Details</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="no-tours-msg" id="noToursMsg">No tours for this destination yet. Contact us for a custom package!</div>

    <div class="tours-back reveal">
        <a href="/tours" data-t="back_to_tours">&larr; Back to Tours</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    // Map setup
    var map = L.map('worldMap', { scrollWheelZoom: false, zoomControl: true }).setView([30, 40], 3);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '', maxZoom: 18
    }).addTo(map);

    // Yerevan home marker
    var yerIcon = L.divIcon({ className: 'yer-marker', iconSize: [12, 12], iconAnchor: [6, 6] });
    L.marker([40.18, 44.51], { icon: yerIcon }).addTo(map).bindTooltip('Yerevan', { permanent: true, direction: 'top', className: 'yer-tooltip', offset: [0, -8] });

    var destinations = @json($destinations);
    var markers = {};
    var activeDest = null;

    Object.keys(destinations).forEach(function(key) {
        var d = destinations[key];
        var icon = L.divIcon({ className: 'dest-marker', iconSize: [18, 18], iconAnchor: [9, 9] });
        var m = L.marker([d.lat, d.lng], { icon: icon }).addTo(map);
        m.bindTooltip(d.flag + ' ' + d.name, { direction: 'top', offset: [0, -12] });
        m.on('click', function() { selectDest(key); });
        markers[key] = m;
    });

    // Draw flight lines from Yerevan
    Object.keys(destinations).forEach(function(key) {
        var d = destinations[key];
        L.polyline([[40.18, 44.51], [d.lat, d.lng]], {
            color: '#3b82f6', weight: 1, opacity: 0.25, dashArray: '4 6'
        }).addTo(map);
    });

    // Destination chips
    document.querySelectorAll('.dest-chip').forEach(function(chip) {
        chip.addEventListener('click', function() { selectDest(this.dataset.dest); });
    });

    function selectDest(key) {
        activeDest = key;
        // Update markers
        Object.keys(markers).forEach(function(k) {
            markers[k].getElement().classList.toggle('active', k === key);
        });
        // Show info panel
        document.getElementById('destDefault').style.display = 'none';
        document.querySelectorAll('.dest-info').forEach(function(el) {
            el.classList.toggle('visible', el.dataset.destInfo === key);
        });
        // Update chips
        document.querySelectorAll('.dest-chip').forEach(function(c) {
            c.classList.toggle('active', c.dataset.dest === key);
        });
        // Pan map
        var d = destinations[key];
        map.flyTo([d.lat, d.lng], 5, { duration: 1 });
        // Filter cards
        filterByDest(key);
    }

    window.filterByDest = function(key) {
        activeDest = key;
        var cards = document.querySelectorAll('.tour-card[data-country]');
        var count = 0;
        cards.forEach(function(c) {
            if (c.dataset.country === key) { c.classList.remove('hidden-card'); count++; }
            else { c.classList.add('hidden-card'); }
        });
        var name = destinations[key] ? destinations[key].name : key;
        document.getElementById('filterName').textContent = destinations[key] ? destinations[key].flag + ' ' + name : name;
        document.getElementById('filterBar').style.display = 'flex';
        document.getElementById('noToursMsg').classList.toggle('visible', count === 0);
        document.getElementById('tourGrid').scrollIntoView({ behavior: 'smooth' });
    };

    window.clearDestFilter = function() {
        activeDest = null;
        document.querySelectorAll('.tour-card[data-country]').forEach(function(c) { c.classList.remove('hidden-card'); });
        document.getElementById('filterBar').style.display = 'none';
        document.getElementById('noToursMsg').classList.remove('visible');
        Object.keys(markers).forEach(function(k) { markers[k].getElement().classList.remove('active'); });
        document.querySelectorAll('.dest-chip').forEach(function(c) { c.classList.remove('active'); });
        document.getElementById('destDefault').style.display = '';
        document.querySelectorAll('.dest-info').forEach(function(el) { el.classList.remove('visible'); });
        map.flyTo([30, 40], 3, { duration: 1 });
    };
})();
</script>
@endpush
