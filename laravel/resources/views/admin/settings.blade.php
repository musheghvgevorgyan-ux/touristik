@extends('layouts.main')

@section('title', 'Settings - Touristik')

@push('styles')
<style>
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin: 0; }
    .settings-form { max-width: 800px; }
    .settings-section { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 1.5rem; margin-bottom: 1.5rem; }
    .settings-section h3 { margin: 0 0 1.2rem; font-size: 1.1rem; color: var(--text-heading, #1a1a2e); padding-bottom: 0.6rem; border-bottom: 2px solid #f0f0f0; display: flex; align-items: center; gap: 0.5rem; }
    .settings-section h3 .section-icon { font-size: 1.2rem; }
    .setting-row { display: flex; flex-direction: column; gap: 0.3rem; margin-bottom: 1.2rem; }
    .setting-row:last-child { margin-bottom: 0; }
    .setting-row label { font-size: 0.85rem; font-weight: 600; color: #333; }
    .setting-row input,
    .setting-row textarea,
    .setting-row select { padding: 0.6rem 0.9rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem; background: #fff; color: #333; font-family: inherit; width: 100%; box-sizing: border-box; }
    .setting-row input:focus,
    .setting-row textarea:focus,
    .setting-row select:focus { outline: none; border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.15); }
    .setting-row textarea { resize: vertical; min-height: 70px; }
    .setting-desc { font-size: 0.8rem; color: #6c757d; margin-top: 0.2rem; }
    .toggle-row { display: flex; align-items: center; gap: 0.8rem; margin-bottom: 1.2rem; }
    .toggle-switch { position: relative; width: 48px; height: 26px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; inset: 0; background: #ccc; border-radius: 26px; transition: background 0.3s; }
    .toggle-slider::before { content: ''; position: absolute; width: 20px; height: 20px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: transform 0.3s; }
    .toggle-switch input:checked + .toggle-slider { background: #FF6B35; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(22px); }
    .toggle-label { font-size: 0.85rem; font-weight: 600; color: #333; }
    .toggle-desc { font-size: 0.8rem; color: #6c757d; }
    .btn-save-all { padding: 0.7rem 2rem; background: #FF6B35; color: #fff; border: none; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 0.5rem; }
    .btn-save-all:hover { background: #e55a2b; }
    @media (max-width: 768px) {
        .settings-form { max-width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="admin-header">
    <h1>{{ $title }}</h1>
</div>

<form method="POST" action="/admin/settings" class="settings-form">
    @csrf

    <!-- Site Settings -->
    <div class="settings-section">
        <h3><span class="section-icon">&#127760;</span> Site</h3>
        <div class="setting-row">
            <label for="set-site_name">Site Name</label>
            <input type="text" name="settings[site_name]" id="set-site_name" value="{{ $settings['site_name'] ?? '' }}">
            <span class="setting-desc">The name shown in the browser tab and header.</span>
        </div>
        <div class="setting-row">
            <label for="set-site_tagline">Tagline</label>
            <input type="text" name="settings[site_tagline]" id="set-site_tagline" value="{{ $settings['site_tagline'] ?? '' }}">
            <span class="setting-desc">A short tagline for the site.</span>
        </div>
        <div class="setting-row">
            <label for="set-hero_title">Hero Title</label>
            <input type="text" name="settings[hero_title]" id="set-hero_title" value="{{ $settings['hero_title'] ?? '' }}">
            <span class="setting-desc">Main heading on the homepage hero section.</span>
        </div>
        <div class="setting-row">
            <label for="set-hero_subtitle">Hero Subtitle</label>
            <textarea name="settings[hero_subtitle]" id="set-hero_subtitle">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
            <span class="setting-desc">Supporting text below the hero title.</span>
        </div>
    </div>

    <!-- Contact Settings -->
    <div class="settings-section">
        <h3><span class="section-icon">&#128222;</span> Contact</h3>
        <div class="setting-row">
            <label for="set-contact_email">Contact Email</label>
            <input type="email" name="settings[contact_email]" id="set-contact_email" value="{{ $settings['contact_email'] ?? '' }}">
            <span class="setting-desc">Primary email address shown on the contact page and used for form submissions.</span>
        </div>
        <div class="setting-row">
            <label for="set-contact_phone">Contact Phone</label>
            <input type="text" name="settings[contact_phone]" id="set-contact_phone" value="{{ $settings['contact_phone'] ?? '' }}">
            <span class="setting-desc">Main phone number displayed on the site.</span>
        </div>
    </div>

    <!-- Display Settings -->
    <div class="settings-section">
        <h3><span class="section-icon">&#9881;</span> Display</h3>
        <div class="setting-row">
            <label for="set-items_per_page">Items per Page</label>
            <input type="number" name="settings[items_per_page]" id="set-items_per_page" min="5" max="100" value="{{ $settings['items_per_page'] ?? '20' }}">
            <span class="setting-desc">Number of items to show per page in listings (5-100).</span>
        </div>
        <div class="setting-row">
            <label for="set-currency_default">Default Currency</label>
            <select name="settings[currency_default]" id="set-currency_default">
                <option value="USD" {!! ($settings['currency_default'] ?? '') === 'USD' ? 'selected' : '' !!}>USD - US Dollar</option>
                <option value="EUR" {!! ($settings['currency_default'] ?? '') === 'EUR' ? 'selected' : '' !!}>EUR - Euro</option>
                <option value="AMD" {!! ($settings['currency_default'] ?? '') === 'AMD' ? 'selected' : '' !!}>AMD - Armenian Dram</option>
                <option value="RUB" {!! ($settings['currency_default'] ?? '') === 'RUB' ? 'selected' : '' !!}>RUB - Russian Ruble</option>
            </select>
            <span class="setting-desc">Default currency for new visitors.</span>
        </div>
        <div class="setting-row">
            <label for="set-language_default">Default Language</label>
            <select name="settings[language_default]" id="set-language_default">
                <option value="en" {!! ($settings['language_default'] ?? '') === 'en' ? 'selected' : '' !!}>English</option>
                <option value="ru" {!! ($settings['language_default'] ?? '') === 'ru' ? 'selected' : '' !!}>Russian</option>
                <option value="hy" {!! ($settings['language_default'] ?? '') === 'hy' ? 'selected' : '' !!}>Armenian</option>
            </select>
            <span class="setting-desc">Default language for new visitors.</span>
        </div>
    </div>

    <!-- Analytics -->
    <div class="settings-section">
        <h3><span class="section-icon">&#128200;</span> Analytics</h3>
        <div class="setting-row">
            <label for="set-ga_measurement_id">GA4 Measurement ID</label>
            <input type="text" name="settings[ga_measurement_id]" id="set-ga_measurement_id" placeholder="G-XXXXXXXXXX" value="{{ $settings['ga_measurement_id'] ?? '' }}">
            <span class="setting-desc">Google Analytics 4 measurement ID. Leave empty to disable tracking.</span>
        </div>
    </div>

    <!-- System -->
    <div class="settings-section">
        <h3><span class="section-icon">&#128736;</span> System</h3>
        <div class="toggle-row">
            <label class="toggle-switch">
                <input type="checkbox" name="settings[maintenance_mode]" value="1" {!! !empty($settings['maintenance_mode']) ? 'checked' : '' !!}>
                <span class="toggle-slider"></span>
            </label>
            <div>
                <div class="toggle-label">Maintenance Mode</div>
                <div class="toggle-desc">When enabled, the site shows a maintenance page to non-admin visitors.</div>
            </div>
        </div>
        <div class="setting-row">
            <label for="set-footer_text">Footer Text</label>
            <textarea name="settings[footer_text]" id="set-footer_text">{{ $settings['footer_text'] ?? '' }}</textarea>
            <span class="setting-desc">Copyright text shown at the bottom of every page.</span>
        </div>
    </div>

    <button type="submit" class="btn-save-all">Save All Settings</button>
</form>
@endsection
