@extends('layouts.main')

@section('title', 'Manage Users - Touristik')

@push('styles')
<style>
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin: 0; }
    .admin-filter-bar { display: flex; flex-wrap: wrap; gap: 0.8rem; align-items: flex-end; background: #fff; padding: 1.2rem 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 1.5rem; }
    .admin-filter-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .admin-filter-group label { font-size: 0.8rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
    .admin-filter-group select,
    .admin-filter-group input { padding: 0.55rem 0.8rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem; background: #fff; color: #333; min-width: 140px; }
    .admin-filter-group select:focus,
    .admin-filter-group input:focus { outline: none; border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.15); }
    .btn-filter { padding: 0.55rem 1.2rem; background: #FF6B35; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; align-self: flex-end; }
    .btn-filter:hover { background: #e55a2b; }
    .btn-reset { padding: 0.55rem 1.2rem; background: #6c757d; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; text-decoration: none; align-self: flex-end; display: inline-flex; align-items: center; }
    .btn-reset:hover { background: #5a6268; }
    .admin-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; }
    .admin-table { width: 100%; border-collapse: collapse; min-width: 950px; }
    .admin-table th { text-align: left; padding: 0.8rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .admin-table td { padding: 0.8rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
    .admin-table tr:hover td { background: #f8f9fa; }
    .admin-table a.view-link { color: #FF6B35; text-decoration: none; font-weight: 600; white-space: nowrap; }
    .admin-table a.view-link:hover { text-decoration: underline; }
    .badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 4px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-suspended { background: #f8d7da; color: #721c24; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-admin, .badge-superadmin { background: #e8daef; color: #6c3483; }
    .badge-agent { background: #d1ecf1; color: #0c5460; }
    .badge-customer { background: #e2e3e5; color: #383d41; }
    .empty-state { text-align: center; padding: 3rem 1.5rem; color: #6c757d; }
    .empty-state .empty-icon { font-size: 3rem; margin-bottom: 0.8rem; display: block; }
    .empty-state p { font-size: 1.05rem; margin: 0; }
    .user-email { color: #6c757d; font-size: 0.85rem; }
</style>
@endpush

@section('content')
<div class="admin-header">
    <h1>{{ $title }}</h1>
</div>

<form method="GET" action="/admin/users" class="admin-filter-bar">
    <div class="admin-filter-group">
        <label for="filter-role">Role</label>
        <select name="role" id="filter-role">
            <option value="">All Roles</option>
            <option value="customer" {!! ($filters['role'] ?? '') === 'customer' ? 'selected' : '' !!}>Customer</option>
            <option value="agent" {!! ($filters['role'] ?? '') === 'agent' ? 'selected' : '' !!}>Agent</option>
            <option value="admin" {!! ($filters['role'] ?? '') === 'admin' ? 'selected' : '' !!}>Admin</option>
            <option value="superadmin" {!! ($filters['role'] ?? '') === 'superadmin' ? 'selected' : '' !!}>Superadmin</option>
        </select>
    </div>
    <div class="admin-filter-group">
        <label for="filter-search">Search</label>
        <input type="text" name="search" id="filter-search" placeholder="Name or email..." value="{{ $filters['search'] ?? '' }}">
    </div>
    <button type="submit" class="btn-filter">Filter</button>
    <a href="/admin/users" class="btn-reset">Reset</a>
</form>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Agency</th>
                <th>Last Login</th>
                <th>Registered</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if(empty($users))
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <span class="empty-icon">&#128101;</span>
                            <p>No users found matching your criteria.</p>
                        </div>
                    </td>
                </tr>
            @else
                @foreach($users as $u)
                <tr>
                    <td>{!! (int)$u['id'] !!}</td>
                    <td><strong>{{ ($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '') }}</strong></td>
                    <td><span class="user-email">{{ $u['email'] }}</span></td>
                    <td><span class="badge badge-{{ $u['role'] }}">{{ ucfirst($u['role']) }}</span></td>
                    <td><span class="badge badge-{{ $u['status'] ?? 'active' }}">{{ ucfirst($u['status'] ?? 'Active') }}</span></td>
                    <td>{{ $u['agency_name'] ?? '-' }}</td>
                    <td>{!! !empty($u['last_login_at']) ? \Carbon\Carbon::parse($u['last_login_at'])->format('M d, H:i') : 'Never' !!}</td>
                    <td>{{ \Carbon\Carbon::parse($u['created_at'])->format('M d, Y') }}</td>
                    <td><a href="/admin/users/{!! (int)$u['id'] !!}" class="view-link">View &rarr;</a></td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@php
$baseUrl = '/admin/users';
@include('partials.pagination')
@endphp
@endsection
