<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
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

<section class="contact">
    <h2 data-t="get_in_touch">Get In Touch</h2>
    <?= $message ?>
    <form class="contact-form" method="POST" action="<?= url('contact') ?>">
        <input type="text" name="name" placeholder="Your Name" data-tp="your_name" required>
        <input type="email" name="email" placeholder="Your Email" data-tp="your_email" required>
        <textarea name="message" placeholder="Tell us about your dream trip..." data-tp="dream_trip" rows="5" required></textarea>
        <button type="submit" name="contact_submit" class="btn" data-t="send">Send Message</button>
    </form>
</section>
