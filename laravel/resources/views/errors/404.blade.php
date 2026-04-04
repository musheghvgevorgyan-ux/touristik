@extends('layouts.main')
@section('title', '404 - Page Not Found')
@push('styles')
<style>
    .error-page { text-align: center; padding: 5rem 2rem; min-height: 60vh; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .error-code { font-size: 8rem; font-weight: 800; background: linear-gradient(135deg, #FF6B35, #f7a072); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1; margin-bottom: 0.5rem; }
    .error-icon { font-size: 4rem; margin-bottom: 1rem; animation: float 3s ease-in-out infinite; }
    @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
    .error-page h2 { font-size: 1.6rem; color: var(--text-heading); margin-bottom: 0.8rem; }
    .error-page p { color: var(--text-secondary); font-size: 1.05rem; max-width: 500px; margin: 0 auto 2rem; line-height: 1.6; }
    .error-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
    .error-actions a { padding: 0.8rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 1rem; transition: all 0.2s; }
    .btn-home { background: #FF6B35; color: #fff; }
    .btn-home:hover { background: #e55a2b; transform: translateY(-2px); }
    .btn-contact { background: transparent; color: var(--text-heading); border: 2px solid var(--border-color); }
    .btn-contact:hover { border-color: #FF6B35; color: #FF6B35; }
</style>
@endpush
@section('content')
<div class="error-page">
    <div class="error-icon">&#9992;</div>
    <div class="error-code">404</div>
    <h2>Lost in Transit</h2>
    <p>The page you're looking for seems to have taken a different flight. Let's get you back on track.</p>
    <div class="error-actions">
        <a href="/" class="btn-home">Back to Home</a>
        <a href="/contact" class="btn-contact">Contact Us</a>
    </div>
</div>
@endsection
