<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Database;
use App\Models\User;
use App\Models\Booking;
use App\Services\ActivityService;
use App\Helpers\Flash;

class UserController extends Controller
{
    /**
     * GET /admin/users
     *
     * List all users with role filter and search by name/email.
     * Paginated at 20 per page.
     */
    public function index(): void
    {
        $db = Database::getInstance();

        $page    = max(1, (int) $this->request->get('page', 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $filters = [
            'role'   => trim($this->request->get('role', '')),
            'search' => trim($this->request->get('search', '')),
        ];

        $where  = [];
        $params = [];

        if ($filters['role'] !== '') {
            $where[]  = 'u.role = ?';
            $params[] = $filters['role'];
        }

        if ($filters['search'] !== '') {
            $where[]  = '(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)';
            $term     = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Total count
        $countRow = $db->query(
            "SELECT COUNT(*) AS cnt FROM users u {$whereSql}",
            $params
        )->fetch();
        $total      = (int) $countRow['cnt'];
        $totalPages = (int) ceil($total / $perPage);

        // Fetch page
        $users = $db->query(
            "SELECT u.*,
                    (SELECT COUNT(*) FROM bookings WHERE user_id = u.id) AS booking_count
             FROM users u
             {$whereSql}
             ORDER BY u.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        )->fetchAll();

        $this->view('admin.users.index', [
            'title'       => 'Users — Admin',
            'users'       => $users,
            'filters'     => $filters,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'total'       => $total,
        ]);
    }

    /**
     * GET /admin/users/{id}
     *
     * User profile with their bookings and activity log.
     */
    public function show(string $id): void
    {
        $user = User::find((int) $id);

        if (!$user) {
            Flash::error('User not found.');
            $this->redirect('/admin/users');
            return;
        }

        // User's bookings (most recent 20)
        $bookings = Booking::forUser((int) $id, 20);

        // Activity log
        $activity = ActivityService::forUser((int) $id, 30);

        $this->view('admin.users.show', [
            'title'    => $user['first_name'] . ' ' . $user['last_name'] . ' — Admin',
            'user'     => $user,
            'bookings' => $bookings,
            'activity' => $activity,
        ]);
    }

    /**
     * POST /admin/users/{id}
     *
     * Update user role and status. Only superadmin can change roles.
     */
    public function update(string $id): void
    {
        $user = User::find((int) $id);

        if (!$user) {
            Flash::error('User not found.');
            $this->redirect('/admin/users');
            return;
        }

        $currentUser = $this->currentUser();
        $newRole     = trim($this->request->post('role', ''));
        $newStatus   = trim($this->request->post('status', ''));

        $data    = [];
        $changes = [];

        // Role change — only superadmin allowed
        if ($newRole !== '' && $newRole !== $user['role']) {
            if (($currentUser['role'] ?? '') !== 'superadmin') {
                Flash::error('Only a superadmin can change user roles.');
                $this->redirect('/admin/users/' . $id);
                return;
            }

            $allowedRoles = ['user', 'agent', 'admin', 'superadmin'];
            if (!in_array($newRole, $allowedRoles, true)) {
                Flash::error('Invalid role.');
                $this->redirect('/admin/users/' . $id);
                return;
            }

            $data['role'] = $newRole;
            $changes['role'] = ['from' => $user['role'], 'to' => $newRole];
        }

        // Status change
        if ($newStatus !== '' && $newStatus !== ($user['status'] ?? 'active')) {
            $allowedStatuses = ['active', 'suspended'];
            if (!in_array($newStatus, $allowedStatuses, true)) {
                Flash::error('Invalid status.');
                $this->redirect('/admin/users/' . $id);
                return;
            }

            $data['status'] = $newStatus;
            $changes['status'] = ['from' => $user['status'] ?? 'active', 'to' => $newStatus];
        }

        if (empty($data)) {
            Flash::info('No changes were made.');
            $this->redirect('/admin/users/' . $id);
            return;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        User::update((int) $id, $data);

        ActivityService::log('user.updated', 'user', (int) $id, $changes);

        Flash::success('User updated successfully.');
        $this->redirect('/admin/users/' . $id);
    }
}
