<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Helpers\Flash;
use App\Helpers\Redirect;

class AccountController extends Controller
{
    // ─── Dashboard ──────────────────────────────────────

    /**
     * Customer dashboard with recent bookings count and activity
     */
    public function dashboard(): void
    {
        $user = $this->currentUser();

        $recentBookingsCount = \Core\Database::getInstance()
            ->query(
                "SELECT COUNT(*) as cnt FROM bookings WHERE user_id = ? AND created_at >= ?",
                [$user['id'], date('Y-m-d H:i:s', strtotime('-30 days'))]
            )
            ->fetch()['cnt'] ?? 0;

        $recentActivity = \Core\Database::getInstance()
            ->query(
                "SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC LIMIT 5",
                [$user['id']]
            )
            ->fetchAll();

        $this->view('account.dashboard', [
            'title'               => 'Dashboard — Touristik',
            'user'                => $user,
            'recentBookingsCount' => (int) $recentBookingsCount,
            'recentActivity'      => $recentActivity,
        ]);
    }

    // ─── Profile ────────────────────────────────────────

    /**
     * Show the profile edit form
     */
    public function profile(): void
    {
        $user = $this->currentUser();

        $this->view('account.profile', [
            'title' => 'My Profile — Touristik',
            'user'  => $user,
        ]);
    }

    /**
     * Handle profile update
     */
    public function updateProfile(): void
    {
        $errors = $this->validate([
            'first_name' => 'required|min:2|max:100',
            'last_name'  => 'required|min:2|max:100',
            'email'      => 'required|email',
            'phone'      => 'phone',
        ]);

        $user = $this->currentUser();

        // Check for duplicate email (excluding current user)
        if (empty($errors) && User::emailExists($this->request->post('email'), $user['id'])) {
            $errors['email'] = 'This email address is already in use.';
        }

        if (!empty($errors)) {
            Redirect::withErrors($errors, '/account/profile');
            return;
        }

        User::update($user['id'], [
            'first_name' => $this->request->post('first_name'),
            'last_name'  => $this->request->post('last_name'),
            'email'      => $this->request->post('email'),
            'phone'      => $this->request->post('phone', ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Flash::success('Your profile has been updated successfully.');
        $this->redirect('/account/profile');
    }

    // ─── Bookings ───────────────────────────────────────

    /**
     * Show the user's booking history
     */
    public function bookings(): void
    {
        $user = $this->currentUser();

        $bookings = \Core\Database::getInstance()
            ->query(
                "SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC",
                [$user['id']]
            )
            ->fetchAll();

        $this->view('account.bookings', [
            'title'    => 'My Bookings — Touristik',
            'user'     => $user,
            'bookings' => $bookings,
        ]);
    }
}
