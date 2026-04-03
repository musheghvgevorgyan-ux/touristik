<?php

namespace App\Controllers\Agent;

use Core\Controller;
use Core\Database;
use App\Services\CommissionService;

class CommissionController extends Controller
{
    /**
     * GET /agent/commission
     *
     * Commission report: totals, monthly breakdown, and per-booking detail.
     */
    public function index(): void
    {
        $agencyId = $this->session->get('agency_id');
        $db       = Database::getInstance();

        // Agency details
        $agency = CommissionService::getAgency($agencyId);

        // Totals
        $totalEarned       = CommissionService::totalEarned($agencyId);
        $pendingCommission = CommissionService::pendingCommission($agencyId);

        // This month's commission
        $monthStart = date('Y-m-01 00:00:00');
        $monthRow = $db->query(
            "SELECT COALESCE(SUM(commission), 0) AS total
             FROM bookings
             WHERE agency_id = ? AND status IN ('confirmed', 'completed') AND created_at >= ?",
            [$agencyId, $monthStart]
        )->fetch();
        $thisMonthCommission = (float) ($monthRow['total'] ?? 0);

        // Monthly breakdown (last 12 months)
        $monthlyBreakdown = CommissionService::monthlyBreakdown($agencyId, 12);

        // Per-booking commission detail (last 50 bookings with commission > 0)
        $bookings = $db->query(
            "SELECT reference, guest_first_name, guest_last_name, product_type,
                    net_price, sell_price, commission, currency, status, created_at
             FROM bookings
             WHERE agency_id = ? AND commission > 0
             ORDER BY created_at DESC
             LIMIT 50",
            [$agencyId]
        )->fetchAll();

        $this->view('agent.commission', [
            'title'             => 'Commission Report — Agent Portal',
            'agency'            => $agency,
            'totalEarned'       => $totalEarned,
            'pendingCommission' => $pendingCommission,
            'thisMonth'         => $thisMonthCommission,
            'monthlyBreakdown'  => $monthlyBreakdown,
            'bookings'          => $bookings,
        ]);
    }
}
