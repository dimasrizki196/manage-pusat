<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\RestockRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function create(array $data, int $userId): Transaction
    {
        return DB::transaction(function () use ($data, $userId) {
            $item = Item::query()
                ->with('activity')
                ->lockForUpdate()
                ->findOrFail($data['item_id']);

            if ($item->isInactive()) {
                throw ValidationException::withMessages([
                    'item_id' => 'Barang yang dipilih sudah tidak aktif.',
                ]);
            }

            $quantity = (float) $data['quantity'];

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah transaksi harus lebih dari 0.',
                ]);
            }

            $transactionType = $data['transaction_type'];
            $transactionSource = $data['transaction_source'] ?? null;
            $restockRequestId = $data['restock_request_id'] ?? null;

            if (! in_array($transactionType, ['masuk', 'keluar'], true)) {
                throw ValidationException::withMessages([
                    'transaction_type' => 'Jenis transaksi tidak valid.',
                ]);
            }

            if ($transactionType === 'masuk') {
                if ($transactionSource !== null && ! in_array($transactionSource, ['pengajuan', 'urgent'], true)) {
                    throw ValidationException::withMessages([
                        'transaction_source' => 'Sumber barang masuk tidak valid.',
                    ]);
                }

                if ($transactionSource === 'pengajuan') {
                    if (! $restockRequestId) {
                        throw ValidationException::withMessages([
                            'restock_request_id' => 'Pengajuan restock wajib dipilih.',
                        ]);
                    }

                    $restockRequest = RestockRequest::query()
                        ->lockForUpdate()
                        ->findOrFail($restockRequestId);

                    if ($restockRequest->status !== 'approved') {
                        throw ValidationException::withMessages([
                            'restock_request_id' => 'Pengajuan belum disetujui.',
                        ]);
                    }

                    if ($restockRequest->item_id !== $item->id) {
                        throw ValidationException::withMessages([
                            'restock_request_id' => 'Barang tidak sesuai dengan pengajuan.',
                        ]);
                    }
                }
            }

            if ($transactionType === 'keluar') {
                $transactionSource = null;
                $restockRequestId = null;
            }

            $stockBefore = (float) $item->stock;

            if ($transactionType === 'keluar') {
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
                'transaction_type' => $transactionType,
                'transaction_source' => $transactionSource,
                'restock_request_id' => $restockRequestId,
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
                'restockRequest',
                'activity',
            ]);
        });
    }
}
