<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Central payment service.
 *
 * Uses a gateway abstraction so any payment processor (Stripe, AmeriaBank, etc.)
 * can be plugged in. Currently supports:
 *   - 'office'  : pay at a physical branch (no online charge)
 *   - 'sandbox' : simulated card payment for development/testing
 */
class PaymentService
{
    // ───────────────────────────────────────────────────────────
    //  Create a pending payment record
    // ───────────────────────────────────────────────────────────

    /**
     * Create a pending payment for a booking.
     *
     * @param int    $bookingId  Internal booking ID
     * @param float  $amount     Amount to charge
     * @param string $currency   ISO 4217 currency code (USD, EUR, AMD, ...)
     * @param string $gateway    Gateway name (office, sandbox, stripe, ...)
     * @param string $method     Payment method enum: card, bank_transfer, cash, balance
     *
     * @return Payment The newly created payment model
     */
    public function createPayment(int $bookingId, float $amount, string $currency, string $gateway, string $method): Payment
    {
        $payment = Payment::create([
            'booking_id' => $bookingId,
            'gateway'    => $gateway,
            'amount'     => $amount,
            'currency'   => $currency,
            'method'     => $method,
            'status'     => 'pending',
        ]);

        ActivityService::log('payment.created', 'payment', $payment->id, [
            'booking_id' => $bookingId,
            'gateway'    => $gateway,
            'amount'     => $amount,
            'currency'   => $currency,
            'method'     => $method,
        ]);

        return $payment;
    }

    // ───────────────────────────────────────────────────────────
    //  Process payment via gateway
    // ───────────────────────────────────────────────────────────

    /**
     * Process a payment through its configured gateway.
     *
     * For 'office'  -- marks as pending (customer pays at branch)
     * For 'sandbox' -- simulates a successful card charge
     * For real gateways -- would initiate redirect or server-to-server call
     *
     * @param int   $paymentId  Payment record ID
     * @param array $cardData   Optional card details (sandbox/real gateways)
     *
     * @return array ['success' => bool, 'error' => string, 'message' => string, ...]
     */
    public function processPayment(int $paymentId, array $cardData = []): array
    {
        $payment = Payment::find($paymentId);

        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found.'];
        }

        if ($payment->status === 'completed') {
            return ['success' => false, 'error' => 'Payment has already been completed.'];
        }

        if ($payment->status === 'failed') {
            return ['success' => false, 'error' => 'Payment has already failed. Please create a new payment.'];
        }

