<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Database;
use App\Services\ActivityService;
use App\Helpers\Flash;

class PromoController extends Controller
{
    /**
     * GET /admin/promos
     *
     * List all promo codes with status and usage count.
     */
    public function index(): void
    {
        $db = Database::getInstance();

        $promos = $db->query(
            "SELECT p.*,
                    (SELECT COUNT(*) FROM promo_usage WHERE promo_id = p.id) AS usage_count
             FROM promos p
             ORDER BY p.created_at DESC"
        )->fetchAll();

        $this->view('admin.promos.index', [
            'title'  => 'Promo Codes — Admin',
            'promos' => $promos,
        ]);
    }

    /**
     * POST /admin/promos
     *
     * Create or update a promo code.
     * If an "id" field is present in POST data, it updates; otherwise it creates.
     */
    public function store(): void
    {
        $id = (int) $this->request->post('id', 0);

        $errors = $this->validate([
            'code'  => 'required|max:50',
            'type'  => 'required',
            'value' => 'required|numeric',
        ]);

        if (!empty($errors)) {
            Flash::errors($errors);
            Flash::old($this->request->allPost());
            $this->redirect('/admin/promos');
            return;
        }

        $code          = strtoupper(trim($this->request->post('code', '')));
        $type          = trim($this->request->post('type', 'percentage'));
        $value         = (float) $this->request->post('value', 0);
        $minOrder      = $this->request->post('min_order', null);
        $maxDiscount   = $this->request->post('max_discount', null);
        $productTypes  = trim($this->request->post('product_types', ''));
        $usageLimit    = $this->request->post('usage_limit', null);
        $perUserLimit  = $this->request->post('per_user_limit', null);
        $startsAt      = trim($this->request->post('starts_at', ''));
        $expiresAt     = trim($this->request->post('expires_at', ''));
        $status        = trim($this->request->post('status', 'active'));

        // Validate type
        if (!in_array($type, ['percentage', 'fixed'], true)) {
            Flash::error('Invalid promo type. Must be "percentage" or "fixed".');
            Flash::old($this->request->allPost());
            $this->redirect('/admin/promos');
            return;
        }

        $data = [
            'code'           => $code,
            'type'           => $type,
            'value'          => $value,
            'min_order'      => $minOrder !== null && $minOrder !== '' ? (float) $minOrder : null,
            'max_discount'   => $maxDiscount !== null && $maxDiscount !== '' ? (float) $maxDiscount : null,
            'product_types'  => $productTypes ?: null,
            'usage_limit'    => $usageLimit !== null && $usageLimit !== '' ? (int) $usageLimit : null,
            'per_user_limit' => $perUserLimit !== null && $perUserLimit !== '' ? (int) $perUserLimit : null,
            'starts_at'      => $startsAt !== '' ? $startsAt : null,
            'expires_at'     => $expiresAt !== '' ? $expiresAt : null,
            'status'         => in_array($status, ['active', 'inactive', 'expired'], true) ? $status : 'active',
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        $db = Database::getInstance();

        if ($id > 0) {
            // Update existing promo
            $existing = $db->query("SELECT * FROM promos WHERE id = ? LIMIT 1", [$id])->fetch();
            if (!$existing) {
                Flash::error('Promo code not found.');
                $this->redirect('/admin/promos');
                return;
            }

            // Build UPDATE
            $sets   = [];
            $params = [];
            foreach ($data as $col => $val) {
                $sets[]   = "{$col} = ?";
                $params[] = $val;
            }
            $params[] = $id;

            $db->query(
                "UPDATE promos SET " . implode(', ', $sets) . " WHERE id = ?",
                $params
            );

            ActivityService::log('promo.updated', 'promo', $id, ['code' => $code]);
            Flash::success('Promo code updated successfully.');
        } else {
            // Check for duplicate code
            $duplicate = $db->query(
                "SELECT id FROM promos WHERE code = ? LIMIT 1",
                [$code]
            )->fetch();

            if ($duplicate) {
                Flash::error('A promo code with this code already exists.');
                Flash::old($this->request->allPost());
                $this->redirect('/admin/promos');
                return;
            }

            $data['created_at'] = date('Y-m-d H:i:s');

            $columns      = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));

            $db->query(
                "INSERT INTO promos ({$columns}) VALUES ({$placeholders})",
                array_values($data)
            );

            $newId = (int) $db->lastInsertId();
            ActivityService::log('promo.created', 'promo', $newId, ['code' => $code]);
            Flash::success('Promo code created successfully.');
        }

        $this->redirect('/admin/promos');
    }
}
