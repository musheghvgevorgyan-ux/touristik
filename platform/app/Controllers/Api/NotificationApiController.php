<?php

namespace App\Controllers\Api;

use Core\Controller;
use App\Services\NotificationService;

/**
 * API controller for notification endpoints.
 *
 * All endpoints require an authenticated user (checked via session).
 */
class NotificationApiController extends Controller
{
    /**
     * GET /api/notifications
     *
     * Returns the current user's recent notifications and unread count as JSON.
     *
     * Response:
     * {
     *   "success": true,
     *   "unread_count": 3,
     *   "notifications": [ ... ]
     * }
     */
    public function index(): void
    {
        $user = $this->currentUser();

        if (!$user) {
            $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $limit = max(1, min(50, (int) ($this->request->get('limit', 20))));

        $notifications = NotificationService::forUser($user['id'], $limit);
        $unreadCount   = NotificationService::unreadCount($user['id']);

        // Format timestamps for display
        foreach ($notifications as &$n) {
            $n['time_ago'] = self::timeAgo($n['created_at'] ?? '');
        }
        unset($n);

        $this->json([
            'success'       => true,
            'unread_count'  => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * POST /api/notifications/{id}/read
     *
     * Marks a single notification as read for the current user.
     *
     * Response:
     * { "success": true } or { "success": false, "error": "..." }
     */
    public function markRead(string $id): void
    {
        $user = $this->currentUser();

        if (!$user) {
            $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $notificationId = (int) $id;

        if ($notificationId < 1) {
            $this->json(['success' => false, 'error' => 'Invalid notification ID'], 400);
            return;
        }

        $updated = NotificationService::markRead($notificationId, $user['id']);

        $this->json([
            'success'      => $updated,
            'unread_count' => NotificationService::unreadCount($user['id']),
        ]);
    }

    /**
     * POST /api/notifications/read-all
     *
     * Marks all notifications as read for the current user.
     */
    public function markAllRead(): void
    {
        $user = $this->currentUser();

        if (!$user) {
            $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $count = NotificationService::markAllRead($user['id']);

        $this->json([
            'success'      => true,
            'marked'       => $count,
            'unread_count' => 0,
        ]);
    }

    // ───────────────────────────────────────────────────────────
    //  Private helpers
    // ───────────────────────────────────────────────────────────

    /**
     * Convert a datetime string to a human-readable "time ago" label.
     */
    private static function timeAgo(string $datetime): string
    {
        if (empty($datetime)) {
            return '';
        }

        $now  = time();
        $ts   = strtotime($datetime);
        $diff = $now - $ts;

        if ($diff < 60) {
            return 'just now';
        }
        if ($diff < 3600) {
            $m = (int) floor($diff / 60);
            return $m . ($m === 1 ? ' min ago' : ' mins ago');
        }
        if ($diff < 86400) {
            $h = (int) floor($diff / 3600);
            return $h . ($h === 1 ? ' hour ago' : ' hours ago');
        }
        if ($diff < 604800) {
            $d = (int) floor($diff / 86400);
            return $d . ($d === 1 ? ' day ago' : ' days ago');
        }

        return date('M j, Y', $ts);
    }
}