        return match ($payment->gateway) {
            'office'  => $this->processOffice($payment),
            'sandbox' => $this->processSandbox($payment, $cardData),
            default   => ['success' => false, 'error' => 'Unsupported payment gateway: ' . $payment->gateway],
        };
    }

    // ───────────────────────────────────────────────────────────
    //  Gateway: Office (pay at branch)
    // ───────────────────────────────────────────────────────────

    /**
     * Process an "office" payment.
     *
     * No money is collected online. The payment stays pending and the booking
     * payment_status remains 'unpaid'. Customer pays when visiting a branch.
     */
    private function processOffice(Payment $payment): array
    {
        $instructions = config('payment.office.instructions', 'Visit any of our branches to complete payment.');

        ActivityService::log('payment.office_reserved', 'payment', $payment->id, [
            'booking_id' => $payment->booking_id,
            'amount'     => $payment->amount,
            'currency'   => $payment->currency,
        ]);

        return [
            'success'    => true,
            'error'      => '',
            'message'    => $instructions,
            'gateway'    => 'office',
            'status'     => 'pending',
            'payment_id' => $payment->id,
        ];
    }

    // ───────────────────────────────────────────────────────────
    //  Gateway: Sandbox (test card payment)
    // ───────────────────────────────────────────────────────────

    /**
     * Process a sandbox payment -- simulates a successful card charge.
     *
     * Generates a fake transaction ID, marks payment as completed,
     * and updates the booking payment_status to 'paid'.
     */
    private function processSandbox(Payment $payment, array $cardData = []): array
    {
        // Simulate basic card validation
        $cardNumber = preg_replace('/\s+/', '', $cardData['card_number'] ?? '');

        if (empty($cardNumber) || strlen($cardNumber) < 13) {
            $this->failPayment($payment, 'Invalid card number.');
            return ['success' => false, 'error' => 'Invalid card number.'];
        }

        // Simulate specific failure cards for testing
        if ($cardNumber === '4000000000000002') {
            $this->failPayment($payment, 'Card declined (test decline card).');
            return ['success' => false, 'error' => 'Card declined by issuer.'];
        }

        $transactionId = 'SBX-' . strtoupper(bin2hex(random_bytes(8)));

        DB::transaction(function () use ($payment, $transactionId, $cardNumber) {
            // Mark payment as completed
            $payment->update([
                'status'           => 'completed',
                'transaction_id'   => $transactionId,
                'paid_at'          => now(),
                'gateway_response' => json_encode([
                    'sandbox'        => true,
                    'transaction_id' => $transactionId,
                    'card_last4'     => substr($cardNumber, -4),
                    'processed_at'   => now()->toIso8601String(),
                ]),
            ]);

            // Update booking payment_status to 'paid'
            Booking::where('id', $payment->booking_id)->update(['payment_status' => 'paid']);
        });

        ActivityService::log('payment.completed', 'payment', $payment->id, [
            'booking_id'     => $payment->booking_id,
            'transaction_id' => $transactionId,
            'amount'         => $payment->amount,
            'currency'       => $payment->currency,
            'gateway'        => 'sandbox',
            'card_last4'     => substr($cardNumber, -4),
        ]);

        return [
            'success'        => true,
            'error'          => '',
            'message'        => 'Payment successful! Your booking is confirmed.',
            'gateway'        => 'sandbox',
            'status'         => 'completed',
            'transaction_id' => $transactionId,
            'payment_id'     => $payment->id,
        ];
    }

    // ───────────────────────────────────────────────────────────
    //  Complete a payment (gateway callback)
    // ───────────────────────────────────────────────────────────

    /**
     * Called by a gateway callback to mark a payment as completed.
     *
     * @param int    $paymentId     Payment record ID
     * @param string $transactionId Gateway transaction reference
     * @param array  $gatewayData   Raw response from the gateway (stored as JSON)
     *
     * @return array ['success' => bool, 'error' => string]
     */
    public function completePayment(int $paymentId, string $transactionId, array $gatewayData = []): array
    {
        $payment = Payment::find($paymentId);

        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found.'];
        }

        if ($payment->status === 'completed') {
            return ['success' => true, 'error' => '', 'message' => 'Payment already completed.'];
        }

        if (in_array($payment->status, ['refunded', 'partial_refund'])) {
            return ['success' => false, 'error' => 'Cannot complete a refunded payment.'];
        }

        DB::transaction(function () use ($payment, $transactionId, $gatewayData) {
            $payment->update([
                'status'           => 'completed',
                'transaction_id'   => $transactionId,
                'paid_at'          => now(),
                'gateway_response' => !empty($gatewayData) ? json_encode($gatewayData) : null,
            ]);

            // Update booking payment_status
            Booking::where('id', $payment->booking_id)->update(['payment_status' => 'paid']);
        });

        ActivityService::log('payment.completed', 'payment', $paymentId, [
            'booking_id'     => $payment->booking_id,
            'transaction_id' => $transactionId,
            'amount'         => $payment->amount,
            'currency'       => $payment->currency,
            'gateway'        => $payment->gateway,
        ]);

        return [
            'success'        => true,
            'error'          => '',
            'message'        => 'Payment completed successfully.',
            'transaction_id' => $transactionId,
        ];
    }

    // ───────────────────────────────────────────────────────────
    //  Refund a payment (partial or full)
    // ───────────────────────────────────────────────────────────

    /**
     * Record a refund against an existing payment.
     *
     * Supports partial refunds (amount < payment total) and full refunds.
     * Updates the booking payment_status accordingly.
     *
     * @param int    $paymentId Payment record ID
     * @param float  $amount    Refund amount (0 = full refund)
     * @param string $reason    Reason for the refund
     *
     * @return array ['success' => bool, 'error' => string]
     */
    public function refundPayment(int $paymentId, float $amount = 0, string $reason = ''): array
    {
        $payment = Payment::find($paymentId);

        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found.'];
        }

        if (!in_array($payment->status, ['completed', 'partial_refund'])) {
            return ['success' => false, 'error' => 'Only completed or partially-refunded payments can be refunded.'];
        }

        $paymentAmount   = (float) $payment->amount;
        $alreadyRefunded = (float) $payment->refund_amount;
        $refundable      = $paymentAmount - $alreadyRefunded;

        // Full refund if amount is 0 or exceeds refundable
        if ($amount <= 0 || $amount > $refundable) {
            $amount = $refundable;
        }

        if ($amount <= 0) {
            return ['success' => false, 'error' => 'Nothing left to refund.'];
        }

        $newRefundTotal = $alreadyRefunded + $amount;
        $isFullRefund   = $newRefundTotal >= $paymentAmount;

        DB::transaction(function () use ($payment, $amount, $reason, $newRefundTotal, $isFullRefund) {
            $payment->update([
                'refund_amount' => $newRefundTotal,
                'refund_reason' => $reason,
                'status'        => $isFullRefund ? 'refunded' : 'partial_refund',
            ]);

            // Determine new booking payment status based on all payments
            $bookingId     = $payment->booking_id;
            $totalPaid     = Payment::where('booking_id', $bookingId)->where('status', 'completed')->sum('amount');
            $totalRefunded = Payment::where('booking_id', $bookingId)->sum('refund_amount');

            if ($totalRefunded >= $totalPaid && $totalPaid > 0) {
                Booking::where('id', $bookingId)->update(['payment_status' => 'refunded']);
            } elseif ($totalRefunded > 0) {
                Booking::where('id', $bookingId)->update(['payment_status' => 'paid']);
            }
        });

        // Re-read totals for response
        $totalPaid     = Payment::where('booking_id', $payment->booking_id)->where('status', 'completed')->sum('amount');
        $totalRefunded = Payment::where('booking_id', $payment->booking_id)->sum('refund_amount');

        ActivityService::log('payment.refunded', 'payment', $paymentId, [
            'booking_id'     => $payment->booking_id,
            'refund_amount'  => $amount,
            'reason'         => $reason,
            'total_refunded' => $totalRefunded,
            'total_paid'     => $totalPaid,
        ]);

        return [
            'success'        => true,
            'error'          => '',
            'message'        => "Refund of {$amount} {$payment->currency} processed successfully.",
            'refund_amount'  => $amount,
            'total_refunded' => $totalRefunded,
        ];
    }

    // ───────────────────────────────────────────────────────────
    //  Payment summary for a booking
    // ───────────────────────────────────────────────────────────

    /**
     * Get a payment summary for a booking.
     *
     * @param int $bookingId Internal booking ID
     * @return array ['total_paid', 'total_refunded', 'net_paid', 'payments', 'currency']
     */
    public function getPaymentSummary(int $bookingId): array
    {
        $payments      = Payment::where('booking_id', $bookingId)->orderBy('created_at', 'desc')->get();
        $totalPaid     = Payment::where('booking_id', $bookingId)->where('status', 'completed')->sum('amount');
        $totalRefunded = Payment::where('booking_id', $bookingId)->sum('refund_amount');

        // Determine currency from the first payment, or fall back to booking
        $currency = 'USD';
        if ($payments->isNotEmpty()) {
            $currency = $payments->first()->currency ?? 'USD';
        } else {
            $booking = Booking::find($bookingId);
            if ($booking) {
                $currency = $booking->currency ?? 'USD';
            }
        }

        return [
            'total_paid'     => (float) $totalPaid,
            'total_refunded' => (float) $totalRefunded,
            'net_paid'       => (float) ($totalPaid - $totalRefunded),
            'payments'       => $payments->toArray(),
            'currency'       => $currency,
        ];
    }

    // ───────────────────────────────────────────────────────────
    //  Private helpers
    // ───────────────────────────────────────────────────────────

    /**
     * Mark a payment as failed with an error message in gateway_response.
     */
    private function failPayment(Payment $payment, string $reason): void
    {
        $payment->update([
            'status'           => 'failed',
            'gateway_response' => json_encode([
                'error'     => $reason,
                'failed_at' => now()->toIso8601String(),
            ]),
        ]);

        ActivityService::log('payment.failed', 'payment', $payment->id, [
            'booking_id' => $payment->booking_id,
            'reason'     => $reason,
        ]);
    }
}
