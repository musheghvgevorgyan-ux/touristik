<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Services\ActivityService;

/**
 * Wishlist controller for web pages.
 *
 * Handles the /account/wishlist display and form-based toggle (add/remove).
 */
class WishlistController extends Controller
{
    // ─── Show Wishlist ─────────────────────────────────────

    /**
     * GET /account/wishlist
     *
     * Display the authenticated user's wishlist items as a card grid.
     */
    public function index(): void
    {
        $user = $this->currentUser();

        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $wishlists = Database::getInstance()
            ->query(
                "SELECT * FROM wishlists WHERE user_id = ? ORDER BY created_at DESC",
                [$user['id']]
            )
            ->fetchAll();

        // Decode the stored JSON item_data for each row
        foreach ($wishlists as &$item) {
            if (!empty($item['item_data']) && is_string($item['item_data'])) {
                $item['item_data'] = json_decode($item['item_data'], true) ?? [];
            } else {
                $item['item_data'] = [];
            }
        }
        unset($item);

        $this->view('account.wishlist', [
            'title'     => 'My Wishlist — Touristik',
            'user'      => $user,
            'wishlists' => $wishlists,
        ]);
    }

    // ─── Toggle Wishlist Item ──────────────────────────────

    /**
     * POST /account/wishlist
     *
     * Add or remove an item from the wishlist.
     * Expects: item_type (hotel|tour|destination), item_id, item_data (JSON string)
     */
    public function toggle(): void
    {
        $user = $this->currentUser();

        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $itemType = trim($this->request->post('item_type', ''));
        $itemId   = trim($this->request->post('item_id', ''));
        $itemData = trim($this->request->post('item_data', '{}'));

        // Validate required fields
        $allowedTypes = ['hotel', 'tour', 'destination', 'transfer', 'package'];
        if (empty($itemType) || !in_array($itemType, $allowedTypes)) {
            Flash::error('Invalid item type.');
            $this->redirect('/account/wishlist');
            return;
        }

        if (empty($itemId)) {
            Flash::error('Invalid item.');
            $this->redirect('/account/wishlist');
            return;
        }

        // Validate JSON
        $decoded = json_decode($itemData, true);
        if ($decoded === null && $itemData !== '{}') {
            $itemData = '{}';
        }

        $db = Database::getInstance();

        // Check if item already exists
        $existing = $db->query(
            "SELECT id FROM wishlists WHERE user_id = ? AND item_type = ? AND item_id = ?",
            [$user['id'], $itemType, $itemId]
        )->fetch();

        if ($existing) {
            // Remove from wishlist
            $db->query(
                "DELETE FROM wishlists WHERE id = ?",
                [$existing['id']]
            );

            ActivityService::log('wishlist.removed', 'wishlist', $existing['id'], [
                'item_type' => $itemType,
                'item_id'   => $itemId,
            ]);

            Flash::success('Item removed from your wishlist.');
        } else {
            // Add to wishlist
            $db->query(
                "INSERT INTO wishlists (user_id, item_type, item_id, item_data, created_at)
                 VALUES (?, ?, ?, ?, NOW())",
                [$user['id'], $itemType, $itemId, $itemData]
            );

            $newId = (int) $db->lastInsertId();

            ActivityService::log('wishlist.added', 'wishlist', $newId, [
                'item_type' => $itemType,
                'item_id'   => $itemId,
            ]);

            Flash::success('Item added to your wishlist!');
        }

        // Redirect back to referrer or wishlist page
        $referer = $this->request->server('HTTP_REFERER', '/account/wishlist');
        $this->redirect($referer);
    }
}
