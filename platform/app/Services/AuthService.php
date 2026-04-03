<?php

namespace App\Services;

use App\Models\User;
use Core\App;
use Core\Database;

class AuthService
{
    private \Core\Session $session;

    public function __construct()
    {
        $this->session = App::get('session');
    }

    /**
     * Register a new customer account
     */
    public function register(array $data): array
    {
        // Check if email is taken
        if (User::emailExists($data['email'])) {
            return ['success' => false, 'error' => 'Email is already registered.'];
        }

        $userId = User::register([
            'email'      => $data['email'],
            'password'   => $data['password'],
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'phone'      => $data['phone'] ?? null,
            'role'       => 'customer',
            'status'     => 'active',
        ]);

        // Auto-login after registration
        $this->loginById($userId);

        // Log activity
        ActivityService::log('user.registered', 'user', $userId);

        return ['success' => true, 'user_id' => $userId];
    }

    /**
     * Attempt login with email + password
     */
    public function login(string $email, string $password): array
    {
        $user = User::findByEmail($email);

        if (!$user) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        if (!User::verifyPassword($password, $user['password'])) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        if ($user['status'] !== 'active') {
            return ['success' => false, 'error' => 'Your account is suspended. Please contact support.'];
        }

        $this->loginById($user['id']);
        User::updateLastLogin($user['id']);

        ActivityService::log('user.login', 'user', $user['id']);

        return ['success' => true, 'user' => $user];
    }

    /**
     * Set session data for logged-in user
     */
    private function loginById(int $userId): void
    {
        $user = User::find($userId);
        $this->session->regenerate();
        $this->session->set('user_id', $user['id']);
        $this->session->set('user_role', $user['role']);
        $this->session->set('user_name', $user['first_name'] . ' ' . $user['last_name']);
        $this->session->set('user_email', $user['email']);

        if ($user['agency_id']) {
            $this->session->set('agency_id', $user['agency_id']);
        }
    }

    /**
     * Logout — destroy session
     */
    public function logout(): void
    {
        $userId = $this->session->userId();
        if ($userId) {
            ActivityService::log('user.logout', 'user', $userId);
        }
        $this->session->destroy();
    }

    /**
     * Request password reset — generate token and send email
     */
    public function requestPasswordReset(string $email): array
    {
        $user = User::findByEmail($email);

        if (!$user) {
            // Don't reveal if email exists
            return ['success' => true, 'message' => 'If the email exists, a reset link has been sent.'];
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Invalidate any existing reset tokens for this email
        Database::getInstance()->query(
            "UPDATE password_resets SET used = 1 WHERE email = ? AND used = 0",
            [$email]
        );

        // Create new token
        Database::getInstance()->query(
            "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)",
            [$email, hash('sha256', $token), $expiresAt]
        );

        // TODO: Send email with reset link (Phase 2 — EmailService)
        // For now, log the token in debug mode
        $config = require BASE_PATH . '/config/app.php';
        if ($config['debug']) {
            error_log("Password reset token for {$email}: {$token}");
        }

        ActivityService::log('user.password_reset_requested', 'user', $user['id']);

        return ['success' => true, 'message' => 'If the email exists, a reset link has been sent.'];
    }

    /**
     * Reset password using token
     */
    public function resetPassword(string $token, string $newPassword): array
    {
        $hashedToken = hash('sha256', $token);

        $reset = Database::getInstance()->query(
            "SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW() LIMIT 1",
            [$hashedToken]
        )->fetch();

        if (!$reset) {
            return ['success' => false, 'error' => 'Invalid or expired reset link.'];
        }

        $user = User::findByEmail($reset['email']);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found.'];
        }

        // Update password
        User::update($user['id'], [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);

        // Mark token as used
        Database::getInstance()->query(
            "UPDATE password_resets SET used = 1 WHERE id = ?",
            [$reset['id']]
        );

        ActivityService::log('user.password_reset', 'user', $user['id']);

        return ['success' => true, 'message' => 'Password has been reset. You can now log in.'];
    }
}
