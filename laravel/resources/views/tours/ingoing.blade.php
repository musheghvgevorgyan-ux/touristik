@extends('layouts.main')

@section('title', 'Ingoing Tours - Touristik')
@section('meta_description', 'Explore Armenia with Touristik ingoing tours. Visit ancient monasteries, Lake Sevan, and discover the beauty of the Armenian highlands.')

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
        <h1 data-t="ingoing_title">Discover Armenia</h1>
        <p data-t="ingoing_subtitle">Explore the land of ancient monasteries, breathtaking mountains, and warm hospitality</p>
    </div>

    <div class="card-grid">
        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1558972250-100afca53bde?w=600&q=80">
                <span class="tour-duration" data-t="tour_1day">1 Day</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_yerevan">Classic Yerevan City Tour</h3>
                <p class="tour-desc" data-t="tour_yerevan_desc">Walk through the heart of one of the world's oldest cities. Visit Republic Square, the Cascade, Vernissage market, and enjoy the stunning views of Mount Ararat.</p>
                <div class="tour-card-footer">
                    <span class="price">From $35</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>

        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1603921288457-0a30e11e7db8?w=600&q=80">
                <span class="tour-duration" data-t="tour_1day">1 Day</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_sevan">Lake Sevan & Sevanavank</h3>
                <p class="tour-desc" data-t="tour_sevan_desc">Visit the "Pearl of Armenia" - Lake Sevan, one of the largest high-altitude freshwater lakes in the world. Explore the medieval Sevanavank monastery perched on a peninsula.</p>
                <div class="tour-card-footer">
                    <span class="price">From $45</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>

        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1600959907703-125ba1374a12?w=600&q=80">
                <span class="tour-duration" data-t="tour_1day">1 Day</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_garni">Garni Temple & Geghard Monastery</h3>
                <p class="tour-desc" data-t="tour_garni_desc">Visit the only Greco-Roman pagan temple in the Caucasus region and the UNESCO-listed Geghard Monastery, partially carved out of rock. Enjoy the scenic Azat River gorge.</p>
                <div class="tour-card-footer">
                    <span class="price">From $40</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>

        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1584646098378-0874589d76b1?w=600&q=80">
                <span class="tour-duration" data-t="tour_2days">2 Days</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_khor_virap">Khor Virap & Noravank</h3>
                <p class="tour-desc" data-t="tour_khor_virap_desc">See the iconic Khor Virap monastery with Ararat as its backdrop, then drive through stunning red rock canyons to the 13th-century Noravank monastery complex.</p>
                <div class="tour-card-footer">
                    <span class="price">From $55</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>

        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1608746249753-9346e2dca7c0?w=600&q=80">
                <span class="tour-duration" data-t="tour_3days">3 Days</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_south">Southern Armenia Adventure</h3>
                <p class="tour-desc" data-t="tour_south_desc">A multi-day journey through Armenia's southern highlights: Tatev Monastery, the Wings of Tatev aerial tramway, Jermuk waterfall, and the ancient caves of Khndzoresk.</p>
                <div class="tour-card-footer">
                    <span class="price">From $180</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>

        <div class="tour-card reveal">
            <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1565073182887-6bcefbe225b1?w=600&q=80">
                <span class="tour-duration" data-t="tour_7days">7 Days</span>
            </div>
            <div class="tour-card-content">
                <h3 data-t="tour_grand">Grand Tour of Armenia</h3>
                <p class="tour-desc" data-t="tour_grand_desc">The ultimate Armenia experience covering all major highlights: Yerevan, Garni, Geghard, Sevan, Dilijan, Tatev, Noravank, wine tasting in Areni, and more.</p>
                <div class="tour-card-footer">
                    <span class="price">From $650</span>
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
