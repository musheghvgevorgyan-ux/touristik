<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Services\ActivityService;

/**
 * Review controller.
 *
 * Lets authenticated users view their submitted reviews and leave new
 * reviews for bookings they have completed.
 */
class ReviewController extends Controller
{
    // ─── My Reviews ────────────────────────────────────────

    /**
     * GET /account/reviews
     *
     * Show the user's reviews and a form to leave a new one.
     */
    public function myReviews(): void
    {
        $user = $this->currentUser();

        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $db = Database::getInstance();

        // Get all reviews by this user
        $reviews = $db->query(
            "SELECT r.*, b.reference AS booking_reference, b.product_type
             FROM reviews r
             LEFT JOIN bookings b ON r.booking_id = b.id
             WHERE r.user_id = ?
             ORDER BY r.created_at DESC",
            [$user['id']]
        )->fetchAll();

        // Get bookings that can still be reviewed:
        // - Confirmed or completed status
        // - Not already reviewed by this user
        $pendingBookings = $db->query(
            "SELECT b.id, b.reference, b.product_type,
                    b.guest_first_name, b.guest_last_name,
                    b.created_at, b.status
             FROM bookings b
             WHERE b.user_id = ?
               AND b.status IN ('confirmed', 'completed')
               AND b.id NOT IN (
                   SELECT booking_id FROM reviews WHERE user_id = ?
               )
             ORDER BY b.created_at DESC",
            [$user['id'], $user['id']]
        )->fetchAll();

        $this->view('account.reviews', [
            'title'           => 'My Reviews — Touristik',
            'user'            => $user,
            'reviews'         => $reviews,
            'pendingBookings' => $pendingBookings,
        ]);
    }

    // ─── Store Review ──────────────────────────────────────

    /**
     * POST /account/reviews
     *
     * Create a new review for a booking.
     * Validates that the booking belongs to the user and has not been reviewed.
     */
    public function store(): void
    {
        $user = $this->currentUser();

        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $errors = $this->validate([
            'booking_id' => 'required',
            'rating'     => 'required',
            'title'      => 'required|min:3|max:200',
            'comment'    => 'required|min:10|max:2000',
        ]);

        $bookingId = (int) $this->request->post('booking_id', 0);
        $rating    = (int) $this->request->post('rating', 0);
        $title     = trim($this->request->post('title', ''));
        $comment   = trim($this->request->post('comment', ''));

        // Validate rating range
        if ($rating < 1 || $rating > 5) {
            $errors['rating'] = 'Rating must be between 1 and 5.';
        }

        // Validate booking ownership and eligibility
        if ($bookingId > 0 && empty($errors['booking_id'])) {
            $db = Database::getInstance();

            $booking = $db->query(
                "SELECT id, user_id, status, reference FROM bookings WHERE id = ?",
                [$bookingId]
            )->fetch();

            if (!$booking) {
                $errors['booking_id'] = 'Booking not found.';
            } elseif ((int) $booking['user_id'] !== $user['id']) {
                $errors['booking_id'] = 'This booking does not belong to you.';
            } elseif (!in_array($booking['status'], ['confirmed', 'completed'])) {
                $errors['booking_id'] = 'You can only review confirmed or completed bookings.';
            } else {
                // Check for duplicate review
                $existingReview = $db->query(
                    "SELECT id FROM reviews WHERE booking_id = ? AND user_id = ?",
                    [$bookingId, $user['id']]
                )->fetch();

                if ($existingReview) {
                    $errors['booking_id'] = 'You have already reviewed this booking.';
                }
            }
        }

        if (!empty($errors)) {
            Redirect::withErrors($errors, '/account/reviews');
            return;
        }

        $db = Database::getInstance();

        $db->query(
            "INSERT INTO reviews (user_id, booking_id, rating, title, comment, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, 'pending', NOW(), NOW())",
            [$user['id'], $bookingId, $rating, $title, $comment]
        );

        $reviewId = (int) $db->lastInsertId();

        ActivityService::log('review.created', 'review', $reviewId, [
            'booking_id' => $bookingId,
            'rating'     => $rating,
            'title'      => $title,
        ]);

        Flash::success('Thank you! Your review has been submitted and is awaiting approval.');
        $this->redirect('/account/reviews');
    }
}
