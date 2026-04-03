@extends('layouts.main')
@section('title', '404 - Page Not Found')
@push('styles')
<style>
    .error-page { text-align: center; padding: 5rem 0; }
    .error-page h1 { font-size: 5rem; color: var(--primary); margin-bottom: 0; }
    .error-page h2 { color: var(--dark); margin-bottom: 1rem; }
    .error-page p { color: var(--gray); margin-bottom: 2rem; }
    .error-page a { color: var(--primary); text-decoration: none; font-weight: 600; }
</style>
@endpush
@section('content')
<div class="error-page">
    <h1>404</h1>
    <h2>Page Not Found</h2>
    <p>The page you're looking for doesn't exist or has been moved.</p>
    <a href="/">Back to Home</a>
</div>
@endsection
