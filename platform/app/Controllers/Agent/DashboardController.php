<?php

namespace App\Controllers\Agent;

use Core\Controller;
use Core\Database;
use App\Models\Booking;
use App\Services\CommissionService;

class DashboardController extends Controller
{
    /**
     * GET /agent
     *
     * B2B Agent dashboard with agency stats, recent bookings, and quick links.
     */
    public function index(): void
    {
        $agencyId = $this->session->get('agency_id');
        $db       = Database::getInstance();

        // Fetch agency details
        $agency = CommissionService::getAgency($agencyId);

        if (!$agency) {
            $agency = [
                'name'            => 'Unknown Agency',
                'status'          => 'pending',
                'balance'         => 0,
                'commission_rate' => 0,
                'payment_model'   => 'markup',
            ];
        }

        // Stats
        $totalBookings = Booking::countForAgency($agencyId);

        $monthStart = date('Y-m-01 00:00:00');
        $monthRow = $db->query(
            "SELECT COUNT(*) AS cnt FROM bookings WHERE agency_id = ? AND created_at >= ?",
            [$agencyId, $monthStart]
        )->fetch();
        $thisMonthBookings = (int) ($monthRow['cnt'] ?? 0);

        $totalCommission   = CommissionService::totalEarned($agencyId);
        $pendingCommission = CommissionService::pendingCommission($agencyId);

        // Recent 5 bookings
        $recentBookings = $db->query(
            "SELECT * FROM bookings WHERE agency_id = ? ORDER BY created_at DESC LIMIT 5",
            [$agencyId]
        )->fetchAll();

        $this->view('agent.dashboard', [
            'title'   => 'Agent Dashboard — Touristik',
            'agency'  => $agency,
            'stats'   => [
                'total_bookings'     => $totalBookings,
                'this_month'         => $thisMonthBookings,
                'total_commission'   => $totalCommission,
                'pending_commission' => $pendingCommission,
            ],
            'recentBookings' => $recentBookings,
        ]);
    }
}
