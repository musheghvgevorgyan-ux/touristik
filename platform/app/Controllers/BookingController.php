<?php

namespace App\Controllers;

use Core\Controller;
use App\Suppliers\SupplierFactory;
use App\Helpers\Flash;
use App\Helpers\View as ViewHelper;
use App\Helpers\Redirect;

class BookingController extends Controller
{
    /**
     * GET /booking/create/{type}/{id}
     *
     * Step 1-2 of the booking flow:
     *   - Accepts hotel_data via POST (from search) then redirects to GET
     *   - On GET, runs CheckRate (if RECHECK) or uses cached data (if BOOKABLE)
     *   - Shows booking confirmation form with hotel summary + guest form
     */
    public function create(string $type, string $id): void
    {
        // Handle POST from search page (hotel_data JSON submission)
        if ($this->request->isPost()) {
            $hotelJson = json_decode($this->request->post('hotel_data', '{}'), true);
            if ($hotelJson) {
                $this->session->set('booking_search_hotel', $hotelJson);
            }
            // Redirect to GET to prevent resubmission
            $params = http_build_query([
                'rate_key'  => $this->request->get('rate_key', $this->request->post('rate_key', '')),
                'rate_type' => $this->request->get('rate_type', $this->request->post('rate_type', 'BOOKABLE')),
            ]);
            $this->redirect("/booking/create/{$type}/{$id}?{$params}");
            return;
        }

        if ($type !== 'hotel') {
            Flash::error('Unsupported booking type.');
            $this->redirect('/');
            return;
        }

        $rateKey  = urldecode($this->request->get('rate_key', ''));
        $allowedRateTypes = ['RECHECK', 'BOOKABLE'];
        $rateType = $this->request->get('rate_type', 'BOOKABLE');
        $rateType = in_array($rateType, $allowedRateTypes) ? $rateType : 'BOOKABLE';

        $error     = '';
        $checkData = null;
        $hotelName = '';

        if (!$rateKey) {
            $this->view('booking.form', [
                'title'     => 'Booking — Touristik',
                'checkData' => null,
                'error'     => 'No hotel selected. Please search for hotels first.',
                'hotelName' => '',
            ]);
            return;
        }

        if ($rateType === 'RECHECK') {
            // Rate needs revalidation — call CheckRate via supplier
            try {
                $adapter   = SupplierFactory::make('hotelbeds');
                $checkData = $adapter->checkRate($rateKey);

                if (empty($checkData['available'])) {
                    $error = $checkData['error'] ?? 'The selected rate is no longer available.';
                    $checkData = null;
                } else {
                    // Store verified rate in session
                    $this->session->set('booking_rate', $checkData);
                    $this->session->set('booking_rate_key', $checkData['rate']['key'] ?? $rateKey);
                    $hotelName = $checkData['hotel']['name'] ?? '';
                }
            } catch (\Throwable $e) {
                $error = 'Could not verify rate. Please try again.';
            }
        } else {
            // BOOKABLE rate — skip CheckRate, build data from session cache
            $checkData = [
                'available' => true,
                'hotel' => [
                    'name'        => '',
                    'code'        => '',
                    'category'    => '',
                    'destination' => '',
                    'image'       => '',
                ],
                'room' => ['name' => '', 'code' => ''],
                'rate' => [
                    'key'                   => $rateKey,
                    'net'                   => 0,
                    'selling_rate'          => 0,
                    'currency'              => 'EUR',
                    'board'                 => '',
                    'board_code'            => '',
                    'cancellation_policies' => [],
                    'check_in'             => '',
                    'check_out'            => '',
                    'rooms'                => 1,
                    'adults'               => 1,
                    'children'             => 0,
                    'rateComments'         => '',
                ],
            ];

            // Enrich with cached search data
            $cached = $this->session->get('booking_search_hotel');
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
            $this->session->set('booking_rate', $checkData);
            $this->session->set('booking_rate_key', $rateKey);
        }

        $this->view('booking.form', [
            'title'     => ($hotelName ? $hotelName . ' — ' : '') . 'Booking — Touristik',
            'checkData' => $checkData,
            'error'     => $error,
            'hotelName' => $hotelName,
        ]);
    }

