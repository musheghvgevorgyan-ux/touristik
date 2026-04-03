<?php use App\Helpers\View; ?>

<style>
    .auth-page { max-width: 440px; margin: 2rem auto; }
    .auth-card { background: #fff; border-radius: var(--radius); box-shadow: var(--shadow); padding: 2.5rem; }
    .auth-card h1 { font-size: 1.5rem; margin-bottom: 0.3rem; color: var(--dark); }
    .auth-card p.subtitle { color: var(--gray); margin-bottom: 1.5rem; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.3rem; font-size: 0.9rem; color: #555; }
    .form-group input { width: 100%; padding: 0.7rem 1rem; border: 1px solid #ddd; border-radius: var(--radius); font-size: 1rem; transition: border-color 0.2s; }
    .form-group input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .auth-card .btn { display: inline-block; width: 100%; padding: 0.8rem; background: #FF6B35 !important; color: #fff !important; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; text-align: center; margin-top: 0.5rem; }
    .auth-card .btn:hover { background: #e55a2b !important; }
    .auth-links { text-align: center; margin-top: 1.2rem; font-size: 0.9rem; color: var(--gray); }
    .auth-links a { color: var(--primary); text-decoration: none; font-weight: 500; }
    .auth-links a:hover { text-decoration: underline; }
</style>

<div class="auth-page">
    <div class="auth-card">
        <h1>Welcome Back</h1>
        <p class="subtitle">Log in to your Touristik account</p>

        <form method="POST" action="/login">
            <?= View::csrf() ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= View::old('email') ?>" placeholder="your@email.com" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Your password" required>
            </div>

            <button type="submit" class="btn">Log In</button>
        </form>

        <div class="auth-links">
            <a href="/forgot-password">Forgot your password?</a>
        </div>
        <div class="auth-links">
            Don't have an account? <a href="/register">Create one</a>
        </div>
    </div>
</div>
