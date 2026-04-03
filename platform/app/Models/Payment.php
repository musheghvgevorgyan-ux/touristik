<?php

namespace App\Models;

use Core\Model;

/**
 * Payment model.
 *
 * Table schema: see database/migrations/007_create_payments.php
 */
class Payment extends Model
{
    protected static string $table = 'payments';

    // ───────────────────────────────────────────────────────────
    //  Finders
    // ───────────────────────────────────────────────────────────

    /**
     * Get all payments for a booking, newest first.
     */
    public static function forBooking(int $bookingId): array
    {
        return self::where(['booking_id' => $bookingId], 'created_at DESC');
    }

    /**
     * Find a payment by its gateway transaction ID.
     */
    public static function findByTransactionId(string $transactionId): ?array
    {
        return self::findBy('transaction_id', $transactionId);
    }

    // ───────────────────────────────────────────────────────────
    //  Status helpers
    // ───────────────────────────────────────────────────────────

    /**
     * Get the latest completed payment for a booking.
     */
    public static function lastCompletedForBooking(int $bookingId): ?array
    {
        $results = self::raw(
            "SELECT * FROM payments
             WHERE booking_id = ? AND status = 'completed'
             ORDER BY paid_at DESC LIMIT 1",
            [$bookingId]
        )->fetchAll();

        return $results[0] ?? null;
    }

    /**
     * Calculate total paid amount for a booking (completed payments only).
     */
    public static function totalPaidForBooking(int $bookingId): float
    {
        $row = self::raw(
            "SELECT COALESCE(SUM(amount), 0) as total
             FROM payments
             WHERE booking_id = ? AND status = 'completed'",
            [$bookingId]
        )->fetch();

        return (float)($row['total'] ?? 0);
    }

    /**
     * Calculate total refunded amount for a booking.
     */
    public static function totalRefundedForBooking(int $bookingId): float
    {
        $row = self::raw(
            "SELECT COALESCE(SUM(refund_amount), 0) as total
             FROM payments
             WHERE booking_id = ? AND status IN ('refunded', 'partial_refund')",
            [$bookingId]
        )->fetch();

        return (float)($row['total'] ?? 0);
    }

    /**
     * Update payment status and optionally set paid_at timestamp.
     */
    public static function updateStatus(int $id, string $status): bool
    {
        $data = ['status' => $status];

        if ($status === 'completed') {
            $data['paid_at'] = date('Y-m-d H:i:s');
        }

        return self::update($id, $data);
    }

    /**
     * Record a refund against an existing payment.
     */
    public static function recordRefund(int $id, float $amount, string $reason = ''): bool
    {
        $payment = self::find($id);
        if (!$payment) {
            return false;
        }

        $newRefundTotal = (float)$payment['refund_amount'] + $amount;
        $newStatus      = $newRefundTotal >= (float)$payment['amount'] ? 'refunded' : 'partial_refund';

        return self::update($id, [
            'refund_amount' => $newRefundTotal,
            'refund_reason' => $reason,
            'status'        => $newStatus,
        ]);
    }

    // ───────────────────────────────────────────────────────────
    //  Scoped queries
    // ───────────────────────────────────────────────────────────

    /**
     * Get payments by status.
     */
    public static function byStatus(string $status, int $limit = 100): array
    {
        return self::where(['status' => $status], 'created_at DESC', $limit);
    }

    /**
     * Get payments by gateway.
     */
    public static function byGateway(string $gateway, int $limit = 100): array
    {
        return self::where(['gateway' => $gateway], 'created_at DESC', $limit);
    }
}
