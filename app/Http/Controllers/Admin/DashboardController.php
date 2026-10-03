<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\RestockRequest;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $activeItemsQuery = Item::query()
            ->where(function ($query) {
                $query->whereDoesntHave('activity')
                    ->orWhereHas('activity', function ($activity) {
                        $activity->whereNull('deleted_at');
                    });
            });

        $totalItems = (clone $activeItemsQuery)->count();

        $lowStockItems = (clone $activeItemsQuery)
            ->whereColumn('stock', '<=', 'stock_min')
            ->with(['category', 'unit', 'activity'])
            ->orderBy('stock')
            ->orderBy('name')
            ->get();

        $lowStockCount = $lowStockItems->count();

        $pendingRestockCount = RestockRequest::query()
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereDoesntHave('activity')
                    ->orWhereHas('activity', function ($activity) {
                        $activity->whereNull('deleted_at');
                    });
            })
            ->count();

        $activeUsers = User::query()
            ->where('role', '!=', 'admin')
            ->count();

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $todayTransactions = Transaction::query()
            ->whereHas('activity', function ($query) use ($today, $tomorrow) {
                $query->where('created_at', '>=', $today)
                    ->where('created_at', '<', $tomorrow);
            })
            ->get();

        $todayIncoming = $todayTransactions
            ->where('transaction_type', 'masuk')
            ->sum('quantity');

        $todayOutgoing = $todayTransactions
            ->where('transaction_type', 'keluar')
            ->sum('quantity');

        $todayTransactionCount = $todayTransactions->count();

        $todayIncomingCount = $todayTransactions
            ->where('transaction_type', 'masuk')
            ->count();

        $todayOutgoingCount = $todayTransactions
            ->where('transaction_type', 'keluar')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Grafik Transaksi 7 Hari
        |--------------------------------------------------------------------------
        */

        $transactionStart = Carbon::today()->subDays(6);
        $transactionEnd = Carbon::tomorrow();

        $transactionChartData = Transaction::query()
            ->with('activity')
            ->whereHas('activity', function ($query) use ($transactionStart, $transactionEnd) {
                $query->where('created_at', '>=', $transactionStart)
                    ->where('created_at', '<', $transactionEnd);
            })
            ->get()
            ->groupBy(function ($transaction) {
                return $transaction->activity?->created_at?->format('Y-m-d');
            });

        $transactionChartLabels = [];
        $transactionChartIncoming = [];
        $transactionChartOutgoing = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $key = $date->format('Y-m-d');

            $dayTransactions = $transactionChartData->get($key, collect());

            $transactionChartLabels[] = $date->translatedFormat('d M');

            $transactionChartIncoming[] = (float) $dayTransactions
                ->where('transaction_type', 'masuk')
                ->sum('quantity');

            $transactionChartOutgoing[] = (float) $dayTransactions
                ->where('transaction_type', 'keluar')
                ->sum('quantity');
        }

        /*
        |--------------------------------------------------------------------------
        | Grafik Stok Berdasarkan Kategori
        |--------------------------------------------------------------------------
        */

        $stockByCategory = (clone $activeItemsQuery)
            ->with('category')
            ->get()
            ->groupBy(function ($item) {
                return $item->category?->name ?? 'Tanpa Kategori';
            })
            ->map(function ($items) {
                return (float) $items->sum('stock');
            })
            ->sortDesc();

        $stockCategoryLabels = $stockByCategory->keys()->values();
        $stockCategoryValues = $stockByCategory->values();

        /*
        |--------------------------------------------------------------------------
        | Transaksi Terbaru
        |--------------------------------------------------------------------------
        */

        $recentTransactions = Transaction::query()
            ->with([
                'item.unit',
                'user',
                'activity',
            ])
            ->whereHas('activity', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->latest('id')
            ->limit(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Pengajuan Restock Terbaru
        |--------------------------------------------------------------------------
        */

        $recentRestockRequests = RestockRequest::query()
            ->with([
                'item.unit',
                'user',
                'processor',
                'activity',
            ])
            ->where(function ($query) {
                $query->whereDoesntHave('activity')
                    ->orWhereHas('activity', function ($activity) {
                        $activity->whereNull('deleted_at');
                    });
            })
            ->latest('id')
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'totalItems' => $totalItems,
            'lowStockCount' => $lowStockCount,
            'pendingRestockCount' => $pendingRestockCount,
            'activeUsers' => $activeUsers,
            'todayIncoming' => $todayIncoming,
            'todayOutgoing' => $todayOutgoing,
            'todayTransactionCount' => $todayTransactionCount,
            'todayIncomingCount' => $todayIncomingCount,
            'todayOutgoingCount' => $todayOutgoingCount,
            'lowStockItems' => $lowStockItems,
            'recentTransactions' => $recentTransactions,
            'recentRestockRequests' => $recentRestockRequests,
            'transactionChartLabels' => $transactionChartLabels,
            'transactionChartIncoming' => $transactionChartIncoming,
            'transactionChartOutgoing' => $transactionChartOutgoing,
            'stockCategoryLabels' => $stockCategoryLabels,
            'stockCategoryValues' => $stockCategoryValues,
        ]);
    }
}