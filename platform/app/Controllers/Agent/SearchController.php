<?php

namespace App\Controllers\Agent;

use Core\Controller;
use App\Suppliers\SupplierFactory;
use App\Services\CommissionService;

class SearchController extends Controller
{
    /**
     * GET /agent/search
     *
     * Hotel search form and results for B2B agents.
     * Shows NET prices and suggested sell prices based on agency commission.
     */
    public function index(): void
    {
        $agencyId   = $this->session->get('agency_id');
        $toCity     = trim($this->request->get('destination', ''));
        $date       = $this->request->get('check_in', '');
        $returnDate = $this->request->get('check_out', '');
        $adults     = max(1, (int) $this->request->get('adults', 2));
        $children   = max(0, (int) $this->request->get('children', 0));
        $childAges  = array_map('intval', (array) $this->request->get('child_age', []));

        $searchParams = [
            'destination' => $toCity,
            'check_in'    => $date,
            'check_out'   => $returnDate,
            'adults'      => $adults,
            'children'    => $children,
            'child_ages'  => $childAges,
        ];

        $hotels     = [];
        $hotelError = '';

        // Only search if we have valid params
        if ($toCity && $date && $returnDate && $returnDate > $date) {
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

                    // Enrich each hotel with commission data
                    foreach ($hotels as &$hotel) {
                        $netPrice = (float) ($hotel['price'] ?? 0);
                        $commission = CommissionService::calculateCommission($netPrice, $agencyId);
                        $hotel['net_price']       = $commission['net_price'];
                        $hotel['suggested_sell']   = $commission['sell_price'];
                        $hotel['commission']       = $commission['commission'];
                        $hotel['commission_rate']  = $commission['commission_rate'];
                        $hotel['payment_model']    = $commission['payment_model'];
                    }
                    unset($hotel);
                }
            } catch (\Throwable $e) {
                $hotelError = 'Could not fetch hotel results. Please try again.';
            }
        } elseif ($toCity && (!$date || !$returnDate)) {
            $hotelError = 'Please select check-in and check-out dates.';
        } elseif ($toCity && $returnDate <= $date) {
            $hotelError = 'Check-out date must be after check-in date.';
        }

        $this->view('agent.search', [
            'title'        => 'Hotel Search — Agent Portal',
            'hotels'       => $hotels,
            'searchParams' => $searchParams,
            'hotelError'   => $hotelError,
            'show_net'     => true,
        ]);
    }

    /**
     * POST /agent/search
     *
     * Redirect POST search to GET with query params.
     */
    public function results(): void
    {
        $params = [
            'destination' => trim($this->request->post('destination', '')),
            'check_in'    => $this->request->post('check_in', ''),
            'check_out'   => $this->request->post('check_out', ''),
            'adults'      => $this->request->post('adults', 2),
            'children'    => $this->request->post('children', 0),
        ];

        $childAges = (array) $this->request->post('child_age', []);
        if (!empty($childAges)) {
            $params['child_age'] = $childAges;
        }

        $this->redirect('/agent/search?' . http_build_query($params));
    }
}
