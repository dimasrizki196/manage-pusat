<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Transaction\StoreTransactionRequest;
use App\Models\Item;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    public function index(Request $request): View
    {
        $transactions = Transaction::query()
            ->with([
                'item.unit',
                'user',
                'activity',
            ])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('item', function ($item) use ($search) {
                    $item->where('name', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->transaction_type,
                function ($query, $type) {
                    $query->where(
                        'transaction_type',
                        $type
                    );
                }
            )
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $items = Item::query()
            ->with('unit')
            ->orderBy('name')
            ->get();

        return view(
            'admin.transactions.index',
            compact(
                'transactions',
                'items'
            )
        );
    }

    public function store(
        StoreTransactionRequest $request
    ): RedirectResponse {
        $this->transactionService->create(
            $request->validated(),
            Auth::id()
        );

        return redirect()
            ->route('admin.transactions.index')
            ->with(
                'success',
                'Transaksi stok berhasil dicatat.'
            );
    }

    public function show(
        Transaction $transaction
    ): View {
        $transaction->load([
            'item.unit',
            'item.category',
            'user',
            'activity',
        ]);

        return view(
            'admin.transactions.show',
            compact('transaction')
        );
    }
}