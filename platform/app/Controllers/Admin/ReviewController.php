<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Database;
use App\Services\ActivityService;
use App\Helpers\Flash;

class ReviewController extends Controller
{
    /**
     * GET /admin/reviews
     *
     * List reviews with optional status filter (pending/approved/rejected).
     */
    public function index(): void
    {
        $db = Database::getInstance();

        $page    = max(1, (int) $this->request->get('page', 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $filters = [
            'status' => trim($this->request->get('status', '')),
        ];

        $where  = [];
        $params = [];

        if ($filters['status'] !== '') {
            $where[]  = 'r.status = ?';
            $params[] = $filters['status'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Total count
        $countRow = $db->query(
            "SELECT COUNT(*) AS cnt FROM reviews r {$whereSql}",
            $params
        )->fetch();
        $total      = (int) $countRow['cnt'];
        $totalPages = (int) ceil($total / $perPage);

        // Fetch page with user info
        $reviews = $db->query(
            "SELECT r.*, u.first_name, u.last_name, u.email AS user_email
             FROM reviews r
             LEFT JOIN users u ON r.user_id = u.id
             {$whereSql}
             ORDER BY r.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        )->fetchAll();

        $this->view('admin.reviews.index', [
            'title'       => 'Reviews — Admin',
            'reviews'     => $reviews,
            'filters'     => $filters,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'total'       => $total,
        ]);
    }

    /**
     * POST /admin/reviews/{id}
     *
     * Moderate a review: approve, reject, or add an admin reply.
     */
    public function moderate(string $id): void
    {
        $db = Database::getInstance();

        $review = $db->query(
            "SELECT * FROM reviews WHERE id = ? LIMIT 1",
            [(int) $id]
        )->fetch();

        if (!$review) {
            Flash::error('Review not found.');
            $this->redirect('/admin/reviews');
            return;
        }

        $action     = trim($this->request->post('action', ''));
        $adminReply = trim($this->request->post('admin_reply', ''));

        $data    = ['updated_at' => date('Y-m-d H:i:s')];
        $logAction = '';

        switch ($action) {
            case 'approve':
                $data['status'] = 'approved';
                $logAction = 'review.approved';
                break;

            case 'reject':
                $data['status'] = 'rejected';
                $logAction = 'review.rejected';
                break;

            case 'reply':
                if ($adminReply === '') {
                    Flash::error('Reply text cannot be empty.');
                    $this->redirect('/admin/reviews');
                    return;
                }
                $data['admin_reply'] = $adminReply;
                $logAction = 'review.replied';
                break;

            default:
                Flash::error('Invalid moderation action.');
                $this->redirect('/admin/reviews');
                return;
        }

        // Build UPDATE
        $sets   = [];
        $params = [];
        foreach ($data as $col => $val) {
            $sets[]   = "{$col} = ?";
            $params[] = $val;
        }
        $params[] = (int) $id;

        $db->query(
            "UPDATE reviews SET " . implode(', ', $sets) . " WHERE id = ?",
            $params
        );

        ActivityService::log($logAction, 'review', (int) $id, [
            'action'      => $action,
            'admin_reply' => $adminReply ?: null,
        ]);

        Flash::success('Review ' . $action . ($action === 'reply' ? ' sent' : 'd') . ' successfully.');
        $this->redirect('/admin/reviews');
    }
}
