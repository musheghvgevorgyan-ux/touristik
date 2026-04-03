<?php

namespace App\Controllers;

use Core\Controller;
use App\Suppliers\SupplierFactory;

class HotelController extends Controller
{
    /**
     * GET /hotels/search
     *
     * Shows the search form / results page.
     * When query params are present, delegates to HotelbedsAdapter.
     */
    public function search(): void
    {
        $from       = trim($this->request->get('from', ''));
        $to         = trim($this->request->get('to', ''));
        $date       = $this->request->get('date', '');
        $returnDate = $this->request->get('return_date', '');
        $adults     = max(1, (int) $this->request->get('adults', 1));
        $children   = max(0, (int) $this->request->get('children', 0));
        $childAges  = array_map('intval', (array) $this->request->get('child_age', []));
        $tripType   = $this->request->get('trip', 'roundtrip');

        // Package trip has separate from/to params
        if ($tripType === 'packages') {
            $from = trim($this->request->get('pkg_from', 'Yerevan'));
            $to   = trim($this->request->get('pkg_to', ''));
        }

        $toParts = explode(',', $to);
        $toCity  = trim($toParts[0]);

        $searchParams = [
            'from'        => $from,
            'to'          => $to,
            'toCity'      => $toCity,
            'date'        => $date,
            'return_date' => $returnDate,
            'adults'      => $adults,
            'children'    => $children,
            'child_ages'  => $childAges,
            'tripType'    => $tripType,
        ];

        // ── Flight stub ──
        $flights     = [];
        $flightError = '';
        $iataCodes   = $this->iataCodes();
        $fromCode    = $iataCodes[$from] ?? '';
        $toCode      = $iataCodes[$toCity] ?? '';

        if ($fromCode && $toCode) {
            $flightError = 'Flight search is being updated. Please check back soon.';
        } else {
            $flightError = 'Please select valid departure and destination cities.';
        }

        $searchParams['fromCode'] = $fromCode;
        $searchParams['toCode']   = $toCode;

        // ── Hotel search via supplier ──
        $hotels     = [];
        $hotelError = '';

        if ($date && $returnDate && $returnDate > $date && $toCity) {
            try {
                $adapter = SupplierFactory::make('hotelbeds');
                $result  = $adapter->search([
                    'destination' => $toCity,
                    'checkIn'     => $date,
                    'checkOut'    => $returnDate,
                    'adults'      => $adults,
                    'children'    => $children,
                    'childAges'   => $childAges,
                ]);

                if (!empty($result['error'])) {
                    $hotelError = $result['error'];
                } else {
                    $hotels = $result['hotels'] ?? [];
                }
            } catch (\Throwable $e) {
                $hotelError = 'Could not fetch hotel results. Please try again.';
            }
        } elseif (!$date || !$returnDate) {
            $hotelError = 'Please select check-in and check-out dates to see hotels.';
        } elseif ($returnDate <= $date) {
            $hotelError = 'Return date must be after departure date to search hotels.';
        }

        $this->view('hotels.search', [
            'title'        => 'Search Results — Touristik',
            'hotels'       => $hotels,
            'flights'      => $flights,
            'searchParams' => $searchParams,
            'hotelError'   => $hotelError,
            'flightError'  => $flightError,
        ]);
    }

    /**
     * POST /hotels/results
     *
     * Reserved for future POST-based search (not used currently).
     */
    public function results(): void
    {
        $this->redirect('/hotels/search?' . http_build_query($this->request->allPost()));
    }

    /**
     * GET /hotels/{code}
     *
     * Hotel detail page (future use).
     */
    public function detail(string $code): void
    {
        $this->view('hotels.detail', [
            'title'     => 'Hotel Details — Touristik',
            'hotelCode' => $code,
        ]);
    }

    /**
     * IATA code lookup table
     */
    private function iataCodes(): array
    {
        return [
            'Yerevan' => 'EVN', 'Moscow' => 'SVO', 'Sochi' => 'AER', 'Dubai' => 'DXB',
            'Istanbul' => 'IST', 'Antalya' => 'AYT', 'Paris' => 'CDG', 'London' => 'LHR',
            'Berlin' => 'BER', 'Frankfurt' => 'FRA', 'Munich' => 'MUC', 'Rome' => 'FCO',
            'Milan' => 'MXP', 'Athens' => 'ATH', 'Halkidiki' => 'SKG', 'Crete' => 'HER',
            'Tivat' => 'TIV', 'Tbilisi' => 'TBS', 'Cairo' => 'CAI', 'El Alamein' => 'DBB',
            'Sharm El Sheikh' => 'SSH', 'Hurghada' => 'HRG', 'Barcelona' => 'BCN',
            'Madrid' => 'MAD', 'Bangkok' => 'BKK', 'Phuket' => 'HKT', 'New York' => 'JFK',
            'Los Angeles' => 'LAX', 'Miami' => 'MIA',
        ];
    }
}
