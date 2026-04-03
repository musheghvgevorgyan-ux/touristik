<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Database;

class ReportController extends Controller
{
    /**
     * GET /admin/reports
     *
     * Dashboard with revenue stats, booking counts by status,
     * top destinations, and user registration trends.
     */
    public function index(): void
    {
        $db = Database::getInstance();

        // ── Revenue stats ────────────────────────────────────────
        $revenueTotal = $db->query(
            "SELECT COALESCE(SUM(amount), 0) AS total
             FROM payments
             WHERE status = 'completed'"
        )->fetch()['total'];

        $thisMonthStart = date('Y-m-01 00:00:00');
        $revenueThisMonth = $db->query(
            "SELECT COALESCE(SUM(amount), 0) AS total
             FROM payments
             WHERE status = 'completed' AND paid_at >= ?",
            [$thisMonthStart]
        )->fetch()['total'];

        $lastMonthStart = date('Y-m-01 00:00:00', strtotime('first day of last month'));
        $lastMonthEnd   = date('Y-m-t 23:59:59', strtotime('last day of last month'));
        $revenueLastMonth = $db->query(
            "SELECT COALESCE(SUM(amount), 0) AS total
             FROM payments
             WHERE status = 'completed' AND paid_at >= ? AND paid_at <= ?",
            [$lastMonthStart, $lastMonthEnd]
        )->fetch()['total'];

        // ── Booking counts by status ─────────────────────────────
        $bookingsByStatus = $db->query(
            "SELECT status, COUNT(*) AS cnt
             FROM bookings
             GROUP BY status
             ORDER BY cnt DESC"
        )->fetchAll();

        $totalBookings = 0;
        foreach ($bookingsByStatus as $row) {
            $totalBookings += (int) $row['cnt'];
        }

        // ── Top destinations by booking count ────────────────────
        $topDestinations = $db->query(
            "SELECT d.name, d.country, d.slug, COUNT(b.id) AS booking_count
             FROM destinations d
             INNER JOIN bookings b ON b.destination_id = d.id
             GROUP BY d.id, d.name, d.country, d.slug
             ORDER BY booking_count DESC
             LIMIT 10"
        )->fetchAll();

        // ── User registrations over time (last 12 months) ───────
        $registrations = $db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month,
                    COUNT(*) AS cnt
             FROM users
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY month
             ORDER BY month ASC"
        )->fetchAll();

        // ── Recent revenue by month (last 12 months) ────────────
        $revenueByMonth = $db->query(
            "SELECT DATE_FORMAT(paid_at, '%Y-%m') AS month,
                    COALESCE(SUM(amount), 0) AS total
             FROM payments
             WHERE status = 'completed'
               AND paid_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY month
             ORDER BY month ASC"
        )->fetchAll();

        $this->view('admin.reports.index', [
            'title'            => 'Reports — Admin',
            'revenueTotal'     => (float) $revenueTotal,
            'revenueThisMonth' => (float) $revenueThisMonth,
            'revenueLastMonth' => (float) $revenueLastMonth,
            'bookingsByStatus' => $bookingsByStatus,
            'totalBookings'    => $totalBookings,
            'topDestinations'  => $topDestinations,
            'registrations'    => $registrations,
            'revenueByMonth'   => $revenueByMonth,
        ]);
    }
}
