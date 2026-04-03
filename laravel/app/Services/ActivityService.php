<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * Activity logging service.
 *
 * Records user actions to the activity_log table. Uses Laravel's Auth and
 * Request facades for automatic user and IP resolution.
 *
 * Designed to be fire-and-forget: logging failures never break the application.
 */
class ActivityService
{
    /**
     * Log an activity event.
     *
     * @param string   $action     Action identifier (e.g. 'booking.created', 'payment.completed')
     * @param string   $entityType Entity type (e.g. 'booking', 'payment', 'user')
     * @param int|null $entityId   Entity ID
     * @param array    $details    Additional context data (stored as JSON)
     */
    public static function log(
        string $action,
        string $entityType = '',
        ?int $entityId = null,
        array $details = []
    ): void {
        try {
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => $action,
                'entity_type' => $entityType ?: null,
                'entity_id'   => $entityId,
                'details'     => !empty($details) ? $details : null,
                'ip_address'  => Request::ip(),
            ]);
        } catch (\Throwable $e) {
            // Activity logging should never break the application
            Log::warning('ActivityService error: ' . $e->getMessage());
        }
    }

    /**
     * Get recent activity for a user.
     */
    public static function forUser(int $userId, int $limit = 50)
    {
        return ActivityLog::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent activity for an entity.
     */
    public static function forEntity(string $entityType, int $entityId, int $limit = 50)
    {
        return ActivityLog::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->with('user:id,first_name,last_name,email')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
