@extends('layouts.main')

@section('title', 'Outgoing Tours - Touristik')

@push('styles')
<style>
    .tours-detail-page { max-width: 1200px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .tours-detail-page .section-header { text-align: center; margin-bottom: 2.5rem; }
    .tours-detail-page .section-header h1 { font-size: 2.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .tours-detail-page .section-header p { color: var(--text-secondary); font-size: 1.1rem; max-width: 650px; margin: 0 auto; }
    .tour-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; }
    .tour-card:hover { transform: translateY(-6px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
    .tour-card-img { height: 200px; background-size: cover; background-position: center; position: relative; }
    .tour-card-img .tour-duration { position: absolute; bottom: 12px; left: 12px; background: rgba(0,0,0,0.7); color: #fff; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .tour-card-img .tour-country-badge { position: absolute; top: 12px; right: 12px; background: var(--primary); color: #fff; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .tour-card-content { padding: 1.5rem; }
    .tour-card-content h3 { font-size: 1.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .tour-card-content .tour-desc { color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .tour-card-footer { display: flex; justify-content: space-between; align-items: center; }
    .tour-card-footer .price { font-size: 1.1rem; }
    .tour-card-footer .btn-details { padding: 0.4rem 1rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: var(--radius); font-weight: 600; font-size: 0.85rem; transition: background 0.2s; }
    .tour-card-footer .btn-details:hover { background: var(--primary-dark); }
    .tours-back { text-align: center; margin-top: 2.5rem; }
    .tours-back a { color: var(--primary); text-decoration: none; font-weight: 600; font-size: 1rem; }
    .tours-back a:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="tours-detail-page">
    <div class="section-header reveal">
        <h1 data-t="outgoing_title">Outgoing Tours</h1>
        <p data-t="outgoing_subtitle">Explore the world's most popular destinations with our carefully crafted tour packages</p>
    </div>

    <div class="card-grid">
        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1533105079780-92b9be482077?w=600&q=80">
                <span class="tour-duration" data-t="tour_7days">7 Days</span>
                <span class="tour-country-badge">Greece</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_greece">Greek Islands Explorer</h3>
                <p class="tour-desc" data-t="tour_greece_desc">Island-hop through Santorini, Mykonos, and Athens. Enjoy crystal-clear waters, whitewashed villages, ancient ruins, and vibrant nightlife in the Mediterranean paradise.</p>
                <div class="tour-card-footer">
                    <span class="price">From $890</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>

        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1539768942893-daf53e736b68?w=600&q=80">
                <span class="tour-duration" data-t="tour_5days">5 Days</span>
                <span class="tour-country-badge">Egypt</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_egypt">Egypt: Land of Pharaohs</h3>
                <p class="tour-desc" data-t="tour_egypt_desc">Explore the Pyramids of Giza, cruise the Nile River, visit the Valley of Kings, and discover the wonders of ancient Egyptian civilization in Cairo and Luxor.</p>
                <div class="tour-card-footer">
                    <span class="price">From $750</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>

        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=600&q=80">
                <span class="tour-duration" data-t="tour_5days">5 Days</span>
                <span class="tour-country-badge">UAE</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_dubai">Dubai & Abu Dhabi</h3>
                <p class="tour-desc" data-t="tour_dubai_desc">Experience the futuristic skyline of Dubai, desert safari adventures, luxury shopping, and the cultural gems of Abu Dhabi including the Grand Mosque.</p>
                <div class="tour-card-footer">
                    <span class="price">From $680</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>

        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1573455494060-c5595004fb6c?w=600&q=80">
                <span class="tour-duration" data-t="tour_4days">4 Days</span>
                <span class="tour-country-badge">Georgia</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_georgia">Georgia Highlights</h3>
                <p class="tour-desc" data-t="tour_georgia_desc">Discover Tbilisi's charming old town, the cave city of Vardzia, the wine region of Kakheti, and the mountain scenery of Kazbegi with its iconic Gergeti Trinity Church.</p>
                <div class="tour-card-footer">
                    <span class="price">From $420</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>

        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1589561253898-768105ca91a8?w=600&q=80">
                <span class="tour-duration" data-t="tour_7days">7 Days</span>
                <span class="tour-country-badge">Turkey</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_turkey">Turkey: East Meets West</h3>
                <p class="tour-desc" data-t="tour_turkey_desc">From Istanbul's historic mosques and bazaars to Cappadocia's fairy chimneys, Pamukkale's travertines, and the turquoise coast. A journey through centuries of history.</p>
                <div class="tour-card-footer">
                    <span class="price">From $720</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>

        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&q=80">
                <span class="tour-duration" data-t="tour_5days">5 Days</span>
                <span class="tour-country-badge">Thailand</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_thailand">Thailand Paradise</h3>
                <p class="tour-desc" data-t="tour_thailand_desc">Experience Bangkok's vibrant temples and street food, relax on Phuket's stunning beaches, and explore the cultural treasures of Chiang Mai in northern Thailand.</p>
                <div class="tour-card-footer">
                    <span class="price">From $950</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>
    </div>

    <div class="tours-back reveal">
        <a href="/tours" data-t="back_to_tours">&larr; Back to Tours</a>
    </div>
</div>
@endsection
