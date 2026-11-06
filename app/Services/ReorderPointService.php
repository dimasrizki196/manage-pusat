<?php

namespace App\Services;

use App\Models\Item;

class ReorderPointService
{
    private const PERIOD_DAYS = 30;

    public function calculate(Item $item): array
    {
        $periodEnd = now();
        $periodStart = $periodEnd->copy()->subDays(self::PERIOD_DAYS);

        $totalUsage = (float) $item->transactions()
            ->where('transaction_type', 'keluar')
            ->whereHas('activity', function ($query) use ($periodStart, $periodEnd) {
                $query->where('table_name', 'transactions')
                    ->whereNull('deleted_at')
                    ->whereBetween('created_at', [
                        $periodStart,
                        $periodEnd,
                    ]);
            })
            ->sum('quantity');

        $averageDailyUsage = $totalUsage / self::PERIOD_DAYS;
        $leadTime = (int) ($item->lead_time_days ?? 1);
        $leadTimeDemand = $averageDailyUsage * $leadTime;
        $safetyStock = (float) ($item->safety_stock ?? 0);

        $rop = round($leadTimeDemand + $safetyStock, 2);
        $currentStock = (float) $item->stock;

        return [
            'total_usage' => round($totalUsage, 2),
            'period_days' => self::PERIOD_DAYS,
            'average_daily_usage' => round($averageDailyUsage, 2),
            'lead_time_days' => $leadTime,
            'lead_time_demand' => round($leadTimeDemand, 2),
            'safety_stock' => $safetyStock,
            'rop' => $rop,
            'current_stock' => $currentStock,
            'needs_restock' => $currentStock <= $rop,
        ];
    }
}
