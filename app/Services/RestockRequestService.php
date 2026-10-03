<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\RestockRequest;
use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RestockRequestService
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return RestockRequest::query()
            ->with([
                'item.category',
                'item.unit',
                'user',
                'processor',
                'activity',
            ])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('item', function ($item) use ($search) {
                        $item->where('name', 'like', "%{$search}%");
                    })->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when(
                isset($filters['status']) && $filters['status'] !== '',
                function ($query) use ($filters) {
                    $query->where('status', $filters['status']);
                }
            )
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateByUser(
        int $userId,
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        return RestockRequest::query()
            ->with([
                'item.category',
                'item.unit',
                'user',
                'processor',
                'activity',
            ])
            ->where('user_id', $userId)
            ->whereHas('activity', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('item', function ($item) use ($search) {
                    $item->where('name', 'like', "%{$search}%");
                });
            })
            ->when(
                isset($filters['status']) && $filters['status'] !== '',
                function ($query) use ($filters) {
                    $query->where('status', $filters['status']);
                }
            )
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findByUser(int $id, int $userId): RestockRequest
    {
        return RestockRequest::query()
            ->with([
                'item.category',
                'item.unit',
                'user',
                'processor',
                'activity',
            ])
            ->where('user_id', $userId)
            ->findOrFail($id);
    }

    public function updateByUser(
        RestockRequest $restockRequest,
        array $data,
        int $userId
    ): RestockRequest {
        return DB::transaction(function () use (
            $restockRequest,
            $data,
            $userId
        ) {
            $restockRequest->load('activity');

            if ($restockRequest->user_id !== $userId) {
                abort(403);
            }

            if ($restockRequest->activity?->deleted_at !== null) {
                throw new \RuntimeException(
                    'Ajuan restock yang sudah dihapus tidak dapat diubah.'
                );
            }

            if ($restockRequest->status !== 'pending') {
                throw new \RuntimeException(
                    'Ajuan restock yang sudah diproses tidak dapat diubah.'
                );
            }

            $item = Item::query()
                ->with('activity')
                ->findOrFail($data['item_id']);

            if ($item->isInactive()) {
                throw new \RuntimeException(
                    'Barang yang dipilih sudah nonaktif.'
                );
            }

            $hasPending = RestockRequest::query()
                ->where('item_id', $item->id)
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->where('id', '!=', $restockRequest->id)
                ->whereHas('activity', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->exists();

            if ($hasPending) {
                throw new \RuntimeException(
                    'Masih ada pengajuan barang ini yang sedang menunggu persetujuan.'
                );
            }

            $restockRequest->update([
                'item_id' => $item->id,
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? null,
            ]);

            if ($restockRequest->activity) {
                $restockRequest->activity->update([
                    'updated_by' => $userId,
                    'updated_at' => now(),
                ]);
            }

            return $restockRequest->fresh([
                'item.category',
                'item.unit',
                'user',
                'processor',
                'activity',
            ]);
        });
    }

    public function softDeleteByUser(
        RestockRequest $restockRequest,
        int $userId
    ): void {
        DB::transaction(function () use ($restockRequest, $userId) {
            $restockRequest->load('activity');

            if ($restockRequest->user_id !== $userId) {
                abort(403);
            }

            if ($restockRequest->activity?->deleted_at !== null) {
                throw new \RuntimeException(
                    'Ajuan restock sudah dihapus.'
                );
            }

            if ($restockRequest->status !== 'pending') {
                throw new \RuntimeException(
                    'Ajuan restock yang sudah diproses tidak dapat dihapus.'
                );
            }

            $this->activityLogService->delete(
                $restockRequest,
                $userId
            );
        });
    }

    public function find(int $id): RestockRequest
    {
        return RestockRequest::query()
            ->with([
                'item.category',
                'item.unit',
                'user',
                'processor',
                'activity',
            ])
            ->findOrFail($id);
    }

    public function create(array $data, int $userId): RestockRequest
    {
        return DB::transaction(function () use ($data, $userId) {
            $item = Item::query()
                ->with('activity')
                ->findOrFail($data['item_id']);

            if ($item->isInactive()) {
                throw new \RuntimeException(
                    'Barang yang dipilih sudah tidak aktif.'
                );
            }

            $hasPending = RestockRequest::query()
                ->where('item_id', $item->id)
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->whereHas('activity', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->exists();

            if ($hasPending) {
                throw new \RuntimeException(
                    'Masih ada pengajuan barang ini yang sedang menunggu persetujuan.'
                );
            }

            $restockRequest = RestockRequest::create([
                'item_id' => $item->id,
                'user_id' => $userId,
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? null,
                'status' => 'pending',
            ]);

            $activity = ActivityLog::create([
                'table_name' => 'restock_requests',
                'record_id' => $restockRequest->id,
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            $restockRequest->update([
                'activity_id' => $activity->id,
            ]);

            return $restockRequest->fresh([
                'item.category',
                'item.unit',
                'user',
                'processor',
                'activity',
            ]);
        });
    }

    public function approve(
        int $id,
        int $adminId
    ): RestockRequest {
        return DB::transaction(function () use ($id, $adminId) {
            $restockRequest = RestockRequest::query()
                ->with(['item.activity', 'activity'])
                ->lockForUpdate()
                ->findOrFail($id);

            if ($restockRequest->activity?->deleted_at !== null) {
                throw new \RuntimeException(
                    'Ajuan restock yang sudah dihapus tidak dapat disetujui.'
                );
            }

            if ($restockRequest->status !== 'pending') {
                throw new \RuntimeException(
                    'Ajuan restock sudah diproses sebelumnya.'
                );
            }

            $item = Item::query()
                ->with('activity')
                ->findOrFail($restockRequest->item_id);

            if ($item->isInactive()) {
                throw new \RuntimeException(
                    'Barang sudah nonaktif sehingga ajuan tidak dapat disetujui.'
                );
            }

            $restockRequest->update([
                'status' => 'approved',
                'processed_by' => $adminId,
                'processed_at' => now(),
                'rejection_reason' => null,
            ]);

            if ($restockRequest->activity) {
                $restockRequest->activity->update([
                    'updated_by' => $adminId,
                    'updated_at' => now(),
                ]);
            }

            return $restockRequest->fresh([
                'item.category',
                'item.unit',
                'user',
                'processor',
                'activity',
            ]);
        });
    }

    public function reject(
        int $id,
        int $adminId,
        string $reason
    ): RestockRequest {
        return DB::transaction(function () use ($id, $adminId, $reason) {
            $restockRequest = RestockRequest::query()
                ->with('activity')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($restockRequest->activity?->deleted_at !== null) {
                throw new \RuntimeException(
                    'Ajuan restock yang sudah dihapus tidak dapat ditolak.'
                );
            }

            if ($restockRequest->status !== 'pending') {
                throw new \RuntimeException(
                    'Ajuan restock sudah diproses sebelumnya.'
                );
            }

            $restockRequest->update([
                'status' => 'rejected',
                'processed_by' => $adminId,
                'processed_at' => now(),
                'rejection_reason' => $reason,
            ]);

            if ($restockRequest->activity) {
                $restockRequest->activity->update([
                    'updated_by' => $adminId,
                    'updated_at' => now(),
                ]);
            }

            return $restockRequest->fresh([
                'item.category',
                'item.unit',
                'user',
                'processor',
                'activity',
            ]);
        });
    }

    public function softDelete(
        RestockRequest $restockRequest,
        int $userId
    ): void {
        DB::transaction(function () use ($restockRequest, $userId) {
            $this->activityLogService->delete(
                $restockRequest,
                $userId
            );
        });
    }

    public function restore(
        RestockRequest $restockRequest,
        int $userId
    ): RestockRequest {
        return DB::transaction(function () use (
            $restockRequest,
            $userId
        ) {
            $this->activityLogService->restore(
                $restockRequest,
                $userId
            );

            return $restockRequest->fresh([
                'item.category',
                'item.unit',
                'user',
                'processor',
                'activity',
            ]);
        });
    }

    public function forceDelete(
        RestockRequest $restockRequest
    ): void {
        DB::transaction(function () use ($restockRequest) {
            $restockRequest->forceDelete();
        });
    }
}
