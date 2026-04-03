<?php

namespace App\Services;

use Core\App;
use Core\Database;

class ActivityService
{
    /**
     * Log an activity event
     */
    public static function log(
        string $action,
        string $entityType = '',
        ?int $entityId = null,
        array $details = []
    ): void {
        try {
            $session = App::get('session');
            $request = App::get('request');

            Database::getInstance()->query(
                "INSERT INTO activity_log (user_id, action, entity_type, entity_id, details, ip_address)
                 VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $session->userId(),
                    $action,
                    $entityType ?: null,
                    $entityId,
                    !empty($details) ? json_encode($details) : null,
                    $request->ip(),
                ]
            );
        } catch (\Throwable $e) {
            // Activity logging should never break the application
            error_log('ActivityService error: ' . $e->getMessage());
        }
    }

    /**
     * Get recent activity for a user
     */
    public static function forUser(int $userId, int $limit = 50): array
    {
        return Database::getInstance()->query(
            "SELECT * FROM activity_log WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        )->fetchAll();
    }

    /**
     * Get recent activity for an entity
     */
    public static function forEntity(string $entityType, int $entityId, int $limit = 50): array
    {
        return Database::getInstance()->query(
            "SELECT al.*, u.first_name, u.last_name, u.email
             FROM activity_log al
             LEFT JOIN users u ON al.user_id = u.id
             WHERE al.entity_type = ? AND al.entity_id = ?
             ORDER BY al.created_at DESC LIMIT ?",
            [$entityType, $entityId, $limit]
        )->fetchAll();
    }
}
