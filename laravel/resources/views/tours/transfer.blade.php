@extends('layouts.main')

@section('title', 'Transfer Services - Touristik')
@section('meta_description', 'Reliable airport transfers and intercity transport services in Armenia. Comfortable vehicles, professional drivers, 24/7 availability.')

@push('styles')
<style>
    .transfer-page { max-width: 1100px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .transfer-page .section-header { text-align: center; margin-bottom: 2.5rem; }
    .transfer-page .section-header h1 { font-size: 2.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .transfer-page .section-header p { color: var(--text-secondary); font-size: 1.1rem; max-width: 650px; margin: 0 auto; }
    .transfer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; margin-bottom: 3rem; }
    .transfer-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; }
    .transfer-card:hover { transform: translateY(-6px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
    .transfer-card-img { height: 180px; background-size: cover; background-position: center; position: relative; }
    .transfer-card-img .transfer-badge { position: absolute; top: 12px; left: 12px; background: var(--primary); color: #fff; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .transfer-card-content { padding: 1.5rem; }
    .transfer-card-content h3 { font-size: 1.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .transfer-card-content p { color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1rem; }
    .transfer-features { list-style: none; padding: 0; margin: 0 0 1.2rem; }
    .transfer-features li { padding: 0.3rem 0; color: var(--text-secondary); font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; }
    .transfer-features li::before { content: "\2713"; color: var(--primary); font-weight: 700; }
    .transfer-price-row { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 1rem; }
    .transfer-price-row .price { font-size: 1.1rem; }
    .transfer-price-row .btn-book { padding: 0.5rem 1.2rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: var(--radius); font-weight: 600; font-size: 0.85rem; transition: background 0.2s; }
    .transfer-price-row .btn-book:hover { background: var(--primary-dark); }
    .transfer-info { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 2rem; text-align: center; border-top: 3px solid var(--primary); }
    .transfer-info h2 { font-size: 1.3rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .transfer-info p { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; max-width: 600px; margin: 0 auto 1.2rem; }
    .transfer-info .btn-contact { display: inline-block; padding: 0.8rem 2rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: var(--radius); font-weight: 600; transition: background 0.2s; }
    .transfer-info .btn-contact:hover { background: var(--primary-dark); }
    .tours-back { text-align: center; margin-top: 2.5rem; }
    .tours-back a { color: var(--primary); text-decoration: none; font-weight: 600; font-size: 1rem; }
    .tours-back a:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="transfer-page">
    <div class="section-header reveal">
        <h1 data-t="transfer_title">Transfer Services</h1>
        <p data-t="transfer_subtitle">Comfortable and reliable transportation for all your travel needs in Armenia</p>
    </div>

    <div class="transfer-grid">
        <div class="transfer-card reveal">
            <div class="transfer-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1436491865332-7a61a109db05?w=600&q=80">
                <span class="transfer-badge" data-t="most_popular">Most Popular</span>
            </div>
            <div class="transfer-card-content">
                <h3 data-t="transfer_airport">Airport Transfer</h3>
                <p data-t="transfer_airport_desc">Hassle-free pickup and drop-off at Zvartnots International Airport (EVN). Meet & greet service with professional drivers.</p>
                <ul class="transfer-features">
                    <li data-t="transfer_feat_meet">Meet & greet at arrivals</li>
                    <li data-t="transfer_feat_flight">Flight monitoring included</li>
                    <li data-t="transfer_feat_luggage">Luggage assistance</li>
                    <li data-t="transfer_feat_247">Available 24/7</li>
                </ul>
                <div class="transfer-price-row">
                    <span class="price" data-t="transfer_airport_price">From $15</span>
                    <a href="/contact" class="btn-book" data-t="book_now">Book Now</a>
                </div>
            </div>
        </div>

        <div class="transfer-card reveal">
            <div class="transfer-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80">
            </div>
            <div class="transfer-card-content">
                <h3 data-t="transfer_city">City Transfer</h3>
                <p data-t="transfer_city_desc">Point-to-point transfers within Yerevan. Comfortable sedans and spacious minivans available for individuals and groups.</p>
                <ul class="transfer-features">
                    <li data-t="transfer_feat_sedan">Sedan (up to 3 passengers)</li>
                    <li data-t="transfer_feat_minivan">Minivan (up to 7 passengers)</li>
                    <li data-t="transfer_feat_ac">Air-conditioned vehicles</li>
                    <li data-t="transfer_feat_door">Door-to-door service</li>
                </ul>
                <div class="transfer-price-row">
                    <span class="price" data-t="transfer_city_price">From $8</span>
                    <a href="/contact" class="btn-book" data-t="book_now">Book Now</a>
                </div>
            </div>
        </div>

        <div class="transfer-card reveal">
            <div class="transfer-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&q=80">
            </div>
            <div class="transfer-card-content">
                <h3 data-t="transfer_intercity">Intercity Transfer</h3>
                <p data-t="transfer_intercity_desc">Travel between Armenian cities in comfort. Yerevan to Sevan, Dilijan, Gyumri, Tatev, and other destinations across the country.</p>
                <ul class="transfer-features">
                    <li data-t="transfer_feat_routes">All major routes covered</li>
                    <li data-t="transfer_feat_stops">Photo stops along the way</li>
                    <li data-t="transfer_feat_english">English-speaking drivers</li>
                    <li data-t="transfer_feat_flexible">Flexible scheduling</li>
                </ul>
                <div class="transfer-price-row">
                    <span class="price" data-t="transfer_intercity_price">From $40</span>
                    <a href="/contact" class="btn-book" data-t="book_now">Book Now</a>
                </div>
            </div>
        </div>
    </div>

    <div class="transfer-info reveal">
        <h2 data-t="transfer_custom_title">Need a Custom Transfer?</h2>
        <p data-t="transfer_custom_desc">For group transportation, multi-day driver hire, or special requests, contact our team directly. We'll arrange the perfect solution for your needs.</p>
        <a href="/contact" class="btn-contact" data-t="contact_us">Contact Us</a>
    </div>

    <div class="tours-back reveal">
        <a href="/tours" data-t="back_to_tours">&larr; Back to Tours</a>
    </div>
</div>
@endsection
