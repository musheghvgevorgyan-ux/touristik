<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    /**
     * Find user by email
     */
    public static function findByEmail(string $email): ?array
    {
        return self::findBy('email', $email);
    }

    /**
     * Create a new user with hashed password
     */
    public static function register(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return self::create($data);
    }

    /**
     * Verify password against hash
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Update last login timestamp
     */
    public static function updateLastLogin(int $id): void
    {
        self::update($id, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get users by role
     */
    public static function byRole(string $role, int $limit = 100): array
    {
        return self::where(['role' => $role], 'created_at DESC', $limit);
    }

    /**
     * Get users by agency
     */
    public static function byAgency(int $agencyId): array
    {
        return self::where(['agency_id' => $agencyId], 'last_name ASC');
    }

    /**
     * Check if email exists (for registration validation)
     */
    public static function emailExists(string $email, ?int $exceptId = null): bool
    {
        $db = self::db();
        $sql = "SELECT COUNT(*) as cnt FROM users WHERE email = ?";
        $params = [$email];

        if ($exceptId) {
            $sql .= " AND id != ?";
            $params[] = $exceptId;
        }

        return (int) $db->query($sql, $params)->fetch()['cnt'] > 0;
    }
}
