<?php

namespace App\Services;

use Core\Database;

/**
 * Notification service.
 *
 * Handles creating, reading, and managing user notifications.
 * Provides static helpers for common booking-related notifications.
 */
class NotificationService
{
    // ───────────────────────────────────────────────────────────
    //  Create a notification
    // ───────────────────────────────────────────────────────────

    /**
     * Send a notification to a specific user.
     *
     * @param int    $userId  Recipient user ID
     * @param string $type    Notification type (booking, payment, system, review, wishlist)
     * @param string $title   Short title shown in the dropdown
     * @param string $message Longer description text
     * @param string $link    Optional URL the notification links to
     *
     * @return int The created notification ID
     */
    public static function send(int $userId, string $type, string $title, string $message, string $link = ''): int
    {
        $db = Database::getInstance();

        $db->query(
            "INSERT INTO notifications (user_id, type, title, message, link, is_read, created_at)
             VALUES (?, ?, ?, ?, ?, 0, NOW())",
            [$userId, $type, $title, $message, $link]
        );

        return (int) $db->lastInsertId();
    }

    // ───────────────────────────────────────────────────────────
    //  Mark as read
    // ───────────────────────────────────────────────────────────

    /**
     * Mark a single notification as read (ownership verified).
     *
     * @param int $notificationId
     * @param int $userId
     *
     * @return bool True if the row was updated, false if not found or not owned
     */
    public static function markRead(int $notificationId, int $userId): bool
    {
        $stmt = Database::getInstance()->query(
            "UPDATE notifications SET is_read = 1, read_at = NOW()
             WHERE id = ? AND user_id = ? AND is_read = 0",
            [$notificationId, $userId]
        );

        return $stmt->rowCount() > 0;
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param int $userId
     *
     * @return int Number of rows updated
     */
    public static function markAllRead(int $userId): int
    {
        $stmt = Database::getInstance()->query(
            "UPDATE notifications SET is_read = 1, read_at = NOW()
             WHERE user_id = ? AND is_read = 0",
            [$userId]
        );

        return $stmt->rowCount();
    }

    // ───────────────────────────────────────────────────────────
    //  Query helpers
    // ───────────────────────────────────────────────────────────

    /**
     * Count unread notifications for a user.
     *
     * @param int $userId
     *
     * @return int
     */
    public static function unreadCount(int $userId): int
    {
        $row = Database::getInstance()->query(
            "SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0",
            [$userId]
        )->fetch();

        return (int) ($row['cnt'] ?? 0);
    }

    /**
     * Get the most recent notifications for a user.
     *
     * @param int $userId
     * @param int $limit  Maximum number of notifications to return
     *
     * @return array List of notification rows (newest first)
     */
    public static function forUser(int $userId, int $limit = 20): array
    {
        return Database::getInstance()->query(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        )->fetchAll();
    }

    // ───────────────────────────────────────────────────────────
    //  Static helpers for common events
    // ───────────────────────────────────────────────────────────

    /**
     * Send a booking confirmation notification.
     *
     * @param int    $userId
     * @param string $reference Internal booking reference (TK-YYMMDD-XXX)
     */
    public static function sendBookingConfirmation(int $userId, string $reference): void
    {
        self::send(
            $userId,
            'booking',
            'Booking Confirmed',
            "Your booking {$reference} has been confirmed. Check your bookings for details.",
            '/account/bookings'
        );
    }

    /**
     * Send a payment received notification.
     *
     * @param int    $userId
     * @param string $reference Internal booking reference
     */
    public static function sendPaymentReceived(int $userId, string $reference): void
    {
        self::send(
            $userId,
            'payment',
            'Payment Received',
            "Payment for booking {$reference} has been received. Thank you!",
            '/account/bookings'
        );
    }

    /**
     * Send a booking cancelled notification.
     *
     * @param int    $userId
     * @param string $reference Internal booking reference
     */
    public static function sendBookingCancelled(int $userId, string $reference): void
    {
        self::send(
            $userId,
            'booking',
            'Booking Cancelled',
            "Your booking {$reference} has been cancelled. Contact support if you need assistance.",
            '/account/bookings'
        );
    }
}
