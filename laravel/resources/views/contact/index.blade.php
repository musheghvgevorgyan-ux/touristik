@extends('layouts.main')

@section('title', 'Contact Us - Touristik')

@push('styles')
<style>
    .contact-page { max-width: 1200px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .contact-page .section-header { text-align: center; margin-bottom: 2.5rem; }
    .contact-page .section-header h1 { font-size: 2.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .contact-page .section-header p { color: var(--text-secondary); font-size: 1.1rem; }
    .contact-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start; }
    .contact-form-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 2.5rem; }
    .contact-form-card h2 { font-size: 1.4rem; color: var(--text-heading); margin-bottom: 1.5rem; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.3rem; font-size: 0.9rem; color: var(--text-secondary); }
    .form-group input,
    .form-group select,
    .form-group textarea { width: 100%; padding: 0.7rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius); font-size: 1rem; transition: border-color 0.2s; background: var(--bg-body); color: var(--text-primary); }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .form-group textarea { resize: vertical; min-height: 140px; }
    .btn-submit { display: inline-block; width: 100%; padding: 0.9rem; background: #FF6B35 !important; color: #fff !important; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 0.5rem; }
    .btn-submit:hover { background: #e55a2b !important; }
    .contact-info { display: flex; flex-direction: column; gap: 1.5rem; }
    .branch-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 1.5rem 2rem; border-left: 4px solid var(--primary); }
    .branch-card h3 { font-size: 1.1rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .branch-card p { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; margin: 0; }
    .branch-card a { color: var(--primary); text-decoration: none; font-weight: 500; }
    .branch-card a:hover { text-decoration: underline; }
    .map-placeholder { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); height: 200px; display: flex; align-items: center; justify-content: center; color: var(--text-secondary); font-size: 1rem; margin-top: 1.5rem; border: 2px dashed var(--border-color); }
    @media (max-width: 768px) {
        .contact-layout { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="contact-page">
    @if(!empty($sent))
        <div style="background:#28a745;color:#fff;padding:1.2rem;border-radius:8px;text-align:center;font-weight:600;font-size:1.1rem;margin-bottom:1.5rem;max-width:1200px;margin-left:auto;margin-right:auto;">
            Thank you! Your message has been sent. We will get back to you soon.
        </div>
    @endif

    <div class="section-header reveal">
        <h1 data-t="contact_title">Contact Us</h1>
        <p data-t="contact_subtitle">Get in touch with our team. We're here to help you plan your perfect trip.</p>
    </div>

    <div class="contact-layout">
        <div class="contact-form-card reveal">
            <h2 data-t="send_message">Send Us a Message</h2>
            <div id="contactResult" style="display:none;padding:1rem;border-radius:8px;margin-bottom:1rem;font-weight:600;text-align:center;"></div>
            <form id="contactForm" onsubmit="return submitContact(event)">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                    <label for="name" data-t="form_name">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Your full name" required>
                </div>

                <div class="form-group">
                    <label for="email" data-t="form_email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required>
                </div>

                <div class="form-group">
                    <label for="subject" data-t="form_subject">Subject</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="How can we help?">
                </div>

                <div class="form-group">
                    <label for="message" data-t="form_message">Message</label>
                    <textarea id="message" name="message" placeholder="Tell us more about your inquiry..." required></textarea>
                </div>

                <button type="submit" class="btn-submit" style="background:#FF6B35 !important;color:#fff !important;display:block;width:100%;padding:0.9rem;border:none;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;" data-t="send_btn">Send Message</button>
            </form>
            <script>
            function submitContact(e) {
                e.preventDefault();
                var form = document.getElementById('contactForm');
                var btn = form.querySelector('button');
                var result = document.getElementById('contactResult');
                btn.disabled = true;
                btn.textContent = 'Sending...';

                // Get fresh CSRF token first, then submit
                fetch('/contact', { method: 'GET' })
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    var match = html.match(/name="_token" value="([^"]+)"/);
                    if (match) {
                        form.querySelector('input[name="_token"]').value = match[1];
                    }
                    return fetch('/contact', {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        redirect: 'manual'
                    });
                }).then(function(r) {
                    return r.json().catch(function() { return { success: r.ok }; });
                }).then(function(data) {
                    if (data.success) {
                        result.style.display = 'block';
                        result.style.background = '#d4edda';
                        result.style.color = '#155724';
                        result.textContent = 'Thank you! Your message has been sent. We will get back to you soon.';
                        form.reset();
                    } else {
                        var msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Please check your input.');
                        result.style.display = 'block';
                        result.style.background = '#f8d7da';
                        result.style.color = '#721c24';
                        result.textContent = msg;
                    }
                    btn.disabled = false;
                    btn.textContent = 'Send Message';
                }).catch(function() {
                    result.style.display = 'block';
                    result.style.background = '#f8d7da';
                    result.style.color = '#721c24';
                    result.textContent = 'Network error. Please try again.';
                    btn.disabled = false;
                    btn.textContent = 'Send Message';
                });
                return false;
            }
            </script>
        </div>

        <div class="contact-info">
            <div class="branch-card reveal">
                <h3 data-t="branch_komitas">Komitas Branch</h3>
                <p data-t="branch_komitas_addr">Komitas 38, Yerevan, Armenia</p>
                <p>
                    <a href="tel:+37433060609">+374 33 060 609</a><br>
                    <a href="tel:+37455060609">+374 55 060 609</a>
                </p>
                <p data-t="hours_weekday">Mon - Fri: 10:00 - 20:00</p>
                <p data-t="hours_weekend">Sat - Sun: 11:00 - 18:00</p>
            </div>

            <div class="branch-card reveal">
                <h3 data-t="branch_mashtots">Mashtots Branch</h3>
                <p data-t="branch_mashtots_addr">Mashtots 7/6, Yerevan, Armenia</p>
                <p>
                    <a href="tel:+37444060608">+374 44 060 608</a><br>
                    <a href="tel:+37495060608">+374 95 060 608</a>
                </p>
                <p data-t="hours_weekday">Mon - Fri: 10:00 - 20:00</p>
                <p data-t="hours_weekend">Sat - Sun: 11:00 - 18:00</p>
            </div>

            <div class="branch-card reveal">
                <h3 data-t="branch_mall">Yerevan Mall Branch</h3>
                <p data-t="branch_mall_addr">Arshakunyats 34, Yerevan Mall, 2nd Floor</p>
                <p>
                    <a href="tel:+37433060609">+374 33 060 609</a>
                </p>
                <p data-t="hours_weekday">Mon - Fri: 10:00 - 20:00</p>
                <p data-t="hours_weekend">Sat - Sun: 11:00 - 18:00</p>
            </div>

            <div class="map-placeholder reveal">
                <span data-t="map_placeholder">Map integration coming soon</span>
            </div>
        </div>
    </div>
</div>
@endsection
