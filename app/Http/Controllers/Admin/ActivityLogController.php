<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $activityLogs = ActivityLog::query()
            ->with([
                'creator:id,name',
                'updater:id,name',
                'deleter:id,name',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('table_name', 'like', "%{$search}%")
                        ->orWhere('record_id', 'like', "%{$search}%")
                        ->orWhereHas('creator', function ($user) use ($search) {
                            $user->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('updater', function ($user) use ($search) {
                            $user->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('deleter', function ($user) use ($search) {
                            $user->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('table'), function ($query) use ($request) {
                $query->where(
                    'table_name',
                    $request->table
                );
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $tables = ActivityLog::query()
            ->select('table_name')
            ->distinct()
            ->orderBy('table_name')
            ->pluck('table_name');

        return view(
            'admin.activity_logs.index',
            compact(
                'activityLogs',
                'tables'
            )
        );
    }

    public function show(int $id): View
    {
        $activityLog = ActivityLog::query()
            ->with([
                'creator:id,name',
                'updater:id,name',
                'deleter:id,name',
            ])
            ->findOrFail($id);

        return view(
            'admin.activity_logs.show',
            compact('activityLog')
        );
    }
}