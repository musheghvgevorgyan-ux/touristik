@extends('layouts.main')

@section('title', 'Admin Dashboard - Touristik')

@push('styles')
<style>
    .admin-header { margin-bottom: 2rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin-bottom: 0.3rem; }
    .admin-header p { color: #6c757d; }
    .admin-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.2rem; margin-bottom: 2rem; }
    .admin-stat { background: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
    .admin-stat .stat-value { font-size: 2rem; font-weight: 700; color: #FF6B35; }
    .admin-stat .stat-label { color: #6c757d; font-size: 0.9rem; margin-top: 0.3rem; }
    .admin-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; }
    .admin-table-wrap h3 { padding: 1.2rem 1.5rem 0; color: var(--text-heading, #1a1a2e); }
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { text-align: left; padding: 0.8rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.85rem; text-transform: uppercase; }
    .admin-table td { padding: 0.8rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.95rem; }
    .admin-table tr:hover td { background: #f8f9fa; }
    .badge { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
    .badge-confirmed { background: #d4edda; color: #155724; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
    .admin-quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-top: 2rem; }
    .admin-quick-link { display: flex; align-items: center; gap: 0.8rem; background: #fff; padding: 1rem 1.2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; color: #333; transition: transform 0.2s; }
    .admin-quick-link:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .admin-quick-link .link-icon { font-size: 1.5rem; }
</style>
@endpush

@section('content')
<div class="admin-header">
    <h1>Admin Dashboard</h1>
    <p>Welcome back. Here's an overview of your platform.</p>
</div>

<div class="admin-stats">
    <div class="admin-stat">
        <div class="stat-value">{!! $totalUsers !!}</div>
        <div class="stat-label">Total Users</div>
    </div>
    <div class="admin-stat">
        <div class="stat-value">{!! $totalBookings !!}</div>
        <div class="stat-label">Total Bookings</div>
    </div>
    <div class="admin-stat">
        <div class="stat-value">{!! $newMessages !!}</div>
        <div class="stat-label">New Messages</div>
    </div>
    <div class="admin-stat">
        <div class="stat-value">3</div>
        <div class="stat-label">Branches</div>
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
            @if(empty($recentBookings))
                <tr><td colspan="6" style="text-align:center;color:#999;padding:2rem;">No bookings yet.</td></tr>
            @else
                @foreach($recentBookings as $b)
                <tr>
                    <td><strong>{{ $b['reference'] }}</strong></td>
                    <td>{{ $b['guest_first_name'] . ' ' . $b['guest_last_name'] }}</td>
                    <td>{{ ucfirst($b['product_type']) }}</td>
                    <td><span class="badge badge-{!! $b['status'] !!}">{{ ucfirst($b['status']) }}</span></td>
                    <td>{{ $b['currency'] }} {!! number_format($b['sell_price'], 2) !!}</td>
                    <td>{{ \Carbon\Carbon::parse($b['created_at'])->format('M d, Y') }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<div class="admin-quick-links">
    <a href="/admin/contacts" class="admin-quick-link"><span class="link-icon">&#9993;</span> Messages</a>
    <a href="/admin/bookings" class="admin-quick-link"><span class="link-icon">&#128203;</span> Manage Bookings</a>
    <a href="/admin/users" class="admin-quick-link"><span class="link-icon">&#128101;</span> Manage Users</a>
    <a href="/admin/destinations" class="admin-quick-link"><span class="link-icon">&#127758;</span> Destinations</a>
    <a href="/admin/tours" class="admin-quick-link"><span class="link-icon">&#127960;</span> Tours</a>
    <a href="/admin/settings" class="admin-quick-link"><span class="link-icon">&#9881;</span> Settings</a>
    <a href="/admin/promos" class="admin-quick-link"><span class="link-icon">&#127873;</span> Promo Codes</a>
    <a href="/admin/reviews" class="admin-quick-link"><span class="link-icon">&#11088;</span> Reviews</a>
    <a href="/admin/posts" class="admin-quick-link"><span class="link-icon">&#128221;</span> Blog Posts</a>
    <a href="/admin/profile" class="admin-quick-link"><span class="link-icon">&#128100;</span> My Profile</a>
</div>
@endsection
