<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemService
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function paginate(
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        return Item::query()
            ->with([
                'category',
                'unit',
                'activity',
            ])
            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhereHas('category', function ($category) use ($search) {
                                $category->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('unit', function ($unit) use ($search) {
                                $unit->where('name', 'like', "%{$search}%");
                            });
                    });
                }
            )
            ->when(
                $filters['category_id'] ?? null,
                function ($query, $categoryId) {
                    $query->where('category_id', $categoryId);
                }
            )
            ->when(
                isset($filters['status']) && $filters['status'] !== '',
                function ($query) use ($filters) {
                    if ($filters['status'] === 'active') {
                        $query->where(function ($q) {
                            $q->whereDoesntHave('activity')
                                ->orWhereHas('activity', function ($activity) {
                                    $activity->whereNull('deleted_at');
                                });
                        });
                    }

                    if ($filters['status'] === 'inactive') {
                        $query->whereHas('activity', function ($activity) {
                            $activity->whereNotNull('deleted_at');
                        });
                    }
                }
            )
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data, int $userId): Item
    {
        return DB::transaction(function () use ($data, $userId) {
            $item = Item::create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'unit_id' => $data['unit_id'],
                'stock' => $data['stock'] ?? 0,
                'stock_min' => $data['stock_min'] ?? 0,
            ]);

            $activity = $this->activityLogService->create(
                $item,
                $userId
            );

            $item->updateQuietly([
                'activity_id' => $activity->id,
            ]);

            return $item->fresh([
                'category',
                'unit',
            ]);
        });
    }

    public function update(
        Item $item,
        array $data,
        int $userId
    ): Item {
        return DB::transaction(function () use ($item, $data, $userId) {
            $item->update([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'unit_id' => $data['unit_id'],
                'stock_min' => $data['stock_min'] ?? 0,
            ]);

            $this->activityLogService->update(
                $item,
                $userId
            );

            return $item->fresh([
                'category',
                'unit',
            ]);
        });
    }

    public function softDelete(
        Item $item,
        int $userId
    ): void {
        DB::transaction(function () use ($item, $userId) {
            $this->activityLogService->delete(
                $item,
                $userId
            );
        });
    }


    public function forceDelete(Item $item, int $userId): void
    {
        DB::transaction(function () use ($item) {
            $hasRestockRequests = DB::table('restock_requests')
                ->where('item_id', $item->id)
                ->exists();
    
            $hasTransactions = DB::table('transactions')
                ->where('item_id', $item->id)
                ->exists();
    
            if ($hasRestockRequests || $hasTransactions) {
                throw ValidationException::withMessages([
                    'item' => 'Barang tidak dapat dihapus permanen karena masih memiliki riwayat pengajuan restock atau transaksi.',
                ]);
            }
    
            $item->delete();
        });
    }


    public function restore(Item $item, int $userId): Item
    {
        return DB::transaction(function () use ($item, $userId) {
            $this->activityLogService->restore($item, $userId);

            return $item->fresh([
                'category',
                'unit',
                'activity',
            ]);
        });
    }
}
