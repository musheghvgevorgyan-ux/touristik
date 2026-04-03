<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\Suppliers\SupplierFactory;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create(Request $request, string $type, string $id)
    {
        // Handle POST from search page — store hotel data and show form directly (no redirect)
        if ($request->isMethod('post') && $request->has('hotel_data')) {
            $hotelJson = json_decode($request->input('hotel_data', '{}'), true);
            if ($hotelJson) {
                session(['booking_search_hotel' => $hotelJson]);
            }
        }

        if ($type !== 'hotel') {
            return redirect('/')->with('error', 'Unsupported booking type.');
        }

        // Get rate key from POST, GET, or session
        $rateKey = $request->input('rate_key', session('booking_rate_key', ''));
        $rateType = $request->input('rate_type', 'BOOKABLE');
        $rateType = in_array($rateType, ['RECHECK', 'BOOKABLE']) ? $rateType : 'BOOKABLE';

        $error     = '';
        $checkData = null;
        $hotelName = '';

        if (!$rateKey) {
            return view('booking.form', [
                'checkData' => null,
                'error'     => 'No hotel selected. Please search for hotels first.',
                'hotelName' => '',
            ]);
        }

        if ($rateType === 'RECHECK') {
            try {
                $adapter   = SupplierFactory::make('hotelbeds');
                $checkData = $adapter->checkRate($rateKey);
                if (empty($checkData['available'])) {
                    $error = $checkData['error'] ?? 'Rate no longer available.';
                    $checkData = null;
                } else {
                    session(['booking_rate' => $checkData, 'booking_rate_key' => $checkData['rate']['key'] ?? $rateKey]);
                    $hotelName = $checkData['hotel']['name'] ?? '';
                }
            } catch (\Throwable $e) {
                $error = 'Could not verify rate. Please try again.';
            }
        } else {
            // BOOKABLE rate — build data from session cache
            $checkData = [
                'available' => true,
                'hotel'  => ['name' => '', 'code' => '', 'category' => '', 'destination' => '', 'image' => ''],
                'room'   => ['name' => '', 'code' => ''],
                'rate'   => [
                    'key' => $rateKey, 'net' => 0, 'selling_rate' => 0, 'currency' => 'EUR',
                    'board' => '', 'board_code' => '', 'cancellation_policies' => [],
                    'check_in' => '', 'check_out' => '', 'rooms' => 1,
                    'adults' => 1, 'children' => 0, 'rateComments' => '',
                ],
            ];

            $cached = session('booking_search_hotel');
            if ($cached) {
                $checkData['hotel']['name']     = $cached['name'] ?? '';
                $checkData['hotel']['image']    = $cached['image'] ?? '';
                $checkData['hotel']['category'] = $cached['stars'] ?? '';
                $checkData['hotel']['code']     = $cached['code'] ?? '';
                $checkData['rate']['net']       = $cached['price'] ?? 0;
                $checkData['rate']['currency']  = $cached['currency'] ?? 'EUR';
                $checkData['rate']['board']     = $cached['board'] ?? '';
                $checkData['rate']['check_in']  = $cached['check_in'] ?? '';
                $checkData['rate']['check_out'] = $cached['check_out'] ?? '';
                $checkData['room']['name']      = $cached['room'] ?? '';
                $checkData['rate']['cancellation_policies'] = $cached['cancellation_policies'] ?? [];
                $checkData['rate']['selling_rate'] = $cached['selling_rate'] ?? 0;
            }

            $hotelName = $checkData['hotel']['name'];
            session(['booking_rate' => $checkData, 'booking_rate_key' => $rateKey]);
        }

        return view('booking.form', compact('checkData', 'error', 'hotelName'));
    }

    public function store(Request $request)
    {
        $storedRate    = session('booking_rate');
        $storedRateKey = session('booking_rate_key', '');

        if (!$storedRate || !$storedRateKey) {
            return redirect('/')->with('error', 'Session expired. Please search again.');
        }

        $request->validate([
            'holder_name'    => 'required|string|max:100',
            'holder_surname' => 'required|string|max:100',
        ]);

        $holder = [
            'name'    => $request->input('holder_name'),
            'surname' => $request->input('holder_surname'),
            'remark'  => $request->input('remark', ''),
        ];

        $rooms = [[
            'paxes' => [[
                'roomId'  => 1,
                'type'    => 'AD',
                'name'    => $holder['name'],
                'surname' => $holder['surname'],
            ]]
        ]];

        try {
            $adapter    = SupplierFactory::make('hotelbeds');
            $bookResult = $adapter->book([
                'rateKey' => $storedRateKey,
                'holder'  => $holder,
                'rooms'   => $rooms,
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Booking failed: ' . $e->getMessage());
        }

        if (!empty($bookResult['success'])) {
            $bookingData = $bookResult['booking'] ?? [];
            session(['last_booking' => $bookingData]);

            // Save to database
            $reference = Booking::generateReference();
            Booking::create([
                'reference'        => $reference,
                'supplier_ref'     => $bookingData['reference'] ?? '',
                'user_id'          => auth()->id(),
                'product_type'     => 'hotel',
                'supplier'         => 'hotelbeds',
                'guest_first_name' => $holder['name'],
                'guest_last_name'  => $holder['surname'],
                'guest_email'      => $request->input('holder_email', ''),
                'guest_phone'      => $request->input('holder_phone', ''),
                'product_data'     => $bookingData,
                'net_price'        => $bookingData['total'] ?? 0,
                'sell_price'       => $bookingData['total'] ?? 0,
                'currency'         => $bookingData['currency'] ?? 'EUR',
                'status'           => 'confirmed',
                'payment_status'   => 'unpaid',
                'supplier_response'=> $bookingData,
            ]);

            session()->forget(['booking_rate', 'booking_rate_key']);

            return redirect("/booking/{$reference}")->with('success', 'Booking confirmed!');
        }

        $error = $bookResult['error'] ?? 'Booking failed.';
        return back()->with('error', $error);
    }

    public function show(string $reference)
    {
        $booking = Booking::where('reference', $reference)->first();

        // Try session for just-completed bookings
        $bookingData = session('last_booking');

        if (!$booking && !$bookingData) {
            abort(404, 'Booking not found.');
        }

        return view('booking.confirmation', [
            'booking'     => $booking,
            'bookingData' => $bookingData,
        ]);
    }

    public function cancel(Request $request, string $reference)
    {
        $booking = Booking::where('reference', $reference)->firstOrFail();

        try {
            $adapter = SupplierFactory::make($booking->supplier);
            $adapter->cancel($booking->supplier_ref);
            $booking->update(['status' => 'cancelled']);
            return redirect("/booking/{$reference}")->with('success', 'Booking cancelled.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Cancellation failed: ' . $e->getMessage());
        }
    }
}
