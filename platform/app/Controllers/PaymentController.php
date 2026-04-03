<?php

namespace App\Controllers;

use Core\Controller;
use App\Services\PaymentService;
use App\Services\BookingService;
use App\Services\ActivityService;
use App\Models\Booking;
use App\Helpers\Flash;

/**
 * Payment controller.
 *
 * Handles the checkout flow: displaying payment options, processing payments,
 * and receiving gateway callbacks.
 */
class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct()
    {
        parent::__construct();
        $this->paymentService = new PaymentService();
    }

    // ───────────────────────────────────────────────────────────
    //  GET /payment/{reference} — Checkout page
    // ───────────────────────────────────────────────────────────

    /**
     * Show the checkout page with payment options for a booking.
     *
     * Verifies the current user owns the booking (or is an admin).
     */
    public function checkout(string $reference): void
    {
        $reference = urldecode($reference);

        // Load booking with payment info
        $booking = BookingService::getByReference($reference);

        if (!$booking) {
            Flash::error('Booking not found.');
            $this->redirect('/account/bookings');
            return;
        }

        // Verify ownership: user must own this booking or be admin
        $user = $this->currentUser();
        if (!$this->canAccessBooking($booking, $user)) {
            Flash::error('You do not have permission to access this booking.');
            $this->redirect('/account/bookings');
            return;
        }

        // If already fully paid, redirect to confirmation
        if ($booking['payment_status'] === 'paid') {
            Flash::info('This booking has already been paid.');
            $this->redirect('/booking/' . urlencode($reference));
            return;
        }

        // If booking is cancelled, don't allow payment
        if ($booking['status'] === 'cancelled') {
            Flash::error('Cannot process payment for a cancelled booking.');
            $this->redirect('/booking/' . urlencode($reference));
            return;
        }

        $this->view('payment.checkout', [
            'title'   => 'Payment - ' . $reference . ' - Touristik',
            'booking' => $booking,
        ]);
    }

    // ───────────────────────────────────────────────────────────
    //  POST /payment/process — Process payment
    // ───────────────────────────────────────────────────────────

    /**
     * Process a payment submission.
     *
     * Expects POST data:
     *   - booking_reference : The TK-YYMMDD-XXX booking reference
     *   - payment_method    : 'office' or 'sandbox'
     *   - card_number, card_expiry, card_cvv (for sandbox)
     */
    public function process(): void
    {
        $reference     = trim($this->request->post('booking_reference', ''));
        $paymentMethod = trim($this->request->post('payment_method', ''));

        if (empty($reference) || empty($paymentMethod)) {
            Flash::error('Invalid payment request.');
            $this->redirect('/account/bookings');
            return;
        }

        // Load booking
        $booking = BookingService::getByReference($reference);

        if (!$booking) {
            Flash::error('Booking not found.');
            $this->redirect('/account/bookings');
            return;
        }

        // Verify ownership
        $user = $this->currentUser();
        if (!$this->canAccessBooking($booking, $user)) {
            Flash::error('You do not have permission to process this payment.');
            $this->redirect('/account/bookings');
            return;
        }

        // Prevent double payment
        if ($booking['payment_status'] === 'paid') {
            Flash::info('This booking has already been paid.');
            $this->redirect('/booking/' . urlencode($reference));
            return;
        }

        // Determine gateway and method enum
        $allowedMethods = ['office', 'sandbox'];
        if (!in_array($paymentMethod, $allowedMethods)) {
            Flash::error('Invalid payment method selected.');
            $this->redirect('/payment/' . urlencode($reference));
            return;
        }

        $gateway = $paymentMethod; // office or sandbox
        $method  = $paymentMethod === 'office' ? 'cash' : 'card';
        $amount  = (float) ($booking['sell_price'] ?? $booking['net_price'] ?? 0);
        $currency = $booking['currency'] ?? 'USD';

        if ($amount <= 0) {
            Flash::error('Invalid booking amount.');
            $this->redirect('/payment/' . urlencode($reference));
            return;
        }

        // Create the payment record
        $paymentId = $this->paymentService->createPayment(
            $booking['id'],
            $amount,
            $currency,
            $gateway,
            $method
        );

        // Gather card data for sandbox
        $cardData = [];
        if ($gateway === 'sandbox') {
            $cardData = [
                'card_number' => $this->request->post('card_number', ''),
                'card_expiry' => $this->request->post('card_expiry', ''),
                'card_cvv'    => $this->request->post('card_cvv', ''),
            ];
        }

        // Process the payment
        $result = $this->paymentService->processPayment($paymentId, $cardData);

        if ($result['success']) {
            if ($gateway === 'office') {
                Flash::success('Booking reserved! ' . ($result['message'] ?? 'Please visit our office to complete payment.'));
            } else {
                Flash::success($result['message'] ?? 'Payment processed successfully!');
            }
            $this->redirect('/booking/' . urlencode($reference));
        } else {
            Flash::error($result['error'] ?? 'Payment could not be processed.');
            $this->redirect('/payment/' . urlencode($reference));
        }
    }

    // ───────────────────────────────────────────────────────────
    //  GET/POST /payment/callback — Gateway callback
    // ───────────────────────────────────────────────────────────

    /**
     * Handle callbacks from external payment gateways.
     *
     * Real gateways (Stripe webhooks, AmeriaBank callbacks) will POST here
     * with transaction details. We verify the payment and update status.
     *
     * For now this is a stub for future gateway integrations.
     */
    public function callback(): void
    {
        $gateway       = $this->request->get('gateway', $this->request->post('gateway', ''));
        $paymentId     = (int) $this->request->get('payment_id', $this->request->post('payment_id', 0));
        $transactionId = $this->request->get('transaction_id', $this->request->post('transaction_id', ''));
        $status        = $this->request->get('status', $this->request->post('status', ''));

        // Log the callback for debugging
        ActivityService::log('payment.callback_received', 'payment', $paymentId ?: null, [
            'gateway'        => $gateway,
            'transaction_id' => $transactionId,
            'status'         => $status,
            'method'         => $this->request->method(),
            'ip'             => $this->request->ip(),
        ]);

        if (!$paymentId || !$transactionId) {
            // For API-style callbacks, return JSON
            if ($this->request->isPost() && $this->request->header('Content-Type') === 'application/json') {
                $this->json(['success' => false, 'error' => 'Missing payment_id or transaction_id.'], 400);
                return;
            }

            Flash::error('Invalid payment callback.');
            $this->redirect('/');
            return;
        }

        // Verify and complete the payment
        $gatewayData = array_merge(
            $this->request->allGet(),
            $this->request->isPost() ? $this->request->allPost() : []
        );

        if ($status === 'success' || $status === 'completed') {
            $result = $this->paymentService->completePayment($paymentId, $transactionId, $gatewayData);
        } else {
            // Gateway reported failure
            $result = ['success' => false, 'error' => 'Payment was not successful at the gateway.'];

            ActivityService::log('payment.callback_failed', 'payment', $paymentId, [
                'gateway'        => $gateway,
                'transaction_id' => $transactionId,
                'status'         => $status,
            ]);
        }

        // Find the booking reference to redirect to
        $payment = \App\Models\Payment::find($paymentId);
        $redirectUrl = '/';

        if ($payment) {
            $booking = Booking::find($payment['booking_id']);
            if ($booking) {
                $redirectUrl = '/booking/' . urlencode($booking['reference']);
            }
        }

        if ($result['success']) {
            Flash::success($result['message'] ?? 'Payment completed successfully.');
        } else {
            Flash::error($result['error'] ?? 'Payment verification failed.');
        }

        $this->redirect($redirectUrl);
    }

    // ───────────────────────────────────────────────────────────
    //  Private helpers
    // ───────────────────────────────────────────────────────────

    /**
     * Check if the current user can access a booking.
     *
     * Returns true if the user owns the booking, belongs to the same agency,
     * or has an admin/superadmin role.
     */
    private function canAccessBooking(array $booking, ?array $user): bool
    {
        if (!$user) {
            return false;
        }

        // Admins can access any booking
        if (in_array($user['role'] ?? '', ['admin', 'superadmin'])) {
            return true;
        }

        // Owner check
        if (!empty($booking['user_id']) && (int) $booking['user_id'] === (int) $user['id']) {
            return true;
        }

        // Agency check (agent can access bookings from their agency)
        if (!empty($booking['agency_id']) && !empty($user['agency_id'])
            && (int) $booking['agency_id'] === (int) $user['agency_id']) {
            return true;
        }

        return false;
    }
}
