<?php

namespace App\Services;

use Core\Database;

/**
 * Commission calculation and reporting for B2B agent bookings.
 *
 * Supports three payment models:
 *   - markup:     agent sets their own sell price; commission = sell - net
 *   - commission: sell price = net price; commission = net * rate%
 *   - prepaid:    deducts from agency balance; commission = net * rate%
 */
class CommissionService
{
    /**
     * Calculate commission for a given net price and agency.
     *
     * @param float $netPrice  The supplier net price
     * @param int   $agencyId  The agency ID to look up commission rules
     *
     * @return array ['net_price', 'sell_price', 'commission', 'commission_rate', 'payment_model']
     */
    public static function calculateCommission(float $netPrice, int $agencyId): array
    {
        $db = Database::getInstance();

        $agency = $db->query(
            "SELECT commission_rate, payment_model, balance FROM agencies WHERE id = ? LIMIT 1",
            [$agencyId]
        )->fetch();

        if (!$agency) {
            return [
                'net_price'      => $netPrice,
                'sell_price'     => $netPrice,
                'commission'     => 0.00,
                'commission_rate' => 0.00,
                'payment_model'  => 'markup',
            ];
        }

        $rate  = (float) ($agency['commission_rate'] ?? 0);
        $model = $agency['payment_model'] ?? 'markup';

        switch ($model) {
            case 'markup':
                // Agent sets their own sell price; suggested sell = net + commission%
                $commission = round($netPrice * ($rate / 100), 2);
                $sellPrice  = round($netPrice + $commission, 2);
                break;

            case 'commission':
                // Sell price equals net; commission is a percentage of net paid to agent
                $commission = round($netPrice * ($rate / 100), 2);
                $sellPrice  = $netPrice;
                break;

            case 'prepaid':
                // Deducts from agency balance; commission similar to commission model
                $commission = round($netPrice * ($rate / 100), 2);
                $sellPrice  = $netPrice;
                break;

            default:
                $commission = 0.00;
                $sellPrice  = $netPrice;
                break;
        }

        return [
            'net_price'       => round($netPrice, 2),
            'sell_price'      => round($sellPrice, 2),
            'commission'      => round($commission, 2),
            'commission_rate' => $rate,
            'payment_model'   => $model,
        ];
    }

    /**
     * Total commission earned on confirmed bookings for an agency.
     */
    public static function totalEarned(int $agencyId): float
    {
        $row = Database::getInstance()->query(
            "SELECT COALESCE(SUM(commission), 0) AS total
             FROM bookings
             WHERE agency_id = ? AND status = 'confirmed'",
            [$agencyId]
        )->fetch();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Monthly commission breakdown for the last N months.
     *
     * @return array Each element: ['month' => 'YYYY-MM', 'bookings_count', 'total_net', 'total_commission']
     */
    public static function monthlyBreakdown(int $agencyId, int $months = 12): array
    {
        return Database::getInstance()->query(
            "SELECT
                DATE_FORMAT(created_at, '%Y-%m') AS month,
                COUNT(*) AS bookings_count,
                COALESCE(SUM(net_price), 0) AS total_net,
                COALESCE(SUM(commission), 0) AS total_commission
             FROM bookings
             WHERE agency_id = ? AND status IN ('confirmed', 'completed')
             AND created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month DESC",
            [$agencyId, $months]
        )->fetchAll();
    }

    /**
     * Commission on pending (not yet confirmed) bookings.
     */
    public static function pendingCommission(int $agencyId): float
    {
        $row = Database::getInstance()->query(
            "SELECT COALESCE(SUM(commission), 0) AS total
             FROM bookings
             WHERE agency_id = ? AND status = 'pending'",
            [$agencyId]
        )->fetch();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Get agency details by ID.
     */
    public static function getAgency(int $agencyId): ?array
    {
        return Database::getInstance()->query(
            "SELECT * FROM agencies WHERE id = ? LIMIT 1",
            [$agencyId]
        )->fetch() ?: null;
    }
}
