<?php

namespace App\Services;

use App\Models\RestockRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RestockRequestService
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function paginate(
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        return RestockRequest::query()
            ->with([
                'item.category',
                'item.unit',
                'user',
                'processor',
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
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
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
}