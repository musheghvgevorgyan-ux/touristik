@extends('layouts.main')

@section('title', 'User Detail - Touristik')

@push('styles')
<style>
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin: 0; }
    .admin-header .btn-back { display: inline-flex; align-items: center; gap: 0.4rem; color: #6c757d; text-decoration: none; font-size: 0.9rem; transition: color 0.2s; }
    .admin-header .btn-back:hover { color: #FF6B35; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
    .detail-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 1.5rem; }
    .detail-card h3 { margin: 0 0 1rem; font-size: 1.05rem; color: var(--text-heading, #1a1a2e); padding-bottom: 0.6rem; border-bottom: 2px solid #f0f0f0; }
    .detail-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f8f8f8; }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { color: #6c757d; font-size: 0.88rem; font-weight: 600; }
    .detail-value { color: #333; font-size: 0.92rem; text-align: right; max-width: 60%; word-break: break-word; }
    .badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 4px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-suspended { background: #f8d7da; color: #721c24; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-admin, .badge-superadmin { background: #e8daef; color: #6c3483; }
    .badge-agent { background: #d1ecf1; color: #0c5460; }
    .badge-customer { background: #e2e3e5; color: #383d41; }
    .full-width { grid-column: 1 / -1; }
    .edit-form { display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; }
    .form-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .form-group label { font-size: 0.8rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-group select { padding: 0.55rem 0.8rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem; background: #fff; color: #333; min-width: 150px; }
    .form-group select:focus { outline: none; border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.15); }
    .btn-save { padding: 0.55rem 1.5rem; background: #FF6B35; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-save:hover { background: #e55a2b; }
    .admin-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; margin-bottom: 1.5rem; }
    .admin-table-wrap h3 { padding: 1.2rem 1.5rem 0; color: var(--text-heading, #1a1a2e); margin: 0 0 0.8rem; font-size: 1.05rem; }
    .admin-table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .admin-table th { text-align: left; padding: 0.7rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .admin-table td { padding: 0.7rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
    .admin-table tr:hover td { background: #f8f9fa; }
    .admin-table a.view-link { color: #FF6B35; text-decoration: none; font-weight: 600; }
    .admin-table a.view-link:hover { text-decoration: underline; }
    .ref-code { font-family: 'Courier New', monospace; font-weight: 700; font-size: 0.88rem; }
    .badge-confirmed, .badge-completed, .badge-paid { background: #d4edda; color: #155724; }
    .badge-cancelled, .badge-failed { background: #f8d7da; color: #721c24; }
    .activity-item { display: flex; align-items: flex-start; gap: 1rem; padding: 0.7rem 0; border-bottom: 1px solid #f0f0f0; }
    .activity-item:last-child { border-bottom: none; }
    .activity-dot { width: 8px; height: 8px; border-radius: 50%; background: #FF6B35; margin-top: 0.4rem; flex-shrink: 0; }
    .activity-text { font-size: 0.9rem; color: #333; }
    .activity-meta { font-size: 0.8rem; color: #6c757d; margin-top: 0.15rem; }
    .empty-state { text-align: center; padding: 2rem 1rem; color: #6c757d; font-size: 0.95rem; }
    @media (max-width: 768px) {
        .detail-grid { grid-template-columns: 1fr; }
        .detail-row { flex-direction: column; gap: 0.2rem; }
        .detail-value { text-align: left; max-width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="admin-header">
    <div>
        <a href="/admin/users" class="btn-back">&larr; Back to Users</a>
        <h1>{{ $title }}</h1>
    </div>
</div>

<div class="detail-grid">
    <!-- User Info -->
    <div class="detail-card">
        <h3>User Information</h3>
        <div class="detail-row">
            <span class="detail-label">Full Name</span>
            <span class="detail-value">{{ ($userDetail['first_name'] ?? '') . ' ' . ($userDetail['last_name'] ?? '') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Email</span>
            <span class="detail-value">{{ $userDetail['email'] }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Phone</span>
            <span class="detail-value">{{ $userDetail['phone'] ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Role</span>
            <span class="detail-value"><span class="badge badge-{{ $userDetail['role'] }}">{{ ucfirst($userDetail['role']) }}</span></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value"><span class="badge badge-{{ $userDetail['status'] ?? 'active' }}">{{ ucfirst($userDetail['status'] ?? 'Active') }}</span></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Agency</span>
            <span class="detail-value">{{ $userDetail['agency_name'] ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Language</span>
            <span class="detail-value">{{ strtoupper($userDetail['language'] ?? 'en') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Currency</span>
            <span class="detail-value">{{ strtoupper($userDetail['currency'] ?? 'USD') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Last Login</span>
            <span class="detail-value">{!! !empty($userDetail['last_login_at']) ? \Carbon\Carbon::parse($userDetail['last_login_at'])->format('M d, Y H:i') : 'Never' !!}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Registered</span>
            <span class="detail-value">{{ \Carbon\Carbon::parse($userDetail['created_at'])->format('M d, Y') }}</span>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="detail-card">
        <h3>Edit User</h3>
        <form method="POST" action="/admin/users/{!! (int)$userDetail['id'] !!}">
            @csrf
            <div class="edit-form" style="flex-direction: column; align-items: stretch;">
                <div class="form-group">
                    <label for="edit-role">Role</label>
                    <select name="role" id="edit-role">
                        <option value="customer" {!! ($userDetail['role'] ?? '') === 'customer' ? 'selected' : '' !!}>Customer</option>
                        <option value="agent" {!! ($userDetail['role'] ?? '') === 'agent' ? 'selected' : '' !!}>Agent</option>
                        <option value="admin" {!! ($userDetail['role'] ?? '') === 'admin' ? 'selected' : '' !!}>Admin</option>
                        <option value="superadmin" {!! ($userDetail['role'] ?? '') === 'superadmin' ? 'selected' : '' !!}>Superadmin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-status">Status</label>
                    <select name="status" id="edit-status">
                        <option value="active" {!! ($userDetail['status'] ?? '') === 'active' ? 'selected' : '' !!}>Active</option>
                        <option value="suspended" {!! ($userDetail['status'] ?? '') === 'suspended' ? 'selected' : '' !!}>Suspended</option>
                        <option value="pending" {!! ($userDetail['status'] ?? '') === 'pending' ? 'selected' : '' !!}>Pending</option>
                    </select>
                </div>
                <div style="margin-top: 0.5rem;">
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- User Bookings (Last 10) -->
<div class="admin-table-wrap">
    <h3>Recent Bookings</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Type</th>
                <th>Status</th>
                <th>Price</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if(empty($userBookings))
                <tr><td colspan="6" class="empty-state">No bookings by this user.</td></tr>
            @else
                @foreach(collect($userBookings)->take(10) as $b)
                <tr>
                    <td><span class="ref-code">{{ $b['reference'] }}</span></td>
                    <td>{{ ucfirst($b['product_type'] ?? 'N/A') }}</td>
                    <td><span class="badge badge-{{ $b['status'] }}">{{ ucfirst($b['status']) }}</span></td>
                    <td><strong>{{ $b['currency'] ?? 'USD' }} {!! number_format($b['sell_price'] ?? 0, 2) !!}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($b['created_at'])->format('M d, Y') }}</td>
                    <td><a href="/admin/bookings/{!! (int)$b['id'] !!}" class="view-link">View &rarr;</a></td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<!-- Activity Log (Last 20) -->
<div class="detail-card">
    <h3>Activity Log</h3>
    @if(empty($userActivity))
        <div class="empty-state">No activity recorded for this user.</div>
    @else
        @foreach(collect($userActivity)->take(20) as $activity)
        <div class="activity-item">
            <div class="activity-dot"></div>
            <div>
                <div class="activity-text">{{ $activity['action'] }}</div>
                <div class="activity-meta">{{ \Carbon\Carbon::parse($activity['created_at'])->format('M d, Y H:i') }}</div>
            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection
