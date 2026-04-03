<?php use App\Helpers\View; ?>

<style>
    .profile-page { max-width: 700px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .profile-page h1 { font-size: 1.8rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .profile-page > p { color: var(--text-secondary); margin-bottom: 2rem; }
    .profile-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 2.5rem; margin-bottom: 2rem; }
    .profile-card h2 { font-size: 1.3rem; color: var(--text-heading); margin-bottom: 1.5rem; padding-bottom: 0.8rem; border-bottom: 1px solid var(--border-color); }
    .form-row { display: flex; gap: 1rem; }
    .form-row .form-group { flex: 1; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.3rem; font-size: 0.9rem; color: var(--text-secondary); }
    .form-group input,
    .form-group select { width: 100%; padding: 0.7rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius); font-size: 1rem; transition: border-color 0.2s; background: var(--bg-body); color: var(--text-primary); }
    .form-group input:focus,
    .form-group select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .form-group input[readonly] { background: var(--bg-body); opacity: 0.7; cursor: not-allowed; }
    .form-group .hint { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.2rem; }
    .btn-save { display: inline-block; padding: 0.8rem 2.5rem; background: var(--primary); color: #fff; border: none; border-radius: var(--radius); font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-save:hover { background: var(--primary-dark); }
    .profile-back { margin-top: 1rem; }
    .profile-back a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .profile-back a:hover { text-decoration: underline; }
    @media (max-width: 600px) {
        .form-row { flex-direction: column; gap: 0; }
        .profile-card { padding: 1.5rem; }
    }
</style>

<div class="profile-page">
    <h1 data-t="profile_title">Edit Profile</h1>
    <p data-t="profile_subtitle">Update your personal information and preferences</p>

    <form method="POST" action="/account/profile">
        <?= View::csrf() ?>

        <div class="profile-card reveal">
            <h2 data-t="personal_info">Personal Information</h2>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name" data-t="form_first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= View::e(View::old('first_name', $user['first_name'] ?? '')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name" data-t="form_last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= View::e(View::old('last_name', $user['last_name'] ?? '')) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email" data-t="form_email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= View::e($user['email'] ?? '') ?>" readonly>
                <div class="hint" data-t="email_readonly">Email address cannot be changed</div>
            </div>

            <div class="form-group">
                <label for="phone" data-t="form_phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?= View::e(View::old('phone', $user['phone'] ?? '')) ?>" placeholder="+374 XX XXXXXX">
            </div>
        </div>

        <div class="profile-card reveal">
            <h2 data-t="preferences">Preferences</h2>

            <div class="form-row">
                <div class="form-group">
                    <label for="language" data-t="form_language">Language</label>
                    <select id="language" name="language">
                        <?php $lang = View::old('language', $user['language'] ?? 'en'); ?>
                        <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>English</option>
                        <option value="ru" <?= $lang === 'ru' ? 'selected' : '' ?>>Russian</option>
                        <option value="hy" <?= $lang === 'hy' ? 'selected' : '' ?>>Armenian</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="currency" data-t="form_currency">Currency</label>
                    <select id="currency" name="currency">
                        <?php $curr = View::old('currency', $user['currency'] ?? 'USD'); ?>
                        <option value="USD" <?= $curr === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                        <option value="EUR" <?= $curr === 'EUR' ? 'selected' : '' ?>>EUR (&euro;)</option>
                        <option value="AMD" <?= $curr === 'AMD' ? 'selected' : '' ?>>AMD (&#1423;)</option>
                        <option value="RUB" <?= $curr === 'RUB' ? 'selected' : '' ?>>RUB (&#8381;)</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-save" data-t="save_changes">Save Changes</button>
    </form>

    <form method="POST" action="/account/password" style="margin-top: 2rem;">
        <?= View::csrf() ?>

        <div class="profile-card reveal">
            <h2 data-t="change_password">Change Password</h2>

            <div class="form-group">
                <label for="current_password" data-t="form_current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" placeholder="Enter your current password" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="new_password" data-t="form_new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Minimum 8 characters" required>
                    <div class="hint" data-t="password_hint">At least 8 characters</div>
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation" data-t="form_confirm_password">Confirm New Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Repeat new password" required>
                </div>
            </div>

            <button type="submit" class="btn-save" data-t="update_password">Update Password</button>
        </div>
    </form>

    <div class="profile-back reveal">
        <a href="/account" data-t="back_to_dashboard">&larr; Back to Dashboard</a>
    </div>
</div>
