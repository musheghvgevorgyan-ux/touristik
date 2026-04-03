<?php use App\Helpers\View; ?>

<style>
    .auth-page { max-width: 480px; margin: 2rem auto; }
    .auth-card { background: #fff; border-radius: var(--radius); box-shadow: var(--shadow); padding: 2.5rem; }
    .auth-card h1 { font-size: 1.5rem; margin-bottom: 0.3rem; color: var(--dark); }
    .auth-card p.subtitle { color: var(--gray); margin-bottom: 1.5rem; }
    .form-row { display: flex; gap: 1rem; }
    .form-row .form-group { flex: 1; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.3rem; font-size: 0.9rem; color: #555; }
    .form-group input { width: 100%; padding: 0.7rem 1rem; border: 1px solid #ddd; border-radius: var(--radius); font-size: 1rem; transition: border-color 0.2s; }
    .form-group input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .form-group .hint { font-size: 0.8rem; color: var(--gray); margin-top: 0.2rem; }
    .auth-card .btn { display: inline-block; width: 100%; padding: 0.8rem; background: #FF6B35 !important; color: #fff !important; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 0.5rem; }
    .auth-card .btn:hover { background: #e55a2b !important; }
    .auth-links { text-align: center; margin-top: 1.2rem; font-size: 0.9rem; color: var(--gray); }
    .auth-links a { color: var(--primary); text-decoration: none; font-weight: 500; }
</style>

<div class="auth-page">
    <div class="auth-card">
        <h1>Create Account</h1>
        <p class="subtitle">Join Touristik and start booking your adventures</p>

        <form method="POST" action="/register">
            <?= View::csrf() ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= View::old('first_name') ?>" placeholder="John" required autofocus>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= View::old('last_name') ?>" placeholder="Doe" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= View::old('email') ?>" placeholder="your@email.com" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone (optional)</label>
                <input type="tel" id="phone" name="phone" value="<?= View::old('phone') ?>" placeholder="+374 XX XXXXXX">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Minimum 8 characters" required>
                <div class="hint">At least 8 characters</div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat your password" required>
            </div>

            <button type="submit" class="btn">Create Account</button>
        </form>

        <div class="auth-links">
            Already have an account? <a href="/login">Log in</a>
        </div>
    </div>
</div>
