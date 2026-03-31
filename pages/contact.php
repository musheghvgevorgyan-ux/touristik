<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit']) && verifyCsrf()) {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $msg = htmlspecialchars(trim($_POST['message']));

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $msg) {
        saveContact($pdo, $name, $email, $msg);

        // Send email notification
        $adminEmail = getSetting($pdo, 'contact_email', '');

        // Notify admin
        if ($adminEmail && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            $adminHtml = '<p style="margin:0 0 15px;font-weight:600;color:#203a43;">New contact form submission:</p>'
                . '<table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e0e0e0;border-radius:6px;border-collapse:collapse;margin-bottom:15px;">'
                . '<tr style="background:#f8f9fa;"><td style="border-bottom:1px solid #e0e0e0;font-weight:600;width:100px;">Name</td><td style="border-bottom:1px solid #e0e0e0;">' . $name . '</td></tr>'
                . '<tr><td style="font-weight:600;">Email</td><td><a href="mailto:' . $email . '" style="color:#f18f01;">' . $email . '</a></td></tr>'
                . '</table>'
                . '<div style="background:#f8f9fa;padding:15px 20px;border-radius:6px;border-left:4px solid #f18f01;">'
                . '<p style="margin:0;font-weight:600;color:#203a43;margin-bottom:8px;">Message:</p>'
                . '<p style="margin:0;white-space:pre-wrap;">' . nl2br($msg) . '</p></div>';
            sendHtmlEmail($adminEmail, "New Contact from Touristik: $name", emailTemplate('New Contact Message', $adminHtml), $email);
        }

        // Auto-reply to customer
        $replyHtml = '<p style="margin:0 0 15px;">Dear <strong>' . $name . '</strong>,</p>'
            . '<p style="margin:0 0 15px;">Thank you for contacting Touristik Travel Club!</p>'
            . '<p style="margin:0 0 20px;">We have received your message and will get back to you within 24 hours.</p>'
            . '<div style="background:#f8f9fa;padding:15px 20px;border-radius:6px;border-left:4px solid #2c5364;margin-bottom:20px;">'
            . '<p style="margin:0;font-weight:600;color:#203a43;margin-bottom:8px;">Your message:</p>'
            . '<p style="margin:0;font-style:italic;color:#555;">&ldquo;' . nl2br($msg) . '&rdquo;</p></div>'
            . '<p style="margin:0;">Best regards,<br><strong>Touristik Travel Club</strong></p>';
        sendHtmlEmail($email, "We received your message - Touristik Travel Club", emailTemplate('Message Received', $replyHtml));

        $message = '<div class="alert success" data-t="contact_success">Thank you! We will get back to you soon.</div>';
    } else {
        $message = '<div class="alert error" data-t="contact_error">Please fill in all fields correctly.</div>';
    }
}
?>

<nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="<?= url('home') ?>" data-t="breadcrumb_home">Home</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <span class="breadcrumb-current" data-t="contact">Contact</span>
</nav>

<section class="contact">
    <h2 data-t="get_in_touch">Get In Touch</h2>

    <div class="contact-layout">
        <div class="contact-info-panel">
            <div class="info-block">
                <h3 data-t="contact_branches_title">Our Branches</h3>
                <ul>
                    <li><i class="fa-solid fa-location-dot"></i> <span data-t="branch_1">Komitas 38</span></li>
                    <li><i class="fa-solid fa-location-dot"></i> <span data-t="branch_2">Mashtots 7/6</span></li>
                    <li><i class="fa-solid fa-location-dot"></i> <span data-t="branch_3">Arshakunyats 34 (Yerevan Mall, 2nd floor)</span></li>
                </ul>
            </div>
            <div class="info-block">
                <h3 data-t="contact_hours_title">Working Hours</h3>
                <ul>
                    <li><i class="fa-regular fa-clock"></i> <span data-t="hours_weekday">Mon – Fri: 10:00 – 20:00</span></li>
                    <li><i class="fa-regular fa-clock"></i> <span data-t="hours_weekend">Sat – Sun: 11:00 – 18:00</span></li>
                </ul>
            </div>
            <div class="info-block">
                <h3 data-t="contact_phone_title">Phone & Email</h3>
                <ul>
                    <li><i class="fa-solid fa-phone"></i> <a href="tel:+37433060609">+374 33 060 609</a></li>
                    <li><i class="fa-solid fa-phone"></i> <a href="tel:+37455060609">+374 55 060 609</a></li>
                    <li><i class="fa-solid fa-phone"></i> <a href="tel:+37444060608">+374 44 060 608</a></li>
                    <li><i class="fa-solid fa-phone"></i> <a href="tel:+37495060608">+374 95 060 608</a></li>
                    <li><i class="fa-solid fa-envelope"></i> <a href="mailto:info@touristik.am">info@touristik.am</a></li>
                    <li><i class="fa-solid fa-envelope"></i> <a href="mailto:touristik.visadepartment@gmail.com">touristik.visadepartment@gmail.com</a></li>
                </ul>
            </div>
        </div>

        <div class="contact-form-panel">
            <?= $message ?>
            <form class="contact-form" method="POST" action="<?= url('contact') ?>">
                <?= csrfField() ?>
                <input type="text" name="name" placeholder="Your Name" data-tp="your_name" required>
                <input type="email" name="email" placeholder="Your Email" data-tp="your_email" required>
                <textarea name="message" placeholder="Tell us about your dream trip..." data-tp="dream_trip" rows="5" required></textarea>
                <button type="submit" name="contact_submit" class="btn" data-t="send">Send Message</button>
            </form>
        </div>
    </div>
</section>
