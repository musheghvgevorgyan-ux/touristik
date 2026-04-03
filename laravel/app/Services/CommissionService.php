<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

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
        $agency = Agency::find($agencyId);

        if (!$agency) {
            return [
                'net_price'       => $netPrice,
                'sell_price'      => $netPrice,
                'commission'      => 0.00,
                'commission_rate' => 0.00,
                'payment_model'   => 'markup',
            ];
        }

        $rate  = (float) ($agency->commission_rate ?? 0);
        $model = $agency->payment_model ?? 'markup';

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
        return (float) Booking::where('agency_id', $agencyId)
            ->where('status', 'confirmed')
            ->sum('commission');
    }

    /**
     * Monthly commission breakdown for the last N months.
     *
     * @return array Each element: ['month' => 'YYYY-MM', 'bookings_count', 'total_net', 'total_commission']
     */
    public static function monthlyBreakdown(int $agencyId, int $months = 12): array
    {
        return DB::table('bookings')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') AS month"),
                DB::raw('COUNT(*) AS bookings_count'),
                DB::raw('COALESCE(SUM(net_price), 0) AS total_net'),
                DB::raw('COALESCE(SUM(commission), 0) AS total_commission')
            )
            ->where('agency_id', $agencyId)
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderByDesc('month')
            ->get()
            ->toArray();
    }

    /**
     * Commission on pending (not yet confirmed) bookings.
     */
    public static function pendingCommission(int $agencyId): float
    {
        return (float) Booking::where('agency_id', $agencyId)
            ->where('status', 'pending')
            ->sum('commission');
    }

    /**
     * Get agency details by ID.
     */
    public static function getAgency(int $agencyId): ?Agency
    {
        return Agency::find($agencyId);
    }
}
