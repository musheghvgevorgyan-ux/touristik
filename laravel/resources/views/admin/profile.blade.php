@extends('layouts.main')

@section('title', 'Admin Profile - Touristik')

@push('styles')
<style>
    .admin-profile { max-width: 600px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .admin-profile h1 { font-size: 1.8rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .admin-profile > p { color: var(--text-secondary); margin-bottom: 2rem; }
    .profile-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 2rem; margin-bottom: 1.5rem; }
    .profile-card h2 { font-size: 1.2rem; color: var(--text-heading); margin-bottom: 1.2rem; padding-bottom: 0.6rem; border-bottom: 1px solid var(--border-color); }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.3rem; font-size: 0.9rem; color: var(--text-secondary); }
    .form-group input { width: 100%; padding: 0.7rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius); font-size: 1rem; background: var(--bg-body); color: var(--text-primary); }
    .form-group input:focus { outline: none; border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .form-group .hint { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.2rem; }
    .btn-save { padding: 0.8rem 2rem; background: #FF6B35; color: #fff; border: none; border-radius: var(--radius); font-size: 1rem; font-weight: 600; cursor: pointer; }
    .btn-save:hover { background: #e55a2b; }
    .back-link { margin-top: 1rem; }
    .back-link a { color: #FF6B35; text-decoration: none; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="admin-profile">
    <h1>Admin Profile</h1>
    <p>Update your account details and password</p>

    <form method="POST" action="/admin/profile">
        @csrf

        <div class="profile-card">
            <h2>Account Details</h2>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
        </div>

        <div class="profile-card">
            <h2>Change Password</h2>
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" placeholder="Leave blank to keep current">
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Minimum 8 characters">
                <div class="hint">At least 8 characters</div>
            </div>
            <div class="form-group">
                <label for="new_password_confirmation">Confirm New Password</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Repeat new password">
            </div>
        </div>

        <button type="submit" class="btn-save">Save Changes</button>
    </form>

    <div class="back-link">
        <a href="/admin">&larr; Back to Dashboard</a>
    </div>
</div>
@endsection
