@extends('layouts.main')
@section('title', 'Reset Password - Touristik')
@push('styles')
<style>
    .auth-page { max-width: 440px; margin: 2rem auto; }
    .auth-card { background: #fff; border-radius: var(--radius); box-shadow: var(--shadow); padding: 2.5rem; }
    .auth-card h1 { font-size: 1.5rem; margin-bottom: 0.3rem; color: var(--dark); }
    .auth-card p.subtitle { color: var(--gray); margin-bottom: 1.5rem; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.3rem; font-size: 0.9rem; color: #555; }
    .form-group input { width: 100%; padding: 0.7rem 1rem; border: 1px solid #ddd; border-radius: var(--radius); font-size: 1rem; }
    .form-group input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .form-group .hint { font-size: 0.8rem; color: var(--gray); margin-top: 0.2rem; }
    .btn { display: inline-block; width: 100%; padding: 0.8rem; background: var(--primary); color: #fff; border: none; border-radius: var(--radius); font-size: 1rem; font-weight: 600; cursor: pointer; }
    .btn:hover { background: var(--primary-dark); }
</style>
@endpush
@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1>Reset Password</h1>
        <p class="subtitle">Enter your new password</p>
        <form method="POST" action="/reset-password">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" placeholder="Minimum 8 characters" required autofocus>
                <div class="hint">At least 8 characters</div>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat your password" required>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
    </div>
</div>
@endsection
