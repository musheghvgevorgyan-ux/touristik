<?php

namespace Core;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.use_strict_mode', '1');

            if ($this->isSecure()) {
                ini_set('session.cookie_secure', '1');
            }

            session_start();
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        session_unset();
        session_destroy();
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    // Flash messages — persist for one request only
    public function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    public function getAllFlash(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flash;
    }

    // CSRF token
    public function csrfToken(): string
    {
        if (!$this->has('_csrf_token')) {
            $this->set('_csrf_token', bin2hex(random_bytes(32)));
        }
        return $this->get('_csrf_token');
    }

    public function verifyCsrf(string $token): bool
    {
        return hash_equals($this->get('_csrf_token', ''), $token);
    }

    public function regenerateCsrf(): void
    {
        $this->set('_csrf_token', bin2hex(random_bytes(32)));
    }

    // Auth helpers
    public function userId(): ?int
    {
        return $this->get('user_id');
    }

    public function userRole(): ?string
    {
        return $this->get('user_role');
    }

    public function isLoggedIn(): bool
    {
        return $this->has('user_id');
    }

    private function isSecure(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? 0) == 443;
    }
}
