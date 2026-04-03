<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Database;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\ActivityService;
use App\Helpers\Flash;

class BookingController extends Controller
{
    /**
     * GET /admin/bookings
     *
     * List all bookings with filters (status, date range, search by reference/guest name).
     * Paginated at 20 per page.
     */
    public function index(): void
    {
        $db = Database::getInstance();

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

        $where  = [];
        $params = [];

        if ($filters['status'] !== '') {
            $where[]  = 'b.status = ?';
            $params[] = $filters['status'];
        }

        if ($filters['search'] !== '') {
            $where[]  = '(b.reference LIKE ? OR b.guest_name LIKE ?)';
            $term     = '%' . $filters['search'] . '%';
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

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Total count
        $countRow = $db->query(
            "SELECT COUNT(*) AS cnt FROM bookings b {$whereSql}",
            $params
        )->fetch();
        $total      = (int) $countRow['cnt'];
        $totalPages = (int) ceil($total / $perPage);

        // Fetch page
        $bookings = $db->query(
            "SELECT b.*, u.first_name, u.last_name, u.email AS user_email
             FROM bookings b
             LEFT JOIN users u ON b.user_id = u.id
             {$whereSql}
             ORDER BY b.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        )->fetchAll();

        $this->view('admin.bookings.index', [
            'title'       => 'Bookings — Admin',
            'bookings'    => $bookings,
            'filters'     => $filters,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'total'       => $total,
        ]);
    }

    /**
     * GET /admin/bookings/{id}
     *
     * Single booking detail with payment history, activity log, and cancel option.
     */
    public function show(string $id): void
    {
        $booking = Booking::find((int) $id);

        if (!$booking) {
            Flash::error('Booking not found.');
            $this->redirect('/admin/bookings');
            return;
        }

        // Payment history
        $payments = Payment::forBooking((int) $id);

        // Activity log for this booking
        $activity = ActivityService::forEntity('booking', (int) $id);

        $this->view('admin.bookings.show', [
            'title'    => 'Booking #' . $booking['reference'] . ' — Admin',
            'booking'  => $booking,
            'payments' => $payments,
            'activity' => $activity,
        ]);
    }
}
