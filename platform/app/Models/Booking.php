<?php

namespace App\Models;

use Core\Model;

/**
 * Booking model.
 *
 * Table schema: see database/migrations/006_create_bookings.php
 *
 * Reference format: TK-YYMMDD-XXX  (e.g. TK-260403-A7F)
 */
class Booking extends Model
{
    protected static string $table = 'bookings';

    // ───────────────────────────────────────────────────────────
    //  Reference generation
    // ───────────────────────────────────────────────────────────

    /**
     * Generate a unique booking reference in TK-YYMMDD-XXX format.
     *
     * The last segment is a 3-character uppercase hex string, re-rolled
     * if a collision is detected (extremely unlikely).
     */
    public static function generateReference(): string
    {
        $datePart = date('ymd');

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $random = strtoupper(bin2hex(random_bytes(2)));   // 4 hex chars
            $random = substr($random, 0, 3);                  // trim to 3
            $ref    = "TK-{$datePart}-{$random}";

            if (!self::findByReference($ref)) {
                return $ref;
            }
        }

        // Extremely unlikely fallback: add milliseconds for extra entropy
        $ms = substr((string)hrtime(true), -4, 3);
        return "TK-{$datePart}-{$ms}";
    }

    // ───────────────────────────────────────────────────────────
    //  Finders
    // ───────────────────────────────────────────────────────────

    /**
     * Find a booking by its unique reference code (e.g. TK-260403-A7F).
     */
    public static function findByReference(string $ref): ?array
    {
        return self::findBy('reference', $ref);
    }

    /**
     * Find a booking by its supplier reference (Hotelbeds reference, etc.).
     */
    public static function findBySupplierRef(string $supplierRef): ?array
    {
        return self::findBy('supplier_ref', $supplierRef);
    }

    // ───────────────────────────────────────────────────────────
    //  Scoped queries
    // ───────────────────────────────────────────────────────────

    /**
     * Get bookings for a specific user, newest first.
     */
    public static function forUser(int $userId, int $limit = 50): array
    {
        return self::where(['user_id' => $userId], 'created_at DESC', $limit);
    }

    /**
     * Get bookings for a specific agency, newest first.
     */
    public static function forAgency(int $agencyId, int $limit = 50): array
    {
        return self::where(['agency_id' => $agencyId], 'created_at DESC', $limit);
    }

    /**
     * Get the most recent bookings for a user (dashboard widget).
     */
    public static function recentForUser(int $userId, int $limit = 5): array
    {
        return self::where(['user_id' => $userId], 'created_at DESC', $limit);
    }

    /**
     * Count total bookings for a user.
     */
    public static function countForUser(int $userId): int
    {
        return self::count(['user_id' => $userId]);
    }

    /**
     * Count total bookings for an agency.
     */
    public static function countForAgency(int $agencyId): int
    {
        return self::count(['agency_id' => $agencyId]);
    }

    // ───────────────────────────────────────────────────────────
    //  Status helpers
    // ───────────────────────────────────────────────────────────

    /**
     * Get bookings by status.
     */
    public static function byStatus(string $status, int $limit = 100): array
    {
        return self::where(['status' => $status], 'created_at DESC', $limit);
    }

    /**
     * Get bookings by payment status.
     */
    public static function byPaymentStatus(string $paymentStatus, int $limit = 100): array
    {
        return self::where(['payment_status' => $paymentStatus], 'created_at DESC', $limit);
    }

    /**
     * Update booking status and timestamp.
     */
    public static function updateStatus(int $id, string $status): bool
    {
        return self::update($id, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Update payment status.
     */
    public static function updatePaymentStatus(int $id, string $paymentStatus): bool
    {
        return self::update($id, [
            'payment_status' => $paymentStatus,
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
    }
}
