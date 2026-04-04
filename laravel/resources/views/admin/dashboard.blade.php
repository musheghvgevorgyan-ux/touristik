@extends('layouts.main')

@section('title', 'Admin Dashboard - Touristik')

@push('styles')
<style>
    .admin-page { max-width: 1200px; margin: 0 auto; padding: 2rem; }
    .admin-header { margin-bottom: 2rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin-bottom: 0.3rem; }
    .admin-header p { color: #6c757d; }
    .admin-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .admin-stat { background: #fff; padding: 1.2rem 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
    .admin-stat .stat-icon { font-size: 1.5rem; margin-bottom: 0.3rem; }
    .admin-stat .stat-value { font-size: 1.8rem; font-weight: 700; color: #FF6B35; }
    .admin-stat .stat-label { color: #6c757d; font-size: 0.85rem; margin-top: 0.2rem; }
    .admin-stat.highlight { background: linear-gradient(135deg, #FF6B35, #f7a072); }
    .admin-stat.highlight .stat-value, .admin-stat.highlight .stat-label, .admin-stat.highlight .stat-icon { color: #fff; }
    .dashboard-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
    .dash-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden; }
    .dash-card h3 { padding: 1rem 1.5rem; margin: 0; font-size: 1.1rem; color: var(--text-heading); border-bottom: 1px solid #f0f0f0; }
    .chart-container { padding: 1.5rem; height: 220px; display: flex; align-items: flex-end; gap: 8px; }
    .chart-bar-group { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .chart-bars { display: flex; gap: 3px; align-items: flex-end; height: 160px; }
    .chart-bar { width: 18px; border-radius: 4px 4px 0 0; transition: height 0.5s; position: relative; }
    .chart-bar:hover { opacity: 0.8; }
    .chart-bar.bookings { background: #FF6B35; }
    .chart-bar.users { background: #20c997; }
    .chart-label { font-size: 0.75rem; color: #999; }
    .chart-legend { display: flex; gap: 1rem; padding: 0 1.5rem 1rem; }
    .chart-legend span { font-size: 0.8rem; color: #666; display: flex; align-items: center; gap: 4px; }
    .chart-legend .dot { width: 10px; height: 10px; border-radius: 50%; }
    .msg-list { max-height: 340px; overflow-y: auto; }
    .msg-item { padding: 1rem 1.5rem; border-bottom: 1px solid #f0f0f0; display: flex; gap: 0.8rem; align-items: flex-start; }
    .msg-item:last-child { border-bottom: none; }
    .msg-avatar { width: 36px; height: 36px; border-radius: 50%; background: #FFF3ED; color: #FF6B35; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
    .msg-content { flex: 1; min-width: 0; }
    .msg-content strong { font-size: 0.9rem; color: var(--text-heading); }
    .msg-content p { margin: 0.2rem 0 0; font-size: 0.8rem; color: #999; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .msg-badge { width: 8px; height: 8px; border-radius: 50%; background: #FF6B35; flex-shrink: 0; margin-top: 6px; }
    .admin-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; }
    .admin-table-wrap h3 { padding: 1rem 1.5rem; margin: 0; font-size: 1.1rem; border-bottom: 1px solid #f0f0f0; }
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { text-align: left; padding: 0.7rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; }
    .admin-table td { padding: 0.7rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
    .admin-table tr:hover td { background: #f8f9fa; }
    .badge { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
    .badge-confirmed { background: #d4edda; color: #155724; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
    .admin-quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.8rem; margin-top: 2rem; }
    .admin-quick-link { display: flex; align-items: center; gap: 0.7rem; background: #fff; padding: 0.9rem 1.1rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; color: #333; transition: transform 0.2s; font-size: 0.9rem; }
    .admin-quick-link:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .admin-quick-link .link-icon { font-size: 1.3rem; }
    .view-all { display: block; text-align: center; padding: 0.8rem; color: #FF6B35; text-decoration: none; font-weight: 600; font-size: 0.85rem; border-top: 1px solid #f0f0f0; }
    @media (max-width: 768px) { .dashboard-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="admin-page">
    <div class="admin-header">
        <h1>Dashboard</h1>
        <p>Welcome back! Here's what's happening with Touristik.</p>
    </div>

    <div class="admin-stats">
        @if($newMessages > 0)
        <div class="admin-stat highlight">
            <div class="stat-icon">&#9993;</div>
            <div class="stat-value">{{ $newMessages }}</div>
            <div class="stat-label">New Messages</div>
        </div>
        @else
        <div class="admin-stat">
            <div class="stat-icon">&#9993;</div>
            <div class="stat-value">0</div>
            <div class="stat-label">New Messages</div>
        </div>
        @endif
        <div class="admin-stat">
            <div class="stat-icon">&#128101;</div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Users</div>
        </div>
        <div class="admin-stat">
            <div class="stat-icon">&#128203;</div>
            <div class="stat-value">{{ $totalBookings }}</div>
            <div class="stat-label">Bookings</div>
        </div>
        <div class="admin-stat">
            <div class="stat-icon">&#127758;</div>
            <div class="stat-value">{{ $totalDestinations }}</div>
            <div class="stat-label">Destinations</div>
        </div>
        <div class="admin-stat">
            <div class="stat-icon">&#127960;</div>
            <div class="stat-value">{{ $totalTours }}</div>
            <div class="stat-label">Tours</div>
        </div>
        <div class="admin-stat">
            <div class="stat-icon">&#128221;</div>
            <div class="stat-value">{{ $totalPosts }}</div>
            <div class="stat-label">Blog Posts</div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dash-card">
            <h3>Activity (Last 6 Months)</h3>
            <div class="chart-container">
                @php $maxVal = max(1, max(array_column($monthlyStats, 'bookings')), max(array_column($monthlyStats, 'users'))); @endphp
                @foreach($monthlyStats as $stat)
                <div class="chart-bar-group">
                    <div class="chart-bars">
                        <div class="chart-bar bookings" style="height: {{ max(4, ($stat['bookings'] / $maxVal) * 150) }}px;" title="{{ $stat['bookings'] }} bookings"></div>
                        <div class="chart-bar users" style="height: {{ max(4, ($stat['users'] / $maxVal) * 150) }}px;" title="{{ $stat['users'] }} users"></div>
                    </div>
                    <span class="chart-label">{{ $stat['label'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="chart-legend">
                <span><span class="dot" style="background:#FF6B35;"></span> Bookings</span>
                <span><span class="dot" style="background:#20c997;"></span> New Users</span>
            </div>
        </div>

        <div class="dash-card">
            <h3>Recent Messages</h3>
            <div class="msg-list">
                @forelse($recentContacts as $c)
                <div class="msg-item">
                    @if($c->status === 'new')<div class="msg-badge"></div>@endif
                    <div class="msg-avatar">{{ strtoupper(substr($c->name, 0, 1)) }}</div>
                    <div class="msg-content">
                        <strong>{{ $c->name }}</strong>
                        <p>{{ $c->message }}</p>
                    </div>
                </div>
                @empty
                <div style="padding:2rem;text-align:center;color:#999;">No messages yet</div>
                @endforelse
            </div>
            <a href="/admin/contacts" class="view-all">View All Messages &rarr;</a>
        </div>
    </div>

    <div class="admin-table-wrap">
        <h3>Recent Bookings</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Guest</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBookings as $b)
                <tr>
                    <td><strong>{{ $b->reference }}</strong></td>
                    <td>{{ $b->guest_first_name . ' ' . $b->guest_last_name }}</td>
                    <td>{{ ucfirst($b->product_type) }}</td>
                    <td><span class="badge badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
                    <td>{{ $b->currency }} {{ number_format($b->sell_price, 2) }}</td>
                    <td>{{ $b->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#999;padding:2rem;">No bookings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-quick-links">
        <a href="/admin/contacts" class="admin-quick-link"><span class="link-icon">&#9993;</span> Messages</a>
        <a href="/admin/bookings" class="admin-quick-link"><span class="link-icon">&#128203;</span> Bookings</a>
        <a href="/admin/users" class="admin-quick-link"><span class="link-icon">&#128101;</span> Users</a>
        <a href="/admin/destinations" class="admin-quick-link"><span class="link-icon">&#127758;</span> Destinations</a>
        <a href="/admin/tours" class="admin-quick-link"><span class="link-icon">&#127960;</span> Tours</a>
        <a href="/admin/posts" class="admin-quick-link"><span class="link-icon">&#128221;</span> Blog</a>
        <a href="/admin/promos" class="admin-quick-link"><span class="link-icon">&#127873;</span> Promos</a>
        <a href="/admin/reviews" class="admin-quick-link"><span class="link-icon">&#11088;</span> Reviews</a>
        <a href="/admin/reports" class="admin-quick-link"><span class="link-icon">&#128200;</span> Reports</a>
        <a href="/admin/settings" class="admin-quick-link"><span class="link-icon">&#9881;</span> Settings</a>
        <a href="/admin/profile" class="admin-quick-link"><span class="link-icon">&#128100;</span> Profile</a>
    </div>
</div>
@endsection
