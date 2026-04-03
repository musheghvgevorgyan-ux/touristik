@extends('layouts.main')

@section('title', 'About - Touristik')

@push('styles')
<style>
    .about-page { max-width: 1100px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .about-page .section-header { text-align: center; margin-bottom: 3rem; }
    .about-page .section-header h1 { font-size: 2.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .about-page .section-header p { color: var(--text-secondary); font-size: 1.1rem; max-width: 700px; margin: 0 auto; }
    .about-intro { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center; margin-bottom: 3.5rem; }
    .about-intro-img { border-radius: var(--radius); overflow: hidden; height: 320px; background: url('https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=800&q=80') center/cover no-repeat; box-shadow: var(--shadow); }
    .about-intro-text h2 { font-size: 1.6rem; color: var(--text-heading); margin-bottom: 0.8rem; }
    .about-intro-text p { color: var(--text-secondary); line-height: 1.8; font-size: 1rem; margin-bottom: 1rem; }
    .values-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3.5rem; }
    .value-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 2rem; text-align: center; border-top: 3px solid var(--primary); }
    .value-card .value-icon { font-size: 2.5rem; margin-bottom: 0.8rem; }
    .value-card h3 { font-size: 1.15rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .value-card p { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; }
    .branches-section { margin-bottom: 3.5rem; }
    .branches-section h2 { font-size: 1.6rem; color: var(--text-heading); text-align: center; margin-bottom: 2rem; }
    .branches-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
    .branch-detail-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 2rem; border-left: 4px solid var(--primary); }
    .branch-detail-card h3 { font-size: 1.15rem; color: var(--text-heading); margin-bottom: 0.8rem; }
    .branch-detail-card .branch-info { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.8; }
    .branch-detail-card a { color: var(--primary); text-decoration: none; font-weight: 500; }
    .branch-detail-card a:hover { text-decoration: underline; }
    .team-section { text-align: center; }
    .team-section h2 { font-size: 1.6rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .team-section .team-subtitle { color: var(--text-secondary); margin-bottom: 2rem; }
    .team-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; }
    .team-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 2rem; text-align: center; }
    .team-avatar { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #FF6B35, #f7a072); margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #fff; }
    .team-card h3 { font-size: 1rem; color: var(--text-heading); margin-bottom: 0.2rem; }
    .team-card p { color: var(--text-secondary); font-size: 0.85rem; }
    @media (max-width: 768px) {
        .about-intro { grid-template-columns: 1fr; }
        .about-intro-img { height: 220px; }
    }
</style>
@endpush

@section('content')
<div class="about-page">
    <div class="section-header reveal">
        <h1 data-t="about_title">About Touristik</h1>
        <p data-t="about_subtitle">Your trusted travel partner in Armenia since day one</p>
    </div>

    <div class="about-intro reveal">
        <div class="about-intro-img"></div>
        <div class="about-intro-text">
            <h2 data-t="about_story_title">Our Story</h2>
            <p data-t="about_story_1">Touristik Travel Club was founded with a simple mission: to make travel accessible, enjoyable, and memorable for everyone. Based in Yerevan, Armenia, we have grown into a full-service travel agency with three branches across the city.</p>
            <p data-t="about_story_2">We specialize in both inbound tourism to Armenia and outbound travel worldwide. Whether you're exploring the ancient monasteries of Armenia or lounging on the beaches of Greece, our experienced team ensures every detail is taken care of.</p>
            <p data-t="about_story_3">With partnerships with leading hotel providers like Hotelbeds and Hotelston, we offer competitive prices on hotels worldwide, along with flights, visa support, transfers, and curated tour packages.</p>
        </div>
    </div>

    <div class="values-grid">
        <div class="value-card reveal">
            <div class="value-icon">&#127758;</div>
            <h3 data-t="value_explore">Global Reach</h3>
            <p data-t="value_explore_desc">Access to thousands of hotels, flights, and experiences worldwide through our B2B partnerships and direct contracts.</p>
        </div>
        <div class="value-card reveal">
            <div class="value-icon">&#129309;</div>
            <h3 data-t="value_trust">Trust & Reliability</h3>
            <p data-t="value_trust_desc">We build lasting relationships with our clients through transparency, honest pricing, and dependable service.</p>
        </div>
        <div class="value-card reveal">
            <div class="value-icon">&#128222;</div>
            <h3 data-t="value_support">24/7 Support</h3>
            <p data-t="value_support_desc">Our dedicated team is always available to assist you before, during, and after your trip. Your comfort is our priority.</p>
        </div>
    </div>

    <div class="branches-section">
        <h2 data-t="our_branches">Our Branches</h2>
        <div class="branches-grid">
            <div class="branch-detail-card reveal">
                <h3 data-t="branch_komitas">Komitas Branch</h3>
                <div class="branch-info">
                    <p data-t="branch_komitas_addr">Komitas 38, Yerevan, Armenia</p>
                    <p><a href="tel:+37433060609">+374 33 060 609</a></p>
                    <p><a href="tel:+37455060609">+374 55 060 609</a></p>
                    <p data-t="hours_weekday">Mon - Fri: 10:00 - 20:00</p>
                    <p data-t="hours_weekend">Sat - Sun: 11:00 - 18:00</p>
                </div>
            </div>
            <div class="branch-detail-card reveal">
                <h3 data-t="branch_mashtots">Mashtots Branch</h3>
                <div class="branch-info">
                    <p data-t="branch_mashtots_addr">Mashtots 7/6, Yerevan, Armenia</p>
                    <p><a href="tel:+37444060608">+374 44 060 608</a></p>
                    <p><a href="tel:+37495060608">+374 95 060 608</a></p>
                    <p data-t="hours_weekday">Mon - Fri: 10:00 - 20:00</p>
                    <p data-t="hours_weekend">Sat - Sun: 11:00 - 18:00</p>
                </div>
            </div>
            <div class="branch-detail-card reveal">
                <h3 data-t="branch_mall">Yerevan Mall Branch</h3>
                <div class="branch-info">
                    <p data-t="branch_mall_addr">Arshakunyats 34, Yerevan Mall, 2nd Floor</p>
                    <p><a href="tel:+37433060609">+374 33 060 609</a></p>
                    <p data-t="hours_weekday">Mon - Fri: 10:00 - 20:00</p>
                    <p data-t="hours_weekend">Sat - Sun: 11:00 - 18:00</p>
                </div>
            </div>
        </div>
    </div>

    <div class="team-section">
        <h2 data-t="team_title">Our Team</h2>
        <p class="team-subtitle" data-t="team_subtitle">Dedicated professionals ready to make your travel dreams come true</p>
        <div class="team-grid">
            <div class="team-card reveal">
                <div class="team-avatar">&#128100;</div>
                <h3 data-t="team_management">Management</h3>
                <p data-t="team_management_desc">Strategic planning & partnerships</p>
            </div>
            <div class="team-card reveal">
                <div class="team-avatar">&#9992;</div>
                <h3 data-t="team_travel">Travel Consultants</h3>
                <p data-t="team_travel_desc">Expert advice & trip planning</p>
            </div>
            <div class="team-card reveal">
                <div class="team-avatar">&#128196;</div>
                <h3 data-t="team_visa">Visa Department</h3>
                <p data-t="team_visa_desc">Visa processing & documentation</p>
            </div>
            <div class="team-card reveal">
                <div class="team-avatar">&#128187;</div>
                <h3 data-t="team_tech">Technology</h3>
                <p data-t="team_tech_desc">Platform development & innovation</p>
            </div>
        </div>
    </div>
</div>
@endsection
