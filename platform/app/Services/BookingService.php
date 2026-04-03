<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Suppliers\SupplierFactory;
use Core\Database;

/**
 * Central booking service.
 *
 * Orchestrates the full booking lifecycle: creation from a supplier result,
 * cancellation through the supplier, and retrieval with payment info.
 */
class BookingService
{
    // ───────────────────────────────────────────────────────────
    //  Create a booking from a supplier result
    // ───────────────────────────────────────────────────────────

    /**
     * Persist a new booking after a successful supplier confirmation.
     *
     * @param array    $supplierResult  The unified result from SupplierInterface::book()['booking']
     * @param array    $guestData       Guest details:
     *   - first_name  (string)
     *   - last_name   (string)
     *   - email       (string)
     *   - phone       (string)
     * @param string   $supplier        Supplier name (e.g. 'hotelbeds')
     * @param string   $productType     'hotel', 'flight', 'tour', 'transfer', 'package'
     * @param int|null $userId          Authenticated user ID, or null for guest bookings
     * @param int|null $agencyId        Agency ID for B2B bookings, or null
     *
     * @return array The created booking record (DB row) with its reference
     */
    public static function createFromSupplier(
        array   $supplierResult,
        array   $guestData,
        string  $supplier     = 'hotelbeds',
        string  $productType  = 'hotel',
        ?int    $userId       = null,
        ?int    $agencyId     = null
    ): array {
        $db = Database::getInstance();

        $reference = Booking::generateReference();

        $netPrice  = (float)($supplierResult['total'] ?? 0);
        $sellPrice = $netPrice; // Markup can be applied here in the future

        $bookingData = [
            'reference'       => $reference,
            'supplier_ref'    => $supplierResult['reference'] ?? null,
            'user_id'         => $userId,
            'agency_id'       => $agencyId,
            'agent_id'        => null,
            'product_type'    => $productType,
            'supplier'        => $supplier,
            'guest_first_name' => $guestData['first_name'] ?? '',
            'guest_last_name'  => $guestData['last_name']  ?? '',
            'guest_email'      => $guestData['email']      ?? '',
            'guest_phone'      => $guestData['phone']      ?? null,
            'product_data'    => json_encode($supplierResult),
            'net_price'       => $netPrice,
            'sell_price'      => $sellPrice,
            'commission'      => 0.00,
            'currency'        => $supplierResult['currency'] ?? 'USD',
            'status'          => self::mapSupplierStatus($supplierResult['status'] ?? 'CONFIRMED'),
            'payment_status'  => 'unpaid',
            'supplier_response' => json_encode($supplierResult),
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $bookingId = Booking::create($bookingData);

        // Log the activity
        ActivityService::log(
            'booking.created',
            'booking',
            $bookingId,
            [
                'reference'    => $reference,
                'supplier'     => $supplier,
                'supplier_ref' => $supplierResult['reference'] ?? '',
                'hotel'        => $supplierResult['hotel'] ?? '',
                'total'        => $netPrice,
                'currency'     => $supplierResult['currency'] ?? 'USD',
            ]
        );

        $bookingData['id'] = $bookingId;
        return $bookingData;
    }

    // ───────────────────────────────────────────────────────────
    //  Cancel a booking
    // ───────────────────────────────────────────────────────────

    /**
     * Cancel a booking by its internal reference (TK-YYMMDD-XXX).
     *
     * Calls the supplier's cancel endpoint and updates the local DB record.
     *
     * @param string $reference Internal booking reference
     * @return array ['success' => bool, 'error' => string, 'booking' => array|null]
     */
    public static function cancel(string $reference): array
    {
        $booking = Booking::findByReference($reference);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking not found.', 'booking' => null];
        }

        if ($booking['status'] === 'cancelled') {
            return ['success' => false, 'error' => 'Booking is already cancelled.', 'booking' => $booking];
        }

        $supplierRef = $booking['supplier_ref'] ?? '';

        if (!$supplierRef) {
            // No supplier reference — just mark as cancelled locally
            Booking::updateStatus($booking['id'], 'cancelled');
            $booking['status'] = 'cancelled';

            ActivityService::log('booking.cancelled', 'booking', $booking['id'], [
                'reference' => $reference,
                'reason'    => 'No supplier reference — cancelled locally only',
            ]);

            return ['success' => true, 'error' => '', 'booking' => $booking];
        }

        // Call the supplier's cancel endpoint
        try {
            $adapter       = SupplierFactory::make($booking['supplier']);
            $cancelResult  = $adapter->cancel($supplierRef);
        } catch (\Throwable $e) {
            error_log("BookingService::cancel error for {$reference}: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => 'Could not reach the supplier. Please try again later.',
                'booking' => $booking,
            ];
        }

        if ($cancelResult['success']) {
            Booking::updateStatus($booking['id'], 'cancelled');
            $booking['status'] = 'cancelled';

            // If there are completed payments, handle refund status
            $totalPaid = Payment::totalPaidForBooking($booking['id']);
            if ($totalPaid > 0) {
                Booking::updatePaymentStatus($booking['id'], 'refunded');
                $booking['payment_status'] = 'refunded';
            }

            ActivityService::log('booking.cancelled', 'booking', $booking['id'], [
                'reference'        => $reference,
                'supplier_ref'     => $supplierRef,
                'cancellation_ref' => $cancelResult['cancellation_ref'] ?? '',
            ]);

            return ['success' => true, 'error' => '', 'booking' => $booking];
        }

        // Supplier cancel failed
        ActivityService::log('booking.cancel_failed', 'booking', $booking['id'], [
            'reference'    => $reference,
            'supplier_ref' => $supplierRef,
            'error'        => $cancelResult['error'] ?? 'Unknown error',
        ]);

        return [
            'success' => false,
            'error'   => $cancelResult['error'] ?? 'Cancellation failed at the supplier.',
            'booking' => $booking,
        ];
    }

