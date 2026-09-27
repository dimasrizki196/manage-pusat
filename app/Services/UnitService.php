<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UnitService
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Unit::query()
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data, int $userId): Unit
    {
        return DB::transaction(function () use ($data, $userId) {
            $unit = Unit::create([
                'name' => $data['name'],
            ]);

            $activity = $this->activityLogService->create(
                $unit,
                $userId
            );

            $unit->update([
                'activity_id' => $activity->id,
            ]);

            return $unit->fresh();
        });
    }

    public function update(
        Unit $unit,
        array $data,
        int $userId
    ): Unit {
        return DB::transaction(function () use (
            $unit,
            $data,
            $userId
        ) {
            $unit->update([
                'name' => $data['name'],
            ]);

            $this->activityLogService->update(
                $unit,
                $userId
            );

            return $unit->fresh();
        });
    }

    public function delete(
        Unit $unit,
        int $userId
    ): void {
        DB::transaction(function () use ($unit, $userId) {
            $this->activityLogService->delete(
                $unit,
                $userId
            );
        });
    }

    public function restore(
        Unit $unit,
        int $userId
    ): Unit {
        return DB::transaction(function () use ($unit, $userId) {
            $this->activityLogService->restore(
                $unit,
                $userId
            );

            return $unit->fresh();
        });
    }
}
