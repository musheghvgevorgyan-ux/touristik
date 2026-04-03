<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\Suppliers\SupplierFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Central booking service.
 *
 * Orchestrates the full booking lifecycle: creation from a supplier result,
 * cancellation through the supplier, and retrieval with payment info.
 */
class BookingService
{
    // ───────────────────────────────────────────────────────────
    //  Generate a unique booking reference
    // ───────────────────────────────────────────────────────────

    /**
     * Generate a unique booking reference: TK-YYMMDD-XXX
     */
    public static function generateReference(): string
    {
        $datePart = date('ymd');
        $randomPart = strtoupper(bin2hex(random_bytes(2))); // 4 hex chars

        $reference = "TK-{$datePart}-{$randomPart}";

        // Ensure uniqueness
        while (Booking::where('reference', $reference)->exists()) {
            $randomPart = strtoupper(bin2hex(random_bytes(2)));
            $reference = "TK-{$datePart}-{$randomPart}";
        }

        return $reference;
    }

    // ───────────────────────────────────────────────────────────
    //  Create a booking from a supplier result
    // ───────────────────────────────────────────────────────────

    /**
     * Persist a new booking after a successful supplier confirmation.
     *
     * @param array    $supplierResult  The unified result from SupplierInterface::book()['booking']
     * @param array    $guestData       Guest details (first_name, last_name, email, phone)
     * @param string   $supplier        Supplier name (e.g. 'hotelbeds')
     * @param string   $productType     'hotel', 'flight', 'tour', 'transfer', 'package'
     * @param int|null $userId          Authenticated user ID, or null for guest bookings
     * @param int|null $agencyId        Agency ID for B2B bookings, or null
     *
     * @return Booking The created booking model instance
     */
    public static function createFromSupplier(
        array   $supplierResult,
        array   $guestData,
        string  $supplier     = 'hotelbeds',
        string  $productType  = 'hotel',
        ?int    $userId       = null,
        ?int    $agencyId     = null
    ): Booking {
        return DB::transaction(function () use ($supplierResult, $guestData, $supplier, $productType, $userId, $agencyId) {
            $reference = self::generateReference();
            $netPrice  = (float) ($supplierResult['total'] ?? 0);
            $sellPrice = $netPrice; // Markup can be applied here in the future

            $booking = Booking::create([
                'reference'        => $reference,
                'supplier_ref'     => $supplierResult['reference'] ?? null,
                'user_id'          => $userId,
                'agency_id'        => $agencyId,
                'agent_id'         => null,
                'product_type'     => $productType,
                'supplier'         => $supplier,
                'guest_first_name' => $guestData['first_name'] ?? '',
                'guest_last_name'  => $guestData['last_name']  ?? '',
                'guest_email'      => $guestData['email']      ?? '',
                'guest_phone'      => $guestData['phone']      ?? null,
                'product_data'     => $supplierResult,
                'net_price'        => $netPrice,
                'sell_price'       => $sellPrice,
                'commission'       => 0.00,
                'currency'         => $supplierResult['currency'] ?? 'USD',
                'status'           => self::mapSupplierStatus($supplierResult['status'] ?? 'CONFIRMED'),
                'payment_status'   => 'unpaid',
                'supplier_response' => $supplierResult,
            ]);

            // Log the activity
            ActivityService::log(
                'booking.created',
                'booking',
                $booking->id,
                [
                    'reference'    => $reference,
                    'supplier'     => $supplier,
                    'supplier_ref' => $supplierResult['reference'] ?? '',
                    'hotel'        => $supplierResult['hotel'] ?? '',
                    'total'        => $netPrice,
                    'currency'     => $supplierResult['currency'] ?? 'USD',
                ]
            );

            return $booking;
        });
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
     * @return array ['success' => bool, 'error' => string, 'booking' => Booking|null]
     */
    public static function cancel(string $reference): array
    {
        $booking = Booking::where('reference', $reference)->first();

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking not found.', 'booking' => null];
        }

        if ($booking->status === 'cancelled') {
            return ['success' => false, 'error' => 'Booking is already cancelled.', 'booking' => $booking];
        }

        $supplierRef = $booking->supplier_ref ?? '';

        if (!$supplierRef) {
            // No supplier reference -- just mark as cancelled locally
            $booking->update(['status' => 'cancelled']);

            ActivityService::log('booking.cancelled', 'booking', $booking->id, [
                'reference' => $reference,
                'reason'    => 'No supplier reference -- cancelled locally only',
            ]);

            return ['success' => true, 'error' => '', 'booking' => $booking->fresh()];
        }

        // Call the supplier's cancel endpoint
        try {
            $adapter      = SupplierFactory::make($booking->supplier);
            $cancelResult = $adapter->cancel($supplierRef);
        } catch (\Throwable $e) {
            Log::error("BookingService::cancel error for {$reference}: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => 'Could not reach the supplier. Please try again later.',
                'booking' => $booking,
            ];
        }

        if ($cancelResult['success']) {
            $booking->update(['status' => 'cancelled']);

            // If there are completed payments, handle refund status
            $totalPaid = Payment::where('booking_id', $booking->id)
                ->where('status', 'completed')
                ->sum('amount');

            if ($totalPaid > 0) {
                $booking->update(['payment_status' => 'refunded']);
            }

            ActivityService::log('booking.cancelled', 'booking', $booking->id, [
                'reference'        => $reference,
                'supplier_ref'     => $supplierRef,
                'cancellation_ref' => $cancelResult['cancellation_ref'] ?? '',
            ]);

            return ['success' => true, 'error' => '', 'booking' => $booking->fresh()];
        }

        // Supplier cancel failed
        ActivityService::log('booking.cancel_failed', 'booking', $booking->id, [
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
        $booking = Booking::where('reference', $reference)->first();

        if (!$booking) {
            return null;
        }

        $bookingArray = $booking->toArray();

        // Decode the stored JSON fields for convenience
        if (!empty($bookingArray['product_data']) && is_string($bookingArray['product_data'])) {
            $bookingArray['product_data'] = json_decode($bookingArray['product_data'], true);
        }
        if (!empty($bookingArray['supplier_response']) && is_string($bookingArray['supplier_response'])) {
            $bookingArray['supplier_response'] = json_decode($bookingArray['supplier_response'], true);
        }

        // Attach payment summary
        $payments = Payment::where('booking_id', $booking->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        $bookingArray['payments']       = $payments;
        $bookingArray['total_paid']     = Payment::where('booking_id', $booking->id)
            ->where('status', 'completed')
            ->sum('amount');
        $bookingArray['total_refunded'] = Payment::where('booking_id', $booking->id)
            ->sum('refund_amount');

        return $bookingArray;
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
            'CONFIRMED'                             => 'confirmed',
            'CANCELLED', 'CANCELLATION'             => 'cancelled',
            'ERROR', 'FAILED'                       => 'failed',
            'ON_REQUEST', 'PENDING', 'PRECONFIRMED' => 'pending',
            default                                 => 'confirmed',
        };
    }
}
