<?php

namespace App\Controllers\Api;

use Core\Controller;
use Core\Database;
use App\Services\ActivityService;

/**
 * API controller for wishlist AJAX operations.
 *
 * Provides a JSON toggle endpoint for adding/removing wishlist items
 * without a full page reload.
 */
class WishlistApiController extends Controller
{
    /**
     * POST /api/wishlist/toggle
     *
     * Toggle a wishlist item for the current user.
     *
     * Request body (JSON or form):
     *   - item_type: string (hotel|tour|destination|transfer|package)
     *   - item_id:   string
     *   - item_data: string (JSON) — optional extra data (name, image, price, etc.)
     *
     * Response:
     * {
     *   "success": true,
     *   "added": true|false,
     *   "count": 12
     * }
     */
    public function toggle(): void
    {
        $user = $this->currentUser();

        if (!$user) {
            $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $itemType = trim($this->request->post('item_type', ''));
        $itemId   = trim($this->request->post('item_id', ''));
        $itemData = trim($this->request->post('item_data', '{}'));

        $allowedTypes = ['hotel', 'tour', 'destination', 'transfer', 'package'];

        if (empty($itemType) || !in_array($itemType, $allowedTypes)) {
            $this->json(['success' => false, 'error' => 'Invalid item type'], 400);
            return;
        }

        if (empty($itemId)) {
            $this->json(['success' => false, 'error' => 'Invalid item ID'], 400);
            return;
        }

        // Validate JSON payload
        if (json_decode($itemData, true) === null && $itemData !== '{}') {
            $itemData = '{}';
        }

        $db = Database::getInstance();

        // Check if item already exists in user's wishlist
        $existing = $db->query(
            "SELECT id FROM wishlists WHERE user_id = ? AND item_type = ? AND item_id = ?",
            [$user['id'], $itemType, $itemId]
        )->fetch();

        $added = false;

        if ($existing) {
            // Remove
            $db->query("DELETE FROM wishlists WHERE id = ?", [$existing['id']]);

            ActivityService::log('wishlist.removed', 'wishlist', $existing['id'], [
                'item_type' => $itemType,
                'item_id'   => $itemId,
            ]);
        } else {
            // Add
            $db->query(
                "INSERT INTO wishlists (user_id, item_type, item_id, item_data, created_at)
                 VALUES (?, ?, ?, ?, NOW())",
                [$user['id'], $itemType, $itemId, $itemData]
            );

            $newId = (int) $db->lastInsertId();
            $added = true;

            ActivityService::log('wishlist.added', 'wishlist', $newId, [
                'item_type' => $itemType,
                'item_id'   => $itemId,
            ]);
        }

        // Count remaining wishlist items
        $count = (int) ($db->query(
            "SELECT COUNT(*) AS cnt FROM wishlists WHERE user_id = ?",
            [$user['id']]
        )->fetch()['cnt'] ?? 0);

        $this->json([
            'success' => true,
            'added'   => $added,
            'count'   => $count,
        ]);
    }
}
