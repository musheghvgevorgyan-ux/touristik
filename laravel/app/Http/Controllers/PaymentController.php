<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkout(string $reference, PaymentService $paymentService)
    {
        $booking = Booking::where('reference', $reference)->firstOrFail();

        $paymentData = $paymentService->prepareCheckout($booking);

        return view('payment.checkout', [
            'booking' => $booking,
            'payment' => $paymentData,
        ]);
    }

    public function process(Request $request, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'reference'      => 'required|string',
            'payment_method' => 'required|in:card,bank,idram',
        ]);

        $booking = Booking::where('reference', $validated['reference'])->firstOrFail();

        $result = $paymentService->processPayment($booking, $validated['payment_method']);

        if ($result['redirect'] ?? false) {
            return redirect($result['redirect']);
        }

        return redirect("/booking/{$booking->reference}")
            ->with('success', 'Payment completed successfully.');
    }

    public function callback(Request $request, PaymentService $paymentService)
    {
        $result = $paymentService->handleCallback($request->all());

        if ($result['success']) {
            return redirect("/booking/{$result['reference']}")
                ->with('success', 'Payment confirmed.');
        }

        return redirect("/booking/{$result['reference']}")
            ->with('error', 'Payment failed. Please try again.');
    }
}
