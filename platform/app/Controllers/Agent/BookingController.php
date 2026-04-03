<?php

namespace App\Controllers\Agent;

use Core\Controller;
use Core\Database;
use App\Models\Booking;
use App\Helpers\Flash;

class BookingController extends Controller
{
    /**
     * GET /agent/bookings
     *
     * List agency's bookings with filters and pagination.
     */
    public function index(): void
    {
        $agencyId = $this->session->get('agency_id');
        $db       = Database::getInstance();

        $page    = max(1, (int) $this->request->get('page', 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        // Collect filters
        $filters = [
            'status'    => trim($this->request->get('status', '')),
            'search'    => trim($this->request->get('search', '')),
            'date_from' => trim($this->request->get('date_from', '')),
            'date_to'   => trim($this->request->get('date_to', '')),
        ];

        $where  = ['b.agency_id = ?'];
        $params = [$agencyId];

        if ($filters['status'] !== '') {
            $where[]  = 'b.status = ?';
            $params[] = $filters['status'];
        }

        if ($filters['search'] !== '') {
            $where[]  = '(b.reference LIKE ? OR b.guest_first_name LIKE ? OR b.guest_last_name LIKE ?)';
            $term     = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if ($filters['date_from'] !== '') {
            $where[]  = 'b.created_at >= ?';
            $params[] = $filters['date_from'] . ' 00:00:00';
        }

        if ($filters['date_to'] !== '') {
            $where[]  = 'b.created_at <= ?';
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        // Total count
        $countRow = $db->query(
            "SELECT COUNT(*) AS cnt FROM bookings b {$whereSql}",
            $params
        )->fetch();
        $total      = (int) ($countRow['cnt'] ?? 0);
        $totalPages = max(1, (int) ceil($total / $perPage));

        // Fetch page
        $bookings = $db->query(
            "SELECT b.*
             FROM bookings b
             {$whereSql}
             ORDER BY b.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        )->fetchAll();

        $this->view('agent.bookings', [
            'title'       => 'My Bookings — Agent Portal',
            'bookings'    => $bookings,
            'filters'     => $filters,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
        ]);
    }

    /**
     * GET /agent/bookings/{id}
     *
     * Booking detail — only accessible if the booking belongs to this agency.
     */
    public function show(string $id): void
    {
        $agencyId = $this->session->get('agency_id');
        $booking  = Booking::find((int) $id);

        if (!$booking || (int) ($booking['agency_id'] ?? 0) !== (int) $agencyId) {
            Flash::error('Booking not found or access denied.');
            $this->redirect('/agent/bookings');
            return;
        }

        // Decode product_data JSON for display
        if (!empty($booking['product_data']) && is_string($booking['product_data'])) {
            $booking['product_data'] = json_decode($booking['product_data'], true);
        }

        $this->view('agent.booking-detail', [
            'title'   => 'Booking #' . $booking['reference'] . ' — Agent Portal',
            'booking' => $booking,
        ]);
    }
}
