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
        if ($adminEmail && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            $subject = "New Contact from Touristik: " . $name;
            $body = "Name: $name\nEmail: $email\n\nMessage:\n$msg";
            $headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
            @mail($adminEmail, $subject, $body, $headers);
        }

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
