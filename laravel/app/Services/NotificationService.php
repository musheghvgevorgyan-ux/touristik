<?php

namespace App\Services;

use App\Models\Notification;

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
     * @return Notification The created notification model
     */
    public static function send(int $userId, string $type, string $title, string $message, string $link = ''): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
            'is_read' => false,
        ]);
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
        $updated = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return $updated > 0;
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param int $userId
     * @return int Number of rows updated
     */
    public static function markAllRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    // ───────────────────────────────────────────────────────────
    //  Query helpers
    // ───────────────────────────────────────────────────────────

    /**
     * Count unread notifications for a user.
     */
    public static function unreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get the most recent notifications for a user.
     *
     * @param int $userId
     * @param int $limit  Maximum number of notifications to return
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function forUser(int $userId, int $limit = 20)
    {
        return Notification::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    // ───────────────────────────────────────────────────────────
    //  Static helpers for common events
    // ───────────────────────────────────────────────────────────

    /**
     * Send a booking confirmation notification.
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
