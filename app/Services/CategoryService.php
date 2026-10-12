<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Category::query()
            ->leftJoin('activity_logs', function ($join) {
                $join->on('categories.activity_id', '=', 'activity_logs.id')
                    ->where('activity_logs.table_name', '=', 'categories');
            })
            ->select(
                'categories.*',
                'activity_logs.deleted_at as activity_deleted_at'
            )
            ->orderByRaw('activity_logs.deleted_at IS NOT NULL')
            ->orderBy('categories.name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data, int $userId): Category
    {
        return DB::transaction(function () use ($data, $userId) {
            $category = Category::create([
                'name' => $data['name'],
            ]);

            $activity = $this->activityLogService->create(
                $category,
                $userId
            );

            $category->update([
                'activity_id' => $activity->id,
            ]);

            return $category->fresh();
        });
    }

    public function update(
        Category $category,
        array $data,
        int $userId
    ): Category {
        return DB::transaction(function () use ($category, $data, $userId) {
            $category->update([
                'name' => $data['name'],
            ]);

            $this->activityLogService->update(
                $category,
                $userId
            );

            return $category->fresh();
        });
    }

    public function delete(Category $category, int $userId): void
    {
        DB::transaction(function () use ($category, $userId) {
            $this->activityLogService->delete(
                $category,
                $userId
            );
        });
    }

    public function restore(Category $category, int $userId): void
    {
        DB::transaction(function () use ($category, $userId) {
            $this->activityLogService->restore(
                $category,
                $userId
            );
        });
    }
}
