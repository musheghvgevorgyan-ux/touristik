@extends('layouts.main')
@section('title', 'B2B Agency Registration - Touristik')
@push('styles')
<style>
    .auth-page { max-width: 580px; margin: 2rem auto; }
    .auth-card { background: #fff; border-radius: var(--radius, 8px); box-shadow: var(--shadow, 0 2px 15px rgba(0,0,0,0.1)); padding: 2.5rem; }
    .auth-card h1 { font-size: 1.5rem; margin-bottom: 0.3rem; color: #1a2332; }
    .auth-card p.subtitle { color: #6c757d; margin-bottom: 1.5rem; font-size: 0.95rem; }
    .form-section { margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #eee; }
    .form-section:last-of-type { border-bottom: none; margin-bottom: 0.5rem; padding-bottom: 0; }
    .form-section-title { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: #2c5364; font-weight: 700; margin-bottom: 1rem; }
    .form-row { display: flex; gap: 1rem; }
    .form-row .form-group { flex: 1; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.3rem; font-size: 0.9rem; color: #555; }
    .form-group input, .form-group textarea { width: 100%; padding: 0.7rem 1rem; border: 1px solid #ddd; border-radius: var(--radius, 8px); font-size: 1rem; transition: border-color 0.2s; font-family: inherit; }
    .form-group textarea { resize: vertical; min-height: 60px; }
    .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #2c5364; box-shadow: 0 0 0 3px rgba(44,83,100,0.1); }
    .form-group .hint { font-size: 0.8rem; color: #6c757d; margin-top: 0.2rem; }
    .auth-card .btn { display: inline-block; width: 100%; padding: 0.8rem; background: #2c5364 !important; color: #fff !important; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 0.5rem; }
    .auth-card .btn:hover { background: #1e3a47 !important; }
    .review-notice { background: #e8f4fd; border: 1px solid #bee5eb; border-radius: 8px; padding: 1rem; margin-top: 1rem; font-size: 0.85rem; color: #0c5460; line-height: 1.5; }
    .auth-links { text-align: center; margin-top: 1.2rem; font-size: 0.9rem; color: #6c757d; }
    .auth-links a { color: #2c5364; text-decoration: none; font-weight: 500; }
    .b2b-badge { display: inline-block; background: linear-gradient(135deg, #2c5364, #203a43); color: #fff; padding: 0.2rem 0.7rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 1rem; }
    @media (max-width: 600px) {
        .form-row { flex-direction: column; gap: 0; }
        .auth-card { padding: 1.5rem; }
    }
</style>
@endpush
@section('content')
<div class="auth-page">
    <div class="auth-card">
        <span class="b2b-badge">B2B Partner Program</span>
        <h1>Apply for B2B Account</h1>
        <p class="subtitle">Register your travel agency to access wholesale NET rates and earn commission on every booking.</p>
        <form method="POST" action="/register/agency">
            @csrf
            <div class="form-section">
                <div class="form-section-title">Company Information</div>
                <div class="form-group">
                    <label for="company_name">Company Name *</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Your travel agency name" required autofocus>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="legal_name">Legal Name</label>
                        <input type="text" id="legal_name" name="legal_name" value="{{ old('legal_name') }}" placeholder="Registered legal name">
                    </div>
                    <div class="form-group">
                        <label for="tax_id">Tax ID / VAT</label>
                        <input type="text" id="tax_id" name="tax_id" value="{{ old('tax_id') }}" placeholder="Tax identification number">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="company_email">Company Email *</label>
                        <input type="email" id="company_email" name="company_email" value="{{ old('company_email') }}" placeholder="info@youragency.com" required>
                    </div>
                    <div class="form-group">
                        <label for="company_phone">Company Phone</label>
                        <input type="tel" id="company_phone" name="company_phone" value="{{ old('company_phone') }}" placeholder="+374 XX XXXXXX">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Company Address</label>
                    <textarea id="address" name="address" placeholder="Street address, city, country">{{ old('address') }}</textarea>
                </div>
            </div>
            <div class="form-section">
                <div class="form-section-title">Account Administrator</div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="John" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Doe" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Login Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@youragency.com" required>
                    <div class="hint">This will be your login email for the B2B portal.</div>
                </div>
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" placeholder="Minimum 8 characters" required>
                    <div class="hint">At least 8 characters</div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat your password" required>
                </div>
            </div>
            <button type="submit" class="btn">Apply for B2B Account</button>
            <div class="review-notice">
                Applications are reviewed within 24-48 hours. Once approved, you will receive an email confirmation and can start booking at NET supplier rates through the Agent Portal.
            </div>
        </form>
        <div class="auth-links">
            Already have an account? <a href="/login">Log in</a>
            &nbsp;&middot;&nbsp;
            <a href="/register">Customer registration</a>
        </div>
    </div>
</div>
@endsection
