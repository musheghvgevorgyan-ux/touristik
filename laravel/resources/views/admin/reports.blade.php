@extends('layouts.main')

@section('title', 'Analytics & Reports - Admin')

@push('styles')
<style>
    .reports-page { max-width: 1200px; margin: 0 auto; padding: 2rem; }
    .admin-header { margin-bottom: 2rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .admin-header p { color: #6c757d; }

    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat-card { background: #fff; padding: 1.2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center; }
    .stat-card .stat-icon { font-size: 1.5rem; margin-bottom: 0.3rem; }
    .stat-card .stat-val { font-size: 1.6rem; font-weight: 700; color: #FF6B35; }
    .stat-card .stat-label { font-size: 0.8rem; color: #6c757d; margin-top: 0.2rem; }
    .stat-card .stat-sub { font-size: 0.75rem; color: #28a745; margin-top: 0.2rem; }

    .reports-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
    .report-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden; }
    .report-card h3 { padding: 1rem 1.5rem; margin: 0; font-size: 1.1rem; color: var(--text-heading); border-bottom: 1px solid #f0f0f0; }

    .chart-area { padding: 1.5rem; height: 250px; display: flex; align-items: flex-end; gap: 6px; }
    .chart-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px; }
    .chart-stack { display: flex; flex-direction: column; gap: 2px; align-items: center; width: 100%; height: 200px; justify-content: flex-end; }
    .chart-bar { width: 80%; border-radius: 3px 3px 0 0; min-height: 2px; transition: height 0.5s; position: relative; }
    .chart-bar:hover { opacity: 0.8; }
    .chart-bar.bar-users { background: #20c997; }
    .chart-bar.bar-contacts { background: #FF6B35; }
    .chart-bar.bar-bookings { background: #0d6efd; }
    .chart-lbl { font-size: 0.65rem; color: #999; margin-top: 2px; }
    .chart-legend { display: flex; gap: 1rem; padding: 0 1.5rem 1rem; flex-wrap: wrap; }
    .chart-legend span { font-size: 0.8rem; color: #666; display: flex; align-items: center; gap: 4px; }
    .chart-legend .dot { width: 10px; height: 10px; border-radius: 50%; }

    .type-bars { padding: 1.5rem; }
    .type-row { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
    .type-label { width: 80px; font-size: 0.9rem; font-weight: 600; color: #333; text-transform: capitalize; }
    .type-bar-wrap { flex: 1; background: #f0f0f0; border-radius: 20px; height: 28px; overflow: hidden; }
    .type-bar-fill { height: 100%; border-radius: 20px; display: flex; align-items: center; padding: 0 10px; color: #fff; font-weight: 600; font-size: 0.8rem; transition: width 0.5s; }
    .type-bar-fill.ingoing { background: linear-gradient(90deg, #FF6B35, #f7a072); }
    .type-bar-fill.outgoing { background: linear-gradient(90deg, #0d6efd, #6ea8fe); }
    .type-bar-fill.transfer { background: linear-gradient(90deg, #20c997, #6edbb5); }

    .contacts-list { max-height: 350px; overflow-y: auto; }
    .contact-item { padding: 0.8rem 1.5rem; border-bottom: 1px solid #f0f0f0; display: flex; gap: 0.8rem; align-items: flex-start; }
    .contact-item:last-child { border-bottom: none; }
    .contact-avatar { width: 32px; height: 32px; border-radius: 50%; background: #FFF3ED; color: #FF6B35; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; flex-shrink: 0; }
    .contact-info { flex: 1; min-width: 0; }
    .contact-info strong { font-size: 0.85rem; color: #333; }
    .contact-info p { margin: 0.2rem 0 0; font-size: 0.78rem; color: #999; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .contact-info .contact-date { font-size: 0.72rem; color: #bbb; }
    .badge-new { background: #FF6B35; color: #fff; padding: 0.15rem 0.5rem; border-radius: 3px; font-size: 0.7rem; font-weight: 600; }

    .back-link { margin-top: 1.5rem; }
    .back-link a { color: #FF6B35; text-decoration: none; font-weight: 600; }

    @media (max-width: 768px) { .reports-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="reports-page">
    <div class="admin-header">
        <h1>Analytics & Reports</h1>
        <p>Platform performance overview — {{ now()->format('F Y') }}</p>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon">&#128101;</div>
            <div class="stat-val">{{ $stats['total_users'] }}</div>
            <div class="stat-label">Total Users</div>
            @if($stats['new_users_month'] > 0)<div class="stat-sub">+{{ $stats['new_users_month'] }} this month</div>@endif
        </div>
        <div class="stat-card">
            <div class="stat-icon">&#128203;</div>
            <div class="stat-val">{{ $stats['total_bookings'] }}</div>
            <div class="stat-label">Bookings</div>
            @if($stats['bookings_month'] > 0)<div class="stat-sub">+{{ $stats['bookings_month'] }} this month</div>@endif
        </div>
        <div class="stat-card">
            <div class="stat-icon">&#9993;</div>
            <div class="stat-val">{{ $stats['total_contacts'] }}</div>
            <div class="stat-label">Messages</div>
            @if($stats['contacts_month'] > 0)<div class="stat-sub">+{{ $stats['contacts_month'] }} this month</div>@endif
        </div>
        <div class="stat-card">
            <div class="stat-icon">&#127960;</div>
            <div class="stat-val">{{ $stats['total_tours'] }}</div>
            <div class="stat-label">Tours</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">&#127758;</div>
            <div class="stat-val">{{ $stats['total_destinations'] }}</div>
            <div class="stat-label">Destinations</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">&#128221;</div>
            <div class="stat-val">{{ $stats['total_posts'] }}</div>
            <div class="stat-label">Blog Posts</div>
        </div>
    </div>

    <div class="reports-grid">
        <div class="report-card">
            <h3>Activity — Last 12 Months</h3>
            <div class="chart-area">
                @php
                    $maxVal = max(1, max(array_column($monthly, 'users')), max(array_column($monthly, 'contacts')), max(array_column($monthly, 'bookings')));
                @endphp
                @foreach($monthly as $m)
                <div class="chart-col">
                    <div class="chart-stack">
                        <div class="chart-bar bar-bookings" style="height: {{ max(2, ($m['bookings'] / $maxVal) * 180) }}px;" title="{{ $m['bookings'] }} bookings"></div>
                        <div class="chart-bar bar-contacts" style="height: {{ max(2, ($m['contacts'] / $maxVal) * 180) }}px;" title="{{ $m['contacts'] }} messages"></div>
                        <div class="chart-bar bar-users" style="height: {{ max(2, ($m['users'] / $maxVal) * 180) }}px;" title="{{ $m['users'] }} users"></div>
                    </div>
                    <span class="chart-lbl">{{ $m['short'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="chart-legend">
                <span><span class="dot" style="background:#0d6efd;"></span> Bookings</span>
                <span><span class="dot" style="background:#FF6B35;"></span> Messages</span>
                <span><span class="dot" style="background:#20c997;"></span> Users</span>
            </div>
        </div>

        <div class="report-card">
            <h3>Tours by Type</h3>
            <div class="type-bars">
                @php $maxTours = max(1, max($toursByType)); @endphp
                @foreach($toursByType as $type => $count)
                <div class="type-row">
                    <span class="type-label">{{ $type }}</span>
                    <div class="type-bar-wrap">
                        <div class="type-bar-fill {{ $type }}" style="width: {{ max(5, ($count / $maxTours) * 100) }}%;">{{ $count }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            <h3 style="margin-top:1rem;">Recent Messages</h3>
            <div class="contacts-list">
                @forelse($recentContacts as $c)
                <div class="contact-item">
                    <div class="contact-avatar">{{ strtoupper(substr($c->name, 0, 1)) }}</div>
                    <div class="contact-info">
                        <strong>{{ $c->name }}</strong>
                        @if($c->status === 'new') <span class="badge-new">NEW</span> @endif
                        <p>{{ $c->message }}</p>
                        <span class="contact-date">{{ $c->created_at ? $c->created_at->diffForHumans() : '' }}</span>
                    </div>
                </div>
                @empty
                <div style="padding:2rem;text-align:center;color:#999;">No messages yet</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="back-link">
        <a href="/admin">&larr; Back to Dashboard</a>
    </div>
</div>
@endsection
