<?php

namespace App\Http\Controllers\Barista;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\RestockRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $activeItems = Item::query()
            ->with('activity')
            ->where(function ($query) {
                $query->whereDoesntHave('activity')
                    ->orWhereHas('activity', function ($activity) {
                        $activity->whereNull('deleted_at');
                    });
            })
            ->get();

        $totalItems = $activeItems->count();

        $lowStockItems = $activeItems
            ->filter(function ($item) {
                return (float) $item->stock <= (float) $item->stock_min;
            })
            ->sortBy('stock')
            ->take(5);

        $lowStockCount = $activeItems
            ->filter(function ($item) {
                return (float) $item->stock <= (float) $item->stock_min;
            })
            ->count();

        $pendingRestockCount = RestockRequest::query()
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereDoesntHave('activity')
                    ->orWhereHas('activity', function ($activity) {
                        $activity->whereNull('deleted_at');
                    });
            })
            ->count();

        $todayIncoming = Transaction::query()
            ->where('transaction_type', 'masuk')
            ->whereHas('activity', function ($query) {
                $query->whereDate('created_at', today());
            })
            ->count();

        $todayOutgoing = Transaction::query()
            ->where('transaction_type', 'keluar')
            ->whereHas('activity', function ($query) {
                $query->whereDate('created_at', today());
            })
            ->count();

        $recentTransactions = Transaction::query()
            ->with([
                'item.unit',
                'activity',
            ])
            ->where('user_id', Auth::id())
            ->latest('id')
            ->take(5)
            ->get();

        return view('barista.dashboard', compact(
            'totalItems',
            'lowStockCount',
            'pendingRestockCount',
            'todayIncoming',
            'todayOutgoing',
            'lowStockItems',
            'recentTransactions'
        ));
    }
}