    /**
     * POST /booking/store
     *
     * Step 3: Process the reservation.
     * Validates guest info, calls Hotelbeds Book API, saves to DB, redirects to voucher.
     */
    public function store(): void
    {
        $storedRate    = $this->session->get('booking_rate');
        $storedRateKey = $this->session->get('booking_rate_key', '');

        if (!$storedRate || !$storedRateKey) {
            Flash::error('Session expired. Please search again.');
            $this->redirect('/');
            return;
        }

        $holderName    = trim($this->request->post('holder_name', ''));
        $holderSurname = trim($this->request->post('holder_surname', ''));
        $holderEmail   = filter_var(trim($this->request->post('holder_email', '')), FILTER_VALIDATE_EMAIL);
        $holderPhone   = trim($this->request->post('holder_phone', ''));
        $remark        = trim($this->request->post('remark', ''));

        if (empty($holderName) || empty($holderSurname)) {
            Flash::error('Please enter guest name and surname.');
            Flash::old($this->request->allPost());
            $this->redirect($this->request->get('HTTP_REFERER', '/booking/create/hotel/0'));
            return;
        }

        // Call Hotelbeds Book API
        try {
            $adapter = SupplierFactory::make('hotelbeds');

            $bookResult = $adapter->book([
                'rateKey' => $storedRateKey,
                'holder'  => [
                    'name'    => $holderName,
                    'surname' => $holderSurname,
                ],
                'rooms' => [[
                    'paxes' => [[
                        'roomId'  => 1,
                        'type'    => 'AD',
                        'name'    => $holderName,
                        'surname' => $holderSurname,
                    ]],
                ]],
                'remark' => $remark,
            ]);
        } catch (\Throwable $e) {
            Flash::error('Booking failed: ' . $e->getMessage());
            Flash::old($this->request->allPost());
            $this->redirect('/booking/create/hotel/0?rate_key=' . urlencode($storedRateKey));
            return;
        }

        if (empty($bookResult['success'])) {
            Flash::error($bookResult['error'] ?? 'Booking could not be completed.');
            Flash::old($this->request->allPost());
            $this->redirect('/booking/create/hotel/0?rate_key=' . urlencode($storedRateKey));
            return;
        }

        $bookingData = $bookResult['booking'] ?? $bookResult;
        $reference   = $bookingData['reference'] ?? '';

        // Save booking to database via BookingService (if available)
        if (class_exists('\App\Services\BookingService')) {
            try {
                \App\Services\BookingService::create([
                    'reference'        => $reference,
                    'client_reference' => $bookingData['client_reference'] ?? '',
                    'hotel_name'       => $bookingData['hotel'] ?? ($storedRate['hotel']['name'] ?? ''),
                    'guest_name'       => $holderName . ' ' . $holderSurname,
                    'guest_email'      => $holderEmail ?: '',
                    'guest_phone'      => $holderPhone,
                    'check_in'         => $bookingData['check_in'] ?? null,
                    'check_out'        => $bookingData['check_out'] ?? null,
                    'rooms'            => count($bookingData['rooms'] ?? [1]),
                    'currency'         => $bookingData['currency'] ?? 'EUR',
                    'total_price'      => $bookingData['total_net'] ?? $bookingData['total_selling'] ?? 0,
                    'status'           => $bookingData['status'] ?? 'CONFIRMED',
                    'raw_response'     => json_encode($bookingData),
                    'user_id'          => $this->session->userId(),
                ]);
            } catch (\Throwable $e) {
                // Log but don't fail the booking — the API booking was successful
                error_log('BookingService save failed: ' . $e->getMessage());
            }
        }

        // Store booking in session for the voucher page
        $this->session->set('last_booking', $bookingData);

        // Clear rate data
        $this->session->remove('booking_rate');
        $this->session->remove('booking_rate_key');
        $this->session->remove('booking_search_hotel');

        Flash::success('Booking confirmed successfully!');
        $this->redirect('/booking/' . urlencode($reference));
    }

    /**
     * GET /booking/{reference}
     *
     * Shows the booking voucher / confirmation page.
     */
    public function show(string $reference): void
    {
        $reference = urldecode($reference);
        $booking   = null;

        // Try session cache first
        $lastBooking = $this->session->get('last_booking');
        if ($lastBooking && ($lastBooking['reference'] ?? '') === $reference) {
            $booking = $lastBooking;
        } else {
            // Fetch from API
            try {
                $adapter = SupplierFactory::make('hotelbeds');
                $result  = $adapter->getBooking($reference);
                if (!empty($result['success'])) {
                    $booking = $result['booking'] ?? $result;
                }
            } catch (\Throwable $e) {
                // Fall through to error state
            }
        }

        if (!$booking) {
            Flash::error('Booking not found.');
            $this->view('booking.confirmation', [
                'title'   => 'Booking Not Found — Touristik',
                'booking' => null,
            ]);
            return;
        }

        $this->view('booking.confirmation', [
            'title'   => 'Booking ' . ($booking['reference'] ?? '') . ' — Touristik',
            'booking' => $booking,
        ]);
    }

    /**
     * POST /booking/{reference}/cancel
     *
     * Cancels an existing booking.
     */
    public function cancel(string $reference): void
    {
        $reference = urldecode($reference);

        try {
            $adapter = SupplierFactory::make('hotelbeds');
            $result  = $adapter->cancel($reference);

            if (!empty($result['success'])) {
                // Update in DB if BookingService exists
                if (class_exists('\App\Services\BookingService')) {
                    try {
                        \App\Services\BookingService::updateStatus($reference, 'CANCELLED');
                    } catch (\Throwable $e) {
                        error_log('BookingService cancel update failed: ' . $e->getMessage());
                    }
                }

                Flash::success('Booking has been cancelled successfully.');
            } else {
                Flash::error($result['error'] ?? 'Could not cancel booking.');
            }
        } catch (\Throwable $e) {
            Flash::error('Cancellation failed: ' . $e->getMessage());
        }

        $this->redirect('/booking/' . urlencode($reference));
    }
}
