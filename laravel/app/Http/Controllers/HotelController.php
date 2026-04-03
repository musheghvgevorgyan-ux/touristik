<?php

namespace App\Http\Controllers;

use App\Services\Suppliers\SupplierFactory;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function search(Request $request)
    {
        $tripType = $request->get('trip', 'roundtrip');
        $depart = $request->get('date', '');
        $returnDate = $request->get('return_date', '');
        $adults = (int) $request->get('adults', 1);
        $children = (int) $request->get('children', 0);
        $childAges = array_map('intval', (array) $request->get('child_age', []));

        if ($tripType === 'packages') {
            $from = trim($request->get('pkg_from', 'Yerevan'));
            $to = trim($request->get('pkg_to', ''));
        } else {
            $from = trim($request->get('from', ''));
            $to = trim($request->get('to', ''));
        }
        $toCity = explode(',', $to)[0];

        $searchParams = compact('tripType', 'from', 'to', 'toCity', 'depart', 'returnDate', 'adults', 'children');

        $hotels = [];
        $hotelError = '';
        $flightError = 'Flight search is being updated. Please check back soon.';

        // Only search if we have valid params
        if ($toCity && $depart && $returnDate && $returnDate > $depart) {
            try {
                $adapter = SupplierFactory::make('hotelbeds');
                $result = $adapter->search([
                    'destination' => $toCity,
                    'checkIn'     => $depart,
                    'checkOut'    => $returnDate,
                    'adults'      => $adults,
                    'children'    => $children,
                    'childAges'   => $childAges,
                ]);
                $hotels = $result['hotels'] ?? [];
                if (empty($hotels) && !empty($result['error'])) {
                    $hotelError = $result['error'];
                }
            } catch (\Throwable $e) {
                $hotelError = 'Could not fetch hotel results. Please try again.';
                \Log::error('Hotel search failed: ' . $e->getMessage());
            }
        } elseif (!$depart || !$returnDate) {
            $hotelError = 'Please select check-in and check-out dates to see hotels.';
        } elseif ($returnDate <= $depart) {
            $hotelError = 'Return date must be after departure date.';
        }

        return view('hotels.search', compact('hotels', 'searchParams', 'hotelError', 'flightError'));
    }
}
