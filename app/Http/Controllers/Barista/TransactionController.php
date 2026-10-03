<?php

namespace App\Http\Controllers\Barista;

use App\Http\Controllers\Controller;
use App\Http\Requests\Barista\Transaction\StoreIncomingTransactionRequest;
use App\Http\Requests\Barista\Transaction\StoreOutgoingTransactionRequest;
use App\Models\Item;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\RestockRequest;
use Illuminate\Http\Request;


class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    public function incoming(): View
    {
        $items = Item::query()
            ->with(['category', 'unit', 'activity'])
            ->where(function ($query) {
                $query->whereDoesntHave('activity')
                    ->orWhereHas('activity', function ($activity) {
                        $activity->whereNull('deleted_at');
                    });
            })
            ->orderBy('name')
            ->get();

        $approvedRequests = RestockRequest::query()
            ->with(['item', 'user'])
            ->where('status', 'approved')
            ->where(function ($query) {
                $query->whereDoesntHave('activity')
                    ->orWhereHas('activity', function ($activity) {
                        $activity->whereNull('deleted_at');
                    });
            })
            ->latest('id')
            ->get();

        return view('barista.transactions.incoming', compact(
            'items',
            'approvedRequests'
        ));
    }

    public function storeIncoming(
        StoreIncomingTransactionRequest $request
    ): RedirectResponse {
        $this->transactionService->create([
            'item_id' => $request->validated('item_id'),
            'quantity' => $request->validated('quantity'),
            'transaction_type' => 'masuk',
            'transaction_source' => $request->validated('transaction_source'),
            'restock_request_id' => $request->validated('restock_request_id'),
        ], Auth::id());

        return redirect()
            ->route('barista.transactions.incoming')
            ->with('success', 'Barang masuk berhasil dicatat.');
    }

    public function outgoing(): View
    {
        $items = Item::query()
            ->with([
                'category',
                'unit',
                'activity',
            ])
            ->where(function ($query) {
                $query->whereDoesntHave('activity')
                    ->orWhereHas('activity', function ($activity) {
                        $activity->whereNull('deleted_at');
                    });
            })
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view(
            'barista.transactions.outgoing',
            compact('items')
        );
    }

    public function storeOutgoing(
        StoreOutgoingTransactionRequest $request
    ): RedirectResponse {
        $this->transactionService->create(
            [
                'item_id' => $request->validated('item_id'),
                'quantity' => $request->validated('quantity'),
                'transaction_type' => 'keluar',
            ],
            Auth::id()
        );

        return redirect()
            ->route('barista.transactions.outgoing')
            ->with(
                'success',
                'Barang keluar berhasil dicatat.'
            );
    }

    public function history(Request $request): View
    {
        $transactions = Transaction::query()
            ->with([
                'item.category',
                'item.unit',
                'user',
                'restockRequest',
                'activity',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->whereHas('item', function ($itemQuery) use ($search) {
                    $itemQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('transaction_type'), function ($query) use ($request) {
                $query->where(
                    'transaction_type',
                    $request->input('transaction_type')
                );
            })
            ->when($request->filled('transaction_source'), function ($query) use ($request) {
                $query->where(
                    'transaction_source',
                    $request->input('transaction_source')
                );
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $dateFrom = $request->input('date_from');

                $query->whereHas('activity', function ($activityQuery) use ($dateFrom) {
                    $activityQuery->whereDate('created_at', '>=', $dateFrom);
                });
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $dateTo = $request->input('date_to');

                $query->whereHas('activity', function ($activityQuery) use ($dateTo) {
                    $activityQuery->whereDate('created_at', '<=', $dateTo);
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('barista.transactions.history', compact('transactions'));
    }
}
