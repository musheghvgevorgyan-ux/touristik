<?php use App\Helpers\View; ?>

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
    .btn-submit { display: inline-block; width: 100%; padding: 0.9rem; background: var(--primary); color: #fff; border: none; border-radius: var(--radius); font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-submit:hover { background: var(--primary-dark); }
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

<div class="contact-page">
    <div class="section-header reveal">
        <h1 data-t="contact_title">Contact Us</h1>
        <p data-t="contact_subtitle">Get in touch with our team. We're here to help you plan your perfect trip.</p>
    </div>

    <div class="contact-layout">
        <div class="contact-form-card reveal">
            <h2 data-t="send_message">Send Us a Message</h2>
            <form method="POST" action="/contact">
                <?= View::csrf() ?>

                <div class="form-group">
                    <label for="name" data-t="form_name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?= View::e(View::old('name')) ?>" placeholder="Your full name" required>
                </div>

                <div class="form-group">
                    <label for="email" data-t="form_email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= View::e(View::old('email')) ?>" placeholder="your@email.com" required>
                </div>

                <div class="form-group">
                    <label for="subject" data-t="form_subject">Subject</label>
                    <input type="text" id="subject" name="subject" value="<?= View::e(View::old('subject')) ?>" placeholder="How can we help?" required>
                </div>

                <div class="form-group">
                    <label for="message" data-t="form_message">Message</label>
                    <textarea id="message" name="message" placeholder="Tell us more about your inquiry..." required><?= View::e(View::old('message')) ?></textarea>
                </div>

                <button type="submit" class="btn-submit" data-t="send_btn">Send Message</button>
            </form>
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
