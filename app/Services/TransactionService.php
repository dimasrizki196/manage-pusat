<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function create(
        array $data,
        int $userId
    ): Transaction {
        return DB::transaction(function () use ($data, $userId) {

            $item = Item::query()
                ->lockForUpdate()
                ->findOrFail($data['item_id']);

            $quantity = (float) $data['quantity'];

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah transaksi harus lebih dari 0.',
                ]);
            }

            $stockBefore = (float) $item->stock;

            if ($data['transaction_type'] === 'keluar') {
                if ($quantity > $stockBefore) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Stok tidak mencukupi untuk transaksi keluar.',
                    ]);
                }

                $stockAfter = $stockBefore - $quantity;
            } else {
                $stockAfter = $stockBefore + $quantity;
            }

            $item->updateQuietly([
                'stock' => $stockAfter,
            ]);

            $transaction = Transaction::create([
                'item_id' => $item->id,
                'user_id' => $userId,
                'transaction_type' => $data['transaction_type'],
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
            ]);

            $activity = $this->activityLogService->create(
                $transaction,
                $userId
            );

            $transaction->updateQuietly([
                'activity_id' => $activity->id,
            ]);

            return $transaction->fresh([
                'item',
                'user',
                'activity',
            ]);
        });
    }
}