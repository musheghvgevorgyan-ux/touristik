<?php
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    if (!checkLoginRate()) {
        $error = '<div class="alert error">Too many login attempts. Please wait 15 minutes.</div>';
    } else {
        $error = '<div class="alert error" data-t="login_error">Invalid username or password.</div>';
    }
}
?>

<section class="login-section">
    <h2 data-t="admin_login">Admin Login</h2>
    <?= $error ?>
    <form class="contact-form" method="POST" action="<?= url('login') ?>">
        <?= csrfField() ?>
        <input type="text" name="username" placeholder="Username" data-tp="username" required>
        <input type="password" name="password" placeholder="Password" data-tp="password" required>
        <button type="submit" name="login_submit" class="btn" data-t="login">Login</button>
    </form>
</section>
