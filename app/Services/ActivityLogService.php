<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function create(
        Model $model,
        ?int $createdBy = null
    ): ActivityLog {

        return ActivityLog::create([
            'table_name' => $model->getTable(),
            'record_id' => $model->getKey(),

            'created_by' => $createdBy,
            'created_at' => now(),
        ]);
    }

    public function update(
        Model $model,
        ?int $updatedBy = null
    ): ActivityLog {

        return ActivityLog::updateOrCreate(
            [
                'table_name' => $model->getTable(),
                'record_id' => $model->getKey(),
            ],
            [
                'updated_by' => $updatedBy,
                'updated_at' => now(),
            ]
        );
    }

    public function delete(
        Model $model,
        ?int $deletedBy = null
    ): ActivityLog {

        return ActivityLog::updateOrCreate(
            [
                'table_name' => $model->getTable(),
                'record_id' => $model->getKey(),
            ],
            [
                'deleted_by' => $deletedBy,
                'deleted_at' => now(),
            ]
        );
    }

    public function restore(
        Model $model,
        ?int $updatedBy = null
    ): ActivityLog {

        return ActivityLog::updateOrCreate(
            [
                'table_name' => $model->getTable(),
                'record_id' => $model->getKey(),
            ],
            [
                'updated_by' => $updatedBy,
                'updated_at' => now(),
                'deleted_by' => null,
                'deleted_at' => null,
            ]
        );
    }
}