    // ───────────────────────────────────────────────────────────
    //  Retrieve a booking with payment info
    // ───────────────────────────────────────────────────────────

    /**
     * Get a booking by its internal reference, enriched with payment data.
     *
     * @param string $reference Internal booking reference (TK-YYMMDD-XXX)
     * @return array|null The booking array with extra 'payments', 'total_paid',
     *                    'total_refunded' keys, or null if not found
     */
    public static function getByReference(string $reference): ?array
    {
        $booking = Booking::findByReference($reference);

        if (!$booking) {
            return null;
        }

        // Decode the stored JSON fields for convenience
        if (!empty($booking['product_data']) && is_string($booking['product_data'])) {
            $booking['product_data'] = json_decode($booking['product_data'], true);
        }
        if (!empty($booking['supplier_response']) && is_string($booking['supplier_response'])) {
            $booking['supplier_response'] = json_decode($booking['supplier_response'], true);
        }

        // Attach payment summary
        $booking['payments']       = Payment::forBooking($booking['id']);
        $booking['total_paid']     = Payment::totalPaidForBooking($booking['id']);
        $booking['total_refunded'] = Payment::totalRefundedForBooking($booking['id']);

        return $booking;
    }

    // ───────────────────────────────────────────────────────────
    //  Private helpers
    // ───────────────────────────────────────────────────────────

    /**
     * Map a supplier status string (e.g. Hotelbeds "CONFIRMED") to our
     * internal enum: pending, confirmed, cancelled, completed, failed, refunded.
     */
    private static function mapSupplierStatus(string $supplierStatus): string
    {
        return match (strtoupper($supplierStatus)) {
            'CONFIRMED'                            => 'confirmed',
            'CANCELLED', 'CANCELLATION'            => 'cancelled',
            'ERROR', 'FAILED'                      => 'failed',
            'ON_REQUEST', 'PENDING', 'PRECONFIRMED' => 'pending',
            default                                 => 'confirmed',
        };
    }
}
