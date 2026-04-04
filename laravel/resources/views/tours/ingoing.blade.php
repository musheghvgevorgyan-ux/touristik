@extends('layouts.main')

@section('title', 'Ingoing Tours - Touristik')
@section('meta_description', 'Explore Armenia with Touristik ingoing tours. Visit ancient monasteries, Lake Sevan, and discover the beauty of the Armenian highlands.')

@php $tourCounts = $tours->groupBy('region')->map->count(); @endphp

@push('styles')
<style>
    .tours-detail-page { max-width: 1200px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .tours-detail-page .section-header { text-align: center; margin-bottom: 2rem; }
    .tours-detail-page .section-header h1 { font-size: 2.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .tours-detail-page .section-header p { color: var(--text-secondary); font-size: 1.1rem; max-width: 650px; margin: 0 auto; }

    /* ─── Explorer: Map + Info ───────────────────────────── */
    .armenia-explorer {
        display: flex;
        gap: 2rem;
        align-items: flex-start;
        margin-bottom: 2.5rem;
    }
    .explorer-map {
        flex: 0 0 52%;
        max-width: 460px;
    }
    .explorer-map svg {
        width: 100%;
        height: auto;
        display: block;
        filter: drop-shadow(0 4px 12px rgba(0,0,0,0.08));
    }
    .explorer-info {
        flex: 1;
        min-height: 360px;
    }

    /* ─── Map Regions ────────────────────────────────────── */
    .armenia-region {
        fill: #3b82f6;
        stroke: #fff;
        stroke-width: 1.8;
        cursor: pointer;
        transition: fill 0.22s ease;
    }
    .armenia-region:hover { fill: #1e3a5f; }
    .armenia-region.active { fill: #f97316; }
    .armenia-region[data-region="yerevan"] { fill: #f59e0b; }
    .armenia-region[data-region="yerevan"]:hover { fill: #b45309; }
    .armenia-region[data-region="yerevan"].active { fill: #f97316; }
    .lake-sevan {
        fill: #93c5fd;
        stroke: #fff;
        stroke-width: 1.8;
        stroke-linejoin: miter;
        pointer-events: none;
    }
    .region-label {
        fill: #fff;
        font-family: 'Inter', system-ui, sans-serif;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: 0.3px;
        text-anchor: middle;
        dominant-baseline: central;
        pointer-events: none;
        paint-order: stroke;
        stroke: rgba(0,0,0,0.6);
        stroke-width: 4px;
        stroke-linejoin: round;
    }
    .region-label.label-sm { font-size: 13px; }
    .region-label.label-lg { font-size: 20px; }

    /* ─── Info Panel ─────────────────────────────────────── */
    .info-panel {
        background: var(--bg-card);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
        min-height: 360px;
        display: flex;
        flex-direction: column;
    }
    .info-default {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--text-secondary);
    }
    .info-default .info-icon { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
    .info-default h3 { font-size: 1.3rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .info-default p { font-size: 0.95rem; max-width: 280px; line-height: 1.5; }
    .info-region { display: none; flex-direction: column; flex: 1; }
    .info-region.visible { display: flex; }
    .info-region h3 {
        font-size: 1.5rem;
        color: var(--text-heading);
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .info-region h3 .region-dot {
        width: 12px; height: 12px;
        border-radius: 50%;
        background: #3b82f6;
        display: inline-block;
        flex-shrink: 0;
    }
    .info-region .region-desc {
        color: var(--text-secondary);
        font-size: 0.95rem;
        line-height: 1.55;
        margin-bottom: 1rem;
    }
    .region-stats {
        display: flex;
        gap: 1.2rem;
        margin-bottom: 1rem;
    }
    .region-stat {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-primary);
        background: rgba(59,130,246,0.06);
        padding: 0.45rem 0.9rem;
        border-radius: 8px;
    }
    .region-stat svg { color: #3b82f6; flex-shrink: 0; }
    .region-stat .stat-label { color: var(--text-secondary); font-weight: 500; }
    .region-stat .stat-value { font-weight: 700; }
    .info-region .region-highlights {
        list-style: none;
        padding: 0;
        margin: 0 0 1.2rem 0;
    }
    .info-region .region-highlights li {
        padding: 0.4rem 0;
        font-size: 0.9rem;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .info-region .region-highlights li::before {
        content: '';
        width: 6px; height: 6px;
        background: #3b82f6;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .info-tour-count {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 1rem;
        border-top: 1px solid rgba(0,0,0,0.06);
    }
    .info-tour-count span {
        font-weight: 600;
        color: var(--primary);
        font-size: 0.95rem;
    }
    .btn-view-tours {
        padding: 0.5rem 1.2rem;
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-view-tours:hover { background: var(--primary-dark); }

    /* ─── Filter Bar ─────────────────────────────────────── */
    .map-filter-bar {
        text-align: center;
        margin: 0 0 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
        flex-wrap: wrap;
    }
    .filter-label { font-size: 1rem; color: var(--text-secondary); }
    .filter-active-region {
        background: var(--primary);
        color: #fff;
        padding: 0.35rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .filter-clear {
        background: none;
        border: 1.5px solid var(--primary);
        color: var(--primary);
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-clear:hover { background: var(--primary); color: #fff; }

    /* ─── Tour Cards ─────────────────────────────────────── */
    .tour-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; }
    .tour-card:hover { transform: translateY(-6px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
    .tour-card.hidden-card { display: none; }
    .tour-card-img { height: 200px; background-size: cover; background-position: center; position: relative; }
    .tour-card-img .tour-duration { position: absolute; bottom: 12px; left: 12px; background: rgba(0,0,0,0.7); color: #fff; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .tour-card-img .tour-region-badge { position: absolute; top: 12px; right: 12px; background: rgba(59,130,246,0.9); color: #fff; padding: 0.25rem 0.7rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
    .tour-card-content { padding: 1.5rem; }
    .tour-card-content h3 { font-size: 1.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .tour-card-content .tour-desc { color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .tour-card-footer { display: flex; justify-content: space-between; align-items: center; }
    .tour-card-footer .price { font-size: 1.1rem; font-weight: 600; }
    .tour-card-footer .btn-details { padding: 0.4rem 1rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: var(--radius); font-weight: 600; font-size: 0.85rem; transition: background 0.2s; }
    .tour-card-footer .btn-details:hover { background: var(--primary-dark); }
    .tours-back { text-align: center; margin-top: 2.5rem; }
    .tours-back a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .tours-back a:hover { text-decoration: underline; }
    .no-tours-msg { text-align: center; padding: 3rem 1rem; color: var(--text-secondary); font-size: 1.1rem; display: none; }
    .no-tours-msg.visible { display: block; }

    @media (max-width: 900px) {
        .armenia-explorer { flex-direction: column; }
        .explorer-map { flex: none; max-width: 100%; width: 100%; }
        .explorer-info { min-height: 200px; }
    }
    @media (max-width: 480px) {
        .region-label { font-size: 8px; stroke-width: 2px; }
        .region-label.label-sm { font-size: 6.5px; }
    }
</style>
@endpush

@section('content')
<div class="tours-detail-page">
    <div class="section-header reveal">
        <h1 data-t="ingoing_title">Discover Armenia</h1>
        <p data-t="ingoing_subtitle">Explore the land of ancient monasteries, breathtaking mountains, and warm hospitality</p>
    </div>

    {{-- ─── Explorer: Map + Info Panel ───────────────────── --}}
    <div class="armenia-explorer reveal">
        <div class="explorer-map">
            <svg viewBox="0 0 793 803" xmlns="http://www.w3.org/2000/svg">
                {{-- Aragatsotn --}}
                <path class="armenia-region" data-region="aragatsotn"
                    d="m 184.5425,178.23394 3.08,-1.26 3.54,0.39 4.13,2.32 7.08,8.54 10.33,0 1.47,-7.37 5.02,-0.78 4.43,0 11.5,6.98 2.36,-2.32 2.66,0 3.84,2.71 2.36,3.88 0.29,7.76 -0.55,3.61 0,0 -0.33,2.2 6.49,8.14 2.06,5.42 8.85,10.46 7.38,2.32 14.16,2.32 -0.59,6.2 -2.06,7.73 -3.84,6.96 -4.13,5.42 -3.54,0 0.88,11.21 -4.71,10.04 0,11.58 -1.48,5.02 -7.97,-0.38 -2.36,6.17 -14.16,15.81 -3.84,2.7 -0.88,2.7 1.47,2.7 3.25,0 3.54,-1.93 3.54,0 1.77,3.08 0.59,3.86 -2.65,7.7 -1.66,2.47 0,0 -5.13,8.7 0,4.71 0,0 -4.16,-0.65 -4.47,-2.78 -19.84,-7.51 -5.75,-7.24 -1.5,-0.84 -1.49,0.56 -7.25,8.63 -3.2,1.67 -2.98,0 -3.84,-1.67 -1.92,-3.9 -2.13,-1.39 -2.35,0.83 1.28,20.04 -2.99,2.22 -22.39,-0.55 -7.46,-2.23 -15.78,-2.78 -10.45,-3.34 -9.81,-0.83 -10.88,-8.91 -3.199995,-0.83 -0.64,-4.73 -2.77,-0.84 -6.61,0 -4.69,-0.83 -7.68,-3.62 -14.29,1.11 -5.54,3.34 -1.4,1.69 0,0 -8.02,-3.59 -0.33,-5.11 -1.65,-5.47 -10.58,-10.81 -0.95,-1.86 0.83,-3.42 7.97,-13.31 -0.54,-6.12 -1.54,-3.52 -1.03,-1.65 -2.89,-2.11 0,0 4.68,-0.51 9.86,-2.58 8.22,-6.89 2.31,-4.31 6.25,-4.3 9.53,-0.87 3.29,-0.86 3.29,-2.15 2.63,-0.43 3.62,2.58 5.59,1.73 5.589995,-0.44 2.3,-1.72 2.96,0 4.28,1.72 3.94,3.02 6.91,0 7.56,-4.31 4.28,0 4.6,1.29 5.92,6.04 4.6,0.43 2.63,-1.73 2.96,-0.86 5.27,0 3.61,1.29 4.61,0 10.19,-3.87 3.62,-3.45 2.96,0 1.32,-3.45 0,-3.44 -5.6,-6.47 -0.32,-3.88 0,-5.61 2.96,-7.76 -11.84,-1.72 0,-6.91 -0.99,-3.02 -7.56,-11.66 -0.33,-4.75 0.98,-4.75 3.29,-3.89 4.94,-3.89 5.26,-1.3 7.56,-0.43 z" />

                {{-- Ararat --}}
                <path class="armenia-region" data-region="ararat"
                    d="m 233.3325,425.38394 -1.43,-2.65 -2.73,-2.78 -6.58,-1.84 -3.04,-1.96 0,0 1.99,-15.36 -0.21,-3.33 -5.97,-7.78 -0.64,-3.61 0.85,-3.33 2.34,-1.39 5.76,-0.56 10.88,0.28 5.54,0.83 2.14,1.95 2.13,0.55 0.39,-0.81 0,0 6.6,6.55 3.84,1.54 5.01,0.38 3.84,1.92 4.72,4.99 1.77,0.77 1.48,-0.77 5.01,-5.76 5.02,-0.38 2.65,-1.15 4.72,-4.62 5.91,-3.84 5.16,-8.27 0,0 -0.59,13.84 3.24,5 6.5,1.92 5.01,-0.38 2.07,-0.77 0,-3.85 1.47,-1.53 3.54,0 2.66,-3.08 2.65,-0.77 3.54,0.39 1.19,-3.85 2.06,-3.07 17.56,-0.96 4.13,-9.23 4.42,-1.93 4.72,0.39 5.32,-2.31 23.89,0.77 1.14,-0.89 0,0 1.37,0.7 0,1.92 -3.54,19.23 0,9.99 20.95,31.86 -0.29,14.96 1.47,4.22 0,6.89 -1.47,12.65 1.98,3.54 0,0 0.38,0.67 -7.97,5.36 -3.25,5.35 -1.47,4.6 -0.79,7.14 0,0 -1.91,0 -6.17,4.31 -2.85,3.69 -2.17,5.84 -5.67,3.53 -5.72,2.31 -3.77,0.9 -14.59,0.26 -14.91,1.63 -8.52,1.16 -7.06,2.23 -2.46,-0.34 -1.26,-1.33 0.42,-4.97 -2.37,-1.38 -3.44,0.15 -1.31,-5.07 -3.3,-4.51 -1.42,-0.9 -2.8,0.94 0.16,-2.6 1.55,-4 -2.32,-1.81 -1.7,0.05 -1.95,1.59 -2.08,0.24 -13.39,-8.94 1.32,-2.37 -0.34,-1.8 -4.38,1.52 -1.59,-0.61 -0.75,-1.08 -0.41,-7 -1.68,-2.99 -4.12,-2.91 -2.36,-2.81 -1.66,-8.55 -4.24,-4.85 -4.37,-2.48 -8.56,-12.52 -5.76,-1.67 -2.58,-2.89 -8.99,-6.77 -5.47,0 z" />

                {{-- Armavir --}}
                <path class="armenia-region" data-region="armavir"
                    d="m 54.552505,340.62394 1.4,-1.69 5.54,-3.34 14.29,-1.11 7.68,3.62 4.69,0.83 6.61,0 2.77,0.84 0.64,4.73 3.199995,0.83 10.88,8.91 9.81,0.83 10.45,3.34 15.78,2.78 7.46,2.23 22.39,0.55 2.99,-2.22 -1.28,-20.04 2.35,-0.83 2.13,1.39 1.92,3.9 3.84,1.67 2.98,0 3.2,-1.67 7.25,-8.63 1.49,-0.56 1.5,0.84 5.75,7.24 19.84,7.51 4.47,2.78 4.16,0.65 0,0 1.18,0.19 1.49,5.28 2.13,3.06 -1.71,11.67 1.82,1.26 -0.22,4.72 -0.67,1.41 0,0 -0.39,0.81 -2.13,-0.55 -2.14,-1.95 -5.54,-0.83 -10.88,-0.28 -5.76,0.56 -2.34,1.39 -0.85,3.33 0.64,3.61 5.97,7.78 0.21,3.33 -1.99,15.36 0,0 -3.57,-1.95 -2.37,0.1 -1.87,1.01 -1.24,-1.65 -2.21,-0.63 -1.53,-1.81 -4.88,-0.52 -5.21,1.1 -1.06,1.54 -1.93,-0.33 -1.73,2.05 -5.26,-0.3 -1.69,0.65 -1.02,2.53 -4.75,0.46 -4.65,-1.37 -6.25,0.79 -2.51,-1.82 -11.87,2.71 -13.77,0.77 -3.08,-0.15 -2.5,-1.08 -5.89,2.58 -4.74,-0.58 -11.2,0.68 -6.71,-2.89 -4.18,-3.39 -8.119995,-3.5 -2.42,-2.18 -3.47,-1.08 -8.91,-6.77 -8.09,1.03 -4.67,-1.08 -5.73,-5.03 -8.28,-3.42 -3.52,-4.49 -0.49,-3.34 0.93,-3.93 3.31,-4.58 12.51,-3.37 0.09,-2.01 -1.69,-2.32 -9.41,-7.6 -4.86,-5.58 -0.24,-1.7 0.95,-0.93 7.26,-0.53 -0.06,-9.3 z" />

                {{-- Yerevan --}}
                <path class="armenia-region" data-region="yerevan"
                    d="m 240.7325,356.00394 0,-4.71 5.13,-8.7 0,0 3.72,4.08 11.51,-0.39 12.98,-4.62 4.43,0 2.36,1.16 2.06,6.54 4.13,2.31 12.69,10.78 2.95,5 -0.88,5.77 -1.33,1.73 0,0 -5.16,8.27 -5.91,3.84 -4.72,4.62 -2.65,1.15 -5.02,0.38 -5.01,5.76 -1.48,0.77 -1.77,-0.77 -4.72,-4.99 -3.84,-1.92 -5.01,-0.38 -3.84,-1.54 -6.6,-6.55 0,0 0.67,-1.41 0.22,-4.72 -1.82,-1.26 1.71,-11.67 -2.13,-3.06 -1.49,-5.28 z" />

                {{-- Gegharkunik --}}
                <path class="armenia-region" data-region="gegharkunik"
                    d="m 509.2825,232.81394 -2.02,0.47 -1.54,-1.4 -3.92,-14.8 2.37,-5.3 4.04,-4.52 8.78,1.09 4.87,5.45 -1.07,8.1 -1.54,2.02 -1.9,1.09 -3.32,0.62 -2.25,5.92 -2.5,1.26 z m -169.71,-18.23 3.06,0.81 8.04,-0.62 2.84,-3.42 5.45,-3.86 3.12,-4.12 2.02,-1.4 5.27,2.23 9.1,6.5 6.05,0.94 3.93,-1.53 7.95,-0.77 15.63,-16.57 9.14,-4.37 3.52,0.05 6.43,-1.97 7.1,-0.93 7.81,1.24 7.1,2.18 7.81,3.73 15.05,14.14 0,0 -1.26,2.25 -0.41,3.72 0.55,1.74 4.53,2.83 1.19,1.71 -0.93,7.69 2.22,1.29 1.92,2.55 7.12,0.31 3.56,2.49 2.37,3.89 -0.59,1.87 -2.61,1.56 -0.71,2.18 1.31,3.89 -2.02,1.87 -0.59,2.33 0.95,5.29 3.82,5.6 10.14,7.86 1.98,0.37 1.03,1.2 -0.36,3.89 2.85,1.71 2.26,3.89 9.38,5.59 3.84,0.27 2.81,-0.73 3.68,2.33 4.51,5.75 3.92,2.17 0.83,4.19 1.9,3.57 1.9,1.55 10.8,0.16 5.48,4.19 3.56,1.09 2.9,-0.23 4.91,1.62 3.44,2.79 1.99,3.76 20.46,5.86 1.9,1.09 3.19,4.48 11.52,-0.3 6.77,4.96 0.03,5.79 -2.67,1.84 -1.96,-0.56 -0.14,2.31 5.68,6.13 4.11,5.59 0.46,1.73 -2.92,4.51 -0.81,3.84 -1.76,13.5 0.11,5.01 -0.95,1.7 -1.54,0.77 -2.89,0.03 -7.18,3.81 -0.13,2.97 1.31,2.47 -0.24,4.48 -1.9,7.57 -3.63,5.48 -0.52,3.14 -0.85,1.08 -1.44,0.84 -3.1,-0.48 -4.39,2.49 -1.5,5.36 -1.66,0.62 -1.7,-0.2 -4.24,-5.82 -3.44,-3.55 -2.14,-1.23 -3.8,1.54 -2.07,-1.03 -17.96,0.15 -3.68,-1.85 -1.42,0.15 -2.14,1.7 -3.09,1.08 -8.19,0.15 -2.24,2.14 -1.43,6.66 -5.87,0.08 0,0 -1.18,-1.66 -3.84,-1.92 -7.97,-0.38 -1.77,5.76 -5.9,4.6 -12.39,3.07 -28.62,1.54 -23.6,8.43 -12.98,9.97 -10.33,11.49 -4.43,2.68 -1.55,0.1 0,0 -1.99,-3.55 1.48,-12.64 0,-6.9 -1.48,-4.22 0.3,-14.96 -20.95,-31.86 0,-9.99 3.54,-19.23 0,-1.92 -1.37,-0.7 0,0 -1.14,-2.57 -5.02,-3.08 -3.83,0.39 -3.54,-1.92 -4.42,-6.16 -1.77,-11.94 -3.8,-6.44 -10.24,-47.71 2.82,-4.27 5.24,-4.86 -0.22,-1.13 -7.97,-1.54 -2.36,-1.16 -5.82,-5.29 -2.82,-8.36 -1.63,-8.17 -1.78,-3.11 -1.19,-1.67 -7.12,-2.41 -0.19,-28.99 0,0 1.29,0.29 z" />

                {{-- Kotayk --}}
                <path class="armenia-region" data-region="kotayk"
                    d="m 338.2025,214.24394 0.19,28.99 7.13,2.41 1.18,1.66 1.78,3.11 1.63,8.17 2.82,8.36 5.82,5.29 2.36,1.15 7.97,1.55 0.22,1.13 -5.24,4.86 -2.82,4.28 10.24,47.71 3.8,6.44 1.77,11.94 4.42,6.16 3.54,1.92 3.84,-0.38 5.01,3.08 1.14,2.57 0,0 -1.14,0.89 -23.89,-0.77 -5.32,2.31 -4.72,-0.39 -4.42,1.93 -4.13,9.23 -17.56,0.96 -2.06,3.07 -1.19,3.85 -3.54,-0.39 -2.65,0.77 -2.66,3.08 -3.54,0 -1.47,1.53 0,3.85 -2.07,0.77 -5.01,0.38 -6.5,-1.92 -3.24,-5 0.59,-13.84 0,0 1.33,-1.73 0.88,-5.77 -2.95,-5 -12.69,-10.78 -4.13,-2.31 -2.06,-6.54 -2.36,-1.16 -4.43,0 -12.98,4.62 -11.51,0.39 -3.72,-4.08 0,0 1.66,-2.47 2.65,-7.7 -0.59,-3.86 -1.77,-3.08 -3.54,0 -3.54,1.93 -3.25,0 -1.47,-2.7 0.88,-2.7 3.84,-2.7 14.16,-15.81 2.36,-6.17 7.97,0.38 1.48,-5.02 0,-11.58 4.71,-10.04 -0.88,-11.21 3.54,0 4.13,-5.42 3.84,-6.96 2.06,-7.73 0.59,-6.2 -14.16,-2.32 -7.38,-2.32 -8.85,-10.46 -2.06,-5.42 -6.49,-8.14 0.33,-2.2 0,0 7.63,-6.33 4.72,-2.71 18.89,-1.16 1.77,1.55 2.95,0.77 4.72,0 2.95,-1.94 4.43,0 8.85,2.72 10.32,5.42 6.99,2.37 2.06,2.9 7.57,1.99 2.58,1.64 0,0 0.26,2.34 1.43,1.52 0.67,-0.35 0,0 z" />

                {{-- Lori --}}
                <path class="armenia-region" data-region="lori"
                    d="m 356.2425,13.38394 0.47,3.4 2.13,2.51 4.02,1.57 2.6,1.88 1.89,3.13 -0.7,3.13 -5.92,6.58 -4.73,2.19 -2.6,0.32 -5.21,5.01 4.86,5.68 5.7,3.07 3.38,3.53 3.03,0.47 3.21,2.45 3.73,3.41 0.72,2.24 -1.52,6.12 -1.15,2.35 -2.32,2.35 -9.34,5.06 -4.37,0.71 -4.36,1.88 -5.34,0.35 -4.18,1.76 -3.3,3.88 -1.33,4.82 -0.09,3.76 1.42,6.11 5.7,3.76 14.51,3.25 2.58,1.17 2.94,4.58 0,3.29 -1.34,3.05 -4.89,3.17 -4.54,12.55 -1.34,9.15 -2.31,2.93 -4.1,2.34 -2.22,2.34 -11.75,6.52 -4.1,7.03 -0.71,5.97 1.16,1.41 5.96,2.34 0.54,1.4 -4.63,4.8 0.44,2.81 3.15,5.74 4.34,4.41 -1.06,2.58 0.58,1.98 -2.1,6.83 -1.24,1.41 0,0 -2.58,-1.64 -7.57,-1.99 -2.06,-2.9 -6.99,-2.37 -10.32,-5.42 -8.85,-2.72 -4.43,0 -2.95,1.94 -4.72,0 -2.95,-0.77 -1.77,-1.55 -18.89,1.16 -4.72,2.71 -7.63,6.33 0,0 0.55,-3.61 -0.29,-7.76 -2.36,-3.88 -3.84,-2.71 -2.66,0 -2.36,2.32 -11.5,-6.98 -4.43,0 -5.02,0.78 -1.47,7.37 -10.33,0 -7.08,-8.54 -4.13,-2.32 -3.54,-0.39 -3.08,1.26 0,0 0,-2.1 -3.62,0 -6.58,1.3 -4.93,-0.43 -0.33,-2.16 0.66,-1.73 -0.66,-2.17 -7.56,-3.46 -8.22,1.3 -3.29,-4.33 -1.98,-5.19 -0.98,-15.59 2.3,-4.77 4.93,-4.33 3.29,-1.73 3.95,-1.3 4.93,0 3.62,-2.61 2.3,-3.03 0,-5.2 -1.32,-2.61 4.61,0 3.29,-3.47 3.94,-7.37 0.33,-3.04 -1.31,-1.74 -2.63,-0.43 -9.21,1.3 -4.28,-0.87 -0.98,-2.17 0.65,-12.15 -2.63,-3.91 -3.61,-1.31 -0.99,-5.64 -1.97,-3.05 -1.65,-0.87 -3.29,0 -3.94,-1.73 -2.63,-1.74 -2.96,-3.48 -0.33,-18.7 2.54,-7.18 0,0 8.85,-3.39 17.26,0.72 4.76,-0.86 2.74,-7.55 1.69,-8.49 1.3,-1.31 1.43,-0.05 3.06,2.76 1.73,0.48 5.42,-0.63 3.41,2.75 1.49,3.15 2.81,1.57 4.01,1.18 2.82,-0.2 2.06,-1.65 2.05,0.27 2.39,1.04 3.74,3.68 2.81,-0.19 1.04,-2.17 0.89,-6.87 1.49,-1.48 6.02,3.43 6.24,7.09 5.29,1.61 6.19,3.65 1.42,1.81 4.1,-0.47 7.81,-4.45 4.96,-9.34 1.69,-0.35 1.16,0.7 -1.6,8.96 1.96,0.24 3.73,-3.19 5.35,-1.06 1.69,1.65 0.44,1.77 1.34,0.83 1.33,-0.95 2.94,-14.03 1.07,-0.82 1.6,0 2.76,1.77 3.83,0.23 5.34,-0.23 1.78,-1.18 1.6,0 1.69,5.19 1.16,1.18 6.71,0.65 0.74,0.77 0.24,2.8 1.28,0.68 4.89,-4.34 10.49,-0.38 2.08,0.83 3.73,-1.2 8.38,3.52 5.66,1 3.42,-0.19 4.08,-1.57 -1.23,-3.08 -2.09,-0.76 -3.23,1.32 -5.18,-2.59 -5.88,-0.87 -5.56,-6.29 -0.57,-8.68 0.48,-3.28 1.76,-4.15 1.61,-1.26 3.04,0.82 5.13,6.36 6.12,1.19 3.85,2.14 z" />

                {{-- Shirak --}}
                <path class="armenia-region" data-region="shirak"
                    d="m 64.992505,162.72394 -0.99,-4.31 -5.82,-8.12 -1.3,-7.51 0.12,-4.85 1.3,-6.72 -1.66,-2.97 -0.47,-7.51 -5.7,-4.39 -7.48,-7.36 -9.99,-6.03 -5.4,-0.74 -4.23,-4.45 -3.42,-2.36 -3.41,-1.17 -7.7100001,-0.34 -0.99,-2.7 -1.63,-1.56 0.3,-11.37 -0.74,-2.94 -2.23,-3.53 -2.37,-1.47 -0.91,-4.28 4.17,-8.5 4.81,0.01 3.5800001,-1.94 8.17,-1.96 5.34,-2.36 5.26,0.39 8.57,5.5 3.86,-1.57 11.41,-0.93 3.72,0.53 17.18,8.64 8.26,-10.62 7.58,-5.2 4.81,-2.24 11.869995,-1.17 3.71,-1.38 6.97,-0.59 4.15,0.98 1.79,1.57 2.22,0.2 8.01,-5.89 2.82,0.39 4.15,-0.59 1.33,1.23 0,0 -2.54,7.18 0.33,18.7 2.96,3.48 2.63,1.74 3.94,1.73 3.29,0 1.65,0.87 1.97,3.05 0.99,5.64 3.61,1.31 2.63,3.91 -0.65,12.15 0.98,2.17 4.28,0.87 9.21,-1.3 2.63,0.43 1.31,1.74 -0.33,3.04 -3.94,7.37 -3.29,3.47 -4.61,0 1.32,2.61 0,5.2 -2.3,3.03 -3.62,2.61 -4.93,0 -3.95,1.3 -3.29,1.73 -4.93,4.33 -2.3,4.77 0.98,15.59 1.98,5.19 3.29,4.33 8.22,-1.3 7.56,3.46 0.66,2.17 -0.66,1.73 0.33,2.16 4.93,0.43 6.58,-1.3 3.62,0 0,2.1 0,0 -0.99,11.31 -7.56,0.43 -5.26,1.3 -4.94,3.89 -3.29,3.89 -0.98,4.75 0.33,4.75 7.56,11.66 0.99,3.02 0,6.91 11.84,1.72 -2.96,7.76 0,5.61 0.32,3.88 5.6,6.47 0,3.44 -1.32,3.45 -2.96,0 -3.62,3.45 -10.19,3.87 -4.61,0 -3.61,-1.29 -5.27,0 -2.96,0.86 -2.63,1.73 -4.6,-0.43 -5.92,-6.04 -4.6,-1.29 -4.28,0 -7.56,4.31 -6.91,0 -3.94,-3.02 -4.28,-1.72 -2.96,0 -2.3,1.72 -5.589995,0.44 -5.59,-1.73 -3.62,-2.58 -2.63,0.43 -3.29,2.15 -3.29,0.86 -9.53,0.87 -6.25,4.3 -2.31,4.31 -8.22,6.89 -9.86,2.58 -4.68,0.51 0,0 -5.33,-5.27 -4.62,-1.08 -2.59,-2.71 -0.27,-2.91 2.32,-0.81 0.33,-3.66 4.87,-2.42 9.85,-8.37 1.05,-0.44 0.59,1.21 1.27,0.6 1.24,-5.82 2.08,1.82 1.94,0.09 -1.49,-8.99 6.97,-2.1 4.63,-5.45 3.2,-7.47 1.43,-1.09 1.9,-6.23 1.89,-2.03 1.43,-3.89 0.47,-4.68 2.5,-1.71 2.53,-5.14 -1.53,-3.16 0.89,-0.51 0.24,-4.84 -1.66,-3.5 1.72,-6.13 -1.51,-5.57 z" />

                {{-- Syunik --}}
                <path class="armenia-region" data-region="syunik"
                    d="m 589.9025,574.01394 -2.26,-8.95 -5.35,-5.07 -7.24,-2.45 -8.63,-1.3 0,0 -1.13,-12.04 -0.19,-14.96 3.98,-10.3 -0.95,-6.63 -1.89,-4.66 0.76,-9.58 4.92,-8.85 10.24,-13.27 7.16,-4.38 0,0 3.57,9.9 5.94,0.62 6.76,2.77 9.03,6.14 7.59,1.24 4.86,0 2.73,1.7 5.33,6 0.75,4.17 1.46,2.78 5.96,5.57 1.93,5.04 6.31,3.93 -1.3,2.9 0.95,3.53 1.54,1.39 4.51,-3.23 2.5,0.46 8.78,9.53 3.2,1.53 4.16,3.53 1.69,3.45 -0.86,3 1.49,3.9 0.29,7.19 2.49,-0.81 2.38,-3.53 6.76,-0.61 8.9,1.07 5.82,-4.91 9.25,-5.06 12.44,-0.94 1.15,5.59 1.43,1.84 4,-0.45 5.37,-5.69 1.43,0.61 -0.63,2.99 0.98,3.31 0,4.45 -1.42,4.44 1.42,2.61 5.54,1.59 2.16,-1.92 3.63,1.23 2.94,-2.48 12.6,-1.59 8.74,4.71 2.61,1.99 1.54,3.07 1.07,4.59 -0.59,1.54 -3.56,0 -1.83,-2.28 -2.55,0.05 -1.58,0.77 -2.62,3.86 -6.95,4.16 -0.23,2.08 1.18,3.39 7.98,7.72 1.77,3.21 -2.27,0.29 -2,-1.19 -4.5,-1.1 -1.58,-2.55 -2.28,-0.95 -3.37,2.44 -4.49,7.82 -1.49,0.91 -8.55,-2.07 -4.64,0.17 -1.75,2.48 -0.77,3.88 -2.87,3.22 0.52,9.26 3.68,5.81 1.67,4.43 2.13,1.38 1.3,-0.03 10.77,8.65 2.6,1.3 2.7,-1.43 3.38,-3.4 0.96,0.08 7.14,9.41 0.11,2.84 -2.85,2.45 -0.35,2.13 7.14,11.49 6.93,2.41 1.55,4.52 1.92,2.23 7.69,0.67 1.13,0.29 0.59,1.21 -1.22,1.06 -7.81,-0.15 -4.75,1.67 0.48,2.29 -0.65,2.15 -3.45,5.6 -2.65,2.16 -2.27,3.51 -2.14,-1.52 -2.14,-10.52 -2.85,-1.03 -2.01,2.47 0.95,4.81 -0.6,1.07 -1.9,-0.31 -2.72,-3.11 -4.78,-2.23 -5.16,-0.16 -1.84,2.1 -0.03,6.03 2.19,6.24 3.35,0.66 3.38,2.12 3.86,-1.65 2.14,1.32 -1.96,3.51 0.09,2.42 4.08,2.8 0.02,3.53 1.03,2.2 3.97,2.23 2.29,3.44 -0.3,2.22 -2.31,1.55 -0.84,2.85 1.6,1.94 4.57,0.87 -3.13,4.3 -0.24,1.83 2.61,11.72 -6.05,2.12 -0.36,3.2 1.19,4.25 -1.8,2.23 -3.42,1.88 -0.12,2.28 0.36,3.34 3.32,4.41 4.51,8.2 1.98,7.98 -2.49,1.85 -2.35,-0.72 -0.62,-4.28 -1.01,-0.96 -8.55,1.38 -3.28,-1.04 -2.92,-2.46 -1.18,-0.15 -2.95,0.76 -4.17,-0.35 -1.54,1.97 -1.6,0.16 -11.93,-8.82 -2.61,-1.06 -3.44,0 -2.49,0.91 -5.11,4.71 -2.02,-0.76 -5.46,0.15 -0.8,3.25 0.69,3.43 -1.78,2.43 -2.14,-0.15 0.47,-5.77 -1.42,-2.12 -1.07,0 -1.66,1.36 -1.31,3.8 0,3.03 -1.18,0.61 -2.97,-0.61 -0.95,2.28 2.32,3.98 0,2.12 -1.19,1.22 -2.02,-1.22 -0.73,-1.86 0,0 -0.2,-0.53 0,0 -2.39,-1.4 -2.26,0 -2.96,1.82 -2.14,4.25 -2.37,-1.21 0.11,-3.8 -1.66,-1.21 -2.39,7 -3.66,-0.32 1.19,-15.64 -1.31,-5.01 -3.32,-4.86 -2.02,-6.53 -2.49,-11.71 -0.81,-7.79 0.24,-5.18 -6.08,-5.05 -0.83,-2.59 0.24,-12.02 -1.54,-2.74 -6.3,-2.9 -4.24,-3.44 -0.53,-3.01 0.68,-6.88 -4.81,-6.48 1.18,-7.14 -3.47,-3.52 0,-5.62 -0.83,-3.58 -0.26,-10.9 1.09,-2.95 1.82,-0.7 0.47,-4.89 -0.35,-4.12 -1.63,-2.65 -8.28,-1.99 -5.7,-4.13 -2.49,-2.9 -4.16,-1.07 -7.24,-5.81 -4.39,-2.44 -1.57,-2.04 -11.7,-1.48 -1.58,-0.55 -3.51,-3.77 -0.93,-2.5 0.32,-4.69 3.15,-6.2 8.35,-11.67 0,-3.52 -1.35,-4.32 -2.61,-2.76 -0.8,-2.41 0.78,-10.36 z" />

                {{-- Tavush --}}
                <path class="armenia-region" data-region="tavush"
                    d="m 475.9625,206.84394 -15.04,-14.14 -7.81,-3.73 -7.1,-2.18 -7.8,-1.24 -7.1,0.93 -6.43,1.97 -3.52,-0.05 -9.14,4.37 -15.63,16.56 -7.95,0.77 -3.93,1.53 -6.05,-0.93 -9.1,-6.5 -5.26,-2.23 -2.02,1.4 -3.11,4.12 -5.46,3.86 -2.83,3.41 -8.05,0.63 -3.06,-0.81 0,0 -4.7,-1.16 0,0 -0.67,0.35 -1.43,-1.52 -0.26,-2.34 0,0 1.24,-1.41 2.1,-6.83 -0.58,-1.98 1.06,-2.58 -4.34,-4.41 -3.15,-5.74 -0.44,-2.81 4.63,-4.8 -0.54,-1.4 -5.96,-2.34 -1.16,-1.41 0.71,-5.97 4.1,-7.03 11.75,-6.52 2.22,-2.34 4.1,-2.34 2.31,-2.93 1.34,-9.15 4.54,-12.55 4.89,-3.17 1.34,-3.05 0,-3.29 -2.94,-4.58 -2.58,-1.17 -14.51,-3.25 -5.7,-3.76 -1.42,-6.11 0.09,-3.76 1.33,-4.82 3.3,-3.88 4.18,-1.76 5.34,-0.35 4.36,-1.88 4.37,-0.71 9.34,-5.06 2.32,-2.35 1.15,-2.35 1.52,-6.12 -0.72,-2.24 -3.73,-3.41 -3.21,-2.45 -3.03,-0.47 -3.38,-3.53 -5.7,-3.07 -4.86,-5.68 5.21,-5.01 2.6,-0.32 4.73,-2.19 5.92,-6.58 0.7,-3.13 -1.89,-3.13 -2.6,-1.88 -4.02,-1.57 -2.13,-2.51 -0.47,-3.4 0,0 -0.15,-2.6 1.61,-1.82 2.38,1.44 1.98,3.23 5.23,1.39 5.05,0.04 1.42,-1.01 2.99,-5.09 10.37,-7.95 1.94,1.32 2.01,7.16 5.58,7.52 0.22,2.42 -2.02,7.21 -4.64,3.47 0.31,2.37 1.84,0.78 3.09,-0.78 2.79,3.14 2.61,1.42 2.02,2.98 2.79,-0.08 1.66,-1.49 1.2,-3.19 3.43,-0.82 1.84,-2.04 1.24,-0.24 3.62,2.68 6.31,8.5 7.19,1.18 2.01,2.12 0.6,4.79 -1.48,1.06 -3.06,4.37 -11.06,4.08 -12.26,1.48 -3,2.19 -2.31,3.79 -2.1,7.15 -2.45,3.59 1.1,3.87 2.22,1.64 3.17,-0.22 4.76,-3.42 2.42,-0.08 2.16,0.96 1.79,-0.69 0.87,-6.68 -1.08,-3.67 1.48,-1.85 1.89,-0.2 5.42,2.98 1.01,1.72 0.18,1.89 -1.36,4.31 0.65,2.35 4.46,-0.53 7.18,2.67 3.2,5.8 4.36,4.72 4.36,-1.51 3.09,-5.33 3.76,-0.84 1.61,1.31 1.72,5.8 3.02,3.91 7.49,-0.46 4.92,1.8 1.43,-0.55 1.74,-2.7 10.02,-5.24 5.9,-0.73 1.19,0.49 1.2,2.02 -1.12,2.35 -2.2,2.82 -5.93,4.46 0.17,1.96 2.67,5.33 3.53,1.2 4.72,-2.16 1.5,0.13 3.86,3.68 2.78,4.3 -0.29,1.88 -1.13,1.49 0.83,2.11 5.88,0.78 1.78,1.17 3.26,4.31 4.33,2.27 2.79,0.07 1.48,0.94 0.48,3.13 1.42,1.57 3.5,-2.59 1.84,0 1.9,1.02 1.29,1.77 2.63,7.89 0.18,4.23 -1.6,2.5 -1.43,4.45 -6.41,5.86 -4.63,6.32 -0.77,2.89 -2.07,2.19 -9.38,1.01 -6.53,5.94 -7.24,0.73 -5.1,1.72 -7.83,4.83 -2.02,2.81 -0.12,2.5 1.07,3.74 -1.66,1.72 -1.19,-0.47 -0.83,-1.87 -2.73,0.31 z" />

                {{-- Vayots Dzor --}}
                <path class="armenia-region" data-region="vayots_dzor"
                    d="m 408.5725,495.17394 -1.7,-1.14 -4.5,-0.31 0,0 0.79,-7.14 1.47,-4.6 3.25,-5.35 7.97,-5.36 -0.38,-0.67 0,0 1.56,-0.1 4.42,-2.68 10.33,-11.49 12.98,-9.97 23.61,-8.43 28.62,-1.53 12.39,-3.07 5.9,-4.6 1.77,-5.76 7.97,0.38 3.83,1.92 1.19,1.66 0,0 5.99,-0.01 1.54,6.25 1.67,0.93 11.03,-0.46 2.73,1.23 8.19,0.93 5.58,4.01 1.54,2 9.26,0.62 2.73,0.46 1.54,1.08 2.02,2.16 1.44,4.22 0,1.24 -2.38,2.15 -0.59,1.7 0.95,2.62 4.51,7.09 1.53,6.42 0,0 -7.16,4.38 -10.24,13.27 -4.92,8.85 -0.76,9.58 1.89,4.66 0.95,6.63 -3.98,10.3 0.19,14.96 1.13,12.04 0,0 -3.88,-2.08 -2.2,-0.22 -3.36,2.73 -2.01,2.69 -6.28,1.4 -6.05,4.76 -9.26,-1.38 -2.01,1.38 -3.21,5.82 -3.32,1.84 -2.85,-0.15 -2.14,1.84 -0.74,2.8 -8.39,5.78 -2.97,2.61 -0.51,1.58 -7.57,-0.58 -3.66,-7.39 -2.23,-2.13 -5.47,0.17 -3.73,-2.84 -8.9,0.3 -2.37,-1.37 -7.01,-6.44 -4.83,-11.42 -4.38,-3.23 -7.04,-0.95 -2.21,1.2 -3.52,4.29 -2.18,4.22 -2.98,0.33 -7.27,-3.34 0.09,-14.38 1.8,-3.88 -2.37,-2.15 -0.12,-3.99 2.52,-2.95 -1.68,-5.21 -3.69,-2.66 -3.52,-5.17 -2.47,-6.81 -4.79,-7.61 -6.86,-5.37 z" />

                {{-- Lake Sevan --}}
                <polygon class="lake-sevan"
                    points="385,252 398,244 412,240 424,242 436,248 444,258 450,272 452,288 449,302 446,316 448,330 454,344 460,356 462,364 456,368 448,364 440,354 434,340 428,326 422,312 416,298 408,286 398,274 390,264" />
                <text class="region-label" x="438" y="300" style="fill:#1e5090; stroke:none; font-size:18px;">Lake Sevan</text>

                {{-- Region Labels --}}
                <text class="region-label" x="108" y="195" data-t="region_shirak">Shirak</text>
                <text class="region-label" x="290" y="115" data-t="region_lori">Lori</text>
                <text class="region-label" x="455" y="118" data-t="region_tavush">Tavush</text>
                <text class="region-label" x="170" y="280" data-t="region_aragatsotn">Aragatsotn</text>
                <text class="region-label" x="325" y="298" data-t="region_kotayk">Kotayk</text>
                <text class="region-label" x="132" y="395" data-t="region_armavir">Armavir</text>
                <text class="region-label label-sm" x="272" y="368" data-t="region_yerevan">Yerevan</text>
                <text class="region-label" x="310" y="455" data-t="region_ararat">Ararat</text>
                <text class="region-label" x="548" y="350" data-t="region_gegharkunik">Gegharkunik</text>
                <text class="region-label" x="490" y="508"><tspan x="490" dy="0">Vayots</tspan><tspan x="490" dy="20">Dzor</tspan></text>
                <text class="region-label" x="650" y="635" data-t="region_syunik">Syunik</text>
            </svg>
        </div>

        <div class="explorer-info">
            <div class="info-panel">
                {{-- Default state --}}
                <div class="info-default" id="infoDefault">
                    <div class="info-icon">&#x1F5FA;</div>
                    <h3 data-t="explore_regions">Explore Armenia's Regions</h3>
                    <p data-t="explore_regions_desc">Click on any region on the map to discover its unique attractions and available tours.</p>
                </div>

                {{-- Region info panels --}}
                @php
                $regions = [
                    'yerevan' => ['desc' => 'The pink city — Armenia\'s vibrant capital with cafes, museums, and stunning Mount Ararat views.', 'area' => '223', 'pop' => '1,092,800', 'highlights' => ['Republic Square', 'The Cascade', 'Vernissage Market', 'Matenadaran']],
                    'aragatsotn' => ['desc' => 'Home to Armenia\'s highest peak, Mount Aragats, and ancient fortresses set among alpine meadows.', 'area' => '2,756', 'pop' => '126,300', 'highlights' => ['Mount Aragats', 'Amberd Fortress', 'Byurakan Observatory', 'Saghmosavank']],
                    'ararat' => ['desc' => 'Sacred land at the foot of biblical Mount Ararat with iconic monasteries and fertile valleys.', 'area' => '2,096', 'pop' => '260,400', 'highlights' => ['Khor Virap Monastery', 'Dvin Ancient Capital', 'Ararat Valley Views']],
                    'armavir' => ['desc' => 'The spiritual heart of Armenia, home to Echmiadzin — the oldest cathedral in the world.', 'area' => '1,242', 'pop' => '265,800', 'highlights' => ['Echmiadzin Cathedral', 'Zvartnots Temple', 'Sardarapat Memorial']],
                    'gegharkunik' => ['desc' => 'Home to Lake Sevan, the "Pearl of Armenia" — the largest lake in the Caucasus region.', 'area' => '5,348', 'pop' => '215,400', 'highlights' => ['Lake Sevan', 'Sevanavank Monastery', 'Hayravank', 'Noratus Cemetery']],
                    'kotayk' => ['desc' => 'Cultural treasures from Greco-Roman temples to rock-carved monasteries, minutes from Yerevan.', 'area' => '2,089', 'pop' => '254,400', 'highlights' => ['Garni Temple', 'Geghard Monastery', 'Symphony of Stones', 'Azat River Gorge']],
                    'lori' => ['desc' => 'Lush green gorges with UNESCO World Heritage monasteries — masterpieces of medieval architecture.', 'area' => '3,799', 'pop' => '218,400', 'highlights' => ['Haghpat Monastery', 'Sanahin Monastery', 'Akhtala Fortress', 'Odzun Church']],
                    'shirak' => ['desc' => 'Home to Gyumri, Armenia\'s cultural capital with unique black tufa architecture and vibrant arts.', 'area' => '2,681', 'pop' => '235,600', 'highlights' => ['Gyumri Old Quarter', 'Sev Berd Fortress', 'Marmashen Monastery']],
                    'syunik' => ['desc' => 'Dramatic southern landscapes with the world\'s longest reversible aerial tramway to Tatev.', 'area' => '4,506', 'pop' => '137,600', 'highlights' => ['Tatev Monastery', 'Wings of Tatev', 'Khndzoresk Caves', 'Shaki Waterfall']],
                    'tavush' => ['desc' => 'The "Armenian Switzerland" — pristine forests, crystal-clear lakes, and serene monasteries.', 'area' => '2,704', 'pop' => '121,900', 'highlights' => ['Dilijan National Park', 'Goshavank', 'Haghartsin', 'Parz Lake']],
                    'vayots_dzor' => ['desc' => 'Red rock canyons, medieval monasteries, and the birthplace of the world\'s oldest winemaking.', 'area' => '2,308', 'pop' => '48,800', 'highlights' => ['Noravank Monastery', 'Areni Cave & Winery', 'Jermuk Waterfall']],
                ];
                @endphp

                @foreach($regions as $slug => $info)
                <div class="info-region" data-info="{{ $slug }}">
                    <h3>
                        <span class="region-dot"></span>
                        <span data-t="region_{{ $slug }}">{{ ucwords(str_replace('_', ' ', $slug)) }}</span>
                    </h3>
                    <p class="region-desc">{{ $info['desc'] }}</p>
                    <div class="region-stats">
                        <div class="region-stat">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span class="stat-label">Area &mdash;</span>
                            <span class="stat-value">{{ $info['area'] }} km&sup2;</span>
                        </div>
                        <div class="region-stat">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                            <span class="stat-label">Population &mdash;</span>
                            <span class="stat-value">{{ $info['pop'] }}</span>
                        </div>
                    </div>
                    <ul class="region-highlights">
                        @foreach($info['highlights'] as $h)
                        <li>{{ $h }}</li>
                        @endforeach
                    </ul>
                    <div class="info-tour-count">
                        <span>{{ $tourCounts[$slug] ?? 0 }} {{ ($tourCounts[$slug] ?? 0) === 1 ? 'tour' : 'tours' }} available</span>
                        <button class="btn-view-tours" onclick="document.getElementById('tourGrid').scrollIntoView({behavior:'smooth'})" data-t="view_tours">View Tours</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ─── Filter Bar ───────────────────────────────────── --}}
    <div class="map-filter-bar" id="mapFilterBar" style="display:none;">
        <span class="filter-label" data-t="showing_tours_in">Showing tours in:</span>
        <span class="filter-active-region" id="activeRegionName"></span>
        <button class="filter-clear" id="clearFilter" data-t="show_all">Show All</button>
    </div>

    {{-- ─── Tour Cards Grid ──────────────────────────────── --}}
    <div class="card-grid" id="tourGrid">
        @foreach($tours as $tour)
        <div class="tour-card reveal" data-region="{{ $tour->region }}">
            <div class="tour-card-img lazy-bg" data-bg="{{ $tour->image_url }}">
                <span class="tour-duration">{{ $tour->duration }}</span>
                <span class="tour-region-badge" data-t="region_{{ $tour->region }}">{{ ucwords(str_replace('_', ' ', $tour->region)) }}</span>
            </div>
            <div class="tour-card-content">
                <h3>{{ $tour->title }}</h3>
                <p class="tour-desc">{{ $tour->description }}</p>
                <div class="tour-card-footer">
                    <span class="price">From ${{ number_format($tour->price_from, 0) }}</span>
                    <a href="/contact" class="btn-details" data-t="inquire">Inquire</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="no-tours-msg" id="noToursMsg" data-t="no_tours_region">No tours available in this region yet. Contact us for a custom tour!</div>

    <div class="tours-back reveal">
        <a href="/tours" data-t="back_to_tours">&larr; Back to Tours</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var regions = document.querySelectorAll('.armenia-region');
    var cards = document.querySelectorAll('.tour-card[data-region]');
    var filterBar = document.getElementById('mapFilterBar');
    var activeRegionName = document.getElementById('activeRegionName');
    var clearBtn = document.getElementById('clearFilter');
    var noToursMsg = document.getElementById('noToursMsg');
    var infoDefault = document.getElementById('infoDefault');
    var infoPanels = document.querySelectorAll('.info-region');
    var activeRegion = null;

    function getRegionDisplayName(region) {
        var label = document.querySelector('.region-label[data-t="region_' + region + '"]');
        if (label) return label.textContent;
        return region.replace('_', ' ');
    }

    function showRegionInfo(region) {
        infoDefault.style.display = 'none';
        infoPanels.forEach(function(p) {
            p.classList.toggle('visible', p.getAttribute('data-info') === region);
        });
    }

    function filterTours(region) {
        if (activeRegion === region) {
            clearFilter();
            return;
        }
        activeRegion = region;

        // Update map highlights
        regions.forEach(function(r) { r.classList.remove('active'); });
        var sel = document.querySelector('.armenia-region[data-region="' + region + '"]');
        if (sel) sel.classList.add('active');

        // Show region info
        showRegionInfo(region);

        // Filter tour cards
        var count = 0;
        cards.forEach(function(card) {
            if (card.getAttribute('data-region') === region) {
                card.classList.remove('hidden-card');
                count++;
            } else {
                card.classList.add('hidden-card');
            }
        });

        // Filter bar
        activeRegionName.textContent = getRegionDisplayName(region);
        filterBar.style.display = 'flex';
        noToursMsg.classList.toggle('visible', count === 0);
    }

    function clearFilter() {
        activeRegion = null;
        regions.forEach(function(r) { r.classList.remove('active'); });
        cards.forEach(function(c) { c.classList.remove('hidden-card'); });
        filterBar.style.display = 'none';
        noToursMsg.classList.remove('visible');
        infoDefault.style.display = '';
        infoPanels.forEach(function(p) { p.classList.remove('visible'); });
    }

    regions.forEach(function(r) {
        r.addEventListener('click', function() {
            filterTours(this.getAttribute('data-region'));
        });
    });

    clearBtn.addEventListener('click', clearFilter);
})();
</script>
@endpush